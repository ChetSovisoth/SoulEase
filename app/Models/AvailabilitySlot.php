<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AvailabilitySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'start_time',
        'end_time',
        'is_booked',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_booked' => 'boolean',
    ];

    // Relationships
    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function therapySession()
    {
        return $this->hasOne(TherapySession::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false)
                    ->where('start_time', '>', now());
    }
}
