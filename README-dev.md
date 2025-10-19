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

### 💳 Payment Integration (Paddle)
- Paddle payment gateway integration (Sandbox mode)
- Dynamic pricing based on session duration
- Transaction tracking and history
- Support for multiple payment methods
- Automatic session confirmation after successful payment
- Webhook integration for payment verification

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

6a. **Configure Paddle Payment Gateway (Required for payments)**
   Sign up for a Paddle account at https://paddle.com/ and use Sandbox credentials:
   ```env
   PADDLE_CLIENT_TOKEN=your-paddle-client-token
   PADDLE_API_KEY=your-paddle-api-key
   PADDLE_VENDOR_ID=your-vendor-id
   PADDLE_PRODUCT_ID=your-product-id
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

## Test Payment Cards

When testing the payment functionality in Sandbox mode, use these test card numbers:

### Valid Card without 3DS (Direct Success)
```
Card Number: 4242 4242 4242 4242
CCV: 100
Expiration Date: Any valid future date (e.g., 12/25)
Cardholder Name: Any valid name
```

### Valid Card with 3DS Authentication
```
Card Number: 4000 0038 0000 0446
CCV: 100
Expiration Date: Any valid future date (e.g., 12/25)
Cardholder Name: Any valid name
```
*This card will trigger 3DS authentication flow during payment*

### Declined Card
```
Card Number: 4000 0000 0000 0002
CCV: 100
Expiration Date: Any valid future date (e.g., 12/25)
Cardholder Name: Any valid name
```
*This card will always be declined*

### Successful Initial, Subsequent Declined
```
Card Number: 4000 0027 6000 3184
CCV: 100
Expiration Date: Any valid future date (e.g., 12/25)
Cardholder Name: Any valid name
```
*First transaction succeeds, subsequent transactions will be declined*

**Note:** You can use any valid expiration date in the future and any valid cardholder name for testing purposes.

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

### Paddle API (Payment Gateway)
- `POST /api/create-paddle-price` - Create dynamic price for session
- `POST /api/calculate-session-price` - Calculate session cost
- `GET /api/session-duration-options` - Get available duration options
- `POST /api/paddle/webhook` - Handle Paddle payment webhooks (unprotected)

## Payment System Details

The application uses **Paddle** as the payment gateway for processing therapy session payments.

### Pricing Structure

All therapists have a standardized base rate of **$100 per hour**. Session pricing is calculated dynamically based on duration with the following discounts:

| Duration | Base Price | Multiplier | Final Price |
|----------|-----------|------------|-------------|
| 30 minutes | $50 | 1.0x | $50.00 |
| 60 minutes | $100 | 1.0x | $100.00 |
| 90 minutes | $150 | 0.95x | $142.50 (5% discount) |
| 120 minutes | $200 | 0.90x | $180.00 (10% discount) |

*Longer sessions receive progressive discounts to encourage extended therapy sessions.*

### Payment Flow

1. **Session Booking**: Patient selects therapist and available time slot
2. **Duration Selection**: Patient chooses session duration (30/60/90/120 minutes)
3. **Price Calculation**: System calculates total cost using `PricingService`
4. **Payment Record Creation**: A pending payment record is created in the database
5. **Paddle Checkout**: Patient is redirected to Paddle's secure checkout
6. **Payment Processing**: Paddle processes the payment
7. **Webhook Notification**: Paddle sends webhook to confirm payment status
8. **Session Confirmation**: System automatically confirms session upon successful payment
9. **Email Notification**: Both patient and therapist receive confirmation emails

### Payment Features

- **Dynamic Pricing**: Prices are calculated in real-time based on session parameters
- **Secure Processing**: All payment data is handled by Paddle (PCI-DSS compliant)
- **Transaction History**: Complete payment history for patients and therapists
- **Status Tracking**: Payment statuses (pending, completed, failed, refunded)
- **Automatic Confirmation**: Sessions are auto-confirmed after successful payment
- **Webhook Integration**: Real-time payment status updates via Paddle webhooks

### Testing Payments

The application is configured for **Paddle Sandbox** mode. Use the test cards listed in the "Test Payment Cards" section above to simulate different payment scenarios during development and testing.

## Real-time Features

The application uses **Laravel Echo** with **Pusher** for real-time communication features.

### Broadcasting Channels

**Private Channels:**
- `chat.{userId}` - Personal chat channel for each user
  - Authorization: Only the user themselves can subscribe
  - Events: `message.sent` - New message notifications

**Presence Channels:**
- `presence-video-channel` - Video session presence tracking
  - Shows which users are currently in video sessions
  - Future feature: Display online therapists

### Real-time Events

1. **MessageSent Event**
   - Triggered when a user sends a message
   - Broadcasts to recipient's private channel
   - Payload includes full message data with sender/receiver info
   - Enables instant message delivery without page refresh

2. **StartVideoChat Event**
   - Triggered when initiating a video session
   - Broadcasts to presence channel
   - Future use: Notify therapist of patient joining video room

### Frontend Integration

Messages are received in real-time using Laravel Echo:

```javascript
Echo.private(`chat.${userId}`)
    .listen('.message.sent', (event) => {
        // Update UI with new message
        messages.value.push(event.message);
        // Show notification
        // Play sound
    });
```

## Video Conferencing

The application integrates **Jitsi Meet** for secure video therapy sessions.

### Video Features

- **Private Rooms**: Each therapy session gets a unique video room ID
- **Secure Access**: Only authenticated users can join session rooms
- **No Installation**: Browser-based video conferencing (no downloads required)
- **Full HD Video**: Supports high-quality video and audio
- **Screen Sharing**: Therapists can share screens during sessions
- **Chat Integration**: In-video text chat available
- **Recording**: Optional session recording (requires configuration)

### Video Room Configuration

- **Domain**: Configurable via `JITSI_DOMAIN` environment variable
- **Default**: Uses public Jitsi server (`meet.jit.si`)
- **Self-hosted**: Can be configured to use private Jitsi server for enhanced privacy
- **Room Format**: `room-{session-uuid}` ensures unique rooms per session

### Joining a Video Session

1. Patient books and pays for a session
2. Both patient and therapist can access the video room from session details page
3. Click "Join Video Session" button
4. Jitsi interface loads in iframe
5. Allow camera/microphone permissions
6. Start therapy session

**Recommendation for Production**: Deploy a self-hosted Jitsi server for HIPAA compliance and data privacy.

## Development

### Run Development Server
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server (for hot reload)
npm run dev

# Terminal 3 - Queue Worker (for jobs - optional)
php artisan queue:work

# Terminal 4 - WebSocket Server (for real-time features)
# Note: Pusher handles this in cloud, local server only needed for self-hosted solutions
```

### Code Style
```bash
# Format PHP code
./vendor/bin/pint

# Run tests
php artisan test
```

## User Roles & Permissions

The application implements role-based access control with three distinct user types:

### Patient Role
**Capabilities:**
- Browse and search therapists by specialization, language, experience
- View therapist profiles and availability
- Book therapy sessions with available therapists
- Make payments for booked sessions
- Send and receive messages with their therapists
- Join video sessions for confirmed appointments
- View session history and upcoming appointments
- Cancel sessions (before scheduled time)

**Dashboard Features:**
- Upcoming sessions list
- Recent conversations
- Quick booking access
- Payment history

### Therapist Role
**Capabilities:**
- Create and manage availability slots (single or bulk)
- View and manage session bookings
- Confirm or cancel session requests
- Add session notes after completion
- Send and receive messages with patients
- Join video sessions
- View earnings and payment history
- Update professional profile (specialization, qualifications, languages, hourly rate)

**Dashboard Features:**
- Today's sessions
- Pending session requests
- Weekly/monthly statistics
- Patient messages
- Availability calendar management
- Earnings overview

### Admin Role
**Capabilities:**
- View all users (patients and therapists)
- View all therapy sessions across the platform
- Access system analytics and statistics
- Monitor platform revenue
- View recent activity logs
- Manage user accounts
- Access all data for administrative purposes

**Dashboard Features:**
- Total user count (patients, therapists, admins)
- Total sessions (pending, confirmed, completed, cancelled)
- Total revenue tracking
- Recent sessions list
- System-wide analytics

## Session Booking Workflow

### Complete Booking Process

1. **Browse Therapists** (Patient)
   - Filter by specialization, language, experience
   - View therapist profiles and rates
   - Check availability calendar

2. **Select Time Slot** (Patient)
   - View therapist's available time slots
   - Choose preferred date and time
   - Select session duration (30/60/90/120 minutes)

3. **Review Booking** (Patient)
   - See session summary
   - Review pricing (calculated dynamically)
   - Confirm booking details

4. **Create Session** (System)
   - Session created with "pending" status
   - Availability slot marked as booked
   - Payment record created with "pending" status

5. **Process Payment** (Patient)
   - Redirected to Paddle checkout
   - Enter payment details (or use test cards)
   - Complete payment transaction

6. **Payment Confirmation** (System)
   - Paddle sends webhook notification
   - Payment status updated to "completed"
   - Session status updated to "confirmed"
   - Email notifications sent to both parties

7. **Session Communication** (Both)
   - Patient and therapist can exchange messages
   - Discuss session preparation or specific topics

8. **Video Session** (Both)
   - At scheduled time, both parties join video room
   - Conduct therapy session via Jitsi Meet
   - In-session chat and screen sharing available

9. **Session Completion** (Therapist)
   - Mark session as "completed"
   - Add session notes (private)
   - Notes saved for future reference

10. **Post-Session** (Both)
    - Session appears in history
    - Payment reflected in transaction history
    - Future booking recommendations

### Session Status Flow

```
pending → confirmed → in_progress → completed
   ↓
cancelled (any status can be cancelled before completion)
```

## Messaging System

### Features
- **One-to-One Conversations**: Direct messaging between patient and therapist
- **Real-time Delivery**: Messages appear instantly using WebSocket
- **Read Receipts**: Track when messages are read
- **Conversation History**: Full message history preserved
- **Unread Indicators**: Badge showing unread message count
- **Session Context**: Messages can be linked to specific therapy sessions

### Message Flow

1. User composes message in conversation interface
2. Message sent via POST request to `/messages`
3. Message saved to database
4. `MessageSent` event broadcast to recipient's private channel
5. Recipient receives message in real-time (if online)
6. Read status updates when recipient views message
7. Email notification sent (if recipient offline)

## Security Considerations

⚠️ **Important**: This is a PROTOTYPE/MVP built for demonstration purposes. For production deployment, implement the following security measures:

### Required Security Enhancements

1. **SSL/TLS Encryption**
   - Enable HTTPS for all connections
   - Use Let's Encrypt for free SSL certificates
   - Force HTTPS redirects

2. **HIPAA Compliance** (Required for healthcare in US)
   - Implement Business Associate Agreements (BAA)
   - End-to-end encryption for all patient data
   - Audit logging for all data access
   - Data encryption at rest
   - Secure data backup and disaster recovery
   - Patient data access controls
   - Regular security audits

3. **Authentication & Authorization**
   - Enforce strong password policies
   - Implement password complexity requirements
   - Add account lockout after failed attempts
   - Require two-factor authentication for all users
   - Session timeout after inactivity
   - Secure password reset flow

4. **Data Protection**
   - Encrypt sensitive data at rest (messages, session notes, payment info)
   - Use encrypted database connections
   - Implement proper data retention policies
   - Secure file upload handling
   - Sanitize all user inputs
   - Implement Content Security Policy (CSP)

5. **Rate Limiting**
   - API rate limiting to prevent abuse
   - Login attempt throttling
   - Message sending rate limits
   - Payment processing rate limits

6. **Monitoring & Logging**
   - Comprehensive audit logging
   - Real-time security monitoring
   - Intrusion detection system
   - Regular security scans
   - Log retention and analysis
   - Incident response plan

7. **Video Conferencing Security**
   - Self-hosted Jitsi server (not public server)
   - End-to-end encryption for video streams
   - Waiting rooms for sessions
   - Password-protected rooms
   - Recording consent management

8. **Payment Security**
   - PCI DSS compliance (handled by Paddle)
   - Secure webhook verification
   - Fraud detection and prevention
   - Refund and chargeback handling
   - Financial data encryption

9. **Privacy Compliance**
   - GDPR compliance (for EU users)
   - CCPA compliance (for California users)
   - Privacy policy and terms of service
   - Cookie consent management
   - Data export and deletion capabilities
   - User consent tracking

10. **Infrastructure Security**
    - Regular security updates and patches
    - Firewall configuration
    - DDoS protection
    - Database access restrictions
    - Secure server configuration
    - Regular backups with encryption
    - Disaster recovery testing

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

## Frontend Architecture

### Technology Stack
- **Framework**: Vue.js 3 (Composition API)
- **Server-Side Rendering**: Inertia.js 1.0
- **Styling**: Tailwind CSS 3.4
- **UI Components**: Headless UI
- **Real-time**: Laravel Echo + Pusher
- **Date Handling**: date-fns
- **Build Tool**: Vite 7.0
- **State Management**: Vue 3 reactive refs and computed properties

### Component Structure

The frontend is organized into reusable Vue 3 components:

**Layouts:**
- [AppLayout.vue](resources/js/Layouts/AppLayout.vue) - Main authenticated layout with navigation
- [GuestLayout.vue](resources/js/Layouts/GuestLayout.vue) - Layout for login/register pages

**Page Components:**
- Dashboard pages (Patient, Therapist, Admin)
- Therapist browsing and profile pages
- Session management pages
- Messaging interface
- Payment processing pages
- Profile management

**Reusable Components:**
- Form elements (Input, Button, Checkbox, Dropdown)
- Modal dialogs
- Cards and sections
- Navigation components
- Language switcher

### Key Features

1. **Inertia.js Integration**
   - Server-side routing with client-side navigation
   - No API needed - direct access to Laravel controllers
   - Automatic CSRF protection
   - Shared data across all pages (auth user, flash messages)

2. **Vue 3 Composition API**
   - Reactive state management
   - Composable functions for reusability
   - Better TypeScript support (ready for migration)

3. **Tailwind CSS**
   - Utility-first styling
   - Dark mode support
   - Responsive design
   - Custom color schemes

4. **Real-time Updates**
   - WebSocket connections via Laravel Echo
   - Instant message notifications
   - Live session updates

5. **Form Handling**
   - Inertia form helpers with validation
   - Real-time error display
   - Loading states
   - Success/error notifications

### Internationalization (i18n)

The application includes multi-language support:

- Language switcher component in navigation
- Locale stored in session
- Translation files for UI strings
- Easy to add new languages

### Responsive Design

- Mobile-first approach
- Tablet and desktop optimized layouts
- Touch-friendly interfaces
- Adaptive navigation

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

5. **Pusher not connecting**
   - Verify Pusher credentials in `.env`
   - Check VITE_PUSHER_APP_KEY and VITE_PUSHER_APP_CLUSTER are set
   - Ensure broadcasting driver is set to `pusher`
   - Clear config cache: `php artisan config:clear`

6. **Payment webhook not working**
   - Ensure webhook URL is publicly accessible
   - Check Paddle webhook signature verification
   - Review webhook logs in Paddle dashboard
   - Test with ngrok for local development

7. **Video not loading**
   - Check browser permissions for camera/microphone
   - Verify Jitsi domain configuration
   - Test internet connection speed
   - Try different browser (Chrome/Firefox recommended)

8. **Sessions not confirming after payment**
   - Verify Paddle webhook is configured correctly
   - Check payment status in database
   - Review Laravel logs for errors
   - Ensure webhook URL is whitelisted in Paddle

## Future Enhancements

The following features are planned for future releases:

### Phase 2 Features
- [ ] **Advanced Scheduling**
  - Recurring appointments
  - Multiple time zone support
  - Session reminders (email/SMS)
  - Automatic rescheduling

- [ ] **Enhanced Communication**
  - File sharing in messages
  - Voice messages
  - Group therapy sessions
  - Session recording and playback

- [ ] **Therapist Features**
  - Detailed session notes with templates
  - Patient progress tracking
  - Treatment plans
  - Custom intake forms

- [ ] **Patient Features**
  - Therapist reviews and ratings
  - Favorite therapists
  - Session journal
  - Progress tracking dashboard

- [ ] **Payment Features**
  - Subscription-based pricing
  - Package deals (bulk session discounts)
  - Insurance integration
  - Refund management
  - Split payments

### Phase 3 Features
- [ ] **Mobile Application**
  - Native iOS app
  - Native Android app
  - Push notifications

- [ ] **AI Integration**
  - Chatbot for initial assessment
  - Session summaries
  - Mood tracking and analysis
  - Therapist-patient matching algorithm

- [ ] **Analytics**
  - Patient engagement metrics
  - Therapist performance analytics
  - Revenue forecasting
  - Session outcome tracking

- [ ] **Advanced Security**
  - Biometric authentication
  - Encrypted session notes
  - Compliance reporting tools
  - Security audit logs

- [ ] **Platform Features**
  - Multi-clinic support
  - White-label capabilities
  - API for third-party integrations
  - Referral system

## Contributing

Contributions are welcome! Please follow these guidelines:

### Development Workflow

1. **Fork the repository**
   ```bash
   git clone https://github.com/your-username/online-therapy.git
   cd online-therapy
   ```

2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
   - Follow Laravel coding standards
   - Write tests for new features
   - Update documentation as needed

4. **Run tests**
   ```bash
   php artisan test
   npm run test
   ```

5. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add: Brief description of your changes"
   ```

6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Provide clear description of changes
   - Reference any related issues
   - Ensure all tests pass

### Coding Standards

- **PHP**: Follow PSR-12 coding standard
- **JavaScript**: Use ESLint configuration provided
- **Vue**: Follow Vue.js style guide
- **CSS**: Use Tailwind utility classes
- **Commits**: Use conventional commit messages

### Testing

- Write unit tests for all new features
- Maintain test coverage above 70%
- Run `php artisan test` before committing
- Test on multiple browsers

### Documentation

- Update README.md for new features
- Add inline comments for complex logic
- Update API documentation
- Include examples where applicable

## Known Issues

### Current Limitations

1. **Scalability**: Current implementation is optimized for small to medium deployments
2. **Video**: Uses public Jitsi server (not recommended for production)
3. **Email**: Email configuration required for notifications
4. **Time Zones**: Limited time zone support
5. **Mobile**: Not fully optimized for mobile devices
6. **Accessibility**: WCAG compliance not fully implemented

### Reporting Issues

If you encounter any bugs or issues:

1. Check existing issues on GitHub
2. Create a new issue with:
   - Clear description of the problem
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots (if applicable)
   - Environment details (OS, browser, PHP version)

## FAQ

**Q: Can I use this in production?**
A: This is an MVP/prototype. Significant security hardening is required for production use, especially for healthcare applications.

**Q: Is this HIPAA compliant?**
A: No, additional security measures are required for HIPAA compliance. See Security Considerations section.

**Q: Can I customize the pricing?**
A: Yes, pricing logic is in `app/Services/PricingService.php` and can be customized.

**Q: Can I use a different payment gateway?**
A: Yes, but you'll need to implement a new payment service following the PaymentController interface.

**Q: Can I self-host the video conferencing?**
A: Yes, configure your own Jitsi server URL in the `JITSI_DOMAIN` environment variable.

**Q: How do I add more languages?**
A: Add translation files in `resources/lang/` and update the LanguageSwitcher component.

**Q: Can therapists set their own rates?**
A: Yes, therapists can set hourly rates in their profile, but currently the system uses a standardized $100/hour rate.

**Q: How are session notes protected?**
A: Session notes are private and only visible to the therapist who created them.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

### Technologies Used
- [Laravel](https://laravel.com) - PHP Framework
- [Vue.js](https://vuejs.org) - JavaScript Framework
- [Inertia.js](https://inertiajs.com) - Modern Monolith Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS Framework
- [Jitsi Meet](https://jitsi.org) - Open-source Video Conferencing
- [Paddle](https://paddle.com) - Payment Processing
- [Pusher](https://pusher.com) - Real-time WebSocket Service

### Contributors
This project is maintained by the development team and community contributors.

## Support

For issues, questions, or contributions:

- **GitHub Issues**: Report bugs and request features
- **Discussions**: Ask questions and share ideas
- **Pull Requests**: Submit code contributions
- **Documentation**: Help improve documentation

## Changelog

### Version 1.0.0 (Current)
- Initial release
- Core booking functionality
- Real-time messaging
- Video conferencing integration
- Paddle payment integration
- Admin dashboard
- Multi-language support

---

**Built with Laravel & Vue.js** | **License: MIT** | **Status: MVP/Prototype**
