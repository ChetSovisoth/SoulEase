# Deployment Guide - Online Therapy System

## Quick Start

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install --legacy-peer-deps
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup

Update `.env` with your database credentials:

```env
DB_CONNECTION=pgsql  # or mysql, sqlite
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=online-therapy
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations and seeders:

```bash
# Create database (if using PostgreSQL)
# psql -U postgres -c "CREATE DATABASE \"online-therapy\";"

# Run migrations and seed data
php artisan migrate:fresh --seed
```

### 4. Build Assets

```bash
# Development build
npm run dev

# Production build
npm run build
```

### 5. Start Development Servers

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite dev server (for hot module reload)
npm run dev
```

Access the application at: **http://localhost:8000**

---

## Production Deployment

### 1. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 2. Build Production Assets

```bash
npm run build
```

### 3. Set Correct Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Environment Variables

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use production Pusher credentials
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 5. Queue Worker (Optional but Recommended)

```bash
# Start queue worker
php artisan queue:work --daemon

# Or use Supervisor for automatic restart
```

### 6. Task Scheduler (Optional)

Add to crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Docker Deployment

### Using Docker Compose

```bash
# Build and start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate:fresh --seed

# Build assets
docker-compose exec app npm run build
```

Access at: **http://localhost:8000**

---

## Server Requirements

### Minimum Requirements

- **PHP:** >= 8.2
- **Database:** PostgreSQL 12+ / MySQL 5.7+ / SQLite 3.8.8+
- **Node.js:** >= 18.x
- **Memory:** 512MB minimum, 1GB recommended
- **Disk Space:** 500MB

### PHP Extensions

- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- pgsql (for PostgreSQL) or mysql (for MySQL)

---

## Configuration

### Email Setup

Update `.env` for email notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Broadcasting (Real-time Features)

For production, configure Pusher:

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### Jitsi Video Configuration

Update `.env` for video sessions:

```env
JITSI_DOMAIN=meet.jit.si  # or your self-hosted Jitsi instance
```

---

## Nginx Configuration

Example Nginx configuration:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/online-therapy-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Apache Configuration

Example Apache .htaccess (already included):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## SSL/HTTPS Setup

### Using Let's Encrypt (Certbot)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal is set up automatically
```

Update `.env`:

```env
APP_URL=https://yourdomain.com
```

---

## Troubleshooting

### Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

### Permission Issues

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Assets Not Loading

```bash
# Rebuild assets
npm run build

# Check public directory permissions
ls -la public/build
```

### Queue Not Processing

```bash
# Restart queue worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed
```

---

## Monitoring & Logging

### Application Logs

Logs are stored in `storage/logs/laravel.log`

### Error Tracking

Consider integrating:
- **Sentry** for error tracking
- **New Relic** for performance monitoring
- **Laravel Telescope** for debugging (development only)

### Health Check Endpoint

Create a simple health check:

```php
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
```

---

## Backup Strategy

### Database Backup

```bash
# PostgreSQL
pg_dump -U username dbname > backup.sql

# MySQL
mysqldump -u username -p dbname > backup.sql
```

### File Backup

```bash
# Backup uploads and storage
tar -czf storage-backup.tar.gz storage/app/public
```

### Automated Backups

Use Laravel Backup package or cron jobs:

```bash
0 2 * * * cd /path-to-project && php artisan backup:run
```

---

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS for all traffic
- [ ] Keep dependencies updated (`composer update`, `npm update`)
- [ ] Use strong database passwords
- [ ] Enable CSRF protection (enabled by default)
- [ ] Validate all user inputs
- [ ] Use environment variables for sensitive data
- [ ] Set up firewall rules
- [ ] Implement rate limiting
- [ ] Regular security audits

---

## Performance Optimization

### Database Optimization

```bash
# Optimize database tables
php artisan db:optimize
```

### Caching Strategies

```bash
# Use Redis or Memcached for caching
# Update .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### CDN for Assets

Upload built assets to CDN and update `ASSET_URL` in `.env`

---

## Support & Documentation

- **Project Documentation:** See README.md
- **Test Accounts:** See TEST_ACCOUNTS.md
- **API Documentation:** See API_DOCUMENTATION.md
- **Project Summary:** See PROJECT_SUMMARY.md

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com)
- [Vue.js Documentation](https://vuejs.org)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Jitsi Meet Documentation](https://jitsi.github.io/handbook/)
