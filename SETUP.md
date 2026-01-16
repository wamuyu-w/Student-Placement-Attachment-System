# Setup Instructions

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Apache/Nginx web server (or PHP built-in server for development)

## Database Setup

1. **Create the database:**
   - Open phpMyAdmin or MySQL command line
   - Import the `database_schema.sql` file, or run:
   ```sql
   mysql -u root -p < database_schema.sql
   ```

2. **Configure database connection:**
   - Open `config.php`
   - Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'cuea_placement_system');
   ```

## Test Accounts

The database schema includes sample accounts for testing:

### Student Login
- **Username:** student1
- **Password:** password123

### Staff Login
- **Username:** staff1
- **Password:** password123

### Host Organization Login
- **Username:** org1
- **Password:** password123

## Running the Application

### Using PHP Built-in Server (Development)
```bash
php -S localhost:8000
```
Then navigate to `http://localhost:8000` in your browser.

### Using Apache/Nginx
1. Place the project in your web server's document root (e.g., `htdocs`, `www`, or `/var/www/html`)
2. Ensure Apache/Nginx is configured to process PHP files
3. Access via your configured domain or localhost

## File Structure

```
Student-Placement-Attachment-System/
├── assets/                    # Images and static assets
├── Dashboards/               # Dashboard pages for each user type
│   ├── student-dashboard.php
│   ├── staff-dashboard.php
│   └── host-org-dashboard.php
├── Login Pages/              # Login pages and handlers
│   ├── student-login.html
│   ├── staff-login.html
│   ├── host-organization-login.html
│   ├── login.css
│   ├── login.js
│   ├── login-student.php
│   ├── login-staff.php
│   ├── login-host-org.php
│   └── logout.php
├── config.php                # Database configuration
├── database_schema.sql       # Database schema and sample data
├── index.html               # Landing page
└── index.css                # Landing page styles
```

## Password Hashing

The system uses PHP's `password_hash()` and `password_verify()` functions for secure password hashing (bcrypt).

### If you have existing plain text passwords:

1. **Hash all existing passwords:**
   ```bash
   php utils/hash-passwords.php
   ```
   This script will automatically detect and hash any plain text passwords in your database.

2. **Update a single user's password:**
   ```bash
   php utils/update-password.php <table> <username> <new_password>
   ```
   Example:
   ```bash
   php utils/update-password.php students student1 newpassword123
   ```

### Password Hashing Functions

The `config.php` file includes helper functions:
- `hashPassword($password)` - Hashes a password using bcrypt
- `verifyPassword($password, $hash)` - Verifies a password against a hash

All login handlers automatically use `password_verify()` to check credentials.

## Security Notes

1. **Change default passwords** in production
2. **Update database credentials** in `config.php`
3. **Use HTTPS** in production
4. **Sanitize all user inputs** (already implemented)
5. **Use prepared statements** (already implemented)
6. **Password hashing** (already implemented with bcrypt)
7. **Delete utility scripts** (`utils/hash-passwords.php`) after use in production
8. **Implement CSRF protection** for production use

## Features Implemented

- ✅ Client-side form validation (JavaScript)
- ✅ Server-side authentication (PHP)
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ Database integration
- ✅ User-specific dashboards
- ✅ Secure logout functionality

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database exists

### Login Not Working
- Check browser console for JavaScript errors
- Verify PHP error logs
- Ensure sessions are enabled in PHP

### Page Not Found
- Verify file paths are correct
- Check web server configuration
- Ensure `.htaccess` allows PHP execution (if using Apache)
