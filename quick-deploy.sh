#!/bin/bash

echo "🚀 Quick Deploy Script for DigitalOcean"
echo "========================================"

# This script will set up everything on your droplet
# Copy this entire script and run it on your droplet

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Install Laravel using Composer (if we can't upload files)
print_info "Setting up Laravel application..."

cd /var/www/
rm -rf incident-management

# Create a new Laravel project
composer create-project laravel/laravel incident-management
cd incident-management

# Install Laravel UI for authentication
composer require laravel/ui
php artisan ui bootstrap --auth

# Set proper permissions
chown -R www-data:www-data /var/www/incident-management
chmod -R 755 storage bootstrap/cache

print_info "Creating database setup..."

# Database configuration
mysql_secure_installation

read -p "Enter database name (default: incident_management): " DB_NAME
DB_NAME=${DB_NAME:-incident_management}

read -p "Enter database username (default: incident_user): " DB_USER  
DB_USER=${DB_USER:-incident_user}

read -s -p "Enter database password: " DB_PASS
echo ""

# Create database
mysql -u root -p << EOF
CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

# Configure .env
cp .env.example .env
sed -i "s/DB_DATABASE=laravel/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=root/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASS/" .env

# Generate key and run migrations
php artisan key:generate
php artisan migrate

print_info "Setting up Nginx..."

# Create Nginx config
cat > /etc/nginx/sites-available/incident-management << EOF
server {
    listen 80;
    server_name 139.59.110.102;
    root /var/www/incident-management/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/incident-management /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
nginx -t && systemctl reload nginx

print_status "Basic Laravel application deployed!"
print_info "Visit: http://139.59.110.102"
print_info "You can now customize the application with your incident management features."

echo ""
echo "📋 Next Steps:"
echo "=============="
echo "1. Visit http://139.59.110.102 to test"
echo "2. Add your custom incident management code"
echo "3. Configure authentication and roles"
echo "4. Set up SSL certificate"
echo ""