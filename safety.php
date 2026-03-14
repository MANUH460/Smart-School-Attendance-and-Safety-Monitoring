<?php
// ============================================================
//  EduShield — Main Dashboard
//  File: dashboard.php
// ============================================================
require_once __DIR__.'/includes/config.php';
requireAuth();
$user = currentUser();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>EduShield — Command Center</title>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Exo+2:wght@300;400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
  <div class="logo-wrap">
    <div class="logo">
      <div class="logo-icon">🛡</div>
      <div>
        <div class="logo-text">Edu<span>Shield</span></div>
        <div class="logo-sub">SAFETY COMMAND</div>
      </div>
    </div>
  </div>
  <div class="system-status">
    <div class="status-dot"></div>
    <span>SYSTEM ONLINE · v<?= APP_VERSION ?></span>
  </div>

  <div class="nav-section">
    <div class="nav-label">MAIN</div>
    <a class="nav-item active" data-page="dashboard" href="#dashboard">
      <span class="nav-icon">⬛</span> Dashboard
    </a>
    <a class="nav-item" data-page="students" href="#students">
      <span class="nav-icon">👤</span> Students
      <span class="nav-badge green" id="nb-students">—</span>
    </a>
    <a class="nav-item" data-page="attendance" href="#attendance">
      <span class="nav-icon">✅</span> Attendance
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">MONITORING</div>
    <a class="nav-item" data-page="livefeed" href="#livefeed">
      <span class="nav-icon">📹</span> Live Feed
      <span class="nav-badge" id="nb-cameras">6</span>
    </a>
    <a class="nav-item" data-page="safety" href="#safety">
      <span class="nav-icon">🚨</span> Safety
      <span class="nav-badge" id="nb-alerts">—</span>
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">IDENTIFICATION</div>
    <a class="nav-item" data-page="qrscanner" href="#qrscanner">
      <span class="nav-icon">⬜</span> QR Scanner
    </a>
    <a class="nav-item" data-page="rfid" href="#rfid">
      <span class="nav-icon">📡</span> RFID Status
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">SYSTEM</div>
    <?php if(in_array($user['role'],['super_admin','admin'])): ?>
    <a class="nav-item" data-page="admin" href="#admin">
      <span class="nav-icon">⚙</span> Admin Panel
    </a>
    <?php endif; ?>
  </div>

  <div class="sidebar-footer">
    <div class="admin-profile">
      <div class="avatar"><?= strtoupper(substr($user['full_name'],0,1)) ?></div>
      <div class="admin-info">
        <div class="admin-name"><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="admin-role"><?= strtoupper($user['role']) ?></div>
      </div>
      <a href="logout.php" class="logout-btn" title="Logout">↩</a>
    </div>
  </div>
</nav>

<!-- MAIN -->
<div class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
    <div>
      <div class="page-title" id="topTitle">Dashboard</div>
      <div class="page-crumb" id="topCrumb">Overview › Main Dashboard</div>
    </div>
    <div class="topbar-right">
      <div class="live-clock" id="liveClock">00:00:00</div>
      <div class="notif-btn" id="notifBtn" onclick="toggleNotifPanel()">
        🔔<div class="alert-pip" id="alertPip" style="display:none"></div>
      </div>
    </div>
  </div>

  <!-- NOTIFICATION PANEL -->
  <div class="notif-panel" id="notifPanel" style="display:none">
    <div class="notif-panel-header">
      <span>Notifications</span>
      <button onclick="markAllRead()" style="font-size:10px;cursor:pointer;background:none;border:none;color:var(--cyan)">Mark all read</button>
    </div>
    <div id="notifList"></div>
  </div>

  <div class="content">

    <!-- ══════════════════════════════════════
         DASHBOARD
    ══════════════════════════════════════ -->
    <div class="page active" id="page-dashboard">
      <div class="stats-grid" id="dashStats">
        <!-- Populated via JS -->
        <div class="card stat-card skeleton"><div class="skeleton-inner"></div></div>
        <div class="card stat-card skeleton"><div class="skeleton-inner"></div></div>
        <div class="card stat-card skeleton"><div class="skeleton-inner"></div></div>
        <div class="card stat-card skeleton"><div class="skeleton-inner"></div></div>
      </div>
      <div class="grid-3 mb">
        <div class="card chart-card">
          <div class="chart-header">
            <div><div class="chart-title">Weekly Attendance</div><div class="chart-sub">Last 7 days · All classes</div></div>
            <div class="chart-legend">
              <div class="legend-item"><div class="legend-dot" style="background:var(--cyan)"></div>Present</div>
              <div class="legend-item"><div class="legend-dot" style="background:var(--red)"></div>Absent</div>
            </div>
          </div>
          <div class="chart-wrap"><canvas id="weeklyChart"></canvas></div>
        </div>
        <div class="card chart-card">
          <div class="chart-header"><div><div class="chart-title">Today's Breakdown</div></div></div>
          <div class="chart-wrap" style="height:170px"><canvas id="pieChart"></canvas></div>
        </div>
      </div>
      <div class="grid-2 mb">
        <div class="card chart-card">
          <div class="chart-header"><div><div class="chart-title">Monthly Trend</div><div class="chart-sub">Attendance vs Safety Incidents</div></div></div>
          <div class="chart-wrap"><canvas id="trendChart"></canvas></div>
        </div>
        <div class="card" style="padding:16px 18px">
          <div class="card-title">REAL-TIME ACTIVITY LOG</div>
          <div id="activityFeed" class="scrollable" style="max-height:220px"></div>
        </div>
      </div>
      <div class="grid-2 mb">
        <div class="card">
          <div class="card-title">CLASS ATTENDANCE BREAKDOWN</div>
          <div class="chart-wrap" style="height:180px"><canvas id="classChart"></canvas></div>
        </div>
        <div class="card">
          <div class="card-title">RFID CHECK-IN HEATMAP (Hourly)</div>
          <div class="chart-wrap" style="height:180px"><canvas id="heatmapChart"></canvas></div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         STUDENTS
    ══════════════════════════════════════ -->
    <div class="page" id="page-students">
      <div class="page-header"><h2>Student Management</h2><p>Search by Reg Number, Name, Class or Status</p></div>
      <div class="card mb">
        <div class="search-bar">
          <input class="search-input" id="studentSearch" placeholder="🔍  Reg number / Name / Class...">
          <select class="filter-select" id="statusFilter">
            <option value="">All Status</option>
            <option>Present</option><option>Absent</option><option>Late</option><option>Excused</option>
          </select>
          <select class="filter-select" id="classFilter">
            <option value="">All Classes</option>
            <option>Form 1</option><option>Form 2</option><option>Form 3</option><option>Form 4</option>
          </select>
          <button class="search-btn" onclick="loadStudents()">SEARCH</button>
          <button class="btn btn-ghost" onclick="openModal('addStudentModal')">+ Add Student</button>
        </div>
        <div style="display:flex;gap:16px;font-size:11px;color:var(--text-secondary);font-family:var(--font-mono);padding:0 4px 12px">
          Total:<span style="color:var(--cyan)" id="cnt-total">—</span>
          Present:<span style="color:var(--green)" id="cnt-present">—</span>
          Absent:<span style="color:var(--red)" id="cnt-absent">—</span>
          Late:<span style="color:var(--amber)" id="cnt-late">—</span>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr>
              <th>REG NUMBER</th><th>NAME</th><th>CLASS</th><th>STATUS</th>
              <th>CHECK-IN</th><th>METHOD</th><th>RFID TAG</th><th>FLAG</th><th>ACTIONS</th>
            </tr></thead>
            <tbody id="studentTableBody"></tbody>
          </table>
          <div id="noStudents" class="no-results" style="display:none">No students match your search.</div>
        </div>
        <div class="table-pagination" id="studentPagination"></div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         ATTENDANCE
    ══════════════════════════════════════ -->
    <div class="page" id="page-attendance">
      <div class="page-header"><h2>Attendance Records</h2><p>Daily · Weekly · Term overview</p></div>
      <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)" id="attStats"></div>
      <div class="card chart-card mb" style="margin-top:14px">
        <div class="chart-header">
          <div><div class="chart-title">Attendance Over Time — All Classes</div></div>
          <div style="display:flex;gap:6px">
            <button class="tab-btn active" onclick="setAttView('week',this)">Week</button>
            <button class="tab-btn" onclick="setAttView('month',this)">Month</button>
            <button class="tab-btn" onclick="setAttView('term',this)">Term</button>
          </div>
        </div>
        <div class="chart-wrap" style="height:260px"><canvas id="bigAttChart"></canvas></div>
      </div>
      <div class="grid-2 mb">
        <div class="card">
          <div class="card-title">PER CLASS RATE</div>
          <div class="chart-wrap" style="height:200px"><canvas id="classBarChart"></canvas></div>
        </div>
        <div class="card">
          <div class="card-title">DAILY CHECK-IN TIMELINE</div>
          <div class="chart-wrap" style="height:200px"><canvas id="timelineChart"></canvas></div>
        </div>
      </div>
      <div class="card mb">
        <div class="card-title">ATTENDANCE LOG — TODAY</div>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>REG</th><th>NAME</th><th>CLASS</th><th>STATUS</th><th>CHECK-IN</th><th>METHOD</th><th>LOCATION</th></tr></thead>
            <tbody id="attLogBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         LIVE FEED
    ══════════════════════════════════════ -->
    <div class="page" id="page-livefeed">
      <div class="page-header"><h2>Live Security Feed</h2><p>Real-time camera monitoring</p></div>
      <div class="card mb" style="padding:12px 16px">
        <div style="display:flex;align-items:center;gap:16px;font-family:var(--font-mono);font-size:11px;flex-wrap:wrap">
          <span style="color:var(--green)" id="feedStatus">● LOADING...</span>
          <span style="color:var(--text-secondary)">CAMERAS: <span id="camOnline" style="color:var(--cyan)">—</span></span>
          <span style="color:var(--text-secondary)">STORAGE: <span style="color:var(--amber)">78% USED</span></span>
          <span style="color:var(--text-secondary)">QUALITY: <span style="color:var(--cyan)">1080p·30fps</span></span>
          <div style="margin-left:auto;display:flex;gap:8px">
            <button class="btn btn-ghost" style="padding:5px 12px;font-size:10px" onclick="alert('Recording feature requires hardware integration')">⏺ RECORD</button>
            <button class="btn btn-ghost" style="padding:5px 12px;font-size:10px" onclick="alert('Snapshot feature requires camera SDK')">📸 SNAPSHOT</button>
          </div>
        </div>
      </div>
      <div class="feeds-grid" id="feedsGrid"></div>
      <div class="grid-2 mb">
        <div class="card">
          <div class="card-title">DETECTION LOG</div>
          <div id="cameraDetLog" class="scrollable" style="max-height:200px"></div>
        </div>
        <div class="card">
          <div class="card-title">ZONE OCCUPANCY</div>
          <div class="chart-wrap" style="height:200px"><canvas id="occupancyChart"></canvas></div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         QR SCANNER
    ══════════════════════════════════════ -->
    <div class="page" id="page-qrscanner">
      <div class="page-header"><h2>QR Code Identification</h2><p>Generate · Scan · Verify student QR codes</p></div>
      <div class="grid-2 mb">
        <div class="card">
          <div class="card-title">GENERATE STUDENT QR</div>
          <div class="qr-gen-form">
            <input class="input-field" id="qrRegInput" placeholder="Enter Reg Number (e.g. STU-2024-001)">
            <button class="btn btn-primary" onclick="generateQR()">GENERATE</button>
          </div>
          <div id="qrDisplay" style="display:flex;flex-direction:column;align-items:center;gap:14px;padding:20px 0">
            <div class="qr-frame">
              <div class="qr-corner tl"></div><div class="qr-corner tr"></div>
              <div class="qr-corner bl"></div><div class="qr-corner br"></div>
              <div class="qr-inner" id="qrOutput">
                <div style="text-align:center;color:var(--text-dim);font-family:var(--font-mono);font-size:10px">Enter reg number<br>to generate QR</div>
              </div>
            </div>
            <div id="qrStudentMeta" style="text-align:center;display:none">
              <div id="qrSName" style="font-size:14px;font-weight:700"></div>
              <div id="qrSClass" style="font-size:11px;color:var(--text-secondary);font-family:var(--font-mono)"></div>
              <button class="btn btn-ghost" id="qrDlBtn" style="margin-top:8px;font-size:11px" onclick="downloadQR()">⬇ DOWNLOAD QR</button>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-title">SCAN SIMULATOR / MANUAL CHECK-IN</div>
          <div style="padding:10px 0">
            <div class="qr-scanner-box" id="qrScanBox" onclick="simulateScan()">
              <div class="qr-scan-line" id="qrScanLine"></div>
              <div class="qr-frame" style="width:80px;height:80px">
                <div class="qr-corner tl"></div><div class="qr-corner tr"></div>
                <div class="qr-corner bl"></div><div class="qr-corner br"></div>
              </div>
              <div class="qr-status" id="scanStatus">CLICK TO SIMULATE SCAN</div>
            </div>
            <div id="scanFeedback" style="margin-top:12px"></div>
            <div class="sep"></div>
            <div style="margin-top:10px">
              <div class="form-label">MANUAL QR VERIFY (enter token / reg)</div>
              <div style="display:flex;gap:8px;margin-top:6px">
                <input class="input-field" id="manualQRInput" placeholder="STU-2024-XXX or QR token">
                <button class="btn btn-primary" onclick="manualVerify()">VERIFY</button>
              </div>
              <div id="manualResult" style="margin-top:10px"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb">
        <div class="card-title">RECENT QR SCAN LOG</div>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>TIME</th><th>STUDENT</th><th>REG</th><th>LOCATION</th><th>RESULT</th></tr></thead>
            <tbody id="qrLogBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         RFID
    ══════════════════════════════════════ -->
    <div class="page" id="page-rfid">
      <div class="page-header"><h2>RFID Status Monitor</h2><p>Readers · Live reads · Tag registry</p></div>
      <div class="stats-grid" id="rfidStats" style="grid-template-columns:repeat(4,1fr)"></div>
      <div class="grid-2 mb" style="margin-top:14px">
        <div class="card">
          <div class="card-title">READER STATUS</div>
          <div id="rfidReaders" style="display:flex;flex-direction:column;gap:8px;margin-top:4px"></div>
        </div>
        <div class="card">
          <div class="card-title">LIVE TAG READS</div>
          <div id="rfidLiveLog" class="scrollable" style="max-height:300px"></div>
        </div>
      </div>
      <div class="card mb">
        <div class="card-title">RFID TAG REGISTRY</div>
        <div class="search-bar">
          <input class="search-input" id="rfidSearch" placeholder="🔍  Tag ID / Student name...">
          <select class="filter-select" id="rfidStatusFilter">
            <option value="">All</option>
            <option value="Authorized">Authorized</option>
            <option value="Unauthorized">Unauthorized</option>
            <option value="Pending">Pending</option>
          </select>
          <button class="search-btn" onclick="loadRFIDLogs()">FILTER</button>
        </div>
        <div id="rfidRegistry" class="rfid-grid"></div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         SAFETY
    ══════════════════════════════════════ -->
    <div class="page" id="page-safety">
      <div class="page-header"><h2>Real-Time Safety Monitor</h2><p>Zone status · Incidents · Alerts</p></div>
      <div id="alertBanners"></div>
      <div class="zone-grid" id="zoneGrid"></div>
      <div class="grid-2 mb">
        <div class="card">
          <div class="card-title">INCIDENT LOG</div>
          <div id="incidentLog" class="scrollable" style="max-height:320px"></div>
        </div>
        <div class="card">
          <div class="card-title">SAFETY STATISTICS</div>
          <div class="chart-wrap" style="height:280px"><canvas id="safetyChart"></canvas></div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════
         ADMIN
    ══════════════════════════════════════ -->
    <?php if(in_array($user['role'],['super_admin','admin'])): ?>
    <div class="page" id="page-admin">
      <div class="page-header"><h2>Admin Control Panel</h2><p>System · Settings · Enrolment · Logs</p></div>
      <div class="grid-2 mb">
        <div class="card">
          <div class="admin-section-title">📊 System Overview</div>
          <div class="enrollment-grid" id="adminSystemStats"></div>
          <div class="terminal" id="adminTerminal"></div>
        </div>
        <div class="card">
          <div class="admin-section-title">➕ Register New Student</div>
          <div class="add-student-form" id="addStudentInlineForm">
            <div class="form-group">
              <div class="form-label">REG NUMBER</div>
              <input class="form-input" id="aReg" placeholder="STU-2024-XXX">
            </div>
            <div class="form-group">
              <div class="form-label">FULL NAME</div>
              <input class="form-input" id="aName" placeholder="Full Name">
            </div>
            <div class="form-group">
              <div class="form-label">CLASS</div>
              <select class="form-input" id="aClass" style="color:var(--text-primary)"></select>
            </div>
            <div class="form-group">
              <div class="form-label">GENDER</div>
              <select class="form-input" id="aGender" style="color:var(--text-primary)">
                <option>Male</option><option>Female</option><option>Other</option>
              </select>
            </div>
            <div class="form-group">
              <div class="form-label">RFID TAG</div>
              <input class="form-input" id="aRfid" placeholder="T-XXXX">
            </div>
            <div class="form-group">
              <div class="form-label">GUARDIAN PHONE</div>
              <input class="form-input" id="aPhone" placeholder="+254 7XX XXX XXX">
            </div>
            <div class="form-group">
              <div class="form-label">GUARDIAN NAME</div>
              <input class="form-input" id="aGuardian" placeholder="Guardian Full Name">
            </div>
            <div class="form-group">
              <div class="form-label">GUARDIAN EMAIL</div>
              <input class="form-input" id="aEmail" placeholder="guardian@email.com">
            </div>
            <div class="form-full">
              <button class="btn btn-primary" style="width:100%" onclick="registerStudent()">REGISTER STUDENT</button>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb">
        <div class="admin-section-title">⚙ System Settings</div>
        <div class="settings-grid" id="settingsGrid">
          <div class="setting-row"><div><div class="setting-label">Auto SMS Alerts</div><div class="setting-desc">SMS guardian on absence</div></div><div class="toggle on" onclick="toggleSetting(this)"></div></div>
          <div class="setting-row"><div><div class="setting-label">RFID Auto-Register</div><div class="setting-desc">Auto-log RFID check-ins</div></div><div class="toggle on" onclick="toggleSetting(this)"></div></div>
          <div class="setting-row"><div><div class="setting-label">Motion Detection Alerts</div><div class="setting-desc">AI camera motion alerts</div></div><div class="toggle on" onclick="toggleSetting(this)"></div></div>
          <div class="setting-row"><div><div class="setting-label">Night Mode Cameras</div><div class="setting-desc">IR night vision after 18:00</div></div><div class="toggle" onclick="toggleSetting(this)"></div></div>
          <div class="setting-row"><div><div class="setting-label">Email Daily Reports</div><div class="setting-desc">Report to principal daily</div></div><div class="toggle on" onclick="toggleSetting(this)"></div></div>
          <div class="setting-row"><div><div class="setting-label">QR Daily Rotation</div><div class="setting-desc">Rotate QR tokens every 24h</div></div><div class="toggle on" onclick="toggleSetting(this)"></div></div>
        </div>
      </div>
      <div class="card mb">
        <div class="admin-section-title">🔔 Notifications</div>
        <div id="adminNotifList"></div>
      </div>
      <div class="card mb">
        <div class="admin-section-title">📋 System Activity Log</div>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>TIME</th><th>ACTION</th><th>DETAILS</th><th>USER</th><th>IP</th></tr></thead>
            <tbody id="sysLogBody"></tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /content -->
</div><!-- /main -->

<!-- ══ MODAL: Add Student ══ -->
<div class="modal-overlay" id="addStudentModal" style="display:none" onclick="closeModal('addStudentModal',event)">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Register New Student</div>
      <button class="modal-close" onclick="closeModal('addStudentModal')">✕</button>
    </div>
    <div class="add-student-form">
      <div class="form-group"><div class="form-label">REG NUMBER</div><input class="form-input" id="mReg" placeholder="STU-2024-XXX"></div>
      <div class="form-group"><div class="form-label">FULL NAME</div><input class="form-input" id="mName" placeholder="Full Name"></div>
      <div class="form-group"><div class="form-label">CLASS</div><select class="form-input" id="mClass" style="color:var(--text-primary)"></select></div>
      <div class="form-group"><div class="form-label">GENDER</div><select class="form-input" id="mGender" style="color:var(--text-primary)"><option>Male</option><option>Female</option><option>Other</option></select></div>
      <div class="form-group"><div class="form-label">RFID TAG</div><input class="form-input" id="mRfid" placeholder="T-XXXX"></div>
      <div class="form-group"><div class="form-label">GUARDIAN PHONE</div><input class="form-input" id="mPhone" placeholder="+254 7XX XXX XXX"></div>
      <div class="form-full"><button class="btn btn-primary" style="width:100%" onclick="registerStudentModal()">REGISTER</button></div>
    </div>
  </div>
</div>

<!-- ══ MODAL: Student Profile ══ -->
<div class="modal-overlay" id="studentProfileModal" style="display:none" onclick="closeModal('studentProfileModal',event)">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Student Profile</div>
      <button class="modal-close" onclick="closeModal('studentProfileModal')">✕</button>
    </div>
    <div id="studentProfileBody"></div>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  const APP_BASE = '<?= APP_BASE ?>';
  const CURRENT_USER = <?= json_encode(['role'=>$user['role'],'name'=>$user['full_name']]) ?>;
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
