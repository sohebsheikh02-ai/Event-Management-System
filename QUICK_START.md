# 🎫 Event Management System - Quick Start Guide

## ✅ System is Running!

Your Event Management System is now **LIVE and READY** at:

```
🌐 http://localhost:8000/
```

---

## 🚀 Getting Started

### 1️⃣ Visit the Homepage
Open your browser and go to:
```
http://localhost:8000/
```

You'll see:
- ✅ 4 sample events
- ✅ Event categories
- ✅ Search functionality
- ✅ Navigation menu

---

## 👤 User Quick Start

### Option A: Login with Test Account
```
Email:    john@example.com
Password: password123
URL:      http://localhost:8000/login.php
```

**After login, you can:**
- ✅ View 4 sample events
- ✅ Register for any event
- ✅ Download PDF invoices
- ✅ View your registrations

### Option B: Create Your Own Account
```
URL: http://localhost:8000/register.php

Fill in:
- Full Name (required)
- Email (required)
- Phone (optional)
- Password (minimum 6 characters)

Click: Sign Up
```

---

## 👨‍💼 Admin Quick Start

### Login to Admin Panel
```
URL:      http://localhost:8000/admin/login.php
Username: admin
Password: admin123
```

**Admin Dashboard Features:**
- 📊 View statistics (Users, Events, Registrations, Revenue)
- 📝 Manage events (Add, Edit, Delete)
- 👥 View all registered users
- 📋 Track all registrations
- 📄 Download invoices

---

## 📋 Available Sample Events

| Event | Date | Venue | Price | Category |
|-------|------|-------|-------|----------|
| Tech Conference 2025 | Dec 15, 2025 | Convention Center, Mumbai | ₹1,500 | Technology |
| Music Fest | Nov 20, 2025 | Open Grounds, Delhi | ₹800 | Music |
| Startup Meetup | Oct 30, 2025 | WeWork, Bangalore | FREE | Business |
| AI Workshop | Dec 5, 2025 | IIT Auditorium, Chennai | ₹500 | Education |

---

## 🔗 Navigation Map

### Public Pages (No Login Required)
```
Homepage:         http://localhost:8000/
View Event:       http://localhost:8000/event.php?id=1
Login:            http://localhost:8000/login.php
Register:         http://localhost:8000/register.php
Admin Login:      http://localhost:8000/admin/login.php
```

### User Pages (Login Required)
```
User Dashboard:   http://localhost:8000/user/dashboard.php
View Invoice:     http://localhost:8000/pdf/generate_invoice.php?id=1
Logout:           http://localhost:8000/logout.php
```

### Admin Pages (Admin Login Required)
```
Admin Dashboard:        http://localhost:8000/admin/dashboard.php
Manage Events:          http://localhost:8000/admin/events.php
View Users:             http://localhost:8000/admin/users.php
View Registrations:     http://localhost:8000/admin/registrations.php
Admin Logout:           http://localhost:8000/admin/logout.php
```

---

## 🧪 Testing Workflow

### Complete User Journey (15 minutes)

**Step 1: Browse Events (2 min)**
- [ ] Visit: http://localhost:8000/
- [ ] See 4 sample events displayed
- [ ] Try searching for "Tech"
- [ ] Filter by "Technology" category

**Step 2: View Event Details (2 min)**
- [ ] Click on "Tech Conference 2025"
- [ ] View full event details
- [ ] See price and venue information

**Step 3: Register (Sign Up as New User) (3 min)**
- [ ] Click "Sign Up"
- [ ] Enter: Name, Email, Phone, Password
- [ ] Submit the form
- [ ] Verify success message

**Step 4: Login (2 min)**
- [ ] Go to Login page
- [ ] Use credentials you just created
- [ ] Verify you're logged in

**Step 5: Register for Event (2 min)**
- [ ] Go back to event page
- [ ] Click "Confirm Registration"
- [ ] Verify PDF invoice downloads
- [ ] Check file was created

**Step 6: View Dashboard (2 min)**
- [ ] Click "Dashboard" in navigation
- [ ] See your registered events
- [ ] Verify invoice appears

**Step 7: Admin Access (2 min)**
- [ ] Logout from user account
- [ ] Go to Admin Login
- [ ] Use: admin / admin123
- [ ] View admin dashboard stats

---

## 🎯 Key Features to Test

### Search & Filter
```
Try these searches:
- "Tech" → Find Tech Conference
- "Music" → Find Music Fest
- "" + Filter "Technology" → Tech Conference only
```

### Event Registration Flow
```
1. Browse events
2. Click event
3. Login/Register
4. Register for event
5. Download invoice PDF
```

### Admin Features
```
1. Login to admin panel
2. View statistics
3. Add new event (click "Manage Events")
4. Edit event details
5. View all users
6. Track registrations
```

---

## 📱 Device Compatibility

The system works on:
- ✅ Desktop browsers
- ✅ Laptop browsers
- ✅ Mobile browsers (responsive design)
- ✅ Tablets

Try resizing your browser to see responsive design!

---

## 🐛 Troubleshooting

### Can't connect to localhost:8000?
```bash
# Check if PHP server is running
ps aux | grep php

# If not, restart it:
cd /home/ptspl29/Downloads/event-management
php -S localhost:8000
```

### Database connection error?
```
Check config/database.php:
- DB_HOST: localhost
- DB_USER: root
- DB_PASS: SoH@1234
- DB_NAME: event_management
```

### CSS not loading?
- Hard refresh: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
- Check browser console for errors (F12)

### Can't login?
- Try: john@example.com / password123
- If error, re-import database from sql/event_management_schema.sql

---

## 📊 System Information

```
PHP Version:      8.3.6
Server:           Built-in PHP Development Server
Database:         MySQL (event_management)
URL Prefix:       / (at localhost:8000)
Status:           ✅ FULLY OPERATIONAL
```

---

## 📚 Additional Resources

- **Complete Setup Guide**: `SETUP.md`
- **Installation Status**: `INSTALLATION_COMPLETE.md`
- **Test Report**: `LIVE_TEST_REPORT.md`
- **Database Schema**: `sql/event_management_schema.sql`
- **README**: `README.md`

---

## 🚀 Next Steps

### Option 1: Continue Testing Locally
Keep using localhost:8000 for testing various features

### Option 2: Deploy to Production
When ready to deploy:
1. Update database credentials in `config/database.php`
2. Configure Apache/Nginx
3. Set proper file permissions
4. Enable HTTPS
5. Update BASE_URL in config

### Option 3: Share Locally (LAN Testing)
Ask team to connect to your machine's IP:
```
http://{your-local-ip}:8000/
```

---

## ✨ Enjoy!

Your Event Management System is ready to use! 🎫

**Start here:** http://localhost:8000/

Need help? Check the documentation in the project folder.

---

**Happy Event Management!** 🎉
