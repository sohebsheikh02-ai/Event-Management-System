#!/bin/bash
# Event Management System - Quick Start Verification Script
# Run this after installation to verify everything is set up correctly

echo "🎫 Event Management System - Setup Verification"
echo "=================================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to check if file exists
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $2: $1"
        return 0
    else
        echo -e "${RED}✗${NC} $2: $1 (NOT FOUND)"
        return 1
    fi
}

# Function to check if directory exists and is writable
check_dir() {
    if [ -d "$1" ]; then
        if [ -w "$1" ]; then
            echo -e "${GREEN}✓${NC} $2: $1 (writable)"
            return 0
        else
            echo -e "${YELLOW}⚠${NC} $2: $1 (not writable)"
            return 1
        fi
    else
        echo -e "${RED}✗${NC} $2: $1 (NOT FOUND)"
        return 1
    fi
}

echo "Checking PHP Files..."
check_file "config/database.php" "Database Config"
check_file "includes/header.php" "Header File"
check_file "includes/footer.php" "Footer File"
check_file "includes/auth.php" "Auth Functions"
check_file "index.php" "Homepage"
check_file "login.php" "Login Page"
check_file "register.php" "Registration Page"
check_file "event.php" "Event Details"
check_file "register_event.php" "Registration Handler"
check_file "admin/login.php" "Admin Login"
check_file "admin/dashboard.php" "Admin Dashboard"
check_file "user/dashboard.php" "User Dashboard"
check_file "pdf/generate_invoice.php" "Invoice Generator"

echo ""
echo "Checking Libraries & Assets..."
check_file "pdf/fpdf.php" "FPDF Library"
check_file "assets/css/style.css" "CSS Styling"
check_file "sql/event_management_schema.sql" "Database Schema"

echo ""
echo "Checking Directories..."
check_dir "invoices" "Invoices Folder"
check_dir "assets/css" "CSS Folder"
check_dir "pdf" "PDF Folder"
check_dir "admin" "Admin Folder"
check_dir "user" "User Folder"

echo ""
echo "Documentation Files..."
check_file "README.md" "README"
check_file "SETUP.md" "Setup Guide"
check_file "pdf/README.txt" "FPDF Info"

echo ""
echo "=================================================="
echo "✓ Verification Complete!"
echo ""
echo "Next Steps:"
echo "1. Update config/database.php with your credentials"
echo "2. Import sql/event_management_schema.sql into MySQL"
echo "3. Visit http://localhost/event-management/"
echo "4. Default credentials:"
echo "   - User: john@example.com / password123"
echo "   - Admin: admin / admin123"
echo ""
echo "For detailed setup instructions, see SETUP.md"
echo "=================================================="
