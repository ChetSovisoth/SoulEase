# Test Accounts & Data

This document contains all test accounts and data seeded into the database for testing the Online Therapy System.

## Default Password

**All accounts use the same password:** `password`

## Test Accounts

### Admin Account

| Email | Name | Role |
|-------|------|------|
| admin@therapy.com | Admin User | admin |

**Admin Features:**
- View system-wide statistics
- Monitor all users and sessions
- Access admin dashboard with recent activity

---

### Therapist Accounts

| Email | Name | Specialization | Rate/Hour | Experience | Languages |
|-------|------|----------------|-----------|------------|-----------|
| sarah@therapy.com | Dr. Sarah Johnson | Anxiety & Depression | $120 | 10 years | English, Spanish |
| michael@therapy.com | Dr. Michael Chen | Trauma & PTSD | $150 | 8 years | English, Mandarin |
| emily@therapy.com | Dr. Emily Rodriguez | Family & Relationships | $130 | 12 years | English |
| lisa@therapy.com | Dr. Lisa Thompson | Stress & Burnout | $110 | 7 years | English, French |
| james@therapy.com | Dr. James Wilson | Addiction & Substance Abuse | $140 | 15 years | English |

**Therapist Features:**
- Manage availability slots
- View and confirm session bookings
- Join video sessions
- Add session notes
- Message with patients

---

### Patient Accounts

| Email | Name | Bio |
|-------|------|-----|
| john@example.com | John Doe | Looking for help with stress management |
| jane@example.com | Jane Smith | Seeking support for anxiety |
| bob@example.com | Bob Williams | Need help with relationship issues |
| alice@example.com | Alice Cooper | Dealing with work stress and burnout |
| david@example.com | David Martinez | Looking for guidance on life transitions |

**Patient Features:**
- Browse and search therapists
- Book therapy sessions
- Join video sessions
- Make payments
- Message with therapists

---

## Seeded Test Data

### Sessions (5 Total)

1. **Confirmed Session**
   - Patient: John Doe
   - Therapist: Dr. Sarah Johnson
   - Status: Confirmed
   - Payment: Completed
   - Notes: Initial consultation for anxiety management

2. **Pending Session**
   - Patient: Jane Smith
   - Therapist: Dr. Michael Chen
   - Status: Pending (awaiting payment)
   - Payment: Pending

3. **Confirmed Session**
   - Patient: Bob Williams
   - Therapist: Dr. Emily Rodriguez
   - Status: Confirmed
   - Payment: Completed
   - Notes: Couples counseling session

4. **Completed Session (Past)**
   - Patient: John Doe
   - Therapist: Dr. Sarah Johnson
   - Status: Completed
   - Date: 3 days ago
   - Payment: Completed
   - Notes: Patient showed great progress with anxiety management techniques

5. **Cancelled Session**
   - Patient: Alice Cooper
   - Therapist: Dr. Michael Chen
   - Status: Cancelled
   - Payment: Refunded

### Messages (8 Total)

- 3 messages in conversation between John and Dr. Johnson (all read)
- 1 unread message from Jane to Dr. Chen
- 2 messages between Bob and Dr. Rodriguez (1 unread)
- 1 unread message from Alice to Dr. Thompson
- 1 unread message from David to Dr. Johnson

### Availability Slots

- **245 slots total** across all 5 therapists
- Covers the next **7 days**
- **Morning slots:** 9 AM - 12 PM (3 slots per day)
- **Afternoon slots:** 2 PM - 6 PM (4 slots per day)
- **7 slots per day × 7 days × 5 therapists = 245 slots**

---

## Testing Scenarios

### As a Patient (john@example.com)

1. **Browse Therapists:**
   - Go to "Browse Therapists"
   - Filter by specialization (e.g., "Anxiety")
   - View therapist profiles

2. **View Your Sessions:**
   - Check dashboard for upcoming sessions
   - See confirmed session with Dr. Johnson
   - View past completed session

3. **Join Video Session:**
   - Click "Join Video" on confirmed session
   - Test Jitsi Meet integration

4. **Send Messages:**
   - Go to Messages
   - Continue conversation with Dr. Johnson
   - View read/unread status

### As a Therapist (sarah@therapy.com)

1. **View Dashboard:**
   - See today's sessions
   - Check statistics (total, completed, pending)
   - View upcoming appointments

2. **Manage Sessions:**
   - Confirm pending sessions
   - Add session notes
   - Mark sessions as completed

3. **Check Messages:**
   - View conversations with patients
   - Respond to unread messages

4. **Join Video Session:**
   - Access video room for confirmed sessions
   - Take notes during session

### As an Admin (admin@therapy.com)

1. **View System Stats:**
   - Total users (11: 1 admin + 5 therapists + 5 patients)
   - Total sessions (5)
   - Pending sessions
   - Completed sessions

2. **Monitor Activity:**
   - View recent sessions
   - See recent user registrations
   - Check payment statuses

---

## Quick Start Commands

### Reset and Reseed Database
```bash
php artisan migrate:fresh --seed
```

### Start Development Server
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (for hot reload)
npm run dev
```

### Access Application
- **URL:** http://localhost:8000
- **Login:** Use any email from the tables above
- **Password:** password

---

## Payment Testing

When testing payments, use these test card numbers:

- **Success:** 4242 4242 4242 4242
- **Expiry:** Any future date (e.g., 12/25)
- **CVV:** Any 3 digits (e.g., 123)

All payments in the system are **mock/demo** and no real transactions occur.

---

## Notes

- All therapists are **verified** and **available**
- Availability slots are generated for the **next 7 days**
- Sessions have various statuses to test different UI states
- Some messages are marked as read, others as unread for testing notifications
- Video room IDs are automatically generated for each session
- All timestamps are relative to the current date/time

---

## Troubleshooting

### If you need fresh data:
```bash
php artisan migrate:fresh --seed
```

### If sessions aren't showing:
- Check that the scheduled dates are in the future
- Verify the user role matches the view (patient sees their sessions as patient)

### If video doesn't work:
- Check that JITSI_DOMAIN is set in .env
- Ensure session status is "confirmed"
- Verify you're within the join window (15 min before to 60 min after)
