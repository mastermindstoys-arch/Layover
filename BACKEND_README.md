# Layover Solutions Website - Backend Setup

## Database Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Database Configuration

1. **Update Database Credentials**
   Edit `backend/config.php` and update the database credentials:
   ```php
   define('DB_USER', 'your_mysql_username');
   define('DB_PASS', 'your_mysql_password');
   ```

2. **Run Database Setup**
   Execute the setup script from the project root:
   ```bash
   cd /path/to/your/website
   php backend/setup_database.php
   ```

   This will:
   - Create the `Layover` database
   - Create all required tables
   - Insert initial data

### Alternative Manual Setup

If the automated setup doesn't work, you can manually create the database:

1. Connect to MySQL:
   ```bash
   mysql -u your_username -p
   ```

2. Create database and import schema:
   ```sql
   CREATE DATABASE Layover CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE Layover;
   SOURCE backend/database_schema.sql;
   ```

## Testing the Setup

### Start PHP Development Server
```bash
php -S localhost:8000
```

### Test Form Submissions
1. Open `http://localhost:8000` in your browser
2. Fill out the "Get Started" modal form and submit
3. Fill out the contact form and submit
4. Check that data is stored in the database

### Verify Database Content
```bash
mysql -u your_username -p Layover -e "SELECT * FROM get_started_requests;"
mysql -u your_username -p Layover -e "SELECT * FROM contact_submissions;"
```

## Database Schema

The database includes the following tables:

### Core Tables
- `customers` - Customer information
- `projects` - Project details
- `services` - Available services
- `get_started_requests` - Get started form submissions
- `contact_submissions` - Contact form submissions
- `project_status` - Project status tracking
- `activity_log` - System activity logging

### Supporting Tables
- `service_categories` - Service categorization
- `contact_types` - Contact form types
- `priority_levels` - Priority definitions
- `status_definitions` - Status definitions

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Ensure MySQL is running
   - Check credentials in `config.php`
   - Verify user has database creation permissions

2. **Permission Denied**
   - Grant database creation permissions to your MySQL user:
   ```sql
   GRANT ALL PRIVILEGES ON *.* TO 'your_user'@'localhost' WITH GRANT OPTION;
   FLUSH PRIVILEGES;
   ```

3. **PHP Errors**
   - Ensure PHP PDO MySQL extension is installed
   - Check PHP error logs

### Form Testing Without Database

If database setup is problematic, the forms will show appropriate error messages and log issues for debugging.