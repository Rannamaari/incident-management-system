# 🧪 Testing Guide - Incident Management System

## 📋 Overview

This comprehensive testing guide covers all aspects of testing the Incident Management System, including manual testing procedures, automated testing setup, and validation criteria.

## 🎯 Testing Objectives

- Verify all CRUD operations function correctly
- Validate business logic and rules enforcement
- Test role-based access controls
- Ensure data integrity and security
- Validate SLA calculations and real-time updates
- Test export/import functionality
- Verify responsive design across devices

## 🏗️ Test Environment Setup

### Prerequisites
```bash
# Ensure testing dependencies are installed
composer install --dev
npm install

# Set up test database
cp .env .env.testing
php artisan config:clear

# Run migrations for test database
php artisan migrate --env=testing

# Seed test data
php artisan db:seed --env=testing
```

### Test Database Configuration (.env.testing)
```bash
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

## 🔧 Automated Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/IncidentTest.php
```

### Test Structure
```
tests/
├── Feature/
│   ├── IncidentTest.php           # CRUD operations
│   ├── AuthenticationTest.php     # Login/logout
│   ├── RoleBasedAccessTest.php    # Permission testing
│   ├── SlaCalculationTest.php     # SLA logic
│   └── ExportImportTest.php       # Data export/import
├── Unit/
│   ├── IncidentModelTest.php      # Model methods
│   ├── ValidationTest.php         # Business rules
│   └── HelperTest.php             # Utility functions
└── Browser/                       # Laravel Dusk tests
    ├── IncidentManagementTest.php
    └── ResponsiveTest.php
```

## 🧪 Manual Testing Procedures

### 1. Authentication & Authorization Testing

#### Test Login Functionality
| Test Case | Steps | Expected Result |
|-----------|-------|-----------------|
| Valid Admin Login | 1. Go to `/login`<br>2. Enter `admin@incident.com` / `admin123` | Redirected to dashboard |
| Valid Editor Login | 1. Go to `/login`<br>2. Enter `editor@incident.com` / `editor123` | Redirected to dashboard |
| Valid Viewer Login | 1. Go to `/login`<br>2. Enter `viewer@incident.com` / `viewer123` | Redirected to dashboard |
| Invalid Credentials | 1. Enter wrong email/password | Error message displayed |
| Empty Fields | 1. Submit empty form | Validation errors shown |

#### Test Role-Based Access
| User Role | Can Create | Can Edit | Can Delete | Can Export |
|-----------|------------|----------|------------|------------|
| Admin | ✅ | ✅ | ✅ | ✅ |
| Editor | ✅ | ✅ | ❌ | ✅ |
| Viewer | ❌ | ❌ | ❌ | ❌ |

### 2. Incident CRUD Operations Testing

#### Create Incident Tests
```
Test Case: Create New Incident
1. Login as Editor/Admin
2. Click "New Incident" button
3. Fill mandatory fields:
   - Summary: "Test incident summary"
   - Affected Services: "Web Application"
   - Severity: "High"
   - Started At: Current date/time
4. Click "Create Incident"
5. Verify: Incident appears in list with auto-generated code (INC-YYYYMMDD-XXXX)
```

#### Read/View Incident Tests
```
Test Case: View Incident List
1. Navigate to incidents page
2. Verify pagination works (15 items per page)
3. Check all columns display correctly
4. Verify SLA status highlighting (red for breached)

Test Case: View Individual Incident
1. Click "View" on any incident
2. Verify all incident details are displayed
3. Check RCA status badge
4. Verify action buttons based on user role
```

#### Update Incident Tests
```
Test Case: Edit Incident
1. Click "Edit" on an incident
2. Modify summary and severity
3. Save changes
4. Verify: Changes are reflected
5. Verify: SLA recalculated based on new severity

Test Case: Close Incident
1. Open incident details
2. Click "Close Incident"
3. Fill required fields based on severity/duration:
   - Duration > 5 hours: Delay reason required
   - Medium+ severity: Travel/work time required
   - High severity: All RCA fields required
   - Critical: Logs and action points required
4. Submit form
5. Verify: Incident status changes to "Closed"
```

#### Delete Incident Tests
```
Test Case: Delete Incident (Admin Only)
1. Login as Admin
2. Click delete button on incident
3. Confirm deletion
4. Verify: Incident removed from database
5. Verify: Associated RCA files deleted
```

### 3. Business Logic Validation Testing

#### SLA Calculation Tests
| Severity | SLA Target | Test Duration | Expected Status |
|----------|------------|---------------|-----------------|
| Critical | 2 hours | 1 hour | Within SLA |
| Critical | 2 hours | 3 hours | SLA Breached |
| High | 2 hours | 1.5 hours | Within SLA |
| Medium | 6 hours | 8 hours | SLA Breached |
| Low | 12 hours | 10 hours | Within SLA |

#### Duration-Based Validation Tests
```
Test Case: Delay Reason Requirement
1. Create incident with duration > 5 hours
2. Try to close without delay reason
3. Verify: Validation error prevents closure
4. Add delay reason and close
5. Verify: Closure successful

Test Case: Travel/Work Time for Medium+ Severity
1. Create Medium severity incident
2. Try to close without travel/work times
3. Verify: Validation prevents closure
4. Add required times and close
5. Verify: Closure successful
```

#### Critical Incident Special Requirements
```
Test Case: Critical Incident Closure
1. Create Critical severity incident
2. Try to close without logs
3. Verify: Error "At least one log entry required"
4. Add log entry, try to close without action points
5. Verify: Error "At least one action point required"
6. Add incomplete action point, try to close
7. Verify: Error "All action points must be completed"
8. Complete all action points and close
9. Verify: Closure successful
```

### 4. Search and Filter Testing

#### Search Functionality
```
Test Cases:
1. Search by incident code: "INC-2025"
2. Search by summary: "database"
3. Search by category: "RAN"
4. Search by affected services: "core"
5. Test partial matches and case insensitivity
```

#### Filter Functionality
```
Test Cases:
1. Filter by Status: Open, In Progress, Monitoring, Closed
2. Filter by Severity: Critical, High, Medium, Low
3. Combine filters (Status + Severity)
4. Clear filters and verify reset
5. Test pagination with filters applied
```

### 5. Export and Import Testing

#### Export Tests
```
Test Case: CSV Export
1. Apply filters (optional)
2. Click "Export Preview"
3. Verify preview shows correct count and sample data
4. Click "Export CSV"
5. Verify: File downloads with correct name format
6. Open file and verify:
   - All columns present
   - Data matches filtered results
   - Dates in Maldives timezone
   - Special characters handled correctly
```

#### Import Tests
```
Test Case: CSV Import (via Command)
1. Prepare test CSV with proper headers
2. Run: php artisan incidents:import test_data.csv
3. Verify: Import progress displayed
4. Check database for imported records
5. Test with invalid data and verify error handling
```

### 6. RCA Management Testing

#### RCA File Upload
```
Test Case: RCA Upload for High Severity
1. Create High severity incident
2. Try to close without RCA
3. Verify: Error prevents closure
4. Upload valid RCA file (PDF/DOC/DOCX)
5. Verify: File uploaded and incident can be closed

Test Case: RCA Download
1. Find incident with RCA file
2. Click "Download RCA"
3. Verify: File downloads correctly
4. Verify: File opens and is not corrupted
```

### 7. Reports and Analytics Testing

#### Reports Dashboard
```
Test Case: Reports Page Access
1. Navigate to Reports page
2. Verify all charts load:
   - Severity distribution
   - Status distribution
   - Monthly trends
   - Daily trends
   - Category breakdown
   - SLA performance
   - Resolution time distribution
3. Test date filtering:
   - Last month
   - Last quarter
   - Custom date range
4. Verify KPI cards update with filters
```

### 8. Responsive Design Testing

#### Device Testing Matrix
| Device Type | Screen Size | Test Focus |
|-------------|-------------|------------|
| Mobile | 375px width | Navigation, forms, tables |
| Tablet | 768px width | Layout adaptation |
| Desktop | 1024px+ | Full functionality |

#### Mobile-Specific Tests
```
Test Cases:
1. Navigation menu collapses correctly
2. Tables scroll horizontally
3. Forms are usable with touch input
4. Buttons are appropriately sized
5. Text is readable without zooming
6. Modal dialogs fit screen
```

### 9. Performance Testing

#### Load Testing
```
Test Scenarios:
1. Page load times under 2 seconds
2. Large dataset pagination (1000+ incidents)
3. CSV export with 10,000+ records
4. Concurrent user access (5+ users)
5. Database query optimization
```

#### Memory and Resource Testing
```
Test Cases:
1. File upload size limits (10MB)
2. Memory usage during large imports
3. Browser performance with large tables
4. Server resource usage monitoring
```

## 🔍 Security Testing

### Authentication Security
```
Test Cases:
1. Session timeout handling
2. CSRF token validation
3. Password strength requirements
4. Brute force protection
5. Session hijacking prevention
```

### Authorization Security
```
Test Cases:
1. Direct URL access attempts
2. Parameter tampering
3. Privilege escalation attempts
4. Data access across user roles
```

### Input Validation
```
Test Cases:
1. SQL injection attempts
2. XSS prevention
3. File upload security
4. Input sanitization
5. Data type validation
```

## 📊 Test Data Management

### Test Data Sets
```
Incident Test Data:
- Critical incidents with complete data
- High severity incidents with RCA files
- Long-duration incidents (>5 hours)
- Recent incidents for SLA testing
- Historical data for reporting

User Test Data:
- Admin users with full permissions
- Editor users with limited permissions
- Viewer users with read-only access
```

### Test Data Creation
```bash
# Create test incidents
php artisan tinker
factory(App\Models\Incident::class, 50)->create();

# Create users with specific roles
User::create([
    'name' => 'Test Admin',
    'email' => 'testadmin@test.com',
    'password' => Hash::make('testpass'),
    'role' => 'admin'
]);
```

## 🚨 Critical Test Scenarios

### High Priority Tests
1. **Data Integrity**: Ensure no data loss during operations
2. **SLA Accuracy**: Verify SLA calculations are always correct
3. **Role Security**: Confirm users can only access authorized features
4. **Business Rules**: Validate all closure requirements are enforced
5. **File Management**: Ensure RCA files are properly handled

### Edge Cases
1. **Timezone Handling**: Test across different timezones
2. **Large Numbers**: Test with very long incident IDs or durations
3. **Special Characters**: Test with Unicode and special characters
4. **Network Issues**: Test behavior during connection problems
5. **Browser Compatibility**: Test across Chrome, Firefox, Safari, Edge

## 📝 Test Reporting

### Test Results Documentation
```
Test Execution Report Template:
- Test Environment Details
- Test Coverage Percentage
- Passed/Failed Test Count
- Critical Issues Found
- Performance Metrics
- Recommendations
```

### Bug Reporting Format
```
Bug Report Template:
- Bug ID
- Test Case Reference
- Steps to Reproduce
- Expected vs Actual Result
- Severity Level
- Environment Details
- Screenshots/Logs
```

## 🔄 Continuous Testing

### Automated Test Integration
```bash
# Pre-commit hooks
composer install --dev
./vendor/bin/phpunit

# CI/CD Pipeline
- Run tests on every commit
- Performance benchmarks
- Security scans
- Code quality checks
```

### Regression Testing
```
Regression Test Suite:
1. Core functionality after updates
2. Business logic validation
3. Security controls verification
4. Performance benchmarks
5. Browser compatibility
```

## 📚 Testing Best Practices

### General Guidelines
1. **Test Early and Often**: Run tests during development
2. **Document Everything**: Keep detailed test logs
3. **Use Real Data**: Test with production-like datasets
4. **Test Edge Cases**: Don't just test happy paths
5. **Automate When Possible**: Reduce manual testing overhead

### Test Environment Management
1. **Isolated Testing**: Use separate test databases
2. **Data Cleanup**: Reset test data between runs
3. **Version Control**: Track test scripts and data
4. **Environment Parity**: Match production as closely as possible

## 🎯 Success Criteria

### Test Completion Criteria
- [ ] All automated tests pass
- [ ] Manual test cases executed successfully
- [ ] Security vulnerabilities addressed
- [ ] Performance benchmarks met
- [ ] Cross-browser compatibility verified
- [ ] Mobile responsiveness confirmed
- [ ] Business logic validation complete
- [ ] Data integrity verified

### Quality Gates
- **Code Coverage**: Minimum 80%
- **Performance**: Page load < 2 seconds
- **Security**: No critical vulnerabilities
- **Accessibility**: WCAG 2.1 AA compliance
- **Browser Support**: Chrome, Firefox, Safari, Edge
- **Mobile Support**: iOS Safari, Chrome Android

---

This comprehensive testing guide ensures thorough validation of the Incident Management System across all functional and non-functional requirements.