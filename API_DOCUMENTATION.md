# API Documentation - Online Therapy System

## Authentication

All API routes require authentication via Laravel Sanctum. Include the session cookie or API token in your requests.

## Response Format

All API responses follow this format:

```json
{
  "data": {},
  "message": "Success message",
  "errors": {}
}
```

## Endpoints

### Dashboard

#### GET /dashboard
Get role-based dashboard data

**Response:**
- For Patient: upcoming sessions, past sessions, unread messages
- For Therapist: upcoming sessions, today's sessions, unread messages, stats
- For Admin: system statistics, recent sessions, recent users

---

### Therapists

#### GET /therapists
List all verified and available therapists

**Query Parameters:**
- `specialization` (optional): Filter by specialization

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Dr. Sarah Johnson",
      "email": "sarah@therapy.com",
      "bio": "...",
      "therapist_profile": {
        "specialization": "Anxiety & Depression",
        "hourly_rate": "120.00",
        "years_of_experience": 10,
        ...
      }
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### GET /therapists/{id}
View therapist profile and available slots

**Response:**
```json
{
  "therapist": {...},
  "availableSlots": [...]
}
```

#### PUT /therapist/profile
Update therapist profile (therapists only)

**Request Body:**
```json
{
  "specialization": "Anxiety & Depression",
  "qualifications": "Ph.D. in Clinical Psychology",
  "years_of_experience": 10,
  "hourly_rate": 120.00,
  "languages": ["English", "Spanish"],
  "bio": "..."
}
```

---

### Availability Slots

#### GET /availability
List therapist's availability slots (therapists only)

**Response:**
```json
[
  {
    "id": 1,
    "start_time": "2025-10-11 09:00:00",
    "end_time": "2025-10-11 10:00:00",
    "is_booked": false
  }
]
```

#### POST /availability
Create a single availability slot (therapists only)

**Request Body:**
```json
{
  "start_time": "2025-10-11 09:00:00",
  "end_time": "2025-10-11 10:00:00"
}
```

#### POST /availability/bulk
Create multiple availability slots (therapists only)

**Request Body:**
```json
{
  "dates": ["2025-10-11", "2025-10-12"],
  "time_slots": [
    {"start": "09:00", "end": "10:00"},
    {"start": "10:00", "end": "11:00"}
  ]
}
```

#### DELETE /availability/{id}
Delete an availability slot (therapists only, if not booked)

---

### Therapy Sessions

#### GET /sessions
List user's sessions (filtered by role)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "patient": {...},
      "therapist": {...},
      "scheduled_at": "2025-10-11 09:00:00",
      "duration_minutes": 60,
      "status": "confirmed",
      "video_room_id": "room-..."
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### POST /sessions
Book a new session (patients only)

**Request Body:**
```json
{
  "therapist_id": 2,
  "availability_slot_id": 5
}
```

**Response:**
```json
{
  "message": "Session booked successfully!",
  "session": {...}
}
```

#### GET /sessions/{id}
View session details

**Response:**
```json
{
  "session": {
    "id": 1,
    "patient": {...},
    "therapist": {...},
    "scheduled_at": "2025-10-11 09:00:00",
    "status": "confirmed",
    "payment": {...},
    "messages": [...]
  }
}
```

#### PUT /sessions/{id}/status
Update session status (therapists and admins only)

**Request Body:**
```json
{
  "status": "confirmed" // pending|confirmed|completed|cancelled
}
```

#### GET /sessions/{id}/video
Join video room for session

**Requirements:**
- Session status must be "confirmed"
- User must be patient or therapist of the session

---

### Messages

#### GET /messages
List all conversations

**Response:**
```json
{
  "conversations": [
    {
      "id": 2,
      "name": "Dr. Sarah Johnson",
      "email": "sarah@therapy.com"
    }
  ]
}
```

#### GET /messages/conversation/{userId}
View conversation with specific user

**Response:**
```json
{
  "messages": [
    {
      "id": 1,
      "sender": {...},
      "receiver": {...},
      "content": "Hello!",
      "is_read": true,
      "created_at": "2025-10-10 10:00:00"
    }
  ],
  "recipient": {...}
}
```

#### POST /messages
Send a message

**Request Body:**
```json
{
  "receiver_id": 2,
  "content": "Hello, I have a question...",
  "therapy_session_id": 1 // optional
}
```

**Broadcasting:**
This endpoint broadcasts a `message.sent` event to the receiver's private channel `chat.{userId}`

#### GET /messages/unread-count
Get unread message count

**Response:**
```json
{
  "count": 5
}
```

---

### Payments

#### GET /payments/history
View payment history

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "therapy_session": {...},
      "patient": {...},
      "therapist": {...},
      "amount": "120.00",
      "status": "completed",
      "transaction_id": "txn_..."
    }
  ]
}
```

#### GET /payments/{id}
View payment details

**Response:**
```json
{
  "payment": {
    "id": 1,
    "amount": "120.00",
    "status": "completed",
    "payment_method": "credit_card",
    "transaction_id": "txn_...",
    "therapy_session": {...}
  }
}
```

#### POST /payments/{id}/process
Process a payment (patients only)

**Request Body:**
```json
{
  "payment_method": "credit_card", // credit_card|paypal|stripe
  "card_number": "4242424242424242",
  "card_exp": "12/25",
  "card_cvv": "123"
}
```

**Note:** This is a mock payment gateway. Real implementation would use Stripe, PayPal, etc.

**Response:**
- Updates payment status to "completed"
- Updates session status to "confirmed"
- Returns success message

---

## Broadcasting Channels

### Private Channel: chat.{userId}

**Event:** message.sent

**Payload:**
```json
{
  "message": {
    "id": 1,
    "sender_id": 2,
    "receiver_id": 1,
    "content": "Hello!",
    "is_read": false,
    "created_at": "2025-10-10 10:00:00",
    "sender": {...},
    "receiver": {...}
  }
}
```

**Authorization:**
Users can only subscribe to their own chat channel.

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "message": "Resource not found."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Server Error
```json
{
  "message": "Server Error"
}
```

---

## Rate Limiting

API routes are rate-limited to prevent abuse:
- 60 requests per minute for authenticated users
- 10 requests per minute for guests

## Pagination

List endpoints support pagination with the following query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

## Filtering & Sorting

Some endpoints support additional query parameters for filtering and sorting. Check individual endpoint documentation for details.
