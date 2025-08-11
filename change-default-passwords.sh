#!/bin/bash

echo "🔐 Changing Default Passwords for Incident Management System"
echo "=========================================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

# Function to generate a random password
generate_password() {
    openssl rand -base64 12 | tr -d "=+/" | cut -c1-12
}

echo ""
echo "Choose password change method:"
echo "1) Enter custom passwords manually"
echo "2) Generate random secure passwords"
read -p "Enter your choice (1 or 2): " choice

case $choice in
    1)
        # Manual password entry
        echo ""
        echo "🔧 Manual Password Entry Mode"
        echo "------------------------------"
        
        read -s -p "Enter new password for Admin (admin@incident.com): " admin_pass
        echo ""
        read -s -p "Enter new password for Editor (editor@incident.com): " editor_pass
        echo ""
        read -s -p "Enter new password for Viewer (viewer@incident.com): " viewer_pass
        echo ""
        ;;
    2)
        # Generate random passwords
        echo ""
        echo "🎲 Generating Random Secure Passwords"
        echo "-------------------------------------"
        
        admin_pass=$(generate_password)
        editor_pass=$(generate_password)
        viewer_pass=$(generate_password)
        
        echo "Generated passwords:"
        echo "Admin:  $admin_pass"
        echo "Editor: $editor_pass"
        echo "Viewer: $viewer_pass"
        echo ""
        ;;
    *)
        echo "❌ Invalid choice. Exiting."
        exit 1
        ;;
esac

echo "🔄 Updating passwords..."

# Change passwords using the artisan command
php artisan user:change-password admin@incident.com "$admin_pass"
php artisan user:change-password editor@incident.com "$editor_pass"  
php artisan user:change-password viewer@incident.com "$viewer_pass"

echo ""
echo "✅ Password change completed!"
echo ""
echo "📋 Updated Login Credentials:"
echo "=============================="
echo "Admin:  admin@incident.com  | Password: $admin_pass"
echo "Editor: editor@incident.com | Password: $editor_pass"
echo "Viewer: viewer@incident.com | Password: $viewer_pass"
echo ""
echo "⚠️  IMPORTANT: Save these credentials in a secure location!"
echo "🗑️  Consider deleting this script after use for security."