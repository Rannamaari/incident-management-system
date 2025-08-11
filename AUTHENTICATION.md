# 🔐 Authentication & User Management

## Default User Accounts

The system comes with three pre-configured user accounts with different access levels:

### 👑 Administrator (Full Access)
- **Email**: `admin@incident.com`
- **Password**: `admin123`
- **Role**: Admin
- **Permissions**: 
  - Full system access
  - Create, edit, view, and delete incidents
  - Export data
  - Manage all system functions

### ✏️ Editor (Create & Edit Access)
- **Email**: `editor@incident.com`
- **Password**: `editor123`
- **Role**: Editor
- **Permissions**:
  - Create and edit incidents
  - View all incidents
  - Export data
  - Cannot delete incidents

### 👁️ Viewer (Read-Only Access)
- **Email**: `viewer@incident.com`
- **Password**: `viewer123`
- **Role**: Viewer
- **Permissions**:
  - View incidents only
  - Cannot create, edit, or delete
  - Cannot export data

## Role-Based Access Control

### Permission Matrix

| Feature | Admin | Editor | Viewer |
|---------|-------|--------|--------|
| View Incidents | ✅ | ✅ | ✅ |
| Create Incidents | ✅ | ✅ | ❌ |
| Edit Incidents | ✅ | ✅ | ❌ |
| Delete Incidents | ✅ | ❌ | ❌ |
| Close Incidents | ✅ | ✅ | ❌ |
| Export Data | ✅ | ✅ | ❌ |
| View Logs Page | ✅ | ✅ | ✅ |
| Download RCA Files | ✅ | ✅ | ✅ |
| User Management | ✅ | ❌ | ❌ |

## Security Features

### Route Protection
- All routes require authentication
- Role-based middleware controls access levels
- Automatic redirects for unauthorized access

### UI Elements
- Buttons and links appear/hide based on user permissions
- Role indicators in navigation
- Color-coded role display (Admin: Red, Editor: Blue, Viewer: Green)

### Data Validation
- Multi-level validation (Frontend, Backend, Database)
- Protected against unauthorized modifications
- Secure form handling with CSRF protection

## Login Process

1. Navigate to `/login`
2. Enter email and password
3. System validates credentials and role
4. Redirects to appropriate dashboard based on permissions

## Changing User Roles

To change a user's role, update the `role` column in the `users` table:

```sql
UPDATE users SET role = 'admin|editor|viewer' WHERE email = 'user@example.com';
```

## Adding New Users

Use the Laravel tinker command or create a seeder:

```php
User::create([
    'name' => 'New User',
    'email' => 'newuser@incident.com',
    'password' => Hash::make('password123'),
    'role' => 'viewer', // or 'editor' or 'admin'
]);
```

## Session Management

- Sessions expire automatically
- Secure session configuration
- Remember me functionality available
- Logout clears all session data

## Production Security Recommendations

1. Change default passwords immediately
2. Use strong, unique passwords
3. Enable HTTPS/SSL
4. Configure proper firewall rules
5. Regular security updates
6. Monitor access logs
7. Implement rate limiting
8. Use environment-specific configurations