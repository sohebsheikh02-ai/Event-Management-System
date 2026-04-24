# Event Management System (PHP + MySQL)

A complete Event Management System built with **Core PHP**, **MySQL**, and **FPDF** for invoice generation.

## 🚀 Features
- User registration & login (session-based, password hashing)
- Browse, search & filter events
- Register for events (duplicate prevention)
- **Auto-generate downloadable PDF invoices**
- Admin panel: manage events, users, registrations, invoices
- Responsive UI with custom CSS (Bootstrap-inspired)

## 📦 Installation

### Quick Start
**👉 [See SETUP.md for complete installation guide](./SETUP.md)**

### 1. Requirements
- PHP 7.4+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP / WAMP / LAMP)
- FPDF library (included - `pdf/fpdf.php`)

### 2. Setup (3 Steps)
1. Copy the `event-management` folder into `htdocs/` (XAMPP) or `www/` (WAMP)
2. **Start Apache + MySQL**
3. **Import** `sql/event_management_schema.sql` into MySQL via phpMyAdmin
4. **Edit** `config/database.php` with your database credentials
5. Visit `http://localhost/event-management/`

**Detailed instructions** → [📖 Complete Setup Guide](./SETUP.md)

### 3. Default Credentials
- **User**: `john@example.com` / `password123`
- **Admin**: `admin` / `admin123`

## 📁 Folder Structure
```
event-management/
├── config/              # Database connection
├── includes/            # Header, footer, auth helpers
├── admin/               # Admin dashboard & CRUD
├── user/                # User dashboard
├── invoices/            # Generated PDFs (writable)
├── pdf/                 # FPDF library + generator
├── assets/              # CSS / JS
├── sql/                 # Database schema
├── index.php            # Home / events list
├── login.php
├── register.php
├── logout.php
├── event.php            # Event details
├── register_event.php
├── SETUP.md             # 📖 COMPLETE INSTALLATION GUIDE
└── verify_setup.sh      # Verification script
```

## 🔐 Security
- `password_hash()` + `password_verify()`
- Prepared statements (MySQLi)
- Session validation
- Input sanitization
- `.htaccess` protection

## 🧾 Invoice Generation
After registering for an event, the user is redirected to download a PDF invoice generated via **FPDF** (`pdf/generate_invoice.php`).

## 📖 Documentation
- **[SETUP.md](./SETUP.md)** - Complete installation and configuration guide
- **[Database Schema](./sql/event_management_schema.sql)** - SQL database structure
- **[FPDF Info](./pdf/README.txt)** - Invoice generation info

## 🛠️ Verification
Run the verification script to check if everything is set up correctly:
```bash
bash verify_setup.sh
```

## 📞 Support
For setup help, refer to [SETUP.md](./SETUP.md) troubleshooting section.

---

**Quick Setup Guide** → [📖 See SETUP.md](./SETUP.md)
