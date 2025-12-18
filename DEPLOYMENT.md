# Production Deployment Guide - SoulEase Therapy System

## Overview

This guide covers deploying your therapy system with **working video calls** in production.

---

## 🎯 Production Architecture

Your app has **two video call systems**:

1. **Jitsi Meet Sessions** - No special setup needed (uses public Jitsi servers)
2. **Peer-to-Peer Calls** - Requires Reverb WebSocket server running

---

## 📋 Pre-Deployment Checklist

### Required Services:
- [ ] Web Server (Nginx/Apache)
- [ ] PHP 8.2+
- [ ] PostgreSQL Database
- [ ] Node.js & NPM
- [ ] SSL Certificate (for HTTPS)
- [ ] Process Manager (Supervisor/PM2 for keeping Reverb running)

---

## 🚀 Deployment Steps

### 1. Server Setup

#### Install Required Packages
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.2-fpm php8.2-cli php8.2-pgsql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-redis php8.2-bcmath -y

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y

# Install Supervisor (to keep Reverb running)
sudo apt install supervisor -y
```

### 2. Deploy Application

```bash
# Clone your repository
cd /var/www
git clone your-repo-url soulease
cd soulease

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci

# Copy environment file
cp .env.production.example .env

# IMPORTANT: Edit .env with your production values
nano .env
```

### 3. Configure Production Environment

Edit your `.env` file with these **critical settings**:

```env
# App Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_HOST=your-production-db-host
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

# Reverb for Video Calls (IMPORTANT!)
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https

# Frontend (Vite needs these)
VITE_REVERB_HOST=yourdomain.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

### 4. Build Frontend Assets

```bash
# Build production assets
npm run build

# Remove dev dependencies
npm prune --production
```

### 5. Set Permissions

```bash
# Set correct ownership
sudo chown -R www-data:www-data /var/www/soulease

# Set directory permissions
sudo find /var/www/soulease -type d -exec chmod 755 {} \;
sudo find /var/www/soulease -type f -exec chmod 644 {} \;

# Storage and cache need write permissions
sudo chmod -R 775 storage bootstrap/cache
```

### 6. Run Laravel Setup

```bash
# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Cache everything for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔌 Setting Up Reverb WebSocket Server

### Option 1: Using Supervisor (Recommended)

Create Supervisor configuration:

```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```

Add this configuration:

```ini
[program:reverb]
command=/usr/bin/php /var/www/soulease/artisan reverb:start
directory=/var/www/soulease
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/soulease/storage/logs/reverb.log
stopwaitsecs=3600
```

Start Reverb:

```bash
# Update Supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Start Reverb
sudo supervisorctl start reverb

# Check status
sudo supervisorctl status reverb
```

### Option 2: Using PM2

```bash
# Install PM2
sudo npm install -g pm2

# Start Reverb
pm2 start artisan --name reverb -- reverb:start

# Save PM2 config
pm2 save

# Set PM2 to start on boot
pm2 startup
```

---

## 🌐 Nginx Configuration

### Setup Nginx for Reverb WebSocket Proxy

Create Nginx site configuration:

```bash
sudo nano /etc/nginx/sites-available/soulease
```

Add this configuration:

```nginx
# Upstream for Reverb WebSocket
upstream reverb {
    server 127.0.0.1:8080;
}

server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;

    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    root /var/www/soulease/public;
    index index.php;

    # Laravel Application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # WebSocket Proxy for Reverb (CRITICAL for video calls!)
    location /app {
        proxy_pass http://reverb;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

Enable the site:

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/soulease /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## 🔒 SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is set up automatically
```

---

## ✅ Verify Deployment

### 1. Check Services Are Running

```bash
# Check Laravel app
curl https://yourdomain.com

# Check Reverb WebSocket
sudo supervisorctl status reverb
# OR
pm2 status reverb

# Check Nginx
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.2-fpm
```

### 2. Test Video Calls

1. **Jitsi Sessions**: Book a therapy session and click "Join Video Session" - should work immediately
2. **Peer-to-Peer Calls**:
   - Go to `https://yourdomain.com/video-call`
   - Open in two browsers
   - Should see users as "Online"
   - Should be able to call each other

### 3. Check Logs

```bash
# Laravel logs
tail -f /var/www/soulease/storage/logs/laravel.log

# Reverb logs
tail -f /var/www/soulease/storage/logs/reverb.log

# Nginx error logs
tail -f /var/log/nginx/error.log
```

---

## 🔧 Troubleshooting Production

### Video Calls Not Working?

**1. Check Reverb is running:**
```bash
sudo supervisorctl status reverb
# Should show "RUNNING"
```

**2. Check WebSocket connection in browser:**
- Open browser console (F12)
- Go to Network tab
- Filter by "WS" (WebSocket)
- Should see connection to `wss://yourdomain.com/app`

**3. Check Nginx WebSocket proxy:**
```bash
# Test Nginx config
sudo nginx -t

# Check Nginx is proxying /app to Reverb
curl -I https://yourdomain.com/app
```

**4. Check firewall allows port 8080:**
```bash
# If using UFW
sudo ufw allow 8080/tcp
```

**5. Verify .env settings:**
```bash
# Should be your domain, not localhost
grep REVERB_HOST .env
# Output: REVERB_HOST=yourdomain.com

grep REVERB_SCHEME .env
# Output: REVERB_SCHEME=https
```

---

## 🔄 Updating Your Application

```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart Reverb
sudo supervisorctl restart reverb
# OR
pm2 restart reverb
```

---

## 📊 Monitoring

### Check Reverb Performance

```bash
# View Reverb connections
sudo supervisorctl tail -f reverb stdout

# Monitor server resources
htop
```

### Laravel Queue Worker (Optional)

If you add queue jobs later:

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
command=/usr/bin/php /var/www/soulease/artisan queue:work database --sleep=3 --tries=3
directory=/var/www/soulease
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/soulease/storage/logs/worker.log
```

---

## 🎯 Production Checklist

Before going live:

- [ ] `.env` file configured with production values
- [ ] `APP_DEBUG=false` in production
- [ ] SSL certificate installed and working
- [ ] Database backed up
- [ ] Reverb running via Supervisor/PM2
- [ ] Nginx configured with WebSocket proxy
- [ ] All caches cleared and rebuilt
- [ ] Tested Jitsi video sessions
- [ ] Tested peer-to-peer video calls
- [ ] Logs monitored for errors
- [ ] Firewall configured properly
- [ ] Paddle switched to live mode (not sandbox)

---

## 🆘 Support

For issues:
1. Check logs: `storage/logs/laravel.log`
2. Check Reverb: `storage/logs/reverb.log`
3. Check browser console for frontend errors
4. Verify all environment variables are set correctly

---

## 📝 Notes

- **Jitsi sessions** don't require Reverb - they work independently
- **Peer-to-peer calls** require Reverb WebSocket server running
- Reverb runs on port 8080 internally, proxied through Nginx on port 443
- Always use HTTPS in production for WebRTC video calls to work properly
- Keep Reverb credentials secret (don't commit to git)

---

**Your production deployment is now ready! 🚀**
