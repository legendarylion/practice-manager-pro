<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Practice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'timezone',
        'website',
        'description',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($practice) {
            $practice->slug = Str::slug($practice->name);
        });
    }

    public function eventTypes()
    {
        return $this->hasMany(EventType::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function clinicians()
    {
        return $this->hasMany(Clinician::class);
    }

    public function performanceLogs()
    {
        return $this->hasMany(PerformanceLog::class);
    }
}