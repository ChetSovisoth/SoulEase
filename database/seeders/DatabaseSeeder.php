<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TherapistProfile;
use App\Models\AvailabilitySlot;
use App\Models\TherapySession;
use App\Models\Message;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@therapy.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'bio' => 'System Administrator',
            'email_verified_at' => now(),
        ]);

        // Create Therapists
        $therapist1 = User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah@therapy.com',
            'password' => Hash::make('password'),
            'role' => 'therapist',
            'phone' => '+1234567891',
            'bio' => 'Licensed clinical psychologist specializing in anxiety and depression.',
            'email_verified_at' => now(),
        ]);

        TherapistProfile::create([
            'user_id' => $therapist1->id,
            'specialization' => 'Anxiety & Depression',
            'qualifications' => 'Ph.D. in Clinical Psychology, Licensed Psychologist',
            'years_of_experience' => 10,
            'hourly_rate' => 120.00,
            'languages' => ['English', 'Spanish'],
            'is_verified' => true,
            'is_available' => true,
        ]);

        $therapist2 = User::create([
            'name' => 'Dr. Michael Chen',
            'email' => 'michael@therapy.com',
            'password' => Hash::make('password'),
            'role' => 'therapist',
            'phone' => '+1234567892',
            'bio' => 'Specializing in trauma and PTSD treatment.',
            'email_verified_at' => now(),
        ]);

        TherapistProfile::create([
            'user_id' => $therapist2->id,
            'specialization' => 'Trauma & PTSD',
            'qualifications' => 'Psy.D. in Clinical Psychology, EMDR Certified',
            'years_of_experience' => 8,
            'hourly_rate' => 150.00,
            'languages' => ['English', 'Mandarin'],
            'is_verified' => true,
            'is_available' => true,
        ]);

        $therapist3 = User::create([
            'name' => 'Dr. Emily Rodriguez',
            'email' => 'emily@therapy.com',
            'password' => Hash::make('password'),
            'role' => 'therapist',
            'phone' => '+1234567893',
            'bio' => 'Family and relationship counseling expert.',
            'email_verified_at' => now(),
        ]);

        TherapistProfile::create([
            'user_id' => $therapist3->id,
            'specialization' => 'Family & Relationships',
            'qualifications' => 'LMFT, M.A. in Marriage and Family Therapy',
            'years_of_experience' => 12,
            'hourly_rate' => 130.00,
            'languages' => ['English'],
            'is_verified' => true,
            'is_available' => true,
        ]);

        // Create Patients
        $patient1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '+1234567894',
            'bio' => 'Looking for help with stress management.',
            'email_verified_at' => now(),
        ]);

        $patient2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '+1234567895',
            'bio' => 'Seeking support for anxiety.',
            'email_verified_at' => now(),
        ]);

        $patient3 = User::create([
            'name' => 'Bob Williams',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '+1234567896',
            'bio' => 'Need help with relationship issues.',
            'email_verified_at' => now(),
        ]);

        // Additional patients for testing
        $patient4 = User::create([
            'name' => 'Alice Cooper',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '+1234567897',
            'bio' => 'Dealing with work stress and burnout.',
            'email_verified_at' => now(),
        ]);

        $patient5 = User::create([
            'name' => 'David Martinez',
            'email' => 'david@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '+1234567898',
            'bio' => 'Looking for guidance on life transitions.',
            'email_verified_at' => now(),
        ]);

        // Additional therapists
        $therapist4 = User::create([
            'name' => 'Dr. Lisa Thompson',
            'email' => 'lisa@therapy.com',
            'password' => Hash::make('password'),
            'role' => 'therapist',
            'phone' => '+1234567899',
            'bio' => 'Cognitive Behavioral Therapy specialist with focus on stress and burnout.',
            'email_verified_at' => now(),
        ]);

        TherapistProfile::create([
            'user_id' => $therapist4->id,
            'specialization' => 'Stress & Burnout',
            'qualifications' => 'Ph.D. in Counseling Psychology, CBT Certified',
            'years_of_experience' => 7,
            'hourly_rate' => 110.00,
            'languages' => ['English', 'French'],
            'is_verified' => true,
            'is_available' => true,
        ]);

        $therapist5 = User::create([
            'name' => 'Dr. James Wilson',
            'email' => 'james@therapy.com',
            'password' => Hash::make('password'),
            'role' => 'therapist',
            'phone' => '+1234567800',
            'bio' => 'Addiction counseling and substance abuse treatment specialist.',
            'email_verified_at' => now(),
        ]);

        TherapistProfile::create([
            'user_id' => $therapist5->id,
            'specialization' => 'Addiction & Substance Abuse',
            'qualifications' => 'LCSW, Certified Addiction Counselor',
            'years_of_experience' => 15,
            'hourly_rate' => 140.00,
            'languages' => ['English'],
            'is_verified' => true,
            'is_available' => true,
        ]);

        // Create Availability Slots for Therapists (Next 7 days)
        $therapists = [$therapist1, $therapist2, $therapist3, $therapist4, $therapist5];

        foreach ($therapists as $therapist) {
            for ($day = 1; $day <= 7; $day++) {
                $date = Carbon::now()->addDays($day);

                // Morning slots (9 AM - 12 PM)
                for ($hour = 9; $hour <= 11; $hour++) {
                    AvailabilitySlot::create([
                        'therapist_id' => $therapist->id,
                        'start_time' => $date->copy()->setTime($hour, 0),
                        'end_time' => $date->copy()->setTime($hour + 1, 0),
                        'is_booked' => false,
                    ]);
                }

                // Afternoon slots (2 PM - 6 PM)
                for ($hour = 14; $hour <= 17; $hour++) {
                    AvailabilitySlot::create([
                        'therapist_id' => $therapist->id,
                        'start_time' => $date->copy()->setTime($hour, 0),
                        'end_time' => $date->copy()->setTime($hour + 1, 0),
                        'is_booked' => false,
                    ]);
                }
            }
        }

        // Create some booked sessions
        $slot1 = AvailabilitySlot::where('therapist_id', $therapist1->id)->first();
        $slot1->update(['is_booked' => true]);

        $session1 = TherapySession::create([
            'patient_id' => $patient1->id,
            'therapist_id' => $therapist1->id,
            'availability_slot_id' => $slot1->id,
            'scheduled_at' => $slot1->start_time,
            'duration_minutes' => 60,
            'status' => 'confirmed',
            'video_room_id' => 'room-' . uniqid(),
            'notes' => 'Initial consultation for anxiety management.',
        ]);

        Payment::create([
            'therapy_session_id' => $session1->id,
            'patient_id' => $patient1->id,
            'therapist_id' => $therapist1->id,
            'amount' => 120.00,
            'currency' => 'USD',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'transaction_id' => 'txn_' . uniqid(),
            'payment_details' => ['card_last4' => '4242'],
        ]);

        $slot2 = AvailabilitySlot::where('therapist_id', $therapist2->id)->skip(2)->first();
        $slot2->update(['is_booked' => true]);

        $session2 = TherapySession::create([
            'patient_id' => $patient2->id,
            'therapist_id' => $therapist2->id,
            'availability_slot_id' => $slot2->id,
            'scheduled_at' => $slot2->start_time,
            'duration_minutes' => 60,
            'status' => 'pending',
            'video_room_id' => 'room-' . uniqid(),
        ]);

        Payment::create([
            'therapy_session_id' => $session2->id,
            'patient_id' => $patient2->id,
            'therapist_id' => $therapist2->id,
            'amount' => 150.00,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_method' => 'credit_card',
        ]);

        // Create more test sessions
        $slot3 = AvailabilitySlot::where('therapist_id', $therapist3->id)->skip(5)->first();
        $slot3->update(['is_booked' => true]);

        $session3 = TherapySession::create([
            'patient_id' => $patient3->id,
            'therapist_id' => $therapist3->id,
            'availability_slot_id' => $slot3->id,
            'scheduled_at' => $slot3->start_time,
            'duration_minutes' => 60,
            'status' => 'confirmed',
            'video_room_id' => 'room-' . uniqid(),
            'notes' => 'Couples counseling session.',
        ]);

        Payment::create([
            'therapy_session_id' => $session3->id,
            'patient_id' => $patient3->id,
            'therapist_id' => $therapist3->id,
            'amount' => 130.00,
            'currency' => 'USD',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'transaction_id' => 'txn_' . uniqid(),
            'payment_details' => ['card_last4' => '1234'],
        ]);

        // Past completed session
        $pastSlot = AvailabilitySlot::where('therapist_id', $therapist1->id)->skip(1)->first();
        $pastSlot->update(['is_booked' => true]);

        $pastSession = TherapySession::create([
            'patient_id' => $patient1->id,
            'therapist_id' => $therapist1->id,
            'availability_slot_id' => $pastSlot->id,
            'scheduled_at' => Carbon::now()->subDays(3),
            'duration_minutes' => 60,
            'status' => 'completed',
            'video_room_id' => 'room-' . uniqid(),
            'notes' => 'Patient showed great progress with anxiety management techniques.',
        ]);

        Payment::create([
            'therapy_session_id' => $pastSession->id,
            'patient_id' => $patient1->id,
            'therapist_id' => $therapist1->id,
            'amount' => 120.00,
            'currency' => 'USD',
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'transaction_id' => 'txn_' . uniqid(),
            'payment_details' => ['card_last4' => '4242'],
        ]);

        // Cancelled session
        $cancelledSlot = AvailabilitySlot::where('therapist_id', $therapist2->id)->skip(10)->first();
        $cancelledSlot->update(['is_booked' => false]);

        $cancelledSession = TherapySession::create([
            'patient_id' => $patient4->id,
            'therapist_id' => $therapist2->id,
            'availability_slot_id' => $cancelledSlot->id,
            'scheduled_at' => Carbon::now()->addDays(2),
            'duration_minutes' => 60,
            'status' => 'cancelled',
            'video_room_id' => 'room-' . uniqid(),
        ]);

        Payment::create([
            'therapy_session_id' => $cancelledSession->id,
            'patient_id' => $patient4->id,
            'therapist_id' => $therapist2->id,
            'amount' => 150.00,
            'currency' => 'USD',
            'status' => 'refunded',
            'payment_method' => 'credit_card',
            'transaction_id' => 'txn_' . uniqid(),
            'payment_details' => ['card_last4' => '5555'],
        ]);

        // Create some messages
        Message::create([
            'therapy_session_id' => $session1->id,
            'sender_id' => $patient1->id,
            'receiver_id' => $therapist1->id,
            'content' => 'Hi Dr. Johnson, I\'m looking forward to our session tomorrow.',
            'is_read' => true,
            'read_at' => now(),
        ]);

        Message::create([
            'therapy_session_id' => $session1->id,
            'sender_id' => $therapist1->id,
            'receiver_id' => $patient1->id,
            'content' => 'Hello John! I\'m glad you\'re ready. Please prepare any topics you\'d like to discuss.',
            'is_read' => true,
            'read_at' => now(),
        ]);

        Message::create([
            'therapy_session_id' => $session1->id,
            'sender_id' => $patient1->id,
            'receiver_id' => $therapist1->id,
            'content' => 'Sure, I have a few things I\'d like to talk about regarding work stress.',
            'is_read' => true,
            'read_at' => now(),
        ]);

        Message::create([
            'sender_id' => $patient2->id,
            'receiver_id' => $therapist2->id,
            'content' => 'Can I reschedule our appointment?',
            'is_read' => false,
        ]);

        Message::create([
            'therapy_session_id' => $session3->id,
            'sender_id' => $therapist3->id,
            'receiver_id' => $patient3->id,
            'content' => 'Hi Bob, looking forward to working with you on your relationship goals.',
            'is_read' => true,
            'read_at' => now(),
        ]);

        Message::create([
            'therapy_session_id' => $session3->id,
            'sender_id' => $patient3->id,
            'receiver_id' => $therapist3->id,
            'content' => 'Thank you, Dr. Rodriguez. My partner and I are really hoping this helps.',
            'is_read' => false,
        ]);

        // Additional messages for testing
        Message::create([
            'sender_id' => $patient4->id,
            'receiver_id' => $therapist4->id,
            'content' => 'Hi Dr. Thompson, I saw your profile and I\'m interested in booking a session for burnout.',
            'is_read' => false,
        ]);

        Message::create([
            'sender_id' => $patient5->id,
            'receiver_id' => $therapist1->id,
            'content' => 'Hello, I\'m going through a major life change and could use some guidance.',
            'is_read' => false,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('=== Test Accounts (Password: password) ===');
        $this->command->info('');
        $this->command->info('ADMIN:');
        $this->command->info('  admin@therapy.com');
        $this->command->info('');
        $this->command->info('THERAPISTS:');
        $this->command->info('  sarah@therapy.com - Dr. Sarah Johnson (Anxiety & Depression)');
        $this->command->info('  michael@therapy.com - Dr. Michael Chen (Trauma & PTSD)');
        $this->command->info('  emily@therapy.com - Dr. Emily Rodriguez (Family & Relationships)');
        $this->command->info('  lisa@therapy.com - Dr. Lisa Thompson (Stress & Burnout)');
        $this->command->info('  james@therapy.com - Dr. James Wilson (Addiction & Substance Abuse)');
        $this->command->info('');
        $this->command->info('PATIENTS:');
        $this->command->info('  john@example.com - John Doe');
        $this->command->info('  jane@example.com - Jane Smith');
        $this->command->info('  bob@example.com - Bob Williams');
        $this->command->info('  alice@example.com - Alice Cooper');
        $this->command->info('  david@example.com - David Martinez');
        $this->command->info('');
        $this->command->info('TEST DATA:');
        $this->command->info('  - 5 Therapists with verified profiles');
        $this->command->info('  - 5 Patients');
        $this->command->info('  - ' . AvailabilitySlot::count() . ' availability slots (next 7 days)');
        $this->command->info('  - ' . TherapySession::count() . ' therapy sessions (pending, confirmed, completed, cancelled)');
        $this->command->info('  - ' . Message::count() . ' messages');
        $this->command->info('  - ' . Payment::count() . ' payments');
        $this->command->info('');
    }
}
