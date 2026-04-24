# Event Management System (PHP + MySQL)

A complete Event Management System built with **Core PHP**, **MySQL**, and browser-friendly HTML invoices.

## 🚀 Features
- User registration & login (session-based, password hashing)
- Browse, search & filter events
- Register for events (duplicate prevention)
- Preview and print/download invoices from HTML
- Admin panel: manage events, users, registrations, invoices
- Responsive UI with custom CSS (Bootstrap-inspired)

## 📦 Installation

### Quick Start
**👉 [See SETUP.md for complete installation guide](./SETUP.md)**

### 1. Requirements
- PHP 7.4+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP / WAMP / LAMP)
- Docker (required for Render deployment)

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
├── invoices/            # Reserved invoice storage
├── pdf/                 # Invoice preview/download page
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
After registering for an event, the user is redirected to an HTML invoice preview (`pdf/generate_invoice.php`) where they can print or save it as PDF from the browser.

## Render Deployment
This repository is prepared for Render with Docker.

### Included deployment files
- `Dockerfile`
- `docker/start-apache.sh`
- `render.yaml`
- `.dockerignore`

### Environment variables
- `APP_BASE_URL`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

### Deploy steps
1. Push this repository to GitHub.
2. In Render, create a new **Web Service** from the repo.
3. Use the `Docker` runtime.
4. Keep the Dockerfile path as `./Dockerfile`.
5. Set `APP_BASE_URL` to your Render public URL.
6. Set the database environment variables.
7. Import `sql/event_management_schema.sql` into your MySQL database.

### Database note
For the free setup, use an external MySQL provider. Render free plans cover the web service, but not a MySQL private service.

## 📖 Documentation
- **[SETUP.md](./SETUP.md)** - Complete installation and configuration guide
- **[Database Schema](./sql/event_management_schema.sql)** - SQL database structure
- **[FPDF Info](./pdf/README.txt)** - Legacy invoice generation info

## 🛠️ Verification
Run the verification script to check if everything is set up correctly:
```bash
bash verify_setup.sh
```

## 📞 Support
For setup help, refer to [SETUP.md](./SETUP.md) troubleshooting section.

---

**Quick Setup Guide** → [📖 See SETUP.md](./SETUP.md)
