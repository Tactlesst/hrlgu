# HR Leave Management System

A comprehensive web-based Human Resources Leave Management System built with PHP and MySQL.

## Features

- **Employee Management**: Add, edit, archive, and restore employee records
- **Leave Management**: Multiple leave types with customizable balances
- **Leave Applications**: Submit, approve, and reject leave requests
- **Travel Orders**: Manage travel applications and allowances
- **Department & Position Management**: Organize employees by departments and positions
- **Document Management**: Upload and manage employee documents
- **Dashboard Analytics**: View leave statistics and employee data
- **Role-Based Access**: Admin, Employee, and Super Admin roles
- **Leave Credit Accrual**: Automatic leave balance updates

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (with mod_rewrite)

## Requirements

### Development (XAMPP)
- XAMPP 7.4+ or similar (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, Edge)

### Production (Hostinger)
- Business Web Hosting or higher
- PHP 7.4+
- MySQL database
- SSL certificate (recommended)

## Installation

### Local Development (XAMPP)

1. **Clone or download** this repository to `C:\xampp\htdocs\`

2. **Import Database**:
   - Start XAMPP (Apache + MySQL)
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create new database named `HRMS`
   - Import the `database` file

3. **Configure Database**:
   - Copy `config.example.php` to `config.php`
   - Update development settings if needed (default works with XAMPP)

4. **Access Application**:
   - Open browser: `http://localhost/HR-Leave-Management-System/Pages/Login.php`
   - Default login:
     - Username: `admin`
     - Password: `admin1234`

### Production Deployment (Hostinger)

See **[HOSTINGER_DEPLOYMENT_GUIDE.md](HOSTINGER_DEPLOYMENT_GUIDE.md)** for detailed instructions.

**Quick Steps**:
1. Create MySQL database in hPanel
2. Import `database` file via phpMyAdmin
3. Update `config.php` with Hostinger credentials
4. Upload files to `public_html`
5. Set folder permissions (755)
6. Enable SSL certificate

## Default Credentials

⚠️ **IMPORTANT**: Change these immediately after first login!

**Admin Accounts**:
- Username: `admin` / Password: `admin1234`
- Username: `hroffice1` / Password: `hroffice`
- Username: `hroffice2` / Password: `hroffice`
- Username: `hroffice3` / Password: `hroffice1`
- Username: `hroffice4` / Password: `hroffice`

## Project Structure

```
HR-Leave-Management-System/
├── .htaccess                    # Apache configuration
├── config.php                   # Database configuration (create from example)
├── config.example.php           # Configuration template
├── database                     # SQL database schema
├── HOSTINGER_DEPLOYMENT_GUIDE.md
├── README.md
│
├── CSS/                         # Stylesheets
├── Documents/                   # Uploaded employee documents
├── Pictures/                    # Employee photos and images
├── Scripts/                     # JavaScript files
│
└── Pages/                       # PHP application files
    ├── Login.php               # Entry point
    ├── db_connect.php          # Database connection
    ├── Admin-Dashboard.php     # Admin interface
    ├── Employee-Dashboard.php  # Employee interface
    ├── add_employee.php        # Employee management
    ├── apply_leave.php         # Leave application
    ├── approve_application.php # Leave approval
    └── ... (other modules)
```

## Database Schema

### Main Tables:
- **Admin**: System users and roles
- **Employee**: Employee information and documents
- **Department**: Organizational departments
- **Position**: Job positions
- **LeaveType**: Types of leave (vacation, sick, etc.)
- **LeaveAvailability**: Employee leave balances
- **LeaveApplication**: Leave requests
- **LeaveHistory**: Approved/rejected leave records
- **TravelOrder**: Travel order types
- **TravelApplication**: Travel requests
- **EmployeeDocument**: Document attachments
- **AuditLog**: System activity logs

## Security Features

- Password-protected admin and employee access
- SQL injection prevention (mysqli with prepared statements)
- XSS protection headers
- HTTPS redirect support
- Sensitive file access restrictions
- Environment-based configuration
- Error logging (production mode)

## Usage

### For Administrators:
1. Login with admin credentials
2. Manage departments and positions
3. Add/edit employee records
4. Configure leave types and balances
5. Approve or reject leave applications
6. View dashboard analytics

### For Employees:
1. Login with employee credentials (Email + Password)
2. View leave balances
3. Submit leave applications
4. Track application status
5. Upload required documents

## Maintenance

### Backup Database:
- **XAMPP**: Export via phpMyAdmin
- **Hostinger**: Use hPanel backup tools or phpMyAdmin export

### Update Leave Credits:
- Automatic accrual via `update_leave_credits.php`
- Can be scheduled as cron job on Hostinger

### Monitor Logs:
- Check PHP error logs in hPanel
- Review `AuditLog` table for system activities

## Troubleshooting

### Connection Failed Error:
- Verify database credentials in `config.php`
- Ensure MySQL service is running
- Check database name exists

### 500 Internal Server Error:
- Check `.htaccess` syntax
- Review PHP error logs
- Verify PHP version compatibility

### File Upload Issues:
- Check folder permissions (755)
- Increase `upload_max_filesize` in PHP settings
- Verify disk space available

## Support

For deployment issues:
- Review `HOSTINGER_DEPLOYMENT_GUIDE.md`
- Check Hostinger documentation
- Contact Hostinger support (24/7 live chat)

## License

This project is provided as-is for educational and business purposes.

## Version

**Version**: 1.0  
**Last Updated**: October 2025  
**Compatibility**: PHP 7.4+, MySQL 5.7+, Apache 2.4+

---

**Note**: This system is production-ready for Hostinger deployment. Follow the deployment guide for best practices and security recommendations.
