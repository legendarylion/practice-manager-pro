<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinician extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_id',
        'user_id',
        'first_name',
        'last_name',
        'specialty',
        'status',
        'current_caseload',
        'max_caseload',
        'average_sessions_per_week',
        'avg_billable_rate',
    ];

    protected $casts = [
        'current_caseload' => 'integer',
        'max_caseload' => 'integer',
        'average_sessions_per_week' => 'float',
        'avg_billable_rate' => 'float',
    ];

    protected $appends = ['full_name', 'utilization'];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getUtilizationAttribute(): float
    {
        return $this->max_caseload > 0
            ? round($this->current_caseload / $this->max_caseload, 4)
            : 0.0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
