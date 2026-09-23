<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_id',
        'period_type',
        'date_period',
        'total_revenue',
        'total_expenses',
        'total_clients',
        'total_sessions',
        'marketing_spend',
        'payroll',
        'admin_costs',
        'insurance_mix',
        'cancellations',
        'no_shows',
    ];

    protected $casts = [
        'date_period' => 'date',
        'total_revenue' => 'float',
        'total_expenses' => 'float',
        'total_clients' => 'integer',
        'total_sessions' => 'integer',
        'marketing_spend' => 'float',
        'payroll' => 'float',
        'admin_costs' => 'float',
        'insurance_mix' => 'array',
        'cancellations' => 'integer',
        'no_shows' => 'integer',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function scopeForPractice($query, int $practiceId)
    {
        return $query->where('practice_id', $practiceId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('date_period');
    }
}
