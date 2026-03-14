/* ============================================================
   EduShield — Main Stylesheet
   assets/css/style.css
============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Exo+2:wght@300;400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap');

/* ── CSS Variables ── */
:root {
  --bg-deep:   #060a12;
  --bg-panel:  #0c1220;
  --bg-card:   #101828;
  --bg-hover:  #162035;
  --border:    #1e2d42;
  --cyan:      #00d4ff;
  --cyan-dim:  rgba(0,212,255,.25);
  --cyan-glow: rgba(0,212,255,.08);
  --green:     #00e58a;
  --green-dim: rgba(0,229,138,.2);
  --red:       #ff3855;
  --red-dim:   rgba(255,56,85,.2);
  --amber:     #ffb800;
  --amber-dim: rgba(255,184,0,.2);
  --purple:    #b44dff;
  --purple-dim:rgba(180,77,255,.2);
  --text-primary:   #e8f0fe;
  --text-secondary: #6b82a0;
  --text-dim:       #2e3f52;
  --font-mono: 'Share Tech Mono', monospace;
  --font-ui:   'Exo 2', sans-serif;
  --font-data: 'Rajdhani', sans-serif;
  --sidebar-w: 240px;
  --topbar-h:  54px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; overflow: hidden; }
body {
  font-family: var(--font-ui);
  background: var(--bg-deep);
  color: var(--text-primary);
  display: flex;
  height: 100vh;
}
body::before {
  content: '';
  position: fixed; inset: 0;
  background: repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,0,0,.06) 2px,rgba(0,0,0,.06) 4px);
  pointer-events: none; z-index: 9000;
}
a { text-decoration: none; color: inherit; }
button { font-family: var(--font-ui); }

/* ══ SIDEBAR ══ */
.sidebar {
  width: var(--sidebar-w); min-width: var(--sidebar-w);
  background: var(--bg-panel);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  z-index: 200; position: relative; overflow: hidden;
  transition: transform .3s;
}
.sidebar::after {
  content:''; position:absolute; top:0; right:0;
  width:1px; height:100%;
  background: linear-gradient(180deg,transparent,rgba(0,212,255,.2) 40%,rgba(0,212,255,.2) 60%,transparent);
}
.logo-wrap { padding:18px 16px 14px; border-bottom:1px solid var(--border); }
.logo { display:flex; align-items:center; gap:10px; }
.logo-icon {
  width:36px; height:36px;
  background:linear-gradient(135deg,var(--cyan),var(--purple));
  border-radius:8px; display:flex; align-items:center; justify-content:center;
  font-size:18px; box-shadow:0 0 20px rgba(0,212,255,.2);
}
.logo-text { font-size:18px; font-weight:800; letter-spacing:2px; }
.logo-text span { color:var(--cyan); }
.logo-sub { font-size:9px; color:var(--text-secondary); letter-spacing:3px; font-family:var(--font-mono); }
.system-status {
  padding:8px 16px; border-bottom:1px solid var(--border);
  display:flex; align-items:center; gap:8px;
  font-family:var(--font-mono); font-size:10px; color:var(--text-secondary);
}
.status-dot { width:6px; height:6px; border-radius:50%; background:var(--green); box-shadow:0 0 8px var(--green); animation:pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:.3} }

.nav-section { padding:12px 10px 4px; }
.nav-label { font-size:9px; letter-spacing:3px; color:var(--text-dim); padding:0 6px 8px; font-family:var(--font-mono); }
.nav-item {
  display:flex; align-items:center; gap:10px;
  padding:9px 10px; border-radius:8px; cursor:pointer;
  font-size:13px; font-weight:500; color:var(--text-secondary);
  transition:all .2s; margin-bottom:2px;
  border:1px solid transparent; letter-spacing:.3px;
}
.nav-item:hover { background:var(--bg-hover); color:var(--text-primary); border-color:var(--border); }
.nav-item.active { background:var(--cyan-glow); color:var(--cyan); border-color:var(--cyan-dim); }
.nav-icon { font-size:14px; width:20px; text-align:center; }
.nav-badge {
  margin-left:auto; background:var(--red); color:#fff;
  font-size:9px; padding:1px 6px; border-radius:10px;
  font-weight:700; font-family:var(--font-mono);
}
.nav-badge.green { background:var(--green); color:var(--bg-deep); }
.sidebar-footer { margin-top:auto; padding:12px 14px; border-top:1px solid var(--border); }
.admin-profile { display:flex; align-items:center; gap:10px; }
.avatar {
  width:32px; height:32px; border-radius:8px;
  background:linear-gradient(135deg,#1a3a5c,rgba(0,89,255,.3));
  border:1px solid var(--cyan-dim); display:flex; align-items:center;
  justify-content:center; font-size:14px; color:var(--cyan); flex-shrink:0;
}
.admin-info { flex:1; min-width:0; }
.admin-name { font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.admin-role { font-size:9px; color:var(--text-secondary); font-family:var(--font-mono); }
.logout-btn {
  color:var(--text-secondary); font-size:16px; cursor:pointer;
  padding:4px 6px; border-radius:5px; border:1px solid transparent;
  transition:all .2s;
}
.logout-btn:hover { border-color:var(--border); color:var(--red); }

/* ══ MAIN ══ */
.main { flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0; }
.topbar {
  height:var(--topbar-h); min-height:var(--topbar-h);
  background:var(--bg-panel); border-bottom:1px solid var(--border);
  display:flex; align-items:center; padding:0 20px; gap:14px; position:relative;
}
.menu-toggle {
  display:none; background:none; border:none; color:var(--text-secondary);
  font-size:20px; cursor:pointer; padding:4px;
}
.page-title { font-size:15px; font-weight:700; letter-spacing:.8px; }
.page-crumb { font-size:10px; color:var(--text-secondary); font-family:var(--font-mono); }
.topbar-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
.live-clock {
  font-family:var(--font-mono); font-size:13px; color:var(--cyan); letter-spacing:2px;
  background:var(--cyan-glow); padding:4px 10px; border:1px solid var(--cyan-dim); border-radius:6px;
}
.notif-btn {
  width:34px; height:34px; border-radius:8px;
  border:1px solid var(--border); background:var(--bg-card);
  display:flex; align-items:center; justify-content:center;
  cursor:pointer; font-size:15px; position:relative; transition:all .2s;
}
.notif-btn:hover { border-color:var(--cyan-dim); }
.alert-pip {
  position:absolute; top:4px; right:4px;
  width:8px; height:8px; border-radius:50%;
  background:var(--red); border:1px solid var(--bg-panel);
  animation:pulse 1.5s infinite;
}

/* NOTIFICATION PANEL */
.notif-panel {
  position:absolute; top:calc(var(--topbar-h) + 4px); right:16px;
  width:320px; background:var(--bg-panel); border:1px solid var(--border);
  border-radius:12px; z-index:300; box-shadow:0 10px 40px rgba(0,0,0,.5);
}
.notif-panel-header {
  padding:12px 16px; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between;
  font-size:13px; font-weight:700;
}
.notif-item {
  display:flex; align-items:flex-start; gap:10px;
  padding:10px 14px; border-bottom:1px solid var(--border);
  transition:background .2s; cursor:pointer;
}
.notif-item:last-child { border-bottom:none; }
.notif-item:hover { background:var(--bg-hover); }
.notif-item.unread { border-left:2px solid var(--cyan); }
.notif-icon { font-size:16px; flex-shrink:0; margin-top:1px; }
.notif-body { flex:1; min-width:0; }
.notif-title { font-size:11px; font-weight:600; }
.notif-sub { font-size:10px; color:var(--text-secondary); margin-top:2px; font-family:var(--font-mono); }
.notif-time { font-size:9px; color:var(--text-dim); font-family:var(--font-mono); white-space:nowrap; }

/* CONTENT */
.content { flex:1; overflow-y:auto; overflow-x:hidden; padding:18px 22px; position:relative; }
.content::-webkit-scrollbar { width:5px; }
.content::-webkit-scrollbar-thumb { background:var(--border); border-radius:3px; }
.mb { margin-bottom:16px; }

/* PAGES */
.page { display:none; animation:fadeIn .3s ease; }
.page.active { display:block; }
@keyframes fadeIn { from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none} }
.page-header { margin-bottom:14px; }
.page-header h2 { font-size:20px; font-weight:800; }
.page-header p { font-size:11px; color:var(--text-secondary); font-family:var(--font-mono); margin-top:3px; }

/* ══ CARDS ══ */
.card {
  background:var(--bg-card); border:1px solid var(--border); border-radius:12px;
  padding:16px; position:relative; overflow:hidden;
}
.card::before {
  content:''; position:absolute; inset:0; border-radius:12px;
  background:linear-gradient(135deg,rgba(255,255,255,.01),transparent 60%);
  pointer-events:none;
}
.card-title {
  font-size:10px; letter-spacing:2px; color:var(--text-secondary);
  font-family:var(--font-mono); text-transform:uppercase; margin-bottom:10px;
}
.chart-card { padding:16px 18px; }
.chart-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px; }
.chart-title { font-size:14px; font-weight:700; }
.chart-sub { font-size:10px; color:var(--text-secondary); font-family:var(--font-mono); }
.chart-legend { display:flex; gap:12px; flex-wrap:wrap; }
.legend-item { display:flex; align-items:center; gap:5px; font-size:11px; color:var(--text-secondary); }
.legend-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.chart-wrap { position:relative; height:220px; }

/* GRIDS */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:16px; }
.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.grid-3 { display:grid; grid-template-columns:2fr 1fr; gap:14px; }

/* STAT CARDS */
.stat-card { padding:14px 16px; }
.stat-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:10px; }
.stat-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:17px; }
.stat-icon.cyan   { background:var(--cyan-dim);   color:var(--cyan); }
.stat-icon.green  { background:var(--green-dim);  color:var(--green); }
.stat-icon.red    { background:var(--red-dim);    color:var(--red); }
.stat-icon.amber  { background:var(--amber-dim);  color:var(--amber); }
.stat-icon.purple { background:var(--purple-dim); color:var(--purple); }
.stat-delta { font-size:9px; font-family:var(--font-mono); padding:2px 5px; border-radius:4px; }
.stat-delta.up   { background:var(--green-dim); color:var(--green); }
.stat-delta.down { background:var(--red-dim);   color:var(--red); }
.stat-value { font-size:28px; font-weight:800; font-family:var(--font-data); line-height:1.1; }
.stat-label { font-size:11px; color:var(--text-secondary); margin-top:3px; }
.stat-bar { height:2px; background:var(--border); border-radius:2px; margin-top:10px; overflow:hidden; }
.stat-bar-fill { height:100%; border-radius:2px; transition:width 1s ease; }

/* SKELETON */
.skeleton { opacity:.4; }
.skeleton-inner { background:linear-gradient(90deg,var(--border),var(--bg-hover),var(--border)); background-size:200% 100%; animation:shimmer 1.5s infinite; border-radius:8px; height:80px; }
@keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }

/* TABS */
.tab-btn {
  padding:6px 14px; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer;
  border:1px solid var(--border); background:transparent; color:var(--text-secondary);
  transition:all .2s; letter-spacing:.3px; font-family:var(--font-ui);
}
.tab-btn.active { background:var(--cyan-glow); color:var(--cyan); border-color:var(--cyan-dim); }
.tab-btn:hover:not(.active) { background:var(--bg-hover); color:var(--text-primary); }

/* ══ TABLE ══ */
.search-bar { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
.search-input {
  flex:1; min-width:200px; background:var(--bg-panel); border:1px solid var(--border);
  border-radius:8px; padding:9px 14px; color:var(--text-primary); font-size:13px;
  font-family:var(--font-ui); outline:none; transition:border-color .2s;
}
.search-input::placeholder { color:var(--text-dim); }
.search-input:focus { border-color:var(--cyan-dim); box-shadow:0 0 0 3px var(--cyan-glow); }
.filter-select {
  background:var(--bg-panel); border:1px solid var(--border);
  border-radius:8px; padding:9px 12px; color:var(--text-primary);
  font-size:12px; font-family:var(--font-ui); outline:none; cursor:pointer;
}
.search-btn {
  padding:9px 18px; background:var(--cyan); border:none; border-radius:8px;
  color:var(--bg-deep); font-weight:700; font-size:12px; cursor:pointer;
  font-family:var(--font-ui); letter-spacing:1px; transition:all .2s;
}
.search-btn:hover { background:#33dcff; box-shadow:0 0 20px rgba(0,212,255,.25); }
.table-wrap { overflow-y:auto; max-height:440px; border-radius:8px; border:1px solid var(--border); }
.table-wrap::-webkit-scrollbar { width:4px; }
.table-wrap::-webkit-scrollbar-thumb { background:var(--border); }
.data-table { width:100%; border-collapse:collapse; }
.data-table th {
  text-align:left; padding:10px 12px; font-size:9px; letter-spacing:2px;
  color:var(--text-dim); font-family:var(--font-mono); border-bottom:1px solid var(--border);
  background:var(--bg-panel); position:sticky; top:0; white-space:nowrap;
}
.data-table td { padding:10px 12px; font-size:12px; border-bottom:1px solid var(--border); transition:background .15s; }
.data-table tr:hover td { background:var(--bg-hover); }
.data-table tr:last-child td { border-bottom:none; }
.table-pagination { display:flex; gap:6px; padding:10px 0 0; justify-content:flex-end; }
.page-btn {
  padding:4px 10px; border-radius:5px; font-size:11px; cursor:pointer;
  border:1px solid var(--border); background:transparent; color:var(--text-secondary);
  transition:all .2s; font-family:var(--font-ui);
}
.page-btn.active { background:var(--cyan-glow); color:var(--cyan); border-color:var(--cyan-dim); }
.page-btn:hover:not(.active) { background:var(--bg-hover); }

/* BADGES */
.badge {
  display:inline-flex; align-items:center; gap:3px;
  font-size:9px; font-weight:700; font-family:var(--font-mono);
  padding:2px 7px; border-radius:4px; white-space:nowrap;
}
.badge.present { background:var(--green-dim); color:var(--green); }
.badge.absent  { background:var(--red-dim);   color:var(--red); }
.badge.late    { background:var(--amber-dim);  color:var(--amber); }
.badge.excused { background:var(--purple-dim); color:var(--purple); }
.badge.authorized   { background:var(--green-dim);  color:var(--green); }
.badge.unauthorized { background:var(--red-dim);    color:var(--red); }
.badge.pending      { background:var(--amber-dim);  color:var(--amber); }
.badge.online   { background:var(--green-dim);  color:var(--green); }
.badge.offline  { background:var(--red-dim);    color:var(--red); }
.badge.degraded { background:var(--amber-dim);  color:var(--amber); }
.badge.alert    { background:var(--red-dim);    color:var(--red); animation:pulse 1.5s infinite; }

.reg-num { font-family:var(--font-mono); font-size:11px; color:var(--cyan); }
.action-btn {
  padding:3px 9px; border-radius:5px; font-size:10px; cursor:pointer;
  border:1px solid var(--border); background:transparent; color:var(--text-secondary);
  transition:all .2s; font-family:var(--font-ui);
}
.action-btn:hover { border-color:var(--cyan-dim); color:var(--cyan); background:var(--cyan-glow); }
.action-btn.danger:hover { border-color:var(--red-dim); color:var(--red); background:var(--red-dim); }

.no-results { text-align:center; padding:40px; color:var(--text-secondary); font-family:var(--font-mono); font-size:12px; }

/* ══ BUTTONS ══ */
.btn {
  padding:9px 16px; border-radius:8px; font-weight:700; font-size:12px;
  cursor:pointer; border:none; letter-spacing:.8px; font-family:var(--font-ui); transition:all .2s;
}
.btn-primary { background:var(--cyan); color:var(--bg-deep); }
.btn-primary:hover { background:#33dcff; box-shadow:0 0 20px rgba(0,212,255,.25); }
.btn-ghost { background:transparent; border:1px solid var(--border); color:var(--text-secondary); }
.btn-ghost:hover { border-color:var(--cyan-dim); color:var(--cyan); background:var(--cyan-glow); }
.btn-danger { background:var(--red-dim); color:var(--red); border:1px solid var(--red-dim); }
.btn-danger:hover { background:var(--red); color:#fff; }
.btn-success { background:var(--green-dim); color:var(--green); border:1px solid var(--green-dim); }
.btn-success:hover { background:var(--green); color:var(--bg-deep); }

/* ══ LIVE FEED ══ */
.feeds-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:14px; }
.feed-cell {
  background:#000; border:1px solid var(--border); border-radius:10px;
  aspect-ratio:16/9; position:relative; overflow:hidden; cursor:pointer; transition:border-color .2s;
}
.feed-cell:hover { border-color:var(--cyan-dim); }
.feed-cell.alert-feed { border-color:var(--red); box-shadow:0 0 20px rgba(255,56,85,.2); animation:alertBlink 2s infinite; }
@keyframes alertBlink { 0%,100%{box-shadow:0 0 15px rgba(255,56,85,.2)}50%{box-shadow:0 0 30px rgba(255,56,85,.4)} }
.feed-bg { position:absolute; inset:0; background:linear-gradient(135deg,#070a0f,#0d1520); }
.feed-sim { position:absolute; inset:0; background:radial-gradient(circle at 30% 40%,rgba(0,100,200,.06) 0%,transparent 50%),radial-gradient(circle at 70% 60%,rgba(0,200,100,.04) 0%,transparent 40%); }
.feed-overlay { position:absolute; inset:0; background:linear-gradient(0deg,rgba(0,0,0,.8) 0%,transparent 40%); }
.feed-scan-line { position:absolute; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,rgba(0,212,255,.3),transparent); animation:scanLine 4s linear infinite; }
@keyframes scanLine { 0%{top:0;opacity:.8}100%{top:100%;opacity:0} }
.feed-crosshair {
  position:absolute; width:24px; height:24px;
  border:1.5px solid rgba(0,212,255,.4); border-radius:3px;
  animation:crossMove 10s linear infinite;
}
.feed-crosshair::before,.feed-crosshair::after { content:''; position:absolute; background:rgba(0,212,255,.4); }
.feed-crosshair::before { width:1px; height:100%; left:50%; }
.feed-crosshair::after { height:1px; width:100%; top:50%; }
@keyframes crossMove { 0%{top:20%;left:20%}20%{top:15%;left:60%}40%{top:55%;left:70%}60%{top:60%;left:25%}80%{top:30%;left:45%}100%{top:20%;left:20%} }
.feed-hud { position:absolute; inset:0; padding:8px; display:flex; flex-direction:column; }
.feed-top { display:flex; align-items:center; justify-content:space-between; }
.feed-cam-id { font-family:var(--font-mono); font-size:9px; color:rgba(255,255,255,.6); }
.feed-live { display:flex; align-items:center; gap:3px; font-family:var(--font-mono); font-size:8px; color:var(--red); background:rgba(255,56,85,.15); padding:2px 5px; border-radius:3px; border:1px solid rgba(255,56,85,.3); }
.feed-live-dot { width:5px; height:5px; border-radius:50%; background:var(--red); animation:pulse 1s infinite; }
.feed-bottom { margin-top:auto; }
.feed-location { font-family:var(--font-mono); font-size:8px; color:rgba(255,255,255,.45); }
.feed-count { font-size:12px; font-weight:600; color:#fff; margin-top:1px; }
.feed-person { position:absolute; width:14px; height:22px; border:1px solid rgba(0,229,138,.4); border-radius:2px; background:rgba(0,229,138,.04); }

/* ══ QR ══ */
.qr-gen-form { display:flex; gap:8px; margin-bottom:14px; }
.input-field {
  flex:1; background:var(--bg-panel); border:1px solid var(--border);
  border-radius:8px; padding:9px 14px; color:var(--text-primary);
  font-size:13px; font-family:var(--font-ui); outline:none;
}
.input-field:focus { border-color:var(--cyan-dim); box-shadow:0 0 0 3px var(--cyan-glow); }
.qr-frame { width:120px; height:120px; position:relative; }
.qr-corner { position:absolute; width:18px; height:18px; border-color:var(--cyan); border-style:solid; }
.qr-corner.tl { top:0; left:0;  border-width:3px 0 0 3px; }
.qr-corner.tr { top:0; right:0; border-width:3px 3px 0 0; }
.qr-corner.bl { bottom:0; left:0;  border-width:0 0 3px 3px; }
.qr-corner.br { bottom:0; right:0; border-width:0 3px 3px 0; }
.qr-inner { width:100%; height:100%; display:flex; align-items:center; justify-content:center; }
.qr-scanner-box {
  border:2px dashed var(--border); border-radius:12px;
  padding:24px 0; display:flex; flex-direction:column;
  align-items:center; gap:12px; cursor:pointer; transition:all .3s;
  position:relative; overflow:hidden; background:var(--bg-panel);
}
.qr-scanner-box:hover { border-color:var(--cyan); box-shadow:0 0 30px var(--cyan-glow); }
.qr-scanner-box.scanning { border-color:var(--cyan); border-style:solid; }
.qr-scan-line {
  display:none; position:absolute; left:10%; right:10%; height:2px;
  background:linear-gradient(90deg,transparent,var(--cyan),transparent);
  animation:qrScan 2s ease-in-out infinite; box-shadow:0 0 10px var(--cyan);
}
@keyframes qrScan { 0%{top:10%}50%{top:85%}100%{top:10%} }
.qr-status { font-family:var(--font-mono); font-size:10px; color:var(--text-secondary); }

/* ══ RFID ══ */
.rfid-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-top:8px; }
.rfid-item {
  display:flex; align-items:center; gap:12px;
  padding:12px; background:var(--bg-panel); border:1px solid var(--border);
  border-radius:10px; transition:all .2s; cursor:pointer;
}
.rfid-item:hover { border-color:var(--border-glow); background:var(--bg-hover); }
.rfid-item.Authorized   { border-left:3px solid var(--green); }
.rfid-item.Unauthorized { border-left:3px solid var(--red); }
.rfid-item.Pending      { border-left:3px solid var(--amber); }
.rfid-chip { width:40px; height:25px; border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rfid-chip.auth  { background:var(--green-dim); border:1px solid var(--green); }
.rfid-chip.unauth{ background:var(--red-dim);   border:1px solid var(--red); }
.rfid-chip.pend  { background:var(--amber-dim); border:1px solid var(--amber); }
.rfid-chip-inner { width:22px; height:14px; border-radius:2px; background:repeating-linear-gradient(90deg,rgba(255,255,255,.15) 0,rgba(255,255,255,.15) 1px,transparent 1px,transparent 3px); }
.rfid-info { flex:1; min-width:0; }
.rfid-tag  { font-family:var(--font-mono); font-size:11px; color:var(--cyan); }
.rfid-name { font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rfid-meta { font-size:10px; color:var(--text-secondary); font-family:var(--font-mono); }

/* ══ SAFETY ══ */
.safety-alert-banner {
  background:linear-gradient(90deg,var(--red-dim),transparent);
  border:1px solid rgba(255,56,85,.3); border-radius:10px;
  padding:12px 16px; margin-bottom:12px;
  display:flex; align-items:center; gap:12px;
}
.alert-msg-text { flex:1; }
.alert-msg-title { font-size:12px; font-weight:700; color:var(--red); }
.alert-msg-desc  { font-size:10px; color:var(--text-secondary); font-family:var(--font-mono); margin-top:2px; }
.dismiss-btn {
  padding:5px 12px; background:var(--red-dim); border:1px solid rgba(255,56,85,.3);
  border-radius:6px; color:var(--red); font-size:11px; cursor:pointer;
  font-family:var(--font-ui); font-weight:600; white-space:nowrap;
}
.zone-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:14px; }
.zone-card {
  padding:14px; border-radius:10px; border:1px solid var(--border);
  background:var(--bg-card); text-align:center; cursor:pointer; transition:all .2s;
}
.zone-card:hover { transform:translateY(-2px); box-shadow:0 4px 20px rgba(0,0,0,.3); }
.zone-card.Secure  { border-top:2px solid var(--green); }
.zone-card.Crowded { border-top:2px solid var(--amber); }
.zone-card.Breach  { border-top:2px solid var(--red); animation:dangerPulse 2s infinite; }
@keyframes dangerPulse { 0%,100%{box-shadow:none}50%{box-shadow:0 0 20px var(--red-dim)} }
.zone-icon  { font-size:22px; margin-bottom:6px; }
.zone-name  { font-size:11px; font-weight:600; }
.zone-count { font-size:22px; font-weight:800; font-family:var(--font-data); margin:3px 0; }
.zone-status { font-size:9px; font-family:var(--font-mono); letter-spacing:1px; }
.zone-status.Secure  { color:var(--green); }
.zone-status.Crowded { color:var(--amber); }
.zone-status.Breach  { color:var(--red); }
.incident-item {
  padding:11px 14px; border-bottom:1px solid var(--border);
  display:flex; align-items:flex-start; gap:10px; transition:background .2s;
}
.incident-item:hover { background:var(--bg-hover); }
.incident-item:last-child { border-bottom:none; }
.inc-type { width:30px; height:30px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; margin-top:1px; }
.inc-type.critical { background:var(--red-dim); }
.inc-type.warning  { background:var(--amber-dim); }
.inc-type.info     { background:var(--cyan-glow); }
.inc-body { flex:1; min-width:0; }
.inc-id   { font-family:var(--font-mono); font-size:10px; color:var(--cyan); margin-bottom:2px; }
.inc-desc { font-size:12px; }
.inc-time { font-family:var(--font-mono); font-size:9px; color:var(--text-secondary); }
.inc-resolve {
  padding:3px 9px; border-radius:5px; background:var(--green-dim);
  color:var(--green); border:1px solid var(--green-dim);
  font-size:9px; cursor:pointer; font-family:var(--font-ui); font-weight:600; flex-shrink:0;
}

/* ══ ADMIN ══ */
.admin-section-title { font-size:13px; font-weight:700; margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:8px; }
.settings-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.setting-row {
  display:flex; align-items:center; justify-content:space-between;
  padding:11px 13px; background:var(--bg-panel); border:1px solid var(--border); border-radius:8px;
}
.setting-label { font-size:12px; font-weight:500; }
.setting-desc  { font-size:10px; color:var(--text-secondary); margin-top:2px; }
.toggle { width:36px; height:19px; border-radius:10px; background:var(--border); position:relative; cursor:pointer; transition:background .3s; flex-shrink:0; }
.toggle.on { background:var(--cyan); }
.toggle::after { content:''; position:absolute; width:13px; height:13px; border-radius:50%; background:#fff; top:3px; left:3px; transition:left .3s; }
.toggle.on::after { left:20px; }
.enrollment-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
.enroll-stat { padding:12px; background:var(--bg-panel); border:1px solid var(--border); border-radius:8px; text-align:center; }
.enroll-val { font-size:24px; font-weight:800; font-family:var(--font-data); }
.enroll-lbl { font-size:9px; color:var(--text-secondary); font-family:var(--font-mono); margin-top:3px; }
.add-student-form { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-label { font-size:9px; color:var(--text-secondary); font-family:var(--font-mono); letter-spacing:1.5px; }
.form-input {
  background:var(--bg-panel); border:1px solid var(--border); border-radius:7px;
  padding:9px 12px; color:var(--text-primary); font-size:12px;
  font-family:var(--font-ui); outline:none;
}
.form-input:focus { border-color:var(--cyan-dim); box-shadow:0 0 0 3px var(--cyan-glow); }
.form-full { grid-column:1/-1; }

/* TERMINAL */
.terminal {
  background:#000; border:1px solid var(--border); border-radius:8px;
  padding:10px 12px; font-family:var(--font-mono); font-size:10px;
  height:120px; overflow-y:auto; margin-top:10px;
}
.terminal::-webkit-scrollbar { width:3px; }
.terminal::-webkit-scrollbar-thumb { background:var(--border); }
.term-line { margin-bottom:2px; line-height:1.5; }
.term-ok   { color:var(--green); }
.term-err  { color:var(--red); }
.term-warn { color:var(--amber); }
.term-dim  { color:var(--text-secondary); }
.term-cyan { color:var(--cyan); }
.term-cursor { animation:blink .8s infinite; display:inline-block; }
@keyframes blink { 50%{opacity:0} }

/* MODAL */
.modal-overlay {
  position:fixed; inset:0; background:rgba(0,0,0,.7);
  display:flex; align-items:center; justify-content:center;
  z-index:500; backdrop-filter:blur(4px);
}
.modal-box {
  background:var(--bg-card); border:1px solid var(--border); border-radius:16px;
  padding:24px; width:90%; max-width:560px; max-height:90vh;
  overflow-y:auto; position:relative;
  box-shadow:0 20px 60px rgba(0,0,0,.6);
  animation:fadeIn .2s ease;
}
.modal-box::-webkit-scrollbar { width:4px; }
.modal-box::-webkit-scrollbar-thumb { background:var(--border); }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
.modal-title  { font-size:16px; font-weight:700; }
.modal-close  { background:none; border:none; color:var(--text-secondary); font-size:18px; cursor:pointer; padding:4px; border-radius:6px; transition:color .2s; }
.modal-close:hover { color:var(--red); }

/* ACTIVITY / SCROLLABLE */
.activity-item { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid var(--border); }
.activity-item:last-child { border-bottom:none; }
.act-dot { width:7px; height:7px; border-radius:50%; margin-top:5px; flex-shrink:0; }
.act-dot.green  { background:var(--green);  box-shadow:0 0 6px var(--green); }
.act-dot.red    { background:var(--red);    box-shadow:0 0 6px var(--red); }
.act-dot.amber  { background:var(--amber);  box-shadow:0 0 6px var(--amber); }
.act-dot.cyan   { background:var(--cyan);   box-shadow:0 0 6px var(--cyan); }
.act-dot.purple { background:var(--purple); box-shadow:0 0 6px var(--purple); }
.act-content { flex:1; min-width:0; }
.act-msg  { font-size:11px; }
.act-time { font-size:9px; color:var(--text-secondary); font-family:var(--font-mono); margin-top:1px; }
.scrollable { overflow-y:auto; }
.scrollable::-webkit-scrollbar { width:3px; }
.scrollable::-webkit-scrollbar-thumb { background:var(--border); }
.sep { height:1px; background:var(--border); margin:12px 0; }

/* FEEDBACK BOXES */
.result-granted { background:var(--green-dim); border:1px solid var(--green); border-radius:10px; padding:12px; display:flex; align-items:center; gap:10px; }
.result-denied  { background:var(--red-dim);   border:1px solid var(--red);   border-radius:10px; padding:12px; display:flex; align-items:center; gap:10px; }

/* RESPONSIVE */
@media (max-width:1100px) {
  .stats-grid  { grid-template-columns:repeat(2,1fr); }
  .feeds-grid  { grid-template-columns:repeat(2,1fr); }
  .zone-grid   { grid-template-columns:repeat(2,1fr); }
  .settings-grid { grid-template-columns:1fr; }
}
@media (max-width:768px) {
  :root { --sidebar-w:0px; }
  .sidebar { position:fixed; top:0; left:0; bottom:0; width:240px; transform:translateX(-100%); }
  .sidebar.open { transform:translateX(0); }
  .menu-toggle { display:block; }
  .grid-2,.grid-3 { grid-template-columns:1fr; }
  .rfid-grid { grid-template-columns:1fr; }
  .feeds-grid { grid-template-columns:1fr; }
  .add-student-form { grid-template-columns:1fr; }
}
