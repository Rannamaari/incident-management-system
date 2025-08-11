# 🚀 Production Deployment Guide

## Prerequisites

- PHP 8.1+ with required extensions
- Composer
- Node.js & NPM
- MySQL/PostgreSQL database
- Web server (Apache/Nginx)
- SSL certificate (strongly recommended)

## Security Configuration

### 1. Environment Variables

Create a `.env` file with production settings:

```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=incident_management
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Session and Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your.smtp.host
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls

# Security
ASSET_URL=https://yourdomain.com
```

### 2. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE incident_management;
CREATE USER 'incident_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON incident_management.* TO 'incident_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ResolutionTeamSeeder
```

### 3. Application Deployment

```bash
# Clone repository
git clone <repository-url>
cd incident-management-system

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Generate application key
php artisan key:generate --force
```

### 4. Web Server Configuration

#### Nginx Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /path/to/incident-management/public;

    index index.php;

    ssl_certificate /path/to/your/certificate.pem;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Redirect HTTP to HTTPS
    if ($scheme = http) {
        return 301 https://$host$request_uri;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /path/to/incident-management/public

    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite HIGH:!aNULL:!MD5

    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    <Directory /path/to/incident-management/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

## Security Checklist

### 🔒 Essential Security Steps

- [ ] Change default user passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall (allow only ports 80, 443, 22)
- [ ] Set up fail2ban for brute force protection
- [ ] Configure automatic security updates
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Hide sensitive files (.env, logs)
- [ ] Enable database connection encryption
- [ ] Configure proper backup strategy
- [ ] Set up monitoring and logging
- [ ] Implement rate limiting
- [ ] Configure CORS properly
- [ ] Set secure session configuration

### 🛡️ Advanced Security

- [ ] Set up Web Application Firewall (WAF)
- [ ] Configure intrusion detection system
- [ ] Implement IP whitelisting if needed
- [ ] Set up log monitoring and alerts
- [ ] Configure automated backups with encryption
- [ ] Implement vulnerability scanning
- [ ] Set up SSL/TLS monitoring
- [ ] Configure secure headers (already implemented)

## Default User Accounts

⚠️ **IMPORTANT**: Change these default passwords immediately after deployment!

| Role | Email | Default Password | Action Required |
|------|-------|------------------|-----------------|
| Admin | admin@incident.com | admin123 | ⚠️ **CHANGE IMMEDIATELY** |
| Editor | editor@incident.com | editor123 | ⚠️ **CHANGE IMMEDIATELY** |
| Viewer | viewer@incident.com | viewer123 | ⚠️ **CHANGE IMMEDIATELY** |

### Change Default Passwords

```bash
# Using Laravel Tinker
php artisan tinker

# Change passwords
User::where('email', 'admin@incident.com')->update(['password' => Hash::make('new_secure_password')]);
User::where('email', 'editor@incident.com')->update(['password' => Hash::make('new_secure_password')]);
User::where('email', 'viewer@incident.com')->update(['password' => Hash::make('new_secure_password')]);
```

## Monitoring & Maintenance

### 🔍 Log Files to Monitor
- `/storage/logs/laravel.log` - Application logs
- Web server access/error logs
- Database slow query logs
- System security logs

### 📊 Health Checks
- Database connectivity: `/up`
- Application status monitoring
- SSL certificate expiration
- Disk space and performance metrics
- Failed login attempts

### 🔄 Regular Maintenance
- Update dependencies: `composer update` (after testing)
- Clear caches periodically
- Backup database and files
- Review security logs
- Update SSL certificates
- Monitor performance metrics

## Backup Strategy

```bash
# Database backup
mysqldump -u incident_user -p incident_management > backup_$(date +%Y%m%d_%H%M%S).sql

# File backup
tar -czf incident_files_$(date +%Y%m%d_%H%M%S).tar.gz \
  /path/to/incident-management \
  --exclude="storage/logs/*" \
  --exclude="node_modules" \
  --exclude=".git"
```

## Troubleshooting

### Common Issues
1. **Permission errors**: Check file permissions and ownership
2. **Database connection**: Verify credentials and server status
3. **SSL issues**: Check certificate validity and configuration
4. **Performance**: Monitor database queries and enable caching
5. **Session issues**: Ensure proper session driver configuration

### Performance Optimization
- Enable OPcache for PHP
- Configure database query caching
- Set up Redis for session/cache storage
- Optimize database indexes
- Enable gzip compression
- Use CDN for static assets

## Support & Documentation

- Application logs: `/storage/logs/`
- Configuration: `/config/`
- Environment: `/.env`
- Routes: `php artisan route:list`
- Commands: `php artisan list`

For additional support, review the main README.md and AUTHENTICATION.md files.