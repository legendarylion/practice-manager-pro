<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'color',
        'location_type',
        'location_details',
        'active',
        'custom_questions',
        'practice_id',
        'user_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'custom_questions' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($eventType) {
            $eventType->slug = Str::slug($eventType->name . '-' . Str::random(6));
        });
    }

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}