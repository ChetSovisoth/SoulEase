# Online Therapy System (Prototype MVP)

A comprehensive Laravel-based online therapy booking and communication platform built with Inertia.js, Vue.js, and Tailwind CSS.

## Features

### 🔐 User Authentication & Profiles
- Secure sign-up/login using Laravel Jetstream
- Three user roles: Patient, Therapist, Admin
- Profile management with role-specific fields
- Two-factor authentication support

### 📅 Session Booking System
- Therapists can define availability time slots
- Patients can browse and book available slots
- Booking status tracking (pending, confirmed, completed, cancelled)
- Automated video room generation for each session

### 💬 Real-time Text Chat
- One-to-one messaging between therapists and patients
- Real-time updates using Laravel Echo + Pusher
- Read receipts and timestamps
- Message history tracking

### 🎥 Video Sessions
- Integrated Jitsi Meet for video consultations
- Private video rooms per session
- Simple iframe-based implementation

### 💳 Payment Integration (Mock)
- Mock payment gateway for prototyping
- Transaction tracking and history
- Support for multiple payment methods
- Automatic session confirmation after payment

### 👨‍💼 Admin Dashboard
- View all users and bookings
- Analytics (session count, revenue totals)
- User management capabilities

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Inertia.js + Vue.js 3
- **Styling**: Tailwind CSS
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **Real-time**: Laravel Echo + Pusher
- **Video**: Jitsi Meet
- **Authentication**: Laravel Jetstream + Sanctum

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

### Step-by-Step Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd online-therapy-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install --legacy-peer-deps
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**
   The project is pre-configured for SQLite. The database file will be created automatically. If you prefer MySQL/PostgreSQL, update the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=therapy_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Configure Pusher (Optional - for real-time chat)**
   Sign up for a free Pusher account at https://pusher.com/ and update your `.env`:
   ```env
   BROADCAST_CONNECTION=pusher
   PUSHER_APP_ID=your-app-id
   PUSHER_APP_KEY=your-app-key
   PUSHER_APP_SECRET=your-app-secret
   PUSHER_APP_CLUSTER=mt1

   VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
   VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
   ```

7. **Run Migrations and Seed Database**
   ```bash
   php artisan migrate:fresh --seed
   ```

8. **Build Assets**
   ```bash
   npm run build
   ```

9. **Start the Development Server**
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Test Accounts

After seeding, you can log in with these accounts:

### Admin
- Email: admin@therapy.com
- Password: password

### Therapists
- Dr. Sarah Johnson: sarah@therapy.com / password
- Dr. Michael Chen: michael@therapy.com / password
- Dr. Emily Rodriguez: emily@therapy.com / password

### Patients
- John Doe: john@example.com / password
- Jane Smith: jane@example.com / password
- Bob Williams: bob@example.com / password

## Project Structure

```
├── app/
│   ├── Events/              # Broadcasting events (MessageSent)
│   ├── Http/Controllers/    # All application controllers
│   ├── Models/              # Eloquent models
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
├── resources/
│   ├── js/                  # Vue.js components (to be created)
│   └── views/               # Blade templates
├── routes/
│   ├── web.php             # Web routes
│   ├── api.php             # API routes
│   └── channels.php        # Broadcast channels
└── .env.example            # Environment configuration template
```

## Database Schema

### Users
- role (patient/therapist/admin)
- name, email, phone, bio
- Two-factor authentication fields

### Therapist Profiles
- specialization, qualifications
- years_of_experience, hourly_rate
- languages (JSON array)
- verification and availability status

### Availability Slots
- therapist_id, start_time, end_time
- is_booked flag

### Therapy Sessions
- patient_id, therapist_id
- scheduled_at, duration_minutes
- status (pending/confirmed/completed/cancelled)
- video_room_id

### Messages
- sender_id, receiver_id, content
- is_read, read_at
- therapy_session_id (optional)

### Payments
- therapy_session_id, patient_id, therapist_id
- amount, currency, status
- payment_method, transaction_id
- payment_details (JSON)

## API Routes

All routes are protected by authentication middleware.

### Dashboard
- `GET /dashboard` - Role-based dashboard

### Therapists
- `GET /therapists` - List all therapists
- `GET /therapists/{id}` - View therapist profile
- `PUT /therapist/profile` - Update therapist profile

### Availability
- `GET /availability` - List therapist's slots
- `POST /availability` - Create single slot
- `POST /availability/bulk` - Create multiple slots
- `DELETE /availability/{id}` - Delete slot

### Sessions
- `GET /sessions` - List sessions
- `POST /sessions` - Book a session
- `GET /sessions/{id}` - View session details
- `PUT /sessions/{id}/status` - Update session status
- `GET /sessions/{id}/video` - Join video room

### Messages
- `GET /messages` - List conversations
- `GET /messages/conversation/{userId}` - View conversation
- `POST /messages` - Send message
- `GET /messages/unread-count` - Get unread count

### Payments
- `GET /payments/history` - Payment history
- `GET /payments/{id}` - View payment
- `POST /payments/{id}/process` - Process payment

## Development

### Run Development Server
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server (for hot reload)
npm run dev

# Terminal 3 - Queue Worker (for jobs)
php artisan queue:work

# Terminal 4 - Laravel Echo Server (for real-time features)
# Install laravel-echo-server globally first
npm install -g laravel-echo-server
laravel-echo-server start
```

### Code Style
```bash
# Format PHP code
./vendor/bin/pint

# Run tests
php artisan test
```

## Security Considerations

⚠️ **Important**: This is a PROTOTYPE/MVP, not production-ready. For production deployment:

1. Enable HTTPS/SSL
2. Implement proper HIPAA-compliant security measures
3. Add rate limiting
4. Implement proper session encryption
5. Use real payment gateway (not mock)
6. Add comprehensive logging and monitoring
7. Implement data backup strategies
8. Add GDPR/privacy compliance features
9. Secure video conferencing (consider self-hosted Jitsi)
10. Add comprehensive input validation and sanitization

## Deployment

### Basic Deployment (DigitalOcean/AWS)

1. **Server Requirements**
   - Ubuntu 22.04 or higher
   - PHP 8.2+
   - Nginx or Apache
   - Composer
   - Node.js & NPM
   - Supervisor (for queue workers)

2. **Deploy Steps**
   ```bash
   # Clone repository
   git clone <repo-url>
   cd online-therapy-system

   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install --legacy-peer-deps
   npm run build

   # Configure environment
   cp .env.example .env
   php artisan key:generate

   # Set permissions
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache

   # Run migrations
   php artisan migrate --force
   php artisan db:seed --force

   # Optimize for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Configure Nginx**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /var/www/online-therapy-system/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

### Docker Deployment

A Dockerfile and docker-compose.yml will be provided separately for containerized deployment.

## Troubleshooting

### Common Issues

1. **SQLite database not found**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

2. **Permission denied errors**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **npm install errors**
   ```bash
   npm install --legacy-peer-deps
   ```

4. **Vite build errors**
   ```bash
   rm -rf node_modules package-lock.json
   npm install --legacy-peer-deps
   npm run build
   ```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues, questions, or contributions, please open an issue on the GitHub repository.
