<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'bio',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function therapistProfile()
    {
        return $this->hasOne(TherapistProfile::class);
    }

    public function availabilitySlots()
    {
        return $this->hasMany(AvailabilitySlot::class, 'therapist_id');
    }

    public function patientSessions()
    {
        return $this->hasMany(TherapySession::class, 'patient_id');
    }

    public function therapistSessions()
    {
        return $this->hasMany(TherapySession::class, 'therapist_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function patientPayments()
    {
        return $this->hasMany(Payment::class, 'patient_id');
    }

    public function therapistPayments()
    {
        return $this->hasMany(Payment::class, 'therapist_id');
    }

    // Helper methods
    public function isPatient()
    {
        return $this->role === 'patient';
    }

    public function isTherapist()
    {
        return $this->role === 'therapist';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
