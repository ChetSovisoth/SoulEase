<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TherapistProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'qualifications',
        'years_of_experience',
        'hourly_rate',
        'languages',
        'is_verified',
        'is_available',
    ];

    protected $casts = [
        'languages' => 'array',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
