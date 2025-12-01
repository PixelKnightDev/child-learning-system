# Child Learning and Progress Management System - Setup Instructions

## Overview
This is a web-based Child Learning and Progress Management System built with PHP and MySQL on XAMPP. It supports multiple role-based dashboards for Admin, Teacher, Student, and Parent users.

## System Requirements
- **Server:** XAMPP (Apache + MySQL + PHP)
- **PHP Version:** 7.4 or higher
- **MySQL Version:** 5.7 or higher
- **Browser:** Any modern browser (Chrome, Firefox, Safari, Edge)

## Installation Steps

### Step 1: Download and Extract Files
1. Download all files from this project
2. Extract them to your XAMPP htdocs directory:
   ```
   C:\xampp\htdocs\child-learning-system\
   ```

### Step 2: Create Database

#### Option A: Using phpMyAdmin (Recommended)
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on "SQL" tab
3. Copy the entire contents of `database.sql` file
4. Paste it into the SQL query box
5. Click "Go" to execute

#### Option B: Using Command Line
1. Open Command Prompt and navigate to the project folder
2. Run the following command:
   ```bash
   mysql -u root -p < database.sql
   ```
3. When prompted for password, leave it blank (default XAMPP setup) or enter your password if set

### Step 3: Load Sample Data (Optional)
To populate the database with demo data:

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `child_learning_system` database
3. Click on "SQL" tab
4. Copy the contents of `seed_data.sql` file
5. Paste into the SQL query box
6. Click "Go" to execute

### Step 4: Verify File Structure
Ensure your file structure matches:
```
child-learning-system/
├── config/
│   ├── db.php
│   └── config.php
├── includes/
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│   └── nav.php
├── admin/
│   ├── dashboard.php
│   ├── users_list.php
│   ├── user_create.php
│   ├── user_edit.php
│   ├── user_delete.php
│   ├── classes_list.php
│   ├── class_create.php
│   ├── class_edit.php
│   ├── class_delete.php
│   ├── students_list.php
│   ├── student_create.php
│   ├── student_edit.php
│   └── student_delete.php
├── teacher/
│   ├── dashboard.php
│   ├── activities_list.php
│   ├── activity_create.php
│   ├── activity_edit.php
│   ├── activity_delete.php
│   ├── quiz_questions.php
│   ├── class_progress.php
│   └── student_progress.php
├── student/
│   ├── dashboard.php
│   ├── activities_list.php
│   ├── activity_view.php
│   ├── quiz_attempt.php
│   ├── quiz_result.php
│   └── progress_dashboard.php
├── parent/
│   ├── dashboard.php
│   └── child_progress.php
├── css/
│   └── style.css
├── js/
│   └── script.js
├── uploads/ (auto-created)
├── login.php
├── logout.php
├── profile.php
├── announcements_list.php
├── announcement_create.php
├── announcement_edit.php
├── announcement_delete.php
├── database.sql
├── seed_data.sql
└── SETUP_INSTRUCTIONS.md
```

### Step 5: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** server
3. Start **MySQL** server
4. Wait for both to show green indicators

### Step 6: Access the Application
Open your browser and navigate to:
```
http://localhost/child-learning-system/login.php
```

## Demo Credentials

Use these credentials to log in with sample data:

| Role   | Email                | Password |
|--------|----------------------|----------|
| Admin  | admin@example.com    | password |
| Teacher| teacher@example.com  | password |
| Student| student1@example.com | password |
| Parent | parent@example.com   | password |

## Features by Role

### Admin
- Dashboard with system statistics
- User management (Create, Edit, Delete)
- Class management
- Student enrollment
- View all announcements

### Teacher
- Dashboard showing classes and activities
- Create and manage learning activities (PDF + Quizzes)
- Create and manage MCQ quiz questions
- View student progress by class
- Post announcements

### Student
- Dashboard with completion statistics
- View assigned activities
- Access PDF learning materials
- Attempt MCQ quizzes
- View quiz results and scores
- Track personal progress

### Parent
- Dashboard with child information
- View child's progress and performance
- View all announcements
- Cannot access quiz questions

## Key Modules

### 1. User Management
- Role-based authentication
- User CRUD operations
- Profile management
- Session management with security

### 2. Learning Content & Activity
- PDF-based learning materials
- MCQ quiz creation and management
- Activity assignment to classes/students
- File upload handling

### 3. Progress Tracking & Evaluation
- Student assignment tracking
- Quiz attempt recording
- Score calculation
- Progress statistics

### 4. Communication & Announcements
- Announcement creation and management
- Visible to all roles
- Class-specific announcement tagging (optional)

### 5. Class & Student Management
- Class creation and assignment
- Teacher-class relationships
- Student-parent relationships (one-to-one)
- Class-based activity assignment

## Database Configuration

The application uses the following database credentials (default XAMPP setup):
- **Host:** localhost
- **User:** root
- **Password:** (empty)
- **Database:** child_learning_system

To modify these credentials, edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'child_learning_system');
```

## File Upload Configuration

- **Upload Directory:** `/uploads/`
- **Allowed File Type:** PDF only
- **Maximum File Size:** 5 MB

Files are automatically created and validated on upload. Ensure the `/uploads/` directory has write permissions.

## Important Notes

1. **Security:** In production, always use strong passwords and HTTPS
2. **Backup:** Regularly backup your database
3. **Maintenance:** Keep XAMPP, PHP, and MySQL updated
4. **Error Logs:** Check Apache error logs in `xampp/apache/logs/error.log` for issues

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP Control Panel
- Verify database credentials in `config/db.php`
- Check that `child_learning_system` database exists

### File Upload Errors
- Ensure `/uploads/` directory exists and is writable
- Check PHP upload_max_filesize and post_max_size settings
- Verify file is in PDF format

### Login Issues
- Clear browser cookies and cache
- Ensure user exists in database
- Check email and password are correct

### Missing Pages or 404 Errors
- Verify all PHP files are in correct directories
- Check that Apache is serving the correct root directory
- Ensure file names match exactly (case-sensitive on Linux)

## Additional Configuration

### Session Settings
Edit `config/config.php` to modify session timeout (currently 1 hour):
```php
'lifetime' => 3600, // Change this value (in seconds)
```

### Records Per Page
Edit `config/config.php` to change pagination limit:
```php
define('RECORDS_PER_PAGE', 10);
```

## Support and Maintenance

- Review error logs regularly
- Keep user account information updated
- Archive old quiz attempts periodically
- Monitor database size and performance
- Test backup and restore procedures

## Deployment Notes

This system is currently configured for **local XAMPP deployment only**. For production deployment:

1. Use a production web server (Apache/Nginx)
2. Implement HTTPS/SSL certificates
3. Use environment variables for sensitive data
4. Set up proper database backups
5. Implement advanced security measures
6. Use cPanel or similar for hosting management

## License
This system is provided as-is for educational purposes.

---

**Last Updated:** December 2025
**Version:** 1.0
**Status:** Development Ready
