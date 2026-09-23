<?php

namespace Database\Seeders;

use App\Models\Clinician;
use App\Models\PerformanceLog;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds a single sample practice with clinicians and four quarters of
 * performance logs. Idempotent — safe to re-run during prototyping.
 */
class DashboardSampleSeeder extends Seeder
{
    public function run(): void
    {
        $practice = Practice::firstOrCreate(
            ['slug' => 'sample-practice'],
            [
                'name' => 'Sample Mental Health Practice',
                'email' => 'owner@samplepractice.test',
                'timezone' => 'America/New_York',
                'description' => 'Seeded sample practice for dashboard prototype.',
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@samplepractice.test'],
            [
                'name' => 'Sam Owner',
                'password' => Hash::make('password'),
                'practice_id' => $practice->id,
                'email_verified_at' => now(),
            ]
        );
        if ($owner->practice_id !== $practice->id) {
            $owner->update(['practice_id' => $practice->id]);
        }

        $clinicians = [
            ['Alex',  'Rivera',   'Anxiety & CBT',       'active',   22, 25, 18, 165],
            ['Jordan','Kim',      'Couples Therapy',     'active',   16, 24, 14, 180],
            ['Morgan','Patel',    'Trauma / EMDR',       'active',   24, 25, 19, 200],
            ['Casey', 'Nguyen',   'Child & Adolescent',  'active',   10, 22, 12, 150],
            ['Taylor','Brooks',   'Substance Use',       'inactive',  0, 20,  0, 160],
        ];

        foreach ($clinicians as [$first, $last, $specialty, $status, $cur, $max, $sessWk, $rate]) {
            Clinician::updateOrCreate(
                [
                    'practice_id' => $practice->id,
                    'first_name' => $first,
                    'last_name' => $last,
                ],
                [
                    'specialty' => $specialty,
                    'status' => $status,
                    'current_caseload' => $cur,
                    'max_caseload' => $max,
                    'average_sessions_per_week' => $sessWk,
                    'avg_billable_rate' => $rate,
                ]
            );
        }

        // Four quarterly snapshots, oldest → newest, showing a soft margin dip.
        $quarters = [
            ['2025-06-01', 168000, 122000, 78, 940],
            ['2025-09-01', 182000, 134000, 84, 1020],
            ['2025-12-01', 195000, 151000, 89, 1080],
            ['2026-03-01', 204000, 168000, 92, 1130],
        ];

        foreach ($quarters as [$date, $rev, $exp, $clients, $sessions]) {
            PerformanceLog::updateOrCreate(
                [
                    'practice_id' => $practice->id,
                    'period_type' => 'quarter',
                    'date_period' => $date,
                ],
                [
                    'total_revenue' => $rev,
                    'total_expenses' => $exp,
                    'total_clients' => $clients,
                    'total_sessions' => $sessions,
                ]
            );
        }

        $this->command?->info("Seeded practice '{$practice->name}' (owner: owner@samplepractice.test / password)");
    }
}
