# OCPB - Online Cheque Processing Book

A modern web-based application for managing cheque processing operations with a clean, corporate interface.

## Features

- **Cheque Management**: Create, edit, print, and cancel cheques
- **Account Management**: Manage bank accounts with balance tracking
- **Multi-Company Support**: Handle cheques for multiple companies
- **Adjustment System**: Make positive/negative adjustments to account balances
- **Reporting**: Generate PDF and Excel reports
- **User Authentication**: Secure login system with session management
- **Modern UI**: Clean, corporate-looking interface with responsive design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/LAMP (for local development)

## Installation

### 1. Database Setup

1. Create a MySQL database (e.g., `ocpb`)
2. Import the database schema and data:
   ```sql
   mysql -u root -p ocpb < db.sql
   ```
   Or use phpMyAdmin to import `db.sql`

### 2. Configuration

1. Open `config.php` and update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ocpb'); // Your database name
   define('DB_USER', 'root');  // Your MySQL username
   define('DB_PASS', '');      // Your MySQL password
   ```

2. Update `BASE_URL` if your project is in a subdirectory:
   ```php
   define('BASE_URL', '/account'); // Change to your project path
   ```

### 3. Web Server Setup

#### For XAMPP:
1. Copy the project folder to `C:\xampp\htdocs\account` (or your preferred location)
2. Access via: `http://localhost/account/cp/login.php`

#### For Apache:
1. Place project in your web root or configure virtual host
2. Ensure mod_rewrite is enabled (if using .htaccess)

### 4. File Permissions

Ensure the following directories are writable (if needed):
- `logs/` (if logging is enabled)
- `uploads/` (if file uploads are used)

## Default Login

After importing the database, **check existing users from your db.sql file**:

### Option 1: Use the Check Script (Recommended)
1. Access: `http://localhost/account/cp/check_users.php`
2. This will display all users with their login credentials
3. Use the email/login and password shown to login

### Option 2: Check Database Directly
```sql
SELECT oa_id, oa_login, oa_password, oa_name, oa_department, oa_active 
FROM ocps_admin 
WHERE oa_active = 1;
```

### Option 3: Create New User (if none exist)
```sql
INSERT INTO ocps_admin (oa_login, oa_password, oa_name, oa_department, oa_active) 
VALUES ('admin@example.com', 'admin123', 'Administrator', 'IT', 1);
```

**Note**: 
- The system supports both hashed and plain text passwords (for migration)
- Plain text passwords are automatically hashed on first login
- Use the exact credentials from your `db.sql` file
- See `README_LOGIN.md` for detailed login instructions

## Project Structure

```
account/
├── config.php              # Configuration file
├── DataAccess.php          # Database access layer
├── session.php             # Session management
├── index.php               # Root redirect
├── db.sql                  # Database schema and data
├── SYSTEM_DOCUMENTATION.md # Complete system documentation
│
├── class/                  # Model classes
│   ├── admin/
│   ├── cheque/
│   ├── account/
│   ├── company/
│   ├── bank/
│   ├── beneficiary/
│   ├── signatory/
│   ├── adjustment/
│   ├── chequebook/
│   └── common/
│
├── cp/                     # Control Panel (Main Application)
│   ├── login.php
│   ├── index.php          # Dashboard
│   ├── new_cheque.php
│   ├── pending_cheques.php
│   ├── operation_cheque.php
│   └── ... (other pages)
│
├── css/                    # Stylesheets
│   ├── style.css          # Main stylesheet
│   ├── login.css          # Login page styles
│   └── navi.css           # Navigation styles
│
└── js/                     # JavaScript files
    └── jquery-1.7.2.min.js
```

## Usage

### Creating a Cheque

1. Login to the system
2. Navigate to "New Cheque"
3. Fill in all required fields:
   - Select Company
   - Select Bank
   - Select Account Number
   - Enter Cheque Number
   - Enter Beneficiary
   - Enter Amount and Date
   - Select Signatory
4. Click "Save Cheque"

### Printing a Cheque

1. Go to "Pending Cheques"
2. Click "Print" on the desired cheque
3. The cheque will be marked as printed and a print view will open

### Managing Accounts

1. Navigate to "Bank Accounts" to view all accounts
2. Click "New Account" to create a new bank account
3. View balance and transaction details for each account

### Generating Reports

1. Go to "Reports"
2. Select report type (PDF or Excel)
3. Choose filters (status, date range)
4. Click "Generate Report"

## Security Features

- **SQL Injection Prevention**: All queries use prepared statements
- **Password Hashing**: Uses PHP's `password_hash()` function
- **XSS Protection**: Input sanitization and output escaping
- **Session Security**: HTTPOnly cookies, session timeout, regeneration
- **Input Validation**: Comprehensive validation on all inputs

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Database Connection Error
- Check database credentials in `config.php`
- Ensure MySQL service is running
- Verify database exists and user has permissions

### Session Issues
- Check PHP session configuration
- Ensure `session.php` is included in all protected pages
- Clear browser cookies if needed

### Page Not Found
- Verify `BASE_URL` in `config.php` matches your project path
- Check Apache/Nginx configuration
- Ensure `.htaccess` is working (if used)

## Development

### Adding New Features

1. Create model class in `class/` directory
2. Create view page in `cp/` directory
3. Add operation handler if needed
4. Update navigation in `cp/header.php`

### Code Style

- Follow PSR-12 coding standards
- Use prepared statements for all database queries
- Validate and sanitize all user inputs
- Use meaningful variable and function names

## License

This project is proprietary software. All rights reserved.

## Support

For issues or questions, refer to `SYSTEM_DOCUMENTATION.md` for detailed system documentation.

---

**Version**: 2.0  
**Last Updated**: 2026
