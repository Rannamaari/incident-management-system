#!/bin/bash

echo "🚀 Incident Management System - Deployment Script"
echo "================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
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

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

echo ""
print_info "Preparing application for deployment..."

# Step 1: Clear existing caches
echo ""
print_info "Step 1: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
print_status "Caches cleared"

# Step 2: Install production dependencies
echo ""
print_info "Step 2: Installing production dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction
print_status "Composer dependencies installed"

# Step 3: Build assets
echo ""
print_info "Step 3: Building production assets..."
npm ci
npx vite build
print_status "Assets built"

# Step 4: Cache for production
echo ""
print_info "Step 4: Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
print_status "Production caches created"

# Step 5: Set proper permissions
echo ""
print_info "Step 5: Setting file permissions..."
chmod -R 755 storage bootstrap/cache
print_status "Permissions set"

# Step 6: Create production .env template
echo ""
print_info "Step 6: Creating production environment template..."
cat > .env.production << 'EOL'
APP_NAME="Incident Management System"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_NEW_KEY_HERE

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=incident_management
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=your.smtp.host
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your@email.com"
MAIL_FROM_NAME="${APP_NAME}"

# Security
ASSET_URL=https://yourdomain.com
EOL

print_status "Production .env template created"

# Step 7: Create deployment archive
echo ""
print_info "Step 7: Creating deployment archive..."
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
ARCHIVE_NAME="incident-management-${TIMESTAMP}.tar.gz"

# Create temporary directory for clean files
mkdir -p deploy_temp
rsync -av --exclude-from=- . deploy_temp/ << 'EOL'
.git/
node_modules/
.env
.env.example
storage/logs/*
tests/
.gitignore
.gitattributes
deploy_temp/
EOL

# Create archive
cd deploy_temp
tar -czf "../${ARCHIVE_NAME}" .
cd ..
rm -rf deploy_temp

print_status "Deployment archive created: ${ARCHIVE_NAME}"

# Step 8: Display deployment information
echo ""
echo "🎉 Deployment package ready!"
echo "================================"
echo ""
print_info "Archive: ${ARCHIVE_NAME}"
print_info "Size: $(du -h ${ARCHIVE_NAME} | cut -f1)"
echo ""

# Step 9: Display next steps
echo "📋 Next Steps for Deployment:"
echo "============================="
echo ""
echo "1. 📤 Upload the archive (${ARCHIVE_NAME}) to your server"
echo "2. 🖥️  SSH into your server and extract:"
echo "   tar -xzf ${ARCHIVE_NAME}"
echo ""
echo "3. 🔧 Configure your server:"
echo "   - Copy .env.production to .env"
echo "   - Edit .env with your database credentials"
echo "   - Generate app key: php artisan key:generate --force"
echo ""
echo "4. 🗄️  Setup database:"
echo "   - Create database and user"
echo "   - Run: php artisan migrate --force"
echo "   - Run: php artisan db:seed --class=UserSeeder"
echo "   - Run: php artisan db:seed --class=ResolutionTeamSeeder"
echo ""
echo "5. 🔐 Change default passwords:"
echo "   - Run: ./change-default-passwords.sh"
echo "   - Or use: php artisan user:change-password email@domain.com newpassword"
echo ""
echo "6. 🌐 Configure web server (Nginx/Apache) to point to /public"
echo ""
echo "7. 🔒 Setup SSL certificate (Let's Encrypt recommended)"
echo ""

# Step 10: Security reminders
echo ""
print_warning "Security Checklist:"
echo "==================="
echo "□ Change default user passwords immediately"
echo "□ Set up SSL/HTTPS"
echo "□ Configure firewall (ports 80, 443, 22 only)"
echo "□ Set up fail2ban for brute force protection"
echo "□ Configure backups"
echo "□ Monitor logs regularly"
echo ""

print_status "Deployment preparation complete!"

# Step 11: Hosting recommendations
echo ""
print_info "Recommended Hosting Providers:"
echo "=============================="
echo "💰 Budget ($5-15/month):"
echo "   - DigitalOcean Droplets"
echo "   - Linode Nanode"
echo "   - Vultr Cloud Compute"
echo ""
echo "🚀 Professional ($20-50/month):"
echo "   - AWS EC2 + RDS"
echo "   - Google Cloud Platform"
echo "   - Azure App Service"
echo ""
echo "🎯 Laravel-specific:"
echo "   - Laravel Forge + DigitalOcean"
echo "   - Ploi.io"
echo "   - Laravel Vapor (serverless)"
echo ""

print_info "For detailed deployment instructions, see DEPLOYMENT.md"

echo ""
print_status "Ready to deploy! 🚀"