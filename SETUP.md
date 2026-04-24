# Event Management System - Complete Setup Guide

## Prerequisites
- **PHP 7.4+** (with MySQLi extension enabled)
- **MySQL 5.7+** or **MariaDB**
- **Apache** with mod_rewrite enabled (or any compatible web server)
- **FPDF library** (included - fpdf.php in the `/pdf/` folder)

---

## Installation Steps

### Step 1: Download & Setup Project Files

1. **Download** the event-management folder
2. **Copy** to your web server:
   - **XAMPP/WAMP**: Copy to `htdocs/` or `www/` folder
   - **Linux/macOS**: `/var/www/html/event-management/`
3. **Start Apache & MySQL** services

### Step 2: Create Database

#### Option A: Using phpMyAdmin (Easiest)
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **"Import"**
3. Click **"Choose File"** and select `sql/event_management_schema.sql`
4. Click **"Go"** to import the database
5. Database `event_management` will be created with all tables and sample data

#### Option B: Using MySQL Command Line
```bash
mysql -u root -p < sql/event_management_schema.sql
```

### Step 3: Configure Database Connection

1. Open `config/database.php`
2. Update these values if needed:
   ```php
   define('DB_HOST', 'localhost');    // Your MySQL host
   define('DB_USER', 'root');         // Your MySQL username
   define('DB_PASS', 'SoH@1234');     // Your MySQL password
   define('DB_NAME', 'event_management');
   define('BASE_URL', '/event-management'); // Adjust folder name if needed
   ```

**Example for different setups:**
- **XAMPP default**: User=`root`, Pass=`` (empty)
- **LAMP with sudo**: User=`root`, Pass=`your_mysql_password`
- **Production**: Use your actual credentials

### Step 4: Set Permissions (Linux/macOS only)

```bash
chmod 755 /path/to/event-management
chmod 755 /path/to/event-management/invoices
chmod 644 /path/to/event-management/config/database.php
```

### Step 5: Verify FPDF Library

- Check that `pdf/fpdf.php` exists (bundled with project)
- The system will use it automatically for invoice generation

### Step 6: Access the Application

Open your browser and navigate to:
```
http://localhost/event-management/
```

---

## Default Login Credentials

### User Account
- **Email**: `john@example.com`
- **Password**: `password123`

### Admin Account
- **Username**: `admin`
- **Password**: `admin123`

---

## Features & How to Use

### For Users
1. **Browse Events**: Visit homepage to see all events
2. **Search**: Use search bar to filter by keyword or category
3. **Register**: Click "Register Now" to sign up for an event
4. **Download Invoice**: After registration, PDF invoice generates automatically
5. **Dashboard**: View all registered events in "My Dashboard"

### For Admin
1. **Login**: Go to `/admin/login.php`
2. **Dashboard**: View statistics (Users, Events, Registrations, Revenue)
3. **Manage Events**: Add, edit, or delete events
4. **View Users**: See all registered users
5. **View Registrations**: Track all registrations and invoices

---

## Project Structure

```
event-management/
├── config/              # Database configuration
│   └── database.php     # DB credentials (EDIT THIS)
├── includes/            # Shared files
│   ├── header.php       # Navigation & header
│   ├── footer.php       # Footer
│   └── auth.php         # Authentication functions
├── admin/               # Admin panel
│   ├── login.php        # Admin login
│   ├── dashboard.php    # Admin dashboard
│   ├── events.php       # Manage events
│   ├── users.php        # View users
│   ├── registrations.php # View registrations
│   └── logout.php       # Admin logout
├── user/                # User pages
│   └── dashboard.php    # User dashboard (my events)
├── pdf/                 # Invoice generation
│   ├── fpdf.php         # FPDF library (included)
│   └── generate_invoice.php # Invoice generator
├── invoices/            # Generated PDF storage (writable)
├── assets/              # CSS & JS
│   ├── css/style.css    # Styling
│   └── js/              # JavaScript
├── sql/                 # Database schema
│   └── event_management_schema.sql
├── index.php            # Homepage (events list)
├── login.php            # User login
├── register.php         # User registration
├── register_event.php   # Event registration handler
├── event.php            # Event details page
├── logout.php           # User logout
└── README.md            # Project documentation
```

---

## Database Schema

### Tables Created
- **users**: User accounts
- **admin**: Admin accounts
- **events**: Event listings
- **registrations**: User event registrations
- **invoices**: Generated invoices

### Sample Data Inserted
- **1 Admin**: username=`admin`, password=`admin123`
- **1 User**: email=`john@example.com`, password=`password123`
- **4 Events**: Tech Conference, Music Fest, Startup Meetup, AI Workshop

---

## Security Features

✅ **Password Hashing**: Using `password_hash()` and `password_verify()`
✅ **Prepared Statements**: Protection against SQL injection
✅ **Session Management**: Secure session handling
✅ **Input Sanitization**: `htmlspecialchars()` on all outputs
✅ **Access Control**: Role-based authorization (User/Admin)

---

## Troubleshooting

### "Database connection failed"
- **Check**: MySQL service is running
- **Check**: Database credentials in `config/database.php`
- **Check**: Database `event_management` exists

### "FPDF library not found"
- **Solution**: `pdf/fpdf.php` must exist (already included)
- **Fix**: Don't delete or rename `/pdf/fpdf.php`

### "Unable to create invoice"
- **Check**: `invoices/` folder has write permissions
- **Fix on Linux**: `chmod 755 invoices/`

### CSS not loading
- **Check**: `BASE_URL` in `config/database.php` matches your folder name
- **Example**: If folder is `event-system`, set `BASE_URL = '/event-system'`

### Can't login to admin panel
- **Check**: Admin table has data (from SQL import)
- **Try**: Reset by importing `sql/event_management_schema.sql` again

---

## First Steps After Setup

1. ✅ **Verify**: Navigate to `http://localhost/event-management/`
2. ✅ **Create Account**: Sign up as a new user
3. ✅ **Browse Events**: See sample events created
4. ✅ **Register for Event**: Complete a registration
5. ✅ **Download Invoice**: Check if PDF generates
6. ✅ **Admin Login**: Access admin panel at `/admin/login.php`
7. ✅ **Manage Events**: Try adding/editing events

---

## Customization

### Change Database Credentials
Edit `config/database.php` - updating DB_HOST, DB_USER, DB_PASS

### Change Base URL
If your folder is NOT named `event-management`, update:
```php
define('BASE_URL', '/your-folder-name');
```

### Add More Sample Events
Use Admin Panel → Manage Events → Add New Event

### Modify Styling
Edit `assets/css/style.css` to customize colors, fonts, or layout

---

## Support & Documentation

- **FPDF Library**: http://www.fpdf.org/
- **PHP MySQL**: https://www.php.net/manual/en/book.mysqli.php
- **Session Management**: https://www.php.net/manual/en/book.session.php

---

## Notes

- **Invoices** are stored in the `/invoices/` folder and can be deleted safely
- **Sample Data** can be reset by re-importing the SQL file
- **Database Backup**: Before major changes, export your database from phpMyAdmin
- **Production**: Change default credentials and use strong passwords before deployment

---

**Happy Event Management! 🎫**
