#!/bin/bash

echo "🌊 DigitalOcean Droplet Setup for Incident Management System"
echo "=========================================================="

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root (use sudo)" 
   exit 1
fi

echo ""
print_info "Step 1: Updating system packages..."
apt update && apt upgrade -y
print_status "System updated"

echo ""
print_info "Step 2: Installing required packages..."

# Install PHP 8.1 and required extensions
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

apt install -y \
    nginx \
    mysql-server \
    php8.1 \
    php8.1-fpm \
    php8.1-mysql \
    php8.1-xml \
    php8.1-mbstring \
    php8.1-curl \
    php8.1-zip \
    php8.1-intl \
    php8.1-bcmath \
    php8.1-gd \
    php8.1-sqlite3 \
    unzip \
    curl \
    git \
    certbot \
    python3-certbot-nginx \
    fail2ban \
    ufw

print_status "Packages installed"

echo ""
print_info "Step 3: Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
print_status "Composer installed"

echo ""
print_info "Step 4: Installing Node.js and NPM..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs
print_status "Node.js and NPM installed"

echo ""
print_info "Step 5: Configuring MySQL..."
print_warning "Please set a strong root password when prompted"
mysql_secure_installation

echo ""
print_info "Step 6: Creating application directory..."
mkdir -p /var/www/incident-management
chown -R www-data:www-data /var/www/incident-management
print_status "Application directory created"

echo ""
print_info "Step 7: Configuring UFW Firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable
print_status "Firewall configured"

echo ""
print_info "Step 8: Starting services..."
systemctl enable nginx
systemctl enable mysql
systemctl enable php8.1-fpm
systemctl start nginx
systemctl start mysql
systemctl start php8.1-fpm
print_status "Services started"

echo ""
print_status "Server setup complete!"

echo ""
echo "📋 Next Steps:"
echo "=============="
echo "1. Upload your application archive to /var/www/"
echo "2. Extract and configure the application"
echo "3. Set up database and run migrations"
echo "4. Configure Nginx virtual host"
echo "5. Set up SSL certificate"
echo ""
echo "📝 Important Information:"
echo "========================"
echo "- Web root should point to: /var/www/incident-management/public"
echo "- PHP-FPM socket: /run/php/php8.1-fpm.sock"
echo "- Nginx config directory: /etc/nginx/sites-available/"
echo ""

print_info "Server is ready for application deployment!"