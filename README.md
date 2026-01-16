# Student Placement Attachment System

Final Year Project - Catholic University of Eastern Africa

## Project Structure

```
Student-Placement-Attachment-System/
├── index.php                 # Main entry point
├── config/                   # Configuration files
│   ├── config.php           # Application configuration
│   └── database.php         # Database connection class
├── includes/                 # PHP includes
│   ├── functions.php        # Common functions
│   ├── header.php           # Site header
│   ├── footer.php           # Site footer
│   └── logout.php           # Logout handler
├── pages/                    # Page templates
│   ├── home.php
│   ├── about.php
│   ├── contact.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   └── 404.php
├── api/                      # API endpoints
│   ├── login.php
│   ├── register.php
│   └── contact.php
├── assets/                   # Static assets
│   ├── css/
│   │   └── main.css         # Main stylesheet
│   ├── js/
│   │   └── main.js          # Main JavaScript
│   └── images/              # Image files
├── database/                 # Database scripts
│   └── schema.sql           # Database schema
├── .htaccess                 # Apache configuration
└── .gitignore               # Git ignore file
```

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database:
   ```sql
   CREATE DATABASE student_placement_db;
   ```

2. Import the schema:
   ```bash
   mysql -u root -p student_placement_db < database/schema.sql
   ```

### 2. Configuration

1. Edit `config/config.php` and update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'student_placement_db');
   ```

2. Update `APP_URL` if your project is not in the root directory:
   ```php
   define('APP_URL', 'http://localhost/your-project-path');
   ```

### 3. Web Server Setup

- **Apache**: Ensure mod_rewrite is enabled
- **PHP**: Version 7.4 or higher recommended
- **MySQL**: Version 5.7 or higher

### 4. File Permissions

Ensure the web server has read permissions for all files and write permissions for any upload directories.

## Features

- User authentication (login/register)
- Session management
- CSRF protection
- Flash message system
- Responsive design
- Database abstraction layer
- Clean URL routing
- Security headers

## Development

### Adding New Pages

1. Create a new file in `pages/` directory (e.g., `pages/newpage.php`)
2. Add the page name to `$allowed_pages` array in `index.php`
3. Access via: `index.php?page=newpage`

### Adding API Endpoints

1. Create a new file in `api/` directory
2. Include necessary files and handle the request
3. Return JSON responses

### Styling

- Main stylesheet: `assets/css/main.css`
- Uses CSS variables for theming
- Responsive design with mobile-first approach

### JavaScript

- Main script: `assets/js/main.js`
- Includes form validation, flash message handling, and AJAX helpers

## Security Features

- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- Session security settings
- Secure headers via .htaccess

## TODO

- [ ] Implement actual login/register database logic
- [ ] Add user profile management
- [ ] Implement placement tracking
- [ ] Add report generation
- [ ] Create admin panel
- [ ] Add email functionality

## License

See LICENSE file for details.
