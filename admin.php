# EduShield — Student Safety & Attendance System
### XAMPP Installation Guide

---

## REQUIREMENTS
- XAMPP (Apache 2.4+ · MySQL 5.7+ / MariaDB 10.3+ · PHP 7.4+)
- Browser: Chrome 90+, Firefox 88+, Edge 90+

---

## INSTALLATION — 5 STEPS

### Step 1 — Copy Files
Copy the entire `edushield/` folder to your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\edushield\        (Windows)
/opt/lampp/htdocs/edushield/      (Linux)
/Applications/XAMPP/htdocs/edushield/   (macOS)
```

### Step 2 — Start XAMPP
Open XAMPP Control Panel and start:
- ✅ Apache
- ✅ MySQL

### Step 3 — Create Database
1. Open your browser → http://localhost/phpmyadmin
2. Click **"New"** in the left sidebar
3. Enter database name: `edushield`
4. Click **Create**
5. Click the **Import** tab
6. Click **"Choose File"** and select `edushield/database/edushield.sql`
7. Click **Go** at the bottom

### Step 4 — Configure Database (if needed)
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Your MySQL password (blank by default on XAMPP)
define('DB_NAME', 'edushield');
```

### Step 5 — Open in Browser
Navigate to: **http://localhost/edushield/**

---

## LOGIN CREDENTIALS

| Username | Password   | Role        |
|----------|-----------|-------------|
| admin    | admin123  | Super Admin |
| teacher1 | admin123  | Teacher     |
| security | admin123  | Security    |

---

## FILE STRUCTURE
```
edushield/
├── index.php              → Login page
├── dashboard.php          → Main SPA dashboard
├── logout.php             → Session logout
├── includes/
│   └── config.php         → DB config + auth helpers
├── api/
│   ├── students.php       → Student CRUD + attendance
│   ├── stats.php          → Dashboard statistics
│   ├── qr.php             → QR generate & verify
│   ├── rfid.php           → RFID readers & logs
│   ├── safety.php         → Zones & incidents
│   └── admin.php          → Admin panel API
├── assets/
│   ├── css/style.css      → Full stylesheet
│   └── js/app.js          → Frontend application
└── database/
    └── edushield.sql      → Full schema + seed data
```

---

## FEATURES

### 📊 Dashboard
- Live stat cards (students, present, absent, alerts)
- Weekly attendance bar chart
- Monthly trend (attendance + incidents)
- Class breakdown chart
- RFID hourly heatmap
- Real-time activity log (auto-refreshes every 30s)

### 👥 Student Management
- Search by Reg Number, Name, Class or Status
- Multi-filter (status + class)
- Student profile modal with attendance history
- One-click attendance cycle (Present → Late → Absent → Excused)
- Add new student form

### ✅ Attendance Records
- Today's attendance rate
- Monthly overview line chart (week/month/term toggle)
- Per-class horizontal bar chart
- Daily check-in timeline
- Full attendance log table

### 📹 Live Feed
- 6 simulated camera feeds with HUD overlay
- Alert state for breach zones
- Person detection visualization
- Zone occupancy doughnut chart
- Auto-refresh every 6 seconds

### ⬜ QR Scanner
- Generate QR codes by registration number (downloadable)
- Scan simulator with live DB verification
- Manual verify input
- Recent scan log table

### 📡 RFID Status
- Reader health panel (Online/Degraded/Offline)
- Live tag read log (auto-refresh)
- Tag registry with authorized/unauthorized/pending states
- Stats: authorized, unauthorized, pending counts

### 🚨 Safety Monitor
- Zone status cards (Secure/Crowded/Breach)
- Critical alert banners with dismiss/resolve
- Incident log with resolve actions
- Radar chart (occupancy vs capacity per zone)
- Auto-refresh every 20 seconds

### ⚙ Admin Panel
- System overview stats
- Register new student form
- Toggle system settings
- Notifications panel
- System activity log
- Live terminal console

---

## NOTES
- Camera feeds are simulated (static + animated). Connect real IP cameras via RTSP/HLS for production.
- RFID reading is simulated. Integrate actual reader hardware via `/api/rfid.php?action=scan` POST endpoint.
- SMS/Email alerts require integration with a provider (e.g. Africa's Talking for Kenya).
- For production: change `DB_PASS`, use HTTPS, and rotate session secrets.
