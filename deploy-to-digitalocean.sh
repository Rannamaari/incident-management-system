#!/bin/bash

echo "🌊 Deploying Incident Management System to DigitalOcean"
echo "======================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   print_error "This script must be run as root (use: sudo ./deploy-to-digitalocean.sh)"
   exit 1
fi

echo ""
print_info "Step 1: Setting up application directory..."
cd /var/www/
rm -rf incident-management 2>/dev/null

# Check if archive exists
if [ ! -f "incident-management-*.tar.gz" ]; then
    print_error "No deployment archive found in /var/www/"
    print_info "Please upload your incident-management-*.tar.gz file to /var/www/ first"
    exit 1
fi

ARCHIVE=$(ls incident-management-*.tar.gz | head -n1)
print_info "Found archive: $ARCHIVE"

# Extract archive
tar -xzf "$ARCHIVE"
mv incident-management-* incident-management 2>/dev/null || print_warning "Directory already named correctly"
chown -R www-data:www-data incident-management
print_status "Application extracted and permissions set"

echo ""
print_info "Step 2: Setting up database..."

# Prompt for database details
read -p "Enter database name (default: incident_management): " DB_NAME
DB_NAME=${DB_NAME:-incident_management}

read -p "Enter database username (default: incident_user): " DB_USER
DB_USER=${DB_USER:-incident_user}

read -s -p "Enter database password: " DB_PASS
echo ""

read -p "Enter your domain name (e.g., yourdomain.com): " DOMAIN_NAME

# Create database and user
mysql -u root -p << EOF
CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

print_status "Database created"

echo ""
print_info "Step 3: Configuring environment..."
cd incident-management

# Create .env file from production template
cp .env.production .env

# Update .env with database credentials
sed -i "s/DB_DATABASE=incident_management/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=your_db_user/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=your_secure_password/DB_PASSWORD=$DB_PASS/" .env
sed -i "s/ASSET_URL=https:\/\/yourdomain.com/ASSET_URL=https:\/\/$DOMAIN_NAME/" .env

# Generate application key
php artisan key:generate --force
print_status "Environment configured"

echo ""
print_info "Step 4: Installing dependencies..."
composer install --optimize-autoloader --no-dev
print_status "Dependencies installed"

echo ""
print_info "Step 5: Running database migrations..."
php artisan migrate --force

print_info "Seeding default data..."
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ResolutionTeamSeeder
print_status "Database setup complete"

echo ""
print_info "Step 6: Setting up Nginx configuration..."

# Create Nginx configuration
cat > /etc/nginx/sites-available/incident-management << EOF
server {
    listen 80;
    server_name $DOMAIN_NAME www.$DOMAIN_NAME;
    root /var/www/incident-management/public;
    index index.php index.html index.htm;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Laravel application
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP processing
    location ~ \.php\$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Security - deny access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Deny access to sensitive files
    location ~* \.(env|log|md|json)\$ {
        deny all;
    }

    # Static file caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
}
EOF

# Enable site and remove default
ln -sf /etc/nginx/sites-available/incident-management /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t
if [ $? -eq 0 ]; then
    systemctl reload nginx
    print_status "Nginx configured and reloaded"
else
    print_error "Nginx configuration test failed"
    exit 1
fi

echo ""
print_info "Step 7: Setting file permissions..."
cd /var/www/incident-management
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public
print_status "Permissions set"

echo ""
print_info "Step 8: Setting up SSL certificate..."
print_warning "Setting up Let's Encrypt SSL certificate..."

certbot --nginx -d $DOMAIN_NAME -d www.$DOMAIN_NAME --non-interactive --agree-tos --email admin@$DOMAIN_NAME

if [ $? -eq 0 ]; then
    print_status "SSL certificate installed"
else
    print_warning "SSL setup failed - you may need to configure DNS first"
    print_info "You can run this later: certbot --nginx -d $DOMAIN_NAME"
fi

echo ""
print_info "Step 9: Setting up automatic SSL renewal..."
echo "0 12 * * * /usr/bin/certbot renew --quiet" | crontab -
print_status "SSL auto-renewal configured"

echo ""
print_info "Step 10: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_status "Application optimized"

echo ""
print_status "🎉 Deployment Complete!"

echo ""
echo "📊 Deployment Summary:"
echo "====================="
echo "🌐 Domain: https://$DOMAIN_NAME"
echo "🗄️  Database: $DB_NAME"
echo "👤 DB User: $DB_USER"
echo "📁 Web Root: /var/www/incident-management/public"
echo ""

echo "🔐 Default Login Credentials:"
echo "============================"
echo "🔴 Admin:  admin@incident.com  | Password: admin123"
echo "🔵 Editor: editor@incident.com | Password: editor123" 
echo "🟢 Viewer: viewer@incident.com | Password: viewer123"
echo ""

print_warning "SECURITY: Change default passwords immediately!"
print_info "Run: cd /var/www/incident-management && ./change-default-passwords.sh"

echo ""
echo "📋 Post-Deployment Checklist:"
echo "=============================="
echo "□ Test the application at https://$DOMAIN_NAME"
echo "□ Change default user passwords"
echo "□ Configure email settings in .env"
echo "□ Set up database backups"
echo "□ Monitor application logs: /var/www/incident-management/storage/logs/"
echo "□ Review Nginx logs: /var/log/nginx/"
echo ""

print_status "Your Incident Management System is now live!"
print_info "Visit: https://$DOMAIN_NAME"