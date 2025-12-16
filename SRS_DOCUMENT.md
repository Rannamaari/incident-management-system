# 📋 Software Requirements Specification (SRS)
## Incident Management System

**Document Version:** 1.0
**Date:** September 19, 2025
**Project:** Incident Management System
**Technology:** Laravel 12.x, PHP 8.2+, Tailwind CSS

---

## 📑 Table of Contents

1. [Introduction](#1-introduction)
2. [Overall Description](#2-overall-description)
3. [System Features](#3-system-features)
4. [External Interface Requirements](#4-external-interface-requirements)
5. [Non-Functional Requirements](#5-non-functional-requirements)
6. [System Architecture](#6-system-architecture)
7. [Database Requirements](#7-database-requirements)
8. [Security Requirements](#8-security-requirements)
9. [Appendices](#9-appendices)

---

## 1. Introduction

### 1.1 Purpose
This Software Requirements Specification (SRS) document describes the functional and non-functional requirements for the Incident Management System. This system is designed to track, monitor, and manage IT service incidents with comprehensive SLA monitoring, role-based access control, and advanced reporting capabilities.

### 1.2 Scope
The Incident Management System provides:
- Complete incident lifecycle management (Create, Read, Update, Delete)
- Real-time SLA monitoring and breach detection
- Role-based access control (Admin, Editor, Viewer)
- Root Cause Analysis (RCA) management
- Comprehensive reporting and analytics
- CSV data import/export capabilities
- Mobile-responsive user interface

### 1.3 Definitions, Acronyms, and Abbreviations

| Term | Definition |
|------|------------|
| **SLA** | Service Level Agreement - Target resolution time based on incident severity |
| **RCA** | Root Cause Analysis - Document explaining the underlying cause of an incident |
| **CRUD** | Create, Read, Update, Delete operations |
| **KPI** | Key Performance Indicator |
| **PIR** | Post Incident Review |
| **RBAC** | Role-Based Access Control |
| **UI/UX** | User Interface/User Experience |
| **CSV** | Comma-Separated Values file format |

### 1.4 References
- Laravel 12.x Documentation
- Tailwind CSS Framework
- ISO/IEC 20000 IT Service Management
- ITIL Framework for Incident Management

### 1.5 Overview
This document is structured to provide comprehensive requirements for developing, testing, and maintaining the Incident Management System. It covers functional requirements, system architecture, user interfaces, and quality attributes.

---

## 2. Overall Description

### 2.1 Product Perspective
The Incident Management System is a web-based application built using Laravel framework with the following key characteristics:

- **Standalone System**: Self-contained incident management solution
- **Web-Based**: Accessible through standard web browsers
- **Database-Driven**: Persistent data storage using MySQL/SQLite
- **Role-Based**: Three-tier user access control
- **Mobile-Responsive**: Optimized for desktop, tablet, and mobile devices

### 2.2 Product Functions

#### 2.2.1 Core Functions
- **Incident Management**: Full CRUD operations for incident records
- **SLA Monitoring**: Automatic calculation and real-time status updates
- **User Authentication**: Secure login with role-based permissions
- **Data Export/Import**: CSV format for data exchange
- **File Management**: RCA document upload and storage
- **Reporting**: Comprehensive analytics and KPI dashboards

#### 2.2.2 Supporting Functions
- **Search and Filtering**: Advanced incident discovery
- **Audit Logging**: Complete activity tracking
- **Validation**: Multi-level data integrity checks
- **Notification**: Status change alerts
- **Backup/Recovery**: Data protection mechanisms

### 2.3 User Classes and Characteristics

#### 2.3.1 Administrator
- **Characteristics**: IT managers, system administrators
- **Technical Expertise**: High
- **Frequency of Use**: Daily
- **Key Functions**: Full system access, user management, system configuration

#### 2.3.2 Editor
- **Characteristics**: IT technicians, incident handlers
- **Technical Expertise**: Medium to High
- **Frequency of Use**: Daily
- **Key Functions**: Create/edit incidents, manage RCA files, export data

#### 2.3.3 Viewer
- **Characteristics**: Management, stakeholders, read-only users
- **Technical Expertise**: Low to Medium
- **Frequency of Use**: Weekly/Monthly
- **Key Functions**: View incidents, access reports, download RCA files

### 2.4 Operating Environment

#### 2.4.1 Client-Side Requirements
- **Browser Support**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Screen Resolution**: Minimum 320px (mobile) to 1920px+ (desktop)
- **JavaScript**: ES6+ enabled
- **Network**: Stable internet connection

#### 2.4.2 Server-Side Requirements
- **Operating System**: Linux (Ubuntu 20.04+), Windows Server 2019+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 8.2 or higher
- **Database**: MySQL 8.0+ or SQLite 3.35+
- **Storage**: Minimum 10GB for application and data

### 2.5 Design and Implementation Constraints

#### 2.5.1 Technical Constraints
- **Framework**: Laravel 12.x (PHP framework)
- **Frontend**: Blade templating with Tailwind CSS
- **Database**: Relational database (MySQL/SQLite)
- **File Storage**: Local file system for RCA documents
- **Session Management**: Server-side sessions

#### 2.5.2 Business Constraints
- **Timezone**: Indian/Maldives timezone for all timestamps
- **Language**: English only
- **Currency**: Not applicable
- **Compliance**: ISO 20000 incident management principles

### 2.6 Assumptions and Dependencies

#### 2.6.1 Assumptions
- Users have basic computer literacy
- Stable network connectivity available
- PHP and required extensions are properly configured
- Database server is available and accessible

#### 2.6.2 Dependencies
- Laravel framework and its dependencies
- Composer for PHP package management
- NPM for frontend asset management
- Database server availability
- File system access for RCA storage

---

## 3. System Features

### 3.1 Incident Management

#### 3.1.1 Description
Core functionality for managing incident records throughout their lifecycle from creation to closure.

#### 3.1.2 Functional Requirements

**FR-001: Create Incident**
- The system SHALL allow authorized users (Editor, Admin) to create new incidents
- The system SHALL auto-generate unique incident codes in format "INC-YYYYMMDD-XXXX"
- The system SHALL require mandatory fields: Summary, Affected Services, Severity, Started At
- The system SHALL automatically set SLA targets based on severity level
- The system SHALL validate all input data before saving

**FR-002: View Incidents**
- The system SHALL display incidents in a paginated list (15 items per page)
- The system SHALL show incident code, summary, status, severity, and SLA status
- The system SHALL highlight SLA-breached incidents with visual indicators
- The system SHALL provide detailed view for individual incidents
- The system SHALL be accessible to all authenticated user roles

**FR-003: Update Incident**
- The system SHALL allow authorized users (Editor, Admin) to modify incident details
- The system SHALL recalculate SLA status when severity or timing changes
- The system SHALL validate business rules before saving changes
- The system SHALL maintain audit trail of all modifications
- The system SHALL enforce closure requirements based on incident attributes

**FR-004: Delete Incident**
- The system SHALL allow only Administrators to delete incidents
- The system SHALL remove associated RCA files when deleting incidents
- The system SHALL confirm deletion before permanent removal
- The system SHALL maintain referential integrity in the database

#### 3.1.3 Input/Output Specifications

**Inputs:**
- Incident summary (text, max 1000 characters)
- Affected services (text, max 255 characters)
- Severity level (Critical, High, Medium, Low)
- Start date/time (datetime)
- Resolution date/time (datetime, optional)
- Category, fault type, resolution team (selectable/editable)
- Root cause analysis details (text)
- Work timing details (datetime fields)

**Outputs:**
- Incident list with filtering and search capabilities
- Individual incident detail views
- SLA status calculations and visual indicators
- Validation messages and error handling
- Success confirmations for operations

### 3.2 SLA Management

#### 3.2.1 Description
Automated Service Level Agreement monitoring with real-time calculations and breach detection.

#### 3.2.2 Functional Requirements

**FR-005: SLA Target Configuration**
- The system SHALL define SLA targets by severity:
  - Critical: 120 minutes (2 hours)
  - High: 120 minutes (2 hours)
  - Medium: 360 minutes (6 hours)
  - Low: 720 minutes (12 hours)

**FR-006: SLA Calculation**
- The system SHALL calculate SLA status in real-time for open incidents
- The system SHALL use actual duration for closed incidents
- The system SHALL update SLA status when incident timing changes
- The system SHALL handle timezone conversions correctly

**FR-007: SLA Breach Detection**
- The system SHALL identify when incidents exceed SLA targets
- The system SHALL provide visual indicators for breached SLAs
- The system SHALL distinguish between "SLA Breached" and "Within SLA" for open incidents
- The system SHALL distinguish between "SLA Breached" and "SLA Achieved" for closed incidents

#### 3.2.3 Business Rules
- SLA calculation starts from incident start time
- SLA status updates dynamically for open incidents
- Visual highlighting applies to all breached incidents
- SLA targets cannot be modified by users (system-defined)

### 3.3 User Authentication and Authorization

#### 3.3.1 Description
Secure user authentication with role-based access control system.

#### 3.3.2 Functional Requirements

**FR-008: User Authentication**
- The system SHALL require valid credentials for access
- The system SHALL maintain secure user sessions
- The system SHALL provide logout functionality
- The system SHALL protect against unauthorized access

**FR-009: Role-Based Access Control**
- The system SHALL support three user roles: Admin, Editor, Viewer
- The system SHALL enforce permissions based on user roles:
  - Admin: Full access (create, edit, delete, export)
  - Editor: Create, edit, export (cannot delete)
  - Viewer: Read-only access
- The system SHALL hide unauthorized UI elements based on role
- The system SHALL validate permissions on server-side operations

**FR-010: Default User Accounts**
- The system SHALL provide default accounts:
  - admin@incident.com (Admin role)
  - editor@incident.com (Editor role)
  - viewer@incident.com (Viewer role)
- The system SHALL require password changes on first production use
- The system SHALL display role indicators in the user interface

### 3.4 Data Import and Export

#### 3.4.1 Description
Capabilities for importing historical data and exporting incident information in CSV format.

#### 3.4.2 Functional Requirements

**FR-011: CSV Export**
- The system SHALL export incident data in CSV format
- The system SHALL include all incident fields in exports
- The system SHALL apply current filters to export data
- The system SHALL format dates in Maldives timezone
- The system SHALL be available to Editor and Admin roles only

**FR-012: CSV Import**
- The system SHALL import incident data from CSV files via command line
- The system SHALL validate data formats during import
- The system SHALL handle various date/time formats
- The system SHALL provide import progress and error reporting
- The system SHALL update existing incidents based on incident ID

**FR-013: Export Preview**
- The system SHALL show preview of export data before download
- The system SHALL display record count and sample data
- The system SHALL confirm filter settings before export

### 3.5 Root Cause Analysis (RCA) Management

#### 3.5.1 Description
Management of RCA documents for High and Critical severity incidents.

#### 3.5.2 Functional Requirements

**FR-014: RCA Requirements**
- The system SHALL require RCA files for High and Critical severity incidents
- The system SHALL prevent closure of High/Critical incidents without RCA
- The system SHALL accept PDF, DOC, and DOCX file formats
- The system SHALL limit file size to 10MB maximum

**FR-015: RCA File Management**
- The system SHALL store RCA files securely on the server
- The system SHALL provide download access to all authenticated users
- The system SHALL delete RCA files when incidents are deleted
- The system SHALL track RCA upload timestamp

**FR-016: RCA Status Tracking**
- The system SHALL display RCA status badges:
  - "Not Required" for Low/Medium severity
  - "Pending" for High/Critical without RCA
  - "Attached" for incidents with RCA files
- The system SHALL use color-coded indicators for RCA status

### 3.6 Business Logic and Validation

#### 3.6.1 Description
Complex business rules and validation logic for incident management.

#### 3.6.2 Functional Requirements

**FR-017: Duration-Based Validation**
- The system SHALL require delay reason for incidents exceeding 5 hours
- The system SHALL require travel and work time for Medium+ severity incidents
- The system SHALL prevent closure without required fields

**FR-018: Critical Incident Requirements**
- The system SHALL require at least one log entry for Critical incidents
- The system SHALL require at least one action point for Critical incidents
- The system SHALL require all action points to be completed before closure
- The system SHALL validate completeness before allowing closure

**FR-019: High Severity Requirements**
- The system SHALL require the following fields for High severity closure:
  - Corrective Actions
  - Workaround
  - Solution
  - Recommendation
- The system SHALL validate all required fields are filled

**FR-020: Data Integrity Validation**
- The system SHALL validate date/time relationships (resolved >= started)
- The system SHALL ensure required fields are not empty
- The system SHALL sanitize input data for security
- The system SHALL provide meaningful error messages for validation failures

### 3.7 Search and Filtering

#### 3.7.1 Description
Advanced search and filtering capabilities for incident discovery.

#### 3.7.2 Functional Requirements

**FR-021: Search Functionality**
- The system SHALL provide text search across multiple fields:
  - Incident code
  - Summary
  - Category
  - Affected services
- The system SHALL support partial matches and case-insensitive search
- The system SHALL highlight search results

**FR-022: Filter Options**
- The system SHALL provide filtering by:
  - Status (Open, In Progress, Monitoring, Closed)
  - Severity (Critical, High, Medium, Low)
  - Date ranges
- The system SHALL allow combining multiple filters
- The system SHALL maintain filter state during pagination

**FR-023: Filter Persistence**
- The system SHALL remember filter settings during session
- The system SHALL provide clear filter option
- The system SHALL show active filter indicators

### 3.8 Reporting and Analytics

#### 3.8.1 Description
Comprehensive reporting dashboard with charts and KPI metrics.

#### 3.8.2 Functional Requirements

**FR-024: Reports Dashboard**
- The system SHALL provide a dedicated reports page
- The system SHALL display multiple chart types:
  - Severity distribution (pie chart)
  - Status distribution (pie chart)
  - Monthly trends (line chart)
  - Daily trends (line chart)
  - Category breakdown (bar chart)
  - SLA performance (pie chart)
  - Resolution time distribution (bar chart)

**FR-025: KPI Metrics**
- The system SHALL display key metrics:
  - Total incidents count
  - Open incidents count
  - Critical incidents count
  - SLA breached count
  - Average resolution time
  - RCA required count

**FR-026: Date Filtering**
- The system SHALL provide preset date ranges:
  - Last month
  - Last quarter
  - Last 6 months
  - Last year
  - Year to date
- The system SHALL allow custom date range selection
- The system SHALL update all charts and metrics based on date filter

---

## 4. External Interface Requirements

### 4.1 User Interfaces

#### 4.1.1 General UI Requirements
- **Responsive Design**: The interface SHALL adapt to screen sizes from 320px (mobile) to 1920px+ (desktop)
- **Modern UI**: Clean, professional appearance using Tailwind CSS framework
- **Accessibility**: WCAG 2.1 AA compliant interface elements
- **Browser Compatibility**: Support for Chrome 90+, Firefox 88+, Safari 14+, Edge 90+

#### 4.1.2 Specific Interface Requirements

**Login Page**
- Simple form with email and password fields
- "Remember me" checkbox option
- Clear error messages for invalid credentials
- Responsive layout for mobile devices

**Dashboard/Incidents List**
- Tabular display with sortable columns
- Search bar with real-time filtering
- Status and severity filter dropdowns
- Pagination controls (15 items per page)
- "New Incident" button for authorized users
- Action buttons (View, Edit, Delete) based on user permissions

**Incident Form (Create/Edit)**
- Multi-section form with grouped fields
- Real-time validation feedback
- Dynamic field visibility based on severity and duration
- File upload for RCA documents
- Save and Cancel buttons

**Incident Detail View**
- Comprehensive incident information display
- RCA file download link (if available)
- Action buttons for editing and closing
- Related logs and action points display

**Reports Dashboard**
- Interactive charts using Chart.js library
- KPI cards with key metrics
- Date range picker for filtering
- Responsive chart sizing for mobile

### 4.2 Hardware Interfaces
- **Client Hardware**: Any device capable of running modern web browsers
- **Server Hardware**: Standard web server hardware requirements
- **Storage**: File system access for RCA document storage
- **Network**: Standard TCP/IP network connectivity

### 4.3 Software Interfaces

#### 4.3.1 Database Interface
- **Database Management System**: MySQL 8.0+ or SQLite 3.35+
- **Connection**: PDO-based database abstraction through Laravel Eloquent ORM
- **Transactions**: ACID-compliant transaction support
- **Backup**: Standard database backup and recovery procedures

#### 4.3.2 Web Server Interface
- **HTTP Server**: Apache 2.4+ or Nginx 1.18+
- **PHP Interface**: PHP-FPM or mod_php
- **SSL/TLS**: HTTPS support for secure communications
- **Static Files**: Efficient serving of CSS, JavaScript, and uploaded files

#### 4.3.3 External Service Interfaces
- **Email Service**: SMTP configuration for notifications (optional)
- **File Storage**: Local file system for RCA document storage
- **Session Storage**: File-based or database session storage

### 4.4 Communication Interfaces

#### 4.4.1 Network Protocols
- **HTTP/HTTPS**: Primary communication protocol
- **TCP/IP**: Underlying network protocol
- **WebSocket**: Real-time updates (future enhancement)

#### 4.4.2 Data Formats
- **HTML**: Web page markup
- **JSON**: AJAX API responses
- **CSV**: Data import/export format
- **PDF/DOC/DOCX**: RCA document formats

---

## 5. Non-Functional Requirements

### 5.1 Performance Requirements

#### 5.1.1 Response Time
- **Page Load Time**: Maximum 2 seconds for standard pages
- **Search Results**: Maximum 1 second for search/filter operations
- **Report Generation**: Maximum 5 seconds for complex reports
- **File Upload**: Progress indication for files >1MB

#### 5.1.2 Throughput
- **Concurrent Users**: Support minimum 50 concurrent users
- **Database Operations**: Handle 100 queries per second
- **File Downloads**: Support 10 concurrent RCA downloads

#### 5.1.3 Capacity
- **Incident Records**: Support minimum 100,000 incident records
- **Storage**: 10GB minimum for application and data
- **RCA Files**: Support 1000 concurrent RCA files (10MB each)

### 5.2 Security Requirements

#### 5.2.1 Authentication
- **Password Security**: Minimum 8 characters with complexity requirements
- **Session Management**: Secure session handling with timeout
- **Brute Force Protection**: Account lockout after failed attempts
- **CSRF Protection**: Cross-Site Request Forgery prevention

#### 5.2.2 Authorization
- **Role-Based Access**: Enforce permissions at every access point
- **Data Isolation**: Users only access authorized data
- **Admin Functions**: Restricted to admin role only
- **API Security**: All API endpoints require authentication

#### 5.2.3 Data Protection
- **Input Validation**: Sanitize all user inputs
- **SQL Injection Prevention**: Parameterized queries only
- **XSS Prevention**: Output encoding and CSP headers
- **File Upload Security**: Validate file types and scan content

#### 5.2.4 Communication Security
- **HTTPS Enforcement**: All communications over encrypted channels
- **Secure Headers**: Implement security headers (HSTS, X-Frame-Options, etc.)
- **Data Encryption**: Sensitive data encrypted at rest and in transit

### 5.3 Reliability Requirements

#### 5.3.1 Availability
- **Uptime**: 99.5% availability target
- **Scheduled Maintenance**: Maximum 4 hours monthly downtime
- **Error Recovery**: Graceful handling of system errors
- **Data Integrity**: No data loss during normal operations

#### 5.3.2 Fault Tolerance
- **Database Failures**: Graceful degradation with error messages
- **File System Issues**: Continue operation without RCA uploads
- **Network Problems**: Retry mechanisms for critical operations

### 5.4 Usability Requirements

#### 5.4.1 Ease of Use
- **Learning Curve**: New users productive within 30 minutes
- **Intuitive Interface**: Self-explanatory navigation and controls
- **Error Messages**: Clear, actionable error descriptions
- **Help System**: Context-sensitive help and tooltips

#### 5.4.2 Accessibility
- **WCAG Compliance**: Meet WCAG 2.1 AA standards
- **Keyboard Navigation**: Full functionality via keyboard
- **Screen Reader Support**: Compatible with assistive technologies
- **Color Contrast**: Minimum 4.5:1 contrast ratio

#### 5.4.3 Mobile Usability
- **Touch Interface**: Optimized for touch interactions
- **Responsive Layout**: Adapts to various screen sizes
- **Mobile Performance**: Fast loading on mobile networks
- **Offline Capability**: Basic viewing without network (future)

### 5.5 Scalability Requirements

#### 5.5.1 Horizontal Scaling
- **Load Balancing**: Support multiple web server instances
- **Database Scaling**: Read replicas for reporting queries
- **Session Sharing**: Shared session storage for multi-server

#### 5.5.2 Vertical Scaling
- **Resource Utilization**: Efficient use of CPU and memory
- **Database Optimization**: Indexed queries and optimized schema
- **Caching Strategy**: Implement caching for frequent operations

### 5.6 Maintainability Requirements

#### 5.6.1 Code Quality
- **Documentation**: Comprehensive code documentation
- **Standards Compliance**: Follow PSR standards for PHP
- **Testing**: Unit and integration test coverage >80%
- **Version Control**: Git-based source code management

#### 5.6.2 Deployment
- **Environment Separation**: Development, staging, production environments
- **Configuration Management**: Environment-specific configurations
- **Database Migrations**: Version-controlled database changes
- **Automated Deployment**: CI/CD pipeline support

### 5.7 Compatibility Requirements

#### 5.7.1 Browser Compatibility
- **Modern Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Mobile Browsers**: iOS Safari, Chrome Android
- **Legacy Support**: Graceful degradation for older browsers

#### 5.7.2 Operating System Compatibility
- **Server OS**: Linux (Ubuntu 20.04+), Windows Server 2019+
- **Client OS**: Windows, macOS, Linux, iOS, Android
- **Virtualization**: Docker container support

---

## 6. System Architecture

### 6.1 Architectural Overview

#### 6.1.1 Architecture Pattern
The Incident Management System follows the **Model-View-Controller (MVC)** architectural pattern as implemented by the Laravel framework, with additional layers for business logic and data access.

```
┌─────────────────────────────────────┐
│           Presentation Layer        │
│     (Blade Templates + Tailwind)    │
├─────────────────────────────────────┤
│          Controller Layer           │
│     (HTTP Controllers + Routes)     │
├─────────────────────────────────────┤
│         Business Logic Layer        │
│    (Model Methods + Services)       │
├─────────────────────────────────────┤
│          Data Access Layer          │
│      (Eloquent ORM + Migrations)    │
├─────────────────────────────────────┤
│           Database Layer            │
│        (MySQL/SQLite RDBMS)        │
└─────────────────────────────────────┘
```

#### 6.1.2 Component Architecture

**Frontend Components:**
- **User Interface**: Responsive web interface using Blade templates
- **Styling**: Tailwind CSS for consistent styling and responsive design
- **JavaScript**: Vanilla JavaScript for interactivity and AJAX calls
- **Charts**: Chart.js library for data visualization

**Backend Components:**
- **Web Framework**: Laravel 12.x providing MVC structure
- **Authentication**: Laravel's built-in authentication system
- **Database ORM**: Eloquent for database operations
- **File Storage**: Laravel's file storage abstraction
- **Command System**: Artisan commands for data import/export

**Data Layer:**
- **Primary Database**: MySQL or SQLite for incident data
- **File Storage**: Local file system for RCA documents
- **Session Storage**: Database or file-based sessions
- **Cache**: File-based caching for performance

### 6.2 System Components

#### 6.2.1 Core Application Components

**Models:**
- `Incident`: Core incident data and business logic
- `User`: User authentication and role management
- `IncidentLog`: Timeline entries for incidents
- `ActionPoint`: Action items for critical incidents
- `Category`, `OutageCategory`, `FaultType`, `ResolutionTeam`: Reference data

**Controllers:**
- `IncidentController`: CRUD operations for incidents
- `ReportsController`: Analytics and reporting functionality
- `LogsController`: Historical incident logs
- `IncidentRCAController`: RCA file management
- `ProfileController`: User profile management

**Views:**
- `incidents/`: Incident management interface
- `reports/`: Analytics dashboard
- `logs/`: Historical data views
- `layouts/`: Common layout templates
- `auth/`: Authentication pages

**Services:**
- `RcaGenerator`: RCA document processing
- Validation services for business rules
- Export/import services for CSV operations

#### 6.2.2 Infrastructure Components

**Web Server Layer:**
- Apache/Nginx: HTTP request handling
- PHP-FPM: PHP process management
- SSL/TLS: Encrypted communications

**Application Layer:**
- Laravel Framework: Core application structure
- Composer: Dependency management
- Artisan: Command-line interface

**Database Layer:**
- MySQL/SQLite: Relational data storage
- Migration system: Database version control
- Seeder system: Test data generation

### 6.3 Data Flow Architecture

#### 6.3.1 User Request Flow
```
User Request → Web Server → Laravel Router → Controller → Model → Database
                                      ↓
User Response ← View (Blade) ← Controller ← Model ← Database Response
```

#### 6.3.2 Authentication Flow
```
Login Request → AuthController → User Model → Database Verification
                      ↓
Session Creation ← AuthController ← Validation Success
                      ↓
Dashboard Redirect ← Role-based Routing
```

#### 6.3.3 Incident Management Flow
```
Create/Update → IncidentController → Validation → Business Logic → Database
                        ↓
SLA Calculation → Model Events → Auto-calculations → Storage
                        ↓
Response → View Rendering → User Interface Update
```

### 6.4 Integration Architecture

#### 6.4.1 Internal Integrations
- **Model Relationships**: Eloquent relationships between entities
- **Event System**: Model events for automatic calculations
- **Middleware**: Request/response processing pipeline
- **Service Providers**: Dependency injection and service binding

#### 6.4.2 External Integrations
- **File System**: Local storage for RCA documents
- **Email System**: SMTP for notifications (optional)
- **CSV Processing**: Import/export functionality
- **Backup System**: Database and file backup procedures

### 6.5 Deployment Architecture

#### 6.5.1 Development Environment
```
Developer Machine:
├── PHP 8.2+ with Extensions
├── Composer for Dependencies
├── Node.js + NPM for Assets
├── Local Database (SQLite)
└── Development Web Server
```

#### 6.5.2 Production Environment
```
Production Server:
├── Web Server (Apache/Nginx)
├── PHP-FPM Process Manager
├── MySQL Database Server
├── File Storage System
├── SSL Certificate
└── Backup System
```

#### 6.5.3 Scalability Architecture
```
Load Balancer
├── Web Server 1 (Laravel App)
├── Web Server 2 (Laravel App)
└── Web Server N (Laravel App)
            ↓
    Shared Database Server
            ↓
    Shared File Storage
```

---

## 7. Database Requirements

### 7.1 Database Overview

#### 7.1.1 Database Management System
- **Primary DBMS**: MySQL 8.0+ for production environments
- **Alternative DBMS**: SQLite 3.35+ for development/testing
- **Character Set**: UTF-8 (utf8mb4) for full Unicode support
- **Collation**: utf8mb4_unicode_ci for case-insensitive comparisons

#### 7.1.2 Database Design Principles
- **Normalization**: Third Normal Form (3NF) with selective denormalization
- **Referential Integrity**: Foreign key constraints where appropriate
- **Data Types**: Optimal data types for performance and storage
- **Indexing**: Strategic indexing for query performance

### 7.2 Database Schema

#### 7.2.1 Core Tables

**incidents**
```sql
CREATE TABLE incidents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    incident_code VARCHAR(20) UNIQUE NOT NULL,
    summary TEXT NOT NULL,
    outage_category VARCHAR(255),
    category VARCHAR(255),
    outage_category_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    fault_type_id BIGINT UNSIGNED,
    resolution_team_id BIGINT UNSIGNED,
    affected_services VARCHAR(255) NOT NULL,
    started_at TIMESTAMP NOT NULL,
    resolved_at TIMESTAMP NULL,
    duration_minutes INT UNSIGNED,
    fault_type VARCHAR(255),
    root_cause TEXT,
    delay_reason TEXT,
    resolution_team VARCHAR(255),
    journey_started_at TIMESTAMP NULL,
    island_arrival_at TIMESTAMP NULL,
    work_started_at TIMESTAMP NULL,
    work_completed_at TIMESTAMP NULL,
    travel_time INT UNSIGNED,
    work_time INT UNSIGNED,
    pir_rca_no VARCHAR(255),
    status ENUM('Open', 'In Progress', 'Monitoring', 'Closed') NOT NULL,
    severity ENUM('Critical', 'High', 'Medium', 'Low') NOT NULL,
    sla_minutes INT UNSIGNED NOT NULL,
    exceeded_sla BOOLEAN DEFAULT FALSE,
    sla_status VARCHAR(50),
    rca_required BOOLEAN DEFAULT FALSE,
    rca_file_path VARCHAR(255),
    rca_received_at TIMESTAMP NULL,
    corrective_actions TEXT,
    workaround TEXT,
    solution TEXT,
    recommendation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_incident_code (incident_code),
    INDEX idx_started_at (started_at),
    INDEX idx_status (status),
    INDEX idx_severity (severity),
    INDEX idx_sla_breach (exceeded_sla, started_at),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (outage_category_id) REFERENCES outage_categories(id),
    FOREIGN KEY (fault_type_id) REFERENCES fault_types(id),
    FOREIGN KEY (resolution_team_id) REFERENCES resolution_teams(id)
);
```

**users**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role (role)
);
```

**incident_logs**
```sql
CREATE TABLE incident_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    incident_id BIGINT UNSIGNED NOT NULL,
    occurred_at TIMESTAMP NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    INDEX idx_incident_occurred (incident_id, occurred_at)
);
```

**action_points**
```sql
CREATE TABLE action_points (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    incident_id BIGINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    due_date DATE NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    INDEX idx_incident_due (incident_id, due_date),
    INDEX idx_completed (completed)
);
```

#### 7.2.2 Reference Data Tables

**categories**
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name)
);
```

**outage_categories**
```sql
CREATE TABLE outage_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name)
);
```

**fault_types**
```sql
CREATE TABLE fault_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name)
);
```

**resolution_teams**
```sql
CREATE TABLE resolution_teams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name)
);
```

#### 7.2.3 System Tables

**sessions** (if using database sessions)
```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,

    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);
```

**cache** (if using database cache)
```sql
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,

    INDEX idx_expiration (expiration)
);
```

### 7.3 Data Relationships

#### 7.3.1 Entity Relationship Diagram
```
users (1) ─────── (∞) sessions

incidents (1) ───── (∞) incident_logs
incidents (1) ───── (∞) action_points

categories (1) ───── (∞) incidents
outage_categories (1) ─ (∞) incidents
fault_types (1) ───── (∞) incidents
resolution_teams (1) ─ (∞) incidents
```

#### 7.3.2 Relationship Details

**One-to-Many Relationships:**
- incidents → incident_logs (one incident has many logs)
- incidents → action_points (one incident has many action points)
- categories → incidents (one category used by many incidents)
- outage_categories → incidents
- fault_types → incidents
- resolution_teams → incidents

**Data Integrity Rules:**
- Cascade delete: When incident is deleted, logs and action points are deleted
- Restrict delete: Cannot delete reference data if used by incidents
- Foreign key constraints: Ensure referential integrity

### 7.4 Data Storage Requirements

#### 7.4.1 Storage Estimates

**Base Storage (First Year):**
- incidents: ~10,000 records = ~50MB
- incident_logs: ~30,000 records = ~100MB
- action_points: ~5,000 records = ~10MB
- users: ~100 records = ~1MB
- Reference data: ~500 records = ~1MB
- **Total Database**: ~200MB

**Growth Projections:**
- Year 1: 200MB
- Year 2: 500MB
- Year 3: 1GB
- Year 5: 2.5GB

**RCA File Storage:**
- Average file size: 2MB
- Files per year: 1,000
- Annual storage: 2GB
- 5-year projection: 10GB

#### 7.4.2 Performance Considerations

**Index Strategy:**
- Primary keys: Clustered indexes for optimal performance
- Foreign keys: Indexed for join performance
- Search fields: Composite indexes for common queries
- Date fields: Indexed for temporal queries

**Query Optimization:**
- Use EXPLAIN to analyze query performance
- Optimize N+1 query problems with eager loading
- Implement query result caching for reports
- Monitor slow query log for optimization opportunities

### 7.5 Backup and Recovery

#### 7.5.1 Backup Strategy
- **Full Backup**: Daily complete database backup
- **Incremental Backup**: Hourly transaction log backup
- **File Backup**: Daily backup of RCA files
- **Retention**: 30 days online, 1 year offline

#### 7.5.2 Recovery Procedures
- **Point-in-time Recovery**: Using transaction logs
- **Disaster Recovery**: Complete system restoration
- **Data Verification**: Post-recovery integrity checks
- **Testing**: Monthly backup restoration tests

### 7.6 Data Migration

#### 7.6.1 Migration Scripts
Laravel migration files provide version-controlled database schema changes:
- `2024_01_01_000000_create_incidents_table.php`
- `2025_08_10_013936_create_categories_table.php`
- Additional migration files for schema evolution

#### 7.6.2 Data Seeding
Seeder classes populate initial data:
- `UserSeeder`: Default user accounts
- `ResolutionTeamSeeder`: Initial resolution teams
- `DatabaseSeeder`: Coordinated seeding process

---

## 8. Security Requirements

### 8.1 Security Overview

The Incident Management System implements comprehensive security measures following industry best practices and security frameworks including OWASP guidelines and Laravel security features.

### 8.2 Authentication Security

#### 8.2.1 User Authentication
- **Password Requirements**: Minimum 8 characters with complexity rules
- **Password Hashing**: Bcrypt algorithm with cost factor 12
- **Session Management**: Secure session handling with HTTP-only cookies
- **Login Attempts**: Rate limiting to prevent brute force attacks
- **Password Reset**: Secure token-based password recovery

#### 8.2.2 Session Security
- **Session Timeout**: 2 hours of inactivity
- **Session Regeneration**: New session ID after authentication
- **Secure Cookies**: HTTP-only and Secure flags enabled
- **Cross-Domain Protection**: SameSite cookie attribute
- **Session Storage**: Database or encrypted file storage

#### 8.2.3 Multi-Factor Authentication (Future Enhancement)
- **TOTP Support**: Time-based one-time password integration
- **Recovery Codes**: Backup authentication codes
- **Device Trust**: Remember trusted devices
- **Admin Enforcement**: Mandatory 2FA for admin accounts

### 8.3 Authorization Security

#### 8.3.1 Role-Based Access Control (RBAC)
- **Principle of Least Privilege**: Users get minimum required permissions
- **Role Hierarchy**: Admin > Editor > Viewer
- **Permission Enforcement**: Both frontend and backend validation
- **Admin Functions**: Restricted to admin role exclusively

#### 8.3.2 Access Control Matrix
| Resource | Admin | Editor | Viewer |
|----------|-------|--------|--------|
| View Incidents | ✅ | ✅ | ✅ |
| Create Incidents | ✅ | ✅ | ❌ |
| Edit Incidents | ✅ | ✅ | ❌ |
| Delete Incidents | ✅ | ❌ | ❌ |
| Export Data | ✅ | ✅ | ❌ |
| View Reports | ✅ | ✅ | ✅ |
| System Admin | ✅ | ❌ | ❌ |

#### 8.3.3 URL-Level Security
- **Route Protection**: Middleware guards all protected routes
- **Parameter Validation**: Verify user access to specific resources
- **Direct Access Prevention**: Block unauthorized direct URL access
- **API Endpoint Security**: All API calls require authentication

### 8.4 Data Protection

#### 8.4.1 Input Validation and Sanitization
- **Server-Side Validation**: All inputs validated on server
- **Data Type Validation**: Strict data type enforcement
- **Length Restrictions**: Maximum field length validation
- **Special Character Handling**: Escape or reject dangerous characters
- **Business Rule Validation**: Complex business logic validation

#### 8.4.2 SQL Injection Prevention
- **Parameterized Queries**: Eloquent ORM prevents SQL injection
- **Input Sanitization**: Clean all user inputs
- **Query Builder**: Use Laravel's query builder exclusively
- **Raw Query Restrictions**: Minimize and secure raw SQL queries

#### 8.4.3 Cross-Site Scripting (XSS) Prevention
- **Output Encoding**: HTML encode all user-generated content
- **Content Security Policy**: Restrict script execution sources
- **Template Engine**: Blade templates auto-escape output
- **JavaScript Validation**: Client-side XSS prevention

#### 8.4.4 Cross-Site Request Forgery (CSRF) Prevention
- **CSRF Tokens**: All forms include CSRF protection
- **Token Validation**: Server validates all form submissions
- **AJAX Protection**: CSRF tokens in AJAX requests
- **Token Refresh**: Regular token regeneration

### 8.5 File Upload Security

#### 8.5.1 File Type Validation
- **Extension Whitelist**: Only PDF, DOC, DOCX files allowed
- **MIME Type Validation**: Verify actual file content type
- **File Size Limits**: Maximum 10MB per file
- **Content Scanning**: Basic malware detection

#### 8.5.2 File Storage Security
- **Secure Directory**: Files stored outside web root
- **Access Control**: Direct file access requires authentication
- **File Naming**: Randomized filenames prevent enumeration
- **Virus Scanning**: Integration with antivirus scanning (future)

### 8.6 Communication Security

#### 8.6.1 HTTPS Enforcement
- **SSL/TLS Required**: All communications encrypted
- **Certificate Validation**: Valid SSL certificates required
- **HTTP Redirect**: Automatic redirect from HTTP to HTTPS
- **HSTS Headers**: HTTP Strict Transport Security enabled

#### 8.6.2 Security Headers
```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Content-Security-Policy: default-src 'self'
```

#### 8.6.3 API Security
- **Authentication Required**: All API endpoints require authentication
- **Rate Limiting**: Prevent API abuse and DoS attacks
- **Input Validation**: Strict validation of API inputs
- **Error Handling**: Secure error messages without information disclosure

### 8.7 Database Security

#### 8.7.1 Database Access Control
- **Dedicated User**: Application-specific database user
- **Minimum Privileges**: Database user has only required permissions
- **Connection Security**: Encrypted database connections
- **Network Security**: Database server network isolation

#### 8.7.2 Data Encryption
- **Sensitive Data**: Encrypt sensitive data at rest
- **Connection Encryption**: TLS encryption for database connections
- **Backup Encryption**: Encrypted database backups
- **Key Management**: Secure encryption key storage

### 8.8 Logging and Monitoring

#### 8.8.1 Security Logging
- **Authentication Events**: Log all login attempts
- **Authorization Failures**: Log unauthorized access attempts
- **Data Modifications**: Log all data changes
- **File Operations**: Log file uploads and downloads
- **Error Tracking**: Log security-related errors

#### 8.8.2 Log Security
- **Log Integrity**: Protect logs from modification
- **Retention Policy**: Secure log retention and disposal
- **Access Control**: Restrict access to security logs
- **Monitoring**: Real-time security event monitoring

### 8.9 Incident Response

#### 8.9.1 Security Incident Procedures
- **Detection**: Automated security event detection
- **Response Team**: Designated security response team
- **Escalation**: Clear escalation procedures
- **Documentation**: Complete incident documentation

#### 8.9.2 Breach Response
- **Containment**: Immediate threat containment procedures
- **Assessment**: Security breach impact assessment
- **Notification**: User and authority notification procedures
- **Recovery**: System recovery and hardening procedures

### 8.10 Compliance and Standards

#### 8.10.1 Security Standards
- **OWASP Top 10**: Protection against top web vulnerabilities
- **ISO 27001**: Information security management principles
- **NIST Framework**: Cybersecurity framework alignment
- **Laravel Security**: Framework security best practices

#### 8.10.2 Regular Security Activities
- **Security Reviews**: Quarterly security assessments
- **Vulnerability Scanning**: Regular automated vulnerability scans
- **Penetration Testing**: Annual penetration testing
- **Security Training**: Regular staff security training

### 8.11 Environment-Specific Security

#### 8.11.1 Development Environment
- **Dummy Data**: No production data in development
- **Debug Mode**: Debug information disabled in production
- **Environment Isolation**: Separate development/production environments
- **Access Control**: Limited development environment access

#### 8.11.2 Production Environment
- **Hardened Configuration**: Production-optimized security settings
- **Monitoring**: Comprehensive security monitoring
- **Backup Security**: Encrypted and secure backups
- **Change Control**: Controlled production deployments

---

## 9. Appendices

### 9.1 Glossary

| Term | Definition |
|------|------------|
| **Action Point** | A specific task or action item associated with a Critical incident that must be completed before the incident can be closed |
| **Breach** | When an incident's duration exceeds the defined SLA target time |
| **Business Logic** | Rules and processes that define how the system operates according to organizational requirements |
| **CRUD** | Create, Read, Update, Delete - the four basic operations for data management |
| **CSV** | Comma-Separated Values - a file format for data exchange |
| **Duration** | The time between incident start and resolution, measured in minutes |
| **Eloquent** | Laravel's Object-Relational Mapping (ORM) system for database operations |
| **Incident Code** | Auto-generated unique identifier in format INC-YYYYMMDD-XXXX |
| **KPI** | Key Performance Indicator - metrics used to measure system performance |
| **Laravel** | PHP web application framework used to build the system |
| **Middleware** | Software components that handle requests and responses in the application pipeline |
| **Migration** | Database version control system that manages schema changes |
| **PIR** | Post Incident Review - documentation and analysis after incident closure |
| **RCA** | Root Cause Analysis - document explaining the underlying cause of an incident |
| **RBAC** | Role-Based Access Control - security model for managing user permissions |
| **Seeder** | Database population script for creating initial or test data |
| **SLA** | Service Level Agreement - target resolution time based on incident severity |
| **Tailwind CSS** | Utility-first CSS framework used for styling |

### 9.2 Acronyms and Abbreviations

| Acronym | Full Form |
|---------|-----------|
| **API** | Application Programming Interface |
| **CSS** | Cascading Style Sheets |
| **CSRF** | Cross-Site Request Forgery |
| **DOM** | Document Object Model |
| **GDPR** | General Data Protection Regulation |
| **HTML** | HyperText Markup Language |
| **HTTP** | HyperText Transfer Protocol |
| **HTTPS** | HyperText Transfer Protocol Secure |
| **JSON** | JavaScript Object Notation |
| **MVC** | Model-View-Controller |
| **ORM** | Object-Relational Mapping |
| **PDF** | Portable Document Format |
| **PHP** | PHP: Hypertext Preprocessor |
| **REST** | Representational State Transfer |
| **SQL** | Structured Query Language |
| **SSL** | Secure Sockets Layer |
| **TLS** | Transport Layer Security |
| **UI** | User Interface |
| **URL** | Uniform Resource Locator |
| **UX** | User Experience |
| **WCAG** | Web Content Accessibility Guidelines |
| **XSS** | Cross-Site Scripting |

### 9.3 Technical Specifications

#### 9.3.1 Server Requirements
```
Minimum Requirements:
- CPU: 2 cores, 2.4 GHz
- RAM: 4 GB
- Storage: 20 GB SSD
- Network: 100 Mbps

Recommended Requirements:
- CPU: 4 cores, 3.0 GHz
- RAM: 8 GB
- Storage: 50 GB SSD
- Network: 1 Gbps
```

#### 9.3.2 Software Dependencies
```
PHP Extensions Required:
- bcmath
- ctype
- curl
- dom
- fileinfo
- json
- mbstring
- openssl
- pcre
- pdo
- tokenizer
- xml
- zip

Node.js Packages:
- tailwindcss: ^4.0.0
- vite: ^7.0.4
- axios: ^1.11.0
- laravel-vite-plugin: ^2.0.0

Composer Packages:
- laravel/framework: ^12.0
- laravel/tinker: ^2.10.1
- phpoffice/phpword: ^1.4
```

### 9.4 Configuration Examples

#### 9.4.1 Environment Configuration (.env)
```bash
# Application
APP_NAME="Incident Management System"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Indian/Maldives
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=incident_management
DB_USERNAME=incident_user
DB_PASSWORD=secure_password

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file

# File Storage
FILESYSTEM_DISK=public

# Mail (Optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

#### 9.4.2 Web Server Configuration (Nginx)
```nginx
server {
    listen 443 ssl http2;
    server_name incident.yourdomain.com;
    root /var/www/incident-management/public;
    index index.php;

    ssl_certificate /path/to/certificate.pem;
    ssl_certificate_key /path/to/private.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 9.5 API Reference

#### 9.5.1 Internal API Endpoints
```
GET /api/incidents/export-preview
POST /api/incidents/{id}/generate-rca
GET /api/reports/chart-data
POST /api/incidents/{id}/close
```

#### 9.5.2 Route Definitions
```php
// Public routes (authenticated)
Route::middleware(['auth', 'role:viewer'])->group(function () {
    Route::get('incidents', [IncidentController::class, 'index']);
    Route::get('incidents/{incident}', [IncidentController::class, 'show']);
    Route::get('reports', [ReportsController::class, 'index']);
});

// Editor routes
Route::middleware(['auth', 'role:editor'])->group(function () {
    Route::post('incidents', [IncidentController::class, 'store']);
    Route::put('incidents/{incident}', [IncidentController::class, 'update']);
    Route::get('incidents-export', [IncidentController::class, 'export']);
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('incidents/{incident}', [IncidentController::class, 'destroy']);
});
```

### 9.6 Testing Checklist

#### 9.6.1 Functional Testing
- [ ] User authentication and authorization
- [ ] Incident CRUD operations
- [ ] SLA calculation accuracy
- [ ] Business logic validation
- [ ] File upload/download functionality
- [ ] Search and filtering
- [ ] Data export/import
- [ ] Reports and analytics

#### 9.6.2 Non-Functional Testing
- [ ] Performance under load
- [ ] Security vulnerability assessment
- [ ] Browser compatibility
- [ ] Mobile responsiveness
- [ ] Accessibility compliance
- [ ] Data integrity verification

### 9.7 Deployment Checklist

#### 9.7.1 Pre-Deployment
- [ ] Environment configuration verified
- [ ] Database migrations executed
- [ ] Default users created
- [ ] SSL certificate installed
- [ ] File permissions set correctly
- [ ] Backup procedures tested

#### 9.7.2 Post-Deployment
- [ ] Application accessibility verified
- [ ] User authentication tested
- [ ] Core functionality validated
- [ ] Performance monitoring enabled
- [ ] Security scans completed
- [ ] Documentation updated

### 9.8 Maintenance Schedule

#### 9.8.1 Regular Maintenance
- **Daily**: System health checks, backup verification
- **Weekly**: Security log review, performance monitoring
- **Monthly**: Dependency updates, security patches
- **Quarterly**: Full security assessment, performance optimization
- **Annually**: Comprehensive system audit, disaster recovery testing

#### 9.8.2 Monitoring Metrics
- Application uptime and response times
- Database performance and storage usage
- Security event frequency and types
- User activity and system usage patterns
- Error rates and system exceptions

### 9.9 Support Documentation

#### 9.9.1 User Documentation
- User manual for each role type
- Quick start guides
- Feature tutorials
- Troubleshooting guides
- FAQ documents

#### 9.9.2 Technical Documentation
- System architecture diagrams
- Database schema documentation
- API reference guides
- Deployment procedures
- Maintenance procedures

### 9.10 Change Management

#### 9.10.1 Version Control
- All code changes tracked in Git repository
- Feature branches for new development
- Code review process for all changes
- Tagged releases for deployment versions

#### 9.10.2 Release Process
1. Development and testing in feature branches
2. Code review and approval process
3. Staging environment deployment and testing
4. Production deployment during maintenance window
5. Post-deployment verification and monitoring

---

**Document History:**

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2025-09-19 | System Architect | Initial SRS document creation |

**Approval:**

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Project Manager | | | |
| Technical Lead | | | |
| Quality Assurance | | | |
| Stakeholder | | | |

---

*This Software Requirements Specification document serves as the comprehensive guide for the development, testing, deployment, and maintenance of the Incident Management System. All stakeholders should refer to this document for understanding system capabilities, limitations, and requirements.*