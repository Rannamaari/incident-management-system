# 🔐 User Credentials - Incident Management System

**Last Updated:** January 2025

## Default User Accounts

### 👑 Administrator (Full Access)
- **Name:** Ibrahim Husham
- **Email:** `admin@incident.com`
- **Password:** `admin123`
- **Role:** Admin
- **Permissions:** Full system access (create, edit, delete, view, export)

### 👁️ Viewer (Read-Only Access)
- **Name:** Incident Viewer
- **Email:** `viewer@incident.com`
- **Password:** `viewer123`
- **Role:** Viewer
- **Permissions:** View incidents only (no create/edit/delete)

### ✏️ NOC Editor (Create & Edit Access)
- **Name:** NOC Editor
- **Email:** `noc@incident.com`
- **Password:** `noc123`
- **Role:** Editor
- **Permissions:** Create and edit incidents, view all, export data

### ✏️ IM Editor (Create & Edit Access)
- **Name:** IM Editor
- **Email:** `im@incident.com`
- **Password:** `im123`
- **Role:** Editor
- **Permissions:** Create and edit incidents, view all, export data

---

## Quick Reference

| Email | Password | Role |
|-------|----------|------|
| admin@incident.com | admin123 | Admin |
| viewer@incident.com | viewer123 | Viewer |
| noc@incident.com | noc123 | Editor |
| im@incident.com | im123 | Editor |

---

## Security Note

⚠️ **IMPORTANT:** These are default passwords for development/testing. 

**For production:**
- Change all passwords immediately after deployment
- Use strong, unique passwords
- Enable two-factor authentication if available
- Regularly rotate passwords

---

## How to Reset Passwords

If you need to reset passwords, you can use Laravel Tinker:

```bash
php artisan tinker
```

Then run:
```php
$user = App\Models\User::where('email', 'user@example.com')->first();
$user->password = Hash::make('newpassword');
$user->save();
```

Or use the seeder to reset all to defaults:
```bash
php artisan db:seed --class=UserSeeder
```

