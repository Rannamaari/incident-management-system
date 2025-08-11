# 🚨 Incident Management System

A comprehensive Laravel-based incident management system for tracking, monitoring, and resolving system outages and incidents with real-time SLA monitoring and automated validation.

## 📋 Table of Contents

- [Features](#-features)
- [Installation](#-installation)
- [Usage](#-usage)
- [SLA Management](#-sla-management)
- [Validation Rules](#-validation-rules)
- [Commands](#-commands)
- [API](#-api)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)

## ⭐ Features

### 🎯 Core Incident Management
- **Incident Creation & Tracking** - Create and track incidents with auto-generated incident codes (INC-YYYYMMDD-XXXX)
- **Real-time SLA Monitoring** - Automatic SLA calculation and breach detection
- **Status Management** - Multi-stage incident status (Open, In Progress, Monitoring, Closed)
- **Severity Levels** - Critical, High, Medium, Low with different SLA targets
- **Duration Tracking** - Human-readable duration display (e.g., "2 days 5 hrs 30 mins")

### 📊 Dashboard & Analytics
- **Interactive Dashboard** - Monthly KPI cards with incident statistics
- **Responsive Tables** - Optimized for desktop and mobile viewing
- **Visual SLA Indicators** - Row highlighting for SLA-breached incidents
- **Search & Filtering** - Advanced filtering by status, severity, date ranges
- **Export Capabilities** - CSV export with comprehensive incident data

### 🔍 Advanced Features
- **RCA Management** - Root Cause Analysis tracking for High/Critical incidents
- **Action Points** - Task management for Critical incidents
- **Incident Logs** - Timeline tracking with timestamped notes
- **Delay Validation** - Automatic delay reason requirement for incidents > 5 hours
- **Data Integrity** - Multi-level validation to prevent incomplete records

### 🎨 User Experience
- **Modern UI** - Clean, responsive design with Tailwind CSS
- **Mobile Optimized** - Fully responsive across all device sizes
- **Interactive Modals** - Smart forms that adapt based on incident data
- **Real-time Updates** - Dynamic field visibility based on duration and severity
- **Accessibility** - WCAG compliant interface design

### 📈 Reporting & Logs
- **Comprehensive Logs Page** - Historical record of all incidents
- **Advanced Search** - Multi-criteria search across incident data
- **Data Export** - Filtered export capabilities
- **Audit Trail** - Complete incident lifecycle tracking

## 🚀 Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

### Setup Steps

1. **Clone & Install Dependencies**
```bash
git clone <repository-url>
cd incident-management-system
composer install
npm install
```

2. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Database Setup**
```bash
# Configure database in .env file
php artisan migrate
php artisan db:seed --class=ResolutionTeamSeeder
```

4. **Build Assets**
```bash
npm run build
```

5. **Start Development Server**
```bash
# Local access only
php artisan serve

# Network access (for testing on mobile devices)
php artisan serve --host=0.0.0.0 --port=8000
```

## 📖 Usage

### Creating Incidents

1. **Navigate to Dashboard** - Access the main dashboard
2. **Click "New Incident"** - Start creating a new incident
3. **Fill Required Fields**:
   - Summary (description of the incident)
   - Affected Services
   - Severity Level
   - Start Time

### Closing Incidents

1. **Open Incident Details** - Click "View" on any incident
2. **Click "Close Incident"** - Red button for critical action
3. **Smart Validation**:
   - System automatically shows required fields based on:
     - Duration (> 5 hours requires delay reason)
     - Severity (Medium/High/Critical require travel/work times)
     - Critical incidents require logs and action points

### Dashboard Features

- **Monthly View** - Filter incidents by month
- **KPI Cards** - Total, Open, High Priority, Resolved incidents
- **Search & Filter** - Real-time filtering by status, severity
- **SLA Indicators** - Red highlighted rows for breached incidents
- **Responsive Design** - Works on desktop, tablet, and mobile

## ⏰ SLA Management

### SLA Targets by Severity
- **Critical**: 2 hours
- **High**: 2 hours  
- **Medium**: 6 hours
- **Low**: 12 hours

### SLA Monitoring
- **Real-time Calculation** - SLA status updates automatically
- **Visual Indicators** - Row highlighting for breached incidents
- **Current Status Display**:
  - Open incidents: "SLA Breached" or "Within SLA"
  - Closed incidents: "SLA Breached" or "SLA Achieved"

## 🧠 Business Logic & Validation Rules

### 🎯 Core Business Rules

Our incident management system implements sophisticated business logic to ensure data integrity, compliance, and operational excellence. Here's the complete breakdown of all implemented rules and validations:

### ⏱️ Duration-Based Logic

#### **Rule 1: Delay Reason Requirement**
```
IF incident_duration > 5 hours 
THEN delay_reason = REQUIRED
```
- **Trigger**: Any incident with duration exceeding 5 hours
- **Action**: System automatically shows delay reason field
- **Validation**: Cannot close incident without providing reason
- **Implementation**: 
  - Frontend: JavaScript shows/hides field dynamically
  - Backend: Server validation enforces requirement
  - Database: Model validation prevents saving incomplete data

#### **Rule 2: Travel & Work Time Requirements**
```
IF severity IN ['Medium', 'High', 'Critical'] 
THEN travel_time AND work_time = REQUIRED
```
- **Purpose**: Track resolution effort for higher severity incidents
- **Fields**: Travel time and Work time (in minutes)
- **Validation**: Both fields required when closing Medium+ incidents

### 🚨 Severity-Based Validation Logic

#### **High Severity Requirements**
When closing High severity incidents, ALL of the following are mandatory:
```
HIGH SEVERITY RULES:
├── Corrective Actions (required)
├── Workaround (required) 
├── Solution (required)
└── Recommendation (required)
```

#### **Critical Severity Requirements**
Critical incidents have the most stringent requirements:
```
CRITICAL SEVERITY RULES:
├── At least 1 Log Entry (required)
│   ├── occurred_at (timestamp required)
│   └── note (description required)
├── At least 1 Action Point (required)
│   ├── description (required)
│   ├── due_date (required)
│   └── completion_status (tracked)
└── All Action Points MUST be completed before closing
```

### 🔄 Real-Time SLA Calculation Logic

#### **SLA Targets by Severity**
```
SLA_TARGETS = {
    'Critical': 120 minutes (2 hours),
    'High': 120 minutes (2 hours),
    'Medium': 360 minutes (6 hours),
    'Low': 720 minutes (12 hours)
}
```

#### **SLA Status Calculation**
```
FOR OPEN INCIDENTS:
IF current_time - started_at > sla_target
THEN status = "SLA Breached"
ELSE status = "Within SLA"

FOR CLOSED INCIDENTS:
IF duration_minutes > sla_target  
THEN status = "SLA Breached"
ELSE status = "SLA Achieved"
```

#### **Visual SLA Indicators**
```
IF sla_breached = TRUE
THEN row_highlight = "bg-red-50/80 border-l-4 border-red-400"
ELSE row_highlight = "normal"
```

### 🔐 Multi-Level Validation Architecture

#### **Level 1: Frontend Validation (JavaScript)**
- **Real-time field visibility**: Fields appear/disappear based on duration and severity
- **Input validation**: Prevents invalid data entry
- **User experience**: Immediate feedback without server round-trip

#### **Level 2: Request Validation (Laravel)**
- **CloseIncidentRequest**: Specialized validation for closing incidents
- **Dynamic rules**: Validation rules adapt based on incident data
- **Server-side enforcement**: Prevents bypassing frontend validation

#### **Level 3: Model Validation (Database)**
- **Eloquent events**: Validation on save/update operations
- **Data integrity**: Catches direct database manipulation
- **Exception handling**: Throws errors for invalid data

### 📊 Automatic Data Processing

#### **Incident Code Generation**
```
FORMAT: INC-YYYYMMDD-XXXX
LOGIC: 
├── Date: Current date or incident start date
├── Counter: Daily sequential number (0001, 0002, etc.)
└── Auto-generated on creation if not provided
```

#### **Duration Calculation**
```
DURATION CALCULATION:
├── Primary: Use stored duration_minutes if available
├── Secondary: Calculate from started_at - resolved_at
├── Ongoing: Real-time calculation for open incidents
└── Format: Human-readable (e.g., "2 days 5 hrs 30 mins")
```

#### **Duration Display Logic**
```
IF duration >= 24 hours:
    FORMAT: "X day(s) Y hr(s) Z min(s)"
    EXAMPLE: "2 days 5 hrs 30 mins"
ELSE:
    FORMAT: "X hr(s) Y min(s)" 
    EXAMPLE: "12 hrs 15 mins"

SPECIAL CASES:
├── 0 duration: "0 mins"
├── Ongoing: "{duration} (ongoing)"
└── Singular/Plural: "1 day" vs "2 days"
```

### 🚫 Data Integrity Protection

#### **Import/Creation Validation**
```
PROTECTION RULES:
├── Cannot create closed incidents without proper validation
├── Cannot import incomplete incident data
├── Cannot bypass severity-based requirements
└── Cannot save incidents without required fields
```

#### **Update Validation**
```
UPDATE RULES:
├── Status changes trigger appropriate validations
├── Closing incidents applies CloseIncidentRequest rules  
├── Cannot remove required data from existing incidents
└── Cannot change severity without meeting new requirements
```

### 🔍 Advanced Logic Features

#### **RCA (Root Cause Analysis) Logic**
```
RCA REQUIREMENTS:
IF severity IN ['High', 'Critical']
THEN rca_required = TRUE
ELSE rca_required = FALSE

RCA STATUS LOGIC:
├── "Not Required": Low/Medium severity
├── "Pending": Required but not uploaded
└── "Attached": RCA file uploaded
```

#### **Action Points Completion Logic**
```
FOR CRITICAL INCIDENTS:
BEFORE CLOSING:
├── Check all action points exist
├── Verify all action points are marked complete
├── Prevent closing if any action point incomplete
└── Throw validation error with details
```

#### **Log Entry Validation**
```
LOG ENTRY RULES:
├── Template filtering: Remove "INDEX" placeholder entries
├── Required fields: occurred_at AND note must be filled
├── Timestamp validation: occurred_at must be valid datetime
└── Content validation: note cannot be empty or template text
```

### ⚡ Performance & Optimization Logic

#### **Query Optimization**
```
PERFORMANCE RULES:
├── Pagination: 15 items per page (dashboard), 25 items (logs)
├── Eager loading: Load relationships efficiently
├── Chunked processing: Large exports processed in 1000-record chunks
└── Cached calculations: KPI data cached for performance
```

#### **Real-time Updates**
```
REAL-TIME LOGIC:
├── SLA status: Calculated on every page load
├── Duration display: Updated for ongoing incidents
├── Visual indicators: Applied based on current SLA status
└── Search/filter: No page reload required
```

### 🛡️ Security & Validation Logic

#### **Input Sanitization**
```
SECURITY MEASURES:
├── Template placeholder removal: Filter "INDEX" entries
├── Required field validation: Prevent empty submissions  
├── Data type validation: Ensure proper field types
└── SQL injection protection: Laravel ORM protection
```

#### **Business Rule Enforcement**
```
ENFORCEMENT LEVELS:
├── Frontend: User experience and immediate feedback
├── Controller: Request validation and business logic
├── Model: Data integrity and relationship validation
└── Database: Constraint enforcement and data consistency
```

This comprehensive business logic ensures that every incident is properly documented, validated, and tracked according to operational requirements while maintaining data integrity and user experience.

## 🛠 Commands

### Check Missing Delay Reasons
```bash
# Identify incidents missing delay reasons
php artisan incidents:check-delay-reasons

# Interactive mode to fix missing delay reasons
php artisan incidents:check-delay-reasons --fix

# Export results to CSV
php artisan incidents:check-delay-reasons --export=missing_delays.csv
```

### Database Operations
```bash
# Fresh installation
php artisan migrate:fresh --seed

# Seed resolution teams
php artisan db:seed --class=ResolutionTeamSeeder
```

## 🎨 UI Features

### Responsive Design
- **Desktop**: Full table view with all columns
- **Tablet**: Optimized column visibility
- **Mobile**: Card-based layout with essential information

### Visual Indicators
- **Severity Colors**: Color-coded priority indicators
- **Status Badges**: Clear status visualization  
- **SLA Highlighting**: Red background for breached incidents
- **Duration Display**: Human-readable format (e.g., "1 day 5 hrs 30 mins")

### Interactive Elements
- **Smart Modals**: Forms adapt based on incident data
- **Search & Filter**: Real-time filtering without page reload
- **Export Options**: Filtered CSV exports
- **Mobile Actions**: Touch-optimized buttons and forms

## 📊 Data Export

### CSV Export Features
- **All Fields**: Comprehensive incident data export
- **Timezone Support**: Maldives timezone formatting
- **Excel Compatible**: BOM encoding for proper display
- **Filtered Exports**: Export only filtered results
- **Large Dataset Support**: Chunked processing for performance

### Export Fields
- Incident details (code, summary, category, etc.)
- Timestamps (started, resolved, created, updated)
- Duration (minutes and human-readable)
- SLA information and status
- RCA and action point data
- Resolution team and work details

## 🔧 Technical Details

### Technology Stack
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **JavaScript**: Vanilla JS for interactivity

### Key Components
- **Models**: Incident, IncidentLog, ActionPoint, Category, etc.
- **Controllers**: IncidentController, LogsController
- **Requests**: CloseIncidentRequest with smart validation
- **Commands**: IdentifyMissingDelayReasons

### Performance Optimizations
- **Pagination**: Efficient large dataset handling
- **Lazy Loading**: Optimized database queries
- **Caching**: Strategic caching for KPI calculations
- **Chunked Processing**: Large data export handling

## 🚀 Deployment

### Production Setup
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache  
php artisan view:cache
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

### Server Requirements
- PHP 8.1+ with required extensions
- Database (MySQL 5.7+ or PostgreSQL 10+)
- Web server (Apache/Nginx)
- SSL certificate (recommended)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📝 License

This Incident Management System is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
