# Installation Guide - Belva Bookstore

## Prerequisites

Before installing the Belva Bookstore application, make sure you have the following:

### System Requirements
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB equivalent)
- **Web Server**: Apache, Nginx, or built-in PHP server
- **Browser**: Modern web browser (Chrome, Firefox, Safari, Edge)

### Recommended Development Environment
- **XAMPP**: All-in-one solution for Windows/Mac/Linux
- **WAMP**: For Windows users
- **LAMP**: For Linux users
- **MAMP**: For Mac users

## Installation Steps

### 1. Download and Setup Web Server

#### Option A: Using XAMPP (Recommended)
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP on your system
3. Start Apache and MySQL services from XAMPP Control Panel

#### Option B: Using Other Web Servers
- Ensure PHP and MySQL are properly installed and configured
- Make sure PHP PDO extension is enabled

### 2. Project Setup

1. **Clone/Download the project**:
   ```bash
   # If using Git
   git clone <repository-url> C:/xampp/htdocs/belva/phpweb
   
   # Or extract downloaded ZIP to
   C:/xampp/htdocs/belva/phpweb
   ```

2. **Verify project structure**:
   ```
   phpweb/
   ├── src/           # Source code
   ├── docs/          # Documentation
   ├── sql/           # Database script
   └── .git/          # Git repository
   ```

### 3. Database Setup

1. **Start MySQL service**:
   - In XAMPP: Start MySQL from Control Panel
   - Or start MySQL service manually

2. **Create database**:
   
   **Option A: Using phpMyAdmin** (Recommended)
   - Open browser and go to `http://localhost/phpmyadmin`
   - Click "Import" tab
   - Choose file: `sql/bookstore_db.sql`
   - Click "Go" to import

   **Option B: Using MySQL Command Line**
   ```bash
   mysql -u root -p < sql/bookstore_db.sql
   ```

   **Option C: Manual Setup**
   - Create new database named `bookstore_db`
   - Copy and paste SQL content from `sql/bookstore_db.sql`
   - Execute the SQL commands

### 4. Configuration

1. **Database Configuration**:
   - Open `src/config/database.php`
   - Verify/update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'bookstore_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Default for XAMPP
   ```

2. **File Permissions** (Linux/Mac only):
   ```bash
   chmod -R 755 phpweb/
   ```

### 5. Testing Installation

1. **Access the application**:
   - Open browser and navigate to: `http://localhost/belva/phpweb/src/`

2. **Test login credentials**:
   
   **Admin Account**:
   - Email: `admin@bookstore.com`
   - Password: `password`

   **User Account**:
   - Email: `user@bookstore.com`
   - Password: `password`

### 6. Verify Installation

✅ **Checklist**:
- [ ] Database `bookstore_db` created successfully
- [ ] All tables imported (8 tables total)
- [ ] Sample data loaded
- [ ] Web server running
- [ ] Application accessible via browser
- [ ] Login works for both admin and user accounts
- [ ] No PHP errors displayed

## Troubleshooting

### Common Issues and Solutions

#### 1. Database Connection Error
**Error**: "Connection failed: SQLSTATE[HY000] [1049] Unknown database"

**Solution**:
- Verify database name in `src/config/database.php`
- Ensure database `bookstore_db` exists
- Check MySQL service is running

#### 2. Access Denied for User 'root'
**Error**: "Access denied for user 'root'@'localhost'"

**Solution**:
- Update password in `src/config/database.php`
- For XAMPP: default password is usually empty ('')
- For other setups: use your MySQL root password

#### 3. Page Not Found (404)
**Solution**:
- Verify project is in correct directory: `htdocs/belva/phpweb/`
- Check web server is running
- Ensure URL includes `/src/` at the end

#### 4. PHP Errors
**Common fixes**:
- Check PHP version compatibility (7.4+)
- Ensure PDO extension is enabled
- Verify all required files exist

#### 5. Import SQL File Issues
**Solutions**:
- Check file encoding (should be UTF-8)
- Try importing sections manually
- Increase MySQL timeout settings if file is large

### Getting Help

If you encounter issues not covered here:

1. **Check Error Logs**:
   - XAMPP logs: `xampp/apache/logs/error.log`
   - PHP errors: Enable `display_errors` in PHP settings

2. **Verify Environment**:
   - PHP version: `php -v`
   - MySQL version: Check in phpMyAdmin

3. **Database Issues**:
   - Use phpMyAdmin to inspect tables
   - Verify data import was successful

## Security Notes

**Important**: This installation is for development purposes only.

For production deployment:
- Change default passwords
- Update database credentials
- Enable proper error handling
- Configure secure session settings
- Use HTTPS
- Implement proper backup procedures

## Next Steps

After successful installation:

1. Read the [Usage Guide](USAGE.md) to learn how to use the application
2. Explore the admin panel features
3. Test user functionality
4. Customize the application as needed

## File Structure Reference

```
phpweb/
├── src/
│   ├── index.php              # Main landing page
│   ├── config/database.php    # Database configuration
│   ├── admin/                 # Admin panel files
│   │   ├── dashboard.php      # Admin dashboard
│   │   ├── books.php          # Book management
│   │   ├── users.php          # User management
│   │   └── ...
│   ├── user/                  # User interface
│   │   ├── index.php          # User homepage
│   │   ├── all_books.php      # Browse books
│   │   └── ...
│   └── auth/                  # Authentication
│       ├── login.php          # Login page
│       ├── register.php       # Registration
│       └── logout.php         # Logout handler
├── docs/                      # Documentation
├── sql/                       # Database scripts
└── .git/                      # Version control
```
