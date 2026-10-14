# Bangladesh Students Community Contest Platform - Documentation

A lightweight, secure, and premium contest submission website designed for the Bangladeshi student community at **Assam down town University (ADTU)**, India. This website is fully optimized for **Hostinger shared hosting** environments and does not require Node.js, Firebase, or MongoDB.

---

## 🚀 Deployment Instructions for Hostinger

Follow these steps to deploy the platform live on Hostinger shared hosting:

### Step 1: Set up MySQL Database on Hostinger
1. Log in to your **Hostinger hPanel**.
2. Go to **Databases -> MySQL Databases**.
3. Create a new database:
   - **Database Name**: e.g., `u123456789_bsc`
   - **Database User**: e.g., `u123456789_root`
   - **Password**: Create a strong password and write it down.
4. Once created, click on **Enter phpMyAdmin** next to the newly created database.
5. In phpMyAdmin, click on the **Import** tab at the top.
6. Click **Choose File**, select the SQL schema file located at `/database/schema.sql` in the project, and click **Go** to create the tables and seed default settings.

### Step 2: Configure Database Settings
1. Open the file [config.php](file:///config/config.php) located in the `/config/` directory of the project.
2. Edit the following database constants to match the Hostinger credentials you created in Step 1:
   ```php
   define('DB_HOST', 'localhost'); // Hostinger MySQL server is usually 'localhost'
   define('DB_NAME', 'u123456789_bsc'); // Replace with your Hostinger DB name
   define('DB_USER', 'u123456789_root'); // Replace with your Hostinger DB user
   define('DB_PASS', 'YOUR_DATABASE_PASSWORD'); // Replace with your DB password
   ```
3. Modify the environment setting for production:
   ```php
   define('ENVIRONMENT', 'production'); // This disables detailed DB error dumps to visitors
   ```

### Step 3: Upload Files to Hostinger
1. In Hostinger hPanel, go to **Files -> File Manager**.
2. Navigate to the `public_html/` folder (the root web directory).
3. Upload all the files and folders from the project root into `public_html/`:
   - `admin/`
   - `api/`
   - `assets/`
   - `config/`
   - `database/`
   - `includes/`
   - `uploads/`
   - `.htaccess`
   - `index.php`
   - `contest.php`
   - `README.md`
4. **Permissions Check**: Ensure that the `/uploads/` folder is writeable by the server (folder permission `755` or `777` depending on server settings). The secure `.htaccess` inside it will prevent script executions.

### Step 4: Login to Admin Panel
1. Access the administrator dashboard by visiting: `https://yourdomain.com/admin/` (or `https://yourdomain.com/admin/login.php`).
2. Login using the default seeded admin account:
   - **Username**: `admin`
   - **Password**: `Admin@ADTU2026`
3. **CRITICAL SECURITY STEP**: Go to the **Settings** panel inside the dashboard and update the admin credentials (username and password) immediately after logging in.

---

## 🔒 Security Best Practices Implemented

This platform has been built from the ground up prioritizing security:

1. **SQL Injection Protection**:
   - The platform exclusively uses **PHP Data Objects (PDO)** with prepared statements for all database operations. SQL queries are separated from raw inputs.

2. **Cross-Site Scripting (XSS) Mitigation**:
   - All student entries and log messages are passed through `esc()` (`htmlspecialchars()`) before rendering in HTML blocks to block malicious scripting payloads.

3. **CSRF (Cross-Site Request Forgery) Protection**:
   - All forms, AJAX uploads, and administrative actions utilize session-bound CSRF tokens checked on the server.

4. **Deep File Upload Safeguards**:
   - **MIME Check**: Uses PHP `finfo` byte-checking to verify that the file content is genuinely `application/pdf`, ignoring fake extensions.
   - **Signature Check**: Reads the file header bytes to confirm the `%PDF` signature.
   - **Automatic Renaming**: Uploaded files are renamed using cryptographically secure random values: `bin2hex(random_bytes(16)) . '_' . time() . '.pdf'`.
   - **Disabled execution**: The `/uploads/.htaccess` file explicitly disables execution engines (PHP), restricts file listings, and rejects any web requests to files that do not end in `.pdf`.

5. **Spam & DoS Rate Limiting**:
   - The submission API endpoint dynamically query-checks submissions matching the client's IP address. Submissions are capped at **3 uploads per hour per IP**.

6. **Brute Force Login Lock**:
   - Accounts are locked out from an IP address if more than **5 failed login attempts** are logged in a 10-minute window.

7. **Session Security**:
   - Session cookies utilize `HttpOnly`, `SameSite=Lax`, and `Secure` attributes (if running on HTTPS) to block session-jacking. Admin sessions automatically timeout after **30 minutes of idle inactivity**.

---

## 📊 Live Synced Administrator Features
- **AJAX Polling**: High-performance lightweight Fetch polling updates charts (Chart.js) and tables in-place without page redraws.
- **Search & Sort Data**: Admin table allows client-side sorting and searching, dynamically populating filter list options from database records.
- **UTF-8 Excel CSV Exporter**: Exports spreadsheet records prepending the byte-order-mark (BOM) to ensure Bangla fonts display cleanly in Excel.
- **Database Backup**: Pure-PHP backup engine compiles a downloadable standard SQL dump without needing blocked command execution shell functions.
#
