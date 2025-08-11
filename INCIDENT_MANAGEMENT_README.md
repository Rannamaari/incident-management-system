# Laravel Incident Management System

A production-ready incident management system built with Laravel 10, featuring comprehensive CRUD operations, SLA tracking, RCA file management, and CSV import capabilities.

## Features

- **Complete CRUD Operations**: Create, read, update, and delete incidents
- **SLA Management**: Automatic SLA calculation based on severity levels
- **RCA File Management**: Upload and manage Root Cause Analysis documents
- **CSV Import**: Bulk import historical incident data from Excel/CSV files
- **Advanced Filtering**: Search and filter incidents by status, severity, and text
- **Business Logic**: Enforce RCA requirements for High/Critical incidents
- **Responsive UI**: Tailwind CSS-powered interface with Blade templates

## Technology Stack

- **Backend**: Laravel 10, PHP 8.2+
- **Database**: SQLite (default) / MySQL
- **Frontend**: Blade Templates with Tailwind CSS
- **Authentication**: Simple login system
- **File Storage**: Local public disk for RCA files
- **Timezone**: Indian/Maldives

## ✅ SYSTEM IS NOW RUNNING!

**Server**: http://localhost:8000  
**Login**: admin@example.com / password

## Installation & Setup

### 1. Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite or MySQL database

### 2. Environment Configuration

Update your `.env` file with the following settings:

```env
APP_TIMEZONE=Indian/Maldives
FILESYSTEM_DISK=public

# For MySQL (optional):
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=incident_management
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### 3. Installation Steps

```bash
# Install Composer dependencies
composer install

# Install NPM dependencies and build assets
npm install
npm run build

# Run database migrations
php artisan migrate

# Create symbolic link for file storage
php artisan storage:link

# Create test user
php artisan tinker --execute="App\Models\User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);"

# Start the development server
php artisan serve
```

### 4. Access the Application

1. Visit `http://localhost:8000`
2. Login with: admin@example.com / password
3. You'll be redirected to `/incidents` automatically

## Database Schema

The `incidents` table contains 22 main columns plus additional RCA fields:

1. `incident_id` - Unique identifier
2. `summary` - Incident summary/description
3. `outage_category` - Type of outage
4. `category` - Service category
5. `affected_services` - Systems/services affected
6. `started_at` - Incident start time
7. `resolved_at` - Resolution time
8. `duration_minutes` - Manual duration override
9. `fault_type` - Type of fault
10. `root_cause` - Root cause analysis
11. `delay_reason` - Reason for any delays
12. `resolution_team` - Team that resolved the incident
13. `journey_started_at` - Journey start time
14. `island_arrival_at` - Island arrival time
15. `work_started_at` - Work start time
16. `work_completed_at` - Work completion time
17. `pir_rca_no` - PIR/RCA reference number
18. `status` - Current status (Open, In Progress, Monitoring, Closed)
19. `severity` - Severity level (Critical, High, Medium, Low)
20. `sla_minutes` - SLA duration in minutes
21. `exceeded_sla` - Boolean flag for SLA breach
22. `sla_status` - SLA status text

**Additional RCA Fields:**
- `rca_required` - Boolean flag for RCA requirement
- `rca_file_path` - Path to uploaded RCA file
- `rca_received_at` - RCA file upload timestamp

## Business Rules

### SLA Configuration
- **Critical**: 2 hours (120 minutes)
- **High**: 2 hours (120 minutes)  
- **Medium**: 6 hours (360 minutes)
- **Low**: 12 hours (720 minutes)

### RCA Requirements
- High and Critical severity incidents **require** an RCA file before they can be closed
- The system will prevent closing High/Critical incidents without an RCA attachment
- RCA files must be PDF, DOC, or DOCX format (max 10MB)

### Duration Calculation
- Auto-calculated from `started_at` to `resolved_at` if both are present
- Manual `duration_minutes` override available
- SLA status automatically determined based on duration vs. SLA threshold

## CSV Import Feature

Import historical incident data using the CSV import command:

```bash
php artisan incidents:import /path/to/your/file.csv
```

### Required CSV Format

The CSV file must have exactly these 22 headers in this exact order:

```
Incident ID,Outage Details (Incident Summary),Outage Category,Category,Affected Systems/Services,Start Date and Time,Date and Time Resolved,Durations,Fault/Issue Type,Root Cause,Reason for Delay,Resolution Team,Journey Start Time,Island Arrival Time,Work/Repair Start Time,Repair Completion Time,PIR/RCA No,Incident Status,Severity Level,SLA,Exceeded Beyond SLA,SLA Status
```

### Supported Date Formats
- `d/m/y H:i` (e.g., "1/1/25 9:17")
- `d/m/Y H:i` (e.g., "1/1/2025 9:17")
- `m/d/y H:i` (US format)
- `Y-m-d H:i:s` (MySQL format)
- And other common formats

### Duration Formats
- Minutes as numbers: "120"
- HH:MM format: "2:30"
- HH:MM:SS format: "2:30:00"
- Text format: "2 hours 30 minutes"

## File Structure

```
app/
├── Console/Commands/ImportIncidents.php    # CSV import command
├── Http/Controllers/IncidentController.php # Main controller
└── Models/Incident.php                     # Incident model with business logic

database/migrations/
└── 2024_01_01_000000_create_incidents_table.php

resources/views/
├── layouts/
│   ├── app.blade.php                       # Main layout
│   └── navigation.blade.php                # Navigation component
├── incidents/
│   ├── index.blade.php                     # List with filters
│   ├── create.blade.php                    # Create form
│   └── edit.blade.php                      # Edit form
└── auth/
    └── login.blade.php                     # Login page

routes/
├── web.php                                 # Web routes
└── auth.php                               # Auth routes
```

## Usage Examples

### Creating Incidents
1. Click "New Incident" from the incidents list
2. Fill in all required fields (marked with *)
3. Select appropriate severity (affects SLA calculation)
4. Optionally upload RCA file
5. Submit the form

### Updating Incidents
1. Click "Edit" on any incident from the list
2. Modify fields as needed
3. Upload new RCA file if required
4. Note: High/Critical incidents cannot be closed without RCA

### Filtering & Search
- Use the search box to find incidents by ID, summary, category, or services
- Filter by status or severity using the dropdown menus
- Results are paginated (15 per page)

### CSV Import
```bash
# Import from CSV file
php artisan incidents:import storage/incidents_export.csv

# The command will show progress and results:
# - Imported: X new incidents
# - Updated: X existing incidents  
# - Errors: X failed rows
```

## Testing Checklist

### Basic CRUD Operations
- [x] Create new incident with all fields
- [x] View incidents list with pagination
- [x] Edit existing incident
- [x] Delete incident (removes RCA file)

### Business Logic Validation
- [x] Cannot close High/Critical incident without RCA file
- [x] SLA automatically updates when severity changes
- [x] Duration auto-calculates from start/resolved times
- [x] Manual duration override works correctly
- [x] RCA file upload and download works

### CSV Import Testing
- [x] Import succeeds with proper CSV format
- [x] Various date formats are parsed correctly
- [x] Duration formats are handled properly
- [x] Invalid data uses fallback values
- [x] Duplicate incident_id updates existing records

### UI/UX Testing
- [x] Filters work correctly (search, status, severity)
- [x] Responsive design works on mobile
- [x] Error messages display properly
- [x] Success messages show after operations
- [x] File upload progress and validation

## Security Features

- Authentication required for all operations
- File upload validation (type, size limits)
- SQL injection protection via Eloquent ORM
- CSRF protection on all forms
- Input validation and sanitization

## Performance Considerations

- Database indexes on frequently queried fields
- Pagination for large incident lists
- Optimized file storage in public disk
- Efficient CSV import with progress tracking

## Support & Maintenance

### Common Commands
```bash
# Clear application cache
php artisan cache:clear

# Regenerate storage link
php artisan storage:link

# Run migrations
php artisan migrate

# Import incidents from CSV
php artisan incidents:import /path/to/file.csv

# Create additional users
php artisan tinker --execute="App\Models\User::create(['name' => 'Name', 'email' => 'email@example.com', 'password' => Hash::make('password')]);"
```

### Log Files
- Application logs: `storage/logs/laravel.log`
- Web server logs for file upload issues

---

**🚀 SYSTEM STATUS: READY & RUNNING!**  
**Version**: 1.0.0  
**Laravel**: 12.x  
**PHP**: 8.2+  
**Database**: SQLite/MySQL  
**Server**: http://localhost:8000  
**Login**: admin@example.com / password