# Hostinger Deployment Guide
## HR Leave Management System

---

## Pre-Deployment Checklist

### 1. Hostinger Account Requirements
- [ ] **Hosting Plan**: Business Web Hosting or higher
- [ ] **PHP Version**: 7.4 or higher (set in hPanel)
- [ ] **MySQL Database**: Available in your plan
- [ ] **SSL Certificate**: Recommended (free with Hostinger)

---

## Step-by-Step Deployment

### Step 1: Prepare Database on Hostinger

1. **Login to hPanel** (Hostinger Control Panel)
2. **Create MySQL Database**:
   - Go to **Databases** → **MySQL Databases**
   - Click **Create New Database**
   - Note down:
     - Database name (e.g., `u123456789_hrms`)
     - Database username (e.g., `u123456789_hruser`)
     - Database password (create a strong one)
     - Database host (usually `localhost`)

3. **Import Database**:
   - Click **Manage** next to your database
   - Open **phpMyAdmin**
   - Click **Import** tab
   - Upload the `database` file from your project root
   - Click **Go** to execute

### Step 2: Update Configuration File

1. **Edit `config.php`** in your project root:
   ```php
   // Update these lines in the PRODUCTION SETTINGS section:
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'u123456789_hruser'); // Your Hostinger username
   define('DB_PASSWORD', 'your_strong_password'); // Your Hostinger password
   define('DB_NAME', 'u123456789_hrms'); // Your Hostinger database name
   ```

2. **Create `.production` file** (optional):
   - Create an empty file named `.production` in your project root
   - This forces the system to use production settings

### Step 3: Upload Files to Hostinger

**Option A: File Manager (Recommended for beginners)**
1. Login to hPanel
2. Go to **Files** → **File Manager**
3. Navigate to `public_html` folder
4. Delete default files (index.html, etc.)
5. Upload all project files:
   - Upload as ZIP and extract, OR
   - Upload folders one by one

**Option B: FTP (Recommended for advanced users)**
1. Get FTP credentials from hPanel → **Files** → **FTP Accounts**
2. Use FileZilla or similar FTP client
3. Connect to your server
4. Upload all files to `public_html`

### Step 4: Set Permissions

In File Manager or FTP:
1. Set folder permissions to **755**:
   - `Documents/`
   - `Pictures/`
   - `CSS/`
   - `Scripts/`
   - `Pages/`

2. Set file permissions to **644** for:
   - All `.php` files
   - `config.php`
   - `.htaccess`

### Step 5: Configure PHP Settings

1. In hPanel, go to **Advanced** → **PHP Configuration**
2. Set PHP version to **7.4** or **8.0+**
3. Adjust settings:
   - `upload_max_filesize`: 20M
   - `post_max_size`: 20M
   - `max_execution_time`: 300
   - `memory_limit`: 256M

### Step 6: Test Your Application

1. Visit your domain: `https://yourdomain.com`
2. You should be redirected to the login page
3. Test login with default credentials:
   - Username: `admin`
   - Password: `admin1234`

4. **IMPORTANT**: Change default passwords immediately!

### Step 7: Enable HTTPS (SSL)

1. In hPanel, go to **Security** → **SSL**
2. Enable **Free SSL Certificate**
3. Wait 10-15 minutes for activation
4. Edit `.htaccess` and uncomment these lines:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

---

## Post-Deployment Security

### Immediate Actions:
1. **Change all default passwords** in the Admin table
2. **Delete or secure** these files:
   - `database` (delete after import)
   - `final_database.txt` (delete after import)
   - `HOSTINGER_DEPLOYMENT_GUIDE.md` (delete after deployment)

3. **Update Admin Passwords**:
   ```sql
   -- Login to phpMyAdmin and run:
   UPDATE Admin SET Password = 'new_secure_password' WHERE Username = 'admin';
   ```

4. **Backup Database** regularly via hPanel → Backups

---

## Troubleshooting

### Issue: "Connection failed" error
- **Solution**: Double-check database credentials in `config.php`
- Verify database exists in hPanel
- Check if database user has proper privileges

### Issue: 500 Internal Server Error
- **Solution**: Check `.htaccess` file syntax
- Review PHP error logs in hPanel → **Advanced** → **Error Logs**
- Ensure PHP version is compatible

### Issue: Files not uploading
- **Solution**: Check folder permissions (755 for folders, 644 for files)
- Increase `upload_max_filesize` in PHP Configuration
- Check available disk space

### Issue: Page not found / 404 errors
- **Solution**: Verify all files uploaded correctly
- Check `.htaccess` RewriteEngine is enabled
- Ensure `Pages/Login.php` exists

### Issue: Images/CSS not loading
- **Solution**: Check file paths are correct
- Verify folder permissions
- Clear browser cache

---

## File Structure on Hostinger

```
public_html/
├── .htaccess
├── config.php
├── .production (optional)
├── CSS/
├── Documents/
├── Pages/
│   ├── Login.php (entry point)
│   ├── db_connect.php
│   └── ... (other PHP files)
├── Pictures/
└── Scripts/
```

---

## Maintenance

### Regular Tasks:
- **Weekly**: Backup database via hPanel
- **Monthly**: Review error logs
- **Quarterly**: Update PHP version if needed
- **As needed**: Monitor disk space usage

### Database Backup:
1. hPanel → **Databases** → **phpMyAdmin**
2. Select your database
3. Click **Export**
4. Choose **Quick** export method
5. Download SQL file

---

## Support Resources

- **Hostinger Knowledge Base**: https://support.hostinger.com
- **Hostinger Live Chat**: Available 24/7 in hPanel
- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/

---

## Notes

- This application uses **PHP + MySQL** (no frameworks)
- Tested on PHP 7.4 and 8.0
- Compatible with Apache web server
- Requires MySQL 5.7+ or MariaDB 10.2+

---

**Deployment Date**: _____________
**Domain**: _____________
**Database Name**: _____________
**PHP Version**: _____________
