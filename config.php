// ============================================================
//  EduShield — Main Application JS
//  assets/js/app.js
// ============================================================

'use strict';

// ── State ──────────────────────────────────────────────────
const State = {
  currentPage: 'dashboard',
  charts: {},
  students: [],
  studentPage: 1,
  totalStudents: 0,
  qrInstance: null,
  scanIdx: 0,
  pollTimers: {},
  notifOpen: false,
};

// ── API ────────────────────────────────────────────────────
async function api(endpoint, params = {}, method = 'GET', body = null) {
  const url = new URL(APP_BASE + endpoint, location.origin);
  if (method === 'GET') Object.keys(params).forEach(k => url.searchParams.set(k, params[k]));
  try {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: body ? JSON.stringify(body) : undefined,
    });
    return await res.json();
  } catch (e) {
    console.error('[API Error]', endpoint, e);
    return { success: false, error: e.message };
  }
}

// ── Clock ──────────────────────────────────────────────────
function startClock() {
  const el = document.getElementById('liveClock');
  setInterval(() => { if (el) el.textContent = new Date().toTimeString().slice(0, 8); }, 1000);
}

// ── Navigation ─────────────────────────────────────────────
const pageLabels = {
  dashboard:'Dashboard', students:'Student Management', attendance:'Attendance Records',
  livefeed:'Live Security Feed', safety:'Safety Monitor', qrscanner:'QR Scanner',
  rfid:'RFID Status', admin:'Admin Panel'
};
const pageCrumbs = {
  dashboard:'Overview › Main Dashboard', students:'Students › Manage',
  attendance:'Records › Attendance', livefeed:'Monitoring › Live CCTV',
  safety:'Safety › Zone Monitor', qrscanner:'ID › QR Scanner',
  rfid:'ID › RFID Status', admin:'System › Admin Panel'
};

function navigate(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const pageEl = document.getElementById('page-' + page);
  if (!pageEl) return;
  pageEl.classList.add('active');
  document.querySelector(`.nav-item[data-page="${page}"]`)?.classList.add('active');
  document.getElementById('topTitle').textContent = pageLabels[page] || page;
  document.getElementById('topCrumb').textContent = pageCrumbs[page] || '';
  State.currentPage = page;
  loadPage(page);
  history.replaceState(null, '', '#' + page);
  // Close sidebar on mobile
  document.getElementById('sidebar').classList.remove('open');
}

function loadPage(page) {
  clearPageTimers();
  switch (page) {
    case 'dashboard':   initDashboard();   break;
    case 'students':    loadStudents();    break;
    case 'attendance':  initAttendance();  break;
    case 'livefeed':    initLiveFeed();    break;
    case 'safety':      loadSafety();      break;
    case 'qrscanner':   initQRPage();      break;
    case 'rfid':        initRFID();        break;
    case 'admin':       initAdmin();       break;
  }
}

function clearPageTimers() {
  Object.values(State.pollTimers).forEach(clearInterval);
  State.pollTimers = {};
}

// ── Chart defaults ─────────────────────────────────────────
Chart.defaults.color          = '#6b82a0';
Chart.defaults.borderColor    = '#1e2d42';
Chart.defaults.font.family    = "'Rajdhani', sans-serif";
Chart.defaults.font.size      = 11;

function makeChart(id, cfg) {
  if (State.charts[id]) State.charts[id].destroy();
  const canvas = document.getElementById(id);
  if (!canvas) return null;
  const chart = new Chart(canvas, cfg);
  State.charts[id] = chart;
  return chart;
}

// ═══════════════════════════════════════════════════════════
//   DASHBOARD
// ═══════════════════════════════════════════════════════════
async function initDashboard() {
  const res = await api('/api/stats.php', { action: 'dashboard' });
  if (!res.success) return;
  const d = res.data;

  // Stat cards
  const statsGrid = document.getElementById('dashStats');
  statsGrid.innerHTML = `
    <div class="card stat-card">
      <div class="stat-top"><div class="stat-icon cyan">👥</div><div class="stat-delta up">ENROLLED</div></div>
      <div class="stat-value" style="color:var(--cyan)">${d.total}</div>
      <div class="stat-label">Total Students</div>
      <div class="stat-bar"><div class="stat-bar-fill" style="width:95%;background:var(--cyan)"></div></div>
    </div>
    <div class="card stat-card">
      <div class="stat-top"><div class="stat-icon green">✅</div><div class="stat-delta up">↑ TODAY</div></div>
      <div class="stat-value" style="color:var(--green)">${d.present}</div>
      <div class="stat-label">Present Today</div>
      <div class="stat-bar"><div class="stat-bar-fill" style="width:${Math.round(d.present/d.total*100)}%;background:var(--green)"></div></div>
    </div>
    <div class="card stat-card">
      <div class="stat-top"><div class="stat-icon red">❌</div><div class="stat-delta down">ABSENT</div></div>
      <div class="stat-value" style="color:var(--red)">${d.absent}</div>
      <div class="stat-label">Absent Today</div>
      <div class="stat-bar"><div class="stat-bar-fill" style="width:${Math.round(d.absent/d.total*100)}%;background:var(--red)"></div></div>
    </div>
    <div class="card stat-card">
      <div class="stat-top"><div class="stat-icon amber">🚨</div><div class="stat-delta ${d.alerts>0?'down':'up'}">${d.alerts>0?'ACTIVE':'CLEAR'}</div></div>
      <div class="stat-value" style="color:var(--amber)">${d.alerts}</div>
      <div class="stat-label">Safety Alerts Open</div>
      <div class="stat-bar"><div class="stat-bar-fill" style="width:${Math.min(100,d.alerts*10)}%;background:var(--amber)"></div></div>
    </div>`;

  // Badge updates
  const nb = document.getElementById('nb-students');
  if (nb) nb.textContent = d.total;
  const na = document.getElementById('nb-alerts');
  if (na) { na.textContent = d.alerts; na.style.display = d.alerts > 0 ? '' : 'none'; }
  const pip = document.getElementById('alertPip');
  if (pip) pip.style.display = d.alerts > 0 ? '' : 'none';

  // Weekly chart
  const weekLabels = d.weekly.map(w => w.label);
  const weekPresent = d.weekly.map(w => +w.present);
  const weekAbsent  = d.weekly.map(w => +w.absent);
  makeChart('weeklyChart', {
    type: 'bar',
    data: {
      labels: weekLabels,
      datasets: [
        { label:'Present', data:weekPresent, backgroundColor:'rgba(0,212,255,.65)', borderRadius:5, barPercentage:.55 },
        { label:'Absent',  data:weekAbsent,  backgroundColor:'rgba(255,56,85,.45)', borderRadius:5, barPercentage:.55 }
      ]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}},y:{grid:{color:'#1e2d42'}}} }
  });

  // Pie chart
  const total2 = d.present+d.absent+d.late+d.excused||1;
  makeChart('pieChart', {
    type: 'doughnut',
    data: {
      labels: ['Present','Absent','Late','Excused'],
      datasets:[{ data:[d.present,d.absent,d.late,d.excused],
        backgroundColor:['#00e58a88','#ff385588','#ffb80088','#b44dff88'],
        borderWidth:2, borderColor:'#101828' }]
    },
    options:{ responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'right',labels:{boxWidth:10,padding:8,font:{size:10}}}},cutout:'60%' }
  });

  // Monthly trend (load separately)
  loadMonthlyTrend();

  // Class breakdown
  const cLabels = d.classes.map(c => c.class_name.replace('Form ','F'));
  const cRates  = d.classes.map(c => c.total > 0 ? Math.round(c.present/c.total*100) : 0);
  makeChart('classChart', {
    type:'bar',
    data:{ labels:cLabels,
      datasets:[{ label:'%', data:cRates,
        backgroundColor:'rgba(0,212,255,.5)', borderRadius:4 }] },
    options:{ responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}},y:{min:0,max:100,grid:{color:'#1e2d42'}}} }
  });

  // Heatmap
  const heatHours = Array.from({length:12},(_,i)=>i+6);
  const heatMap = {};
  d.heat.forEach(h => heatMap[+h.h] = +h.cnt);
  makeChart('heatmapChart', {
    type:'bar',
    data:{ labels:heatHours.map(h=>`${h}:00`),
      datasets:[{ label:'Scans', data:heatHours.map(h=>heatMap[h]||0),
        backgroundColor:'rgba(180,77,255,.55)', borderRadius:4 }] },
    options:{ responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}},y:{grid:{color:'#1e2d42'}}} }
  });

  // Activity feed
  renderActivity(d.activity);

  // Poll every 30s
  State.pollTimers.dashboard = setInterval(initDashboard, 30000);
}

async function loadMonthlyTrend() {
  const res = await api('/api/stats.php', { action:'monthly' });
  if (!res.success) return;
  const m = res.data.monthly;
  const inc = res.data.incMonthly;
  const incMap = {};
  inc.forEach(i => incMap[i.ym] = +i.cnt);
  makeChart('trendChart', {
    type:'line',
    data:{ labels:m.map(r=>r.ym),
      datasets:[
        { label:'Attendance %', data:m.map(r=>+r.rate), borderColor:'#00d4ff', backgroundColor:'rgba(0,212,255,.07)', fill:true, tension:.4, borderWidth:2, pointRadius:3 },
        { label:'Incidents',    data:m.map(r=>incMap[r.ym]||0), borderColor:'#ff3855', backgroundColor:'rgba(255,56,85,.07)', fill:true, tension:.4, borderWidth:2, pointRadius:3 }
      ] },
    options:{ responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'top',labels:{boxWidth:10}}},
      scales:{x:{grid:{display:false}},y:{grid:{color:'#1e2d42'}}} }
  });
}

function renderActivity(activities) {
  const el = document.getElementById('activityFeed');
  if (!el) return;
  const colorMap = { attendance:'green', rfid:'cyan', safety:'red', qr:'cyan' };
  el.innerHTML = (activities||[]).map(a => `
    <div class="activity-item">
      <div class="act-dot ${colorMap[a.type]||'cyan'}"></div>
      <div class="act-content">
        <div class="act-msg">${esc(a.msg)}</div>
        <div class="act-time">${a.ts ? timeAgo(a.ts) : ''}</div>
      </div>
    </div>`).join('') || '<div style="padding:20px;text-align:center;color:var(--text-secondary);font-family:var(--font-mono);font-size:11px">No activity today</div>';
}

// ═══════════════════════════════════════════════════════════
//   STUDENTS
// ═══════════════════════════════════════════════════════════
async function loadStudents(page = 1) {
  State.studentPage = page;
  const q   = document.getElementById('studentSearch')?.value || '';
  const st  = document.getElementById('statusFilter')?.value  || '';
  const cls = document.getElementById('classFilter')?.value   || '';

  const res = await api('/api/students.php', { action:'list', q, status:st, class:cls, page });
  if (!res.success) return;
  const { students, total, per_page, counts } = res.data;

  State.students      = students;
  State.totalStudents = total;

  const tbody = document.getElementById('studentTableBody');
  const noRes = document.getElementById('noStudents');

  if (!students.length) {
    tbody.innerHTML = '';
    noRes.style.display = 'block';
  } else {
    noRes.style.display = 'none';
    tbody.innerHTML = students.map(s => `
      <tr>
        <td><span class="reg-num">${esc(s.reg_number)}</span></td>
        <td style="font-weight:600">${esc(s.full_name)}</td>
        <td>${esc(s.class_name||'—')}</td>
        <td><span class="badge ${(s.status||'Absent').toLowerCase()}">${esc(s.status||'Absent')}</span></td>
        <td style="font-family:var(--font-mono);font-size:11px">${s.check_in_time||'—'}</td>
        <td><span style="font-size:10px;color:var(--purple);font-family:var(--font-mono)">${esc(s.method||'—')}</span></td>
        <td style="font-family:var(--font-mono);font-size:11px;color:var(--purple)">${esc(s.rfid_tag||'—')}</td>
        <td>${s.safety_flag=='1'?'<span class="badge absent">⚠ FLAGGED</span>':'<span style="color:var(--text-dim);font-size:10px">—</span>'}</td>
        <td style="white-space:nowrap">
          <button class="action-btn" onclick="viewStudent(${s.id})">Profile</button>
          <button class="action-btn" style="margin-left:4px" onclick="markAtt(${s.id},'${s.reg_number}','${s.status||'Absent'}')">Mark</button>
        </td>
      </tr>`).join('');
  }

  // Counts
  if (counts) {
    document.getElementById('cnt-total').textContent   = total;
    document.getElementById('cnt-present').textContent = counts.pres||0;
    document.getElementById('cnt-absent').textContent  = counts.abs||0;
    document.getElementById('cnt-late').textContent    = counts.late||0;
  }

  // Pagination
  const pages = Math.ceil(total / per_page);
  const pag   = document.getElementById('studentPagination');
  if (pag && pages > 1) {
    let html = '';
    for (let i=1;i<=pages;i++) {
      html += `<button class="page-btn ${i===page?'active':''}" onclick="loadStudents(${i})">${i}</button>`;
    }
    pag.innerHTML = html;
  }

  loadClassSelect('classFilter', null, cls);
}

async function viewStudent(id) {
  const res = await api('/api/students.php', { action:'get', id });
  if (!res.success) return;
  const { student: s, attendance } = res.data;
  document.getElementById('studentProfileBody').innerHTML = `
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px">
      <div style="width:60px;height:60px;border-radius:14px;background:linear-gradient(135deg,var(--cyan-dim),var(--purple-dim));border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:26px">👤</div>
      <div>
        <div style="font-size:18px;font-weight:800">${esc(s.full_name)}</div>
        <div class="reg-num">${esc(s.reg_number)}</div>
        <div style="font-size:11px;color:var(--text-secondary)">${esc(s.class_name||'—')} · ${esc(s.gender)}</div>
      </div>
      <div style="margin-left:auto"><span class="badge ${(s.today_status||'Absent').toLowerCase()}">${esc(s.today_status||'Absent')}</span></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
      <div class="enroll-stat"><div class="enroll-lbl">RFID TAG</div><div style="font-family:var(--font-mono);font-size:13px;color:var(--purple);margin-top:4px">${esc(s.rfid_tag||'Not assigned')}</div></div>
      <div class="enroll-stat"><div class="enroll-lbl">GUARDIAN</div><div style="font-size:12px;margin-top:4px">${esc(s.guardian_name||'—')}</div></div>
      <div class="enroll-stat"><div class="enroll-lbl">PHONE</div><div style="font-family:var(--font-mono);font-size:12px;color:var(--cyan);margin-top:4px">${esc(s.guardian_phone||'—')}</div></div>
      <div class="enroll-stat"><div class="enroll-lbl">CHECK-IN TODAY</div><div style="font-family:var(--font-mono);font-size:12px;color:var(--green);margin-top:4px">${s.check_in_time||'Not yet'}</div></div>
    </div>
    <div style="font-size:10px;letter-spacing:2px;color:var(--text-dim);font-family:var(--font-mono);margin-bottom:8px">RECENT ATTENDANCE</div>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>DATE</th><th>STATUS</th><th>CHECK-IN</th><th>METHOD</th></tr></thead>
        <tbody>${attendance.map(a=>`<tr><td style="font-family:var(--font-mono);font-size:11px">${a.attendance_date}</td><td><span class="badge ${a.status.toLowerCase()}">${a.status}</span></td><td style="font-family:var(--font-mono);font-size:11px">${a.check_in_time||'—'}</td><td style="font-size:11px;color:var(--purple)">${a.method||'—'}</td></tr>`).join('')}</tbody>
      </table>
    </div>
    <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
      <button class="btn btn-primary" onclick="markAtt(${s.id},'${esc(s.reg_number)}','${s.today_status||'Absent'}');closeModal('studentProfileModal')">Update Attendance</button>
      <button class="btn btn-ghost" onclick="closeModal('studentProfileModal')">Close</button>
    </div>`;
  openModal('studentProfileModal');
}

const STATUS_CYCLE = ['Present','Late','Absent','Excused'];
async function markAtt(id, reg, currentStatus) {
  const opts = STATUS_CYCLE;
  const next = opts[(opts.indexOf(currentStatus) + 1) % opts.length];
  const confirm = window.confirm(`Change ${reg} status to: ${next}?`);
  if (!confirm) return;
  const res = await api('/api/students.php', {}, 'POST', { id, status: next, action: '' });
  // Direct fetch
  const r = await fetch(APP_BASE + '/api/students.php?action=update_status', {
    method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ id, status: next })
  });
  const j = await r.json();
  if (j.success) { loadStudents(State.studentPage); termLog('Attendance updated: '+reg+' → '+next,'ok'); }
  else alert(j.error || 'Update failed');
}

async function loadClassSelect(selectId, defaultVal, selectedVal) {
  const sel = document.getElementById(selectId);
  if (!sel || sel.dataset.loaded) return;
  const res = await api('/api/students.php', { action:'classes' });
  if (!res.success) return;
  const classes = res.data.classes;
  if (selectId.startsWith('a') || selectId.startsWith('m')) {
    sel.innerHTML = classes.map(c => `<option value="${c.id}" ${selectedVal==c.id?'selected':''}>${esc(c.class_name)}</option>`).join('');
  }
  sel.dataset.loaded = '1';
}

// ═══════════════════════════════════════════════════════════
//   ATTENDANCE
// ═══════════════════════════════════════════════════════════
let bigAttChart = null;
async function initAttendance() {
  const res = await api('/api/stats.php', { action:'dashboard' });
  if (!res.success) return;
  const d = res.data;

  // Summary stats
  const pct = d.total > 0 ? Math.round(d.present/d.total*100) : 0;
  document.getElementById('attStats').innerHTML = `
    <div class="card stat-card"><div class="stat-icon cyan" style="margin-bottom:8px">📅</div>
      <div class="stat-value" style="color:var(--cyan);font-size:22px">${pct}%</div>
      <div class="stat-label">Today's Rate</div></div>
    <div class="card stat-card"><div class="stat-icon green" style="margin-bottom:8px">✅</div>
      <div class="stat-value" style="color:var(--green);font-size:22px">${d.present}</div>
      <div class="stat-label">Present</div></div>
    <div class="card stat-card"><div class="stat-icon red" style="margin-bottom:8px">❌</div>
      <div class="stat-value" style="color:var(--red);font-size:22px">${d.absent}</div>
      <div class="stat-label">Absent</div></div>`;

  // Big chart (weekly default)
  buildBigAttChart('week');

  // Class bar
  const cLabels = d.classes.map(c => c.class_name.replace('Form ','F'));
  const cRates  = d.classes.map(c => c.total>0?Math.round(c.present/c.total*100):0);
  makeChart('classBarChart', {
    type:'bar', data:{ labels:cLabels,
      datasets:[{ label:'Rate%', data:cRates, backgroundColor:'rgba(0,229,138,.5)', borderRadius:4 }] },
    options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}},
      scales:{x:{min:0,max:100,grid:{color:'#1e2d42'}},y:{grid:{display:false}}} }
  });

  // Timeline
  const hh = d.heat||[];
  const hourMap = {};
  hh.forEach(h => hourMap[+h.h] = +h.cnt);
  const hrs = Array.from({length:12},(_,i)=>i+6);
  makeChart('timelineChart', {
    type:'line', data:{ labels:hrs.map(h=>`${h}:00`),
      datasets:[{ label:'Check-ins', data:hrs.map(h=>hourMap[h]||0),
        borderColor:'#00e58a', backgroundColor:'rgba(0,229,138,.1)', fill:true, tension:.4, borderWidth:2, pointRadius:3 }] },
    options:{ responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}},y:{grid:{color:'#1e2d42'}}} }
  });

  // Attendance log table
  loadAttLog();
}

async function buildBigAttChart(view) {
  const res = await api('/api/stats.php', { action:'monthly' });
  if (!res.success) return;
  const monthly = res.data.monthly;
  const labels = monthly.map(m=>m.ym);
  const data   = monthly.map(m=>+m.rate);
  bigAttChart = makeChart('bigAttChart', {
    type:'line', data:{ labels,
      datasets:[{ label:'Attendance %', data, borderColor:'#00d4ff', backgroundColor:'rgba(0,212,255,.07)', fill:true, tension:.4, borderWidth:2 }] },
    options:{ responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}},y:{min:50,max:100,grid:{color:'#1e2d42'}}} }
  });
}

function setAttView(view, el) {
  document.querySelectorAll('#page-attendance .tab-btn').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  buildBigAttChart(view);
}

async function loadAttLog() {
  const res = await api('/api/students.php', { action:'list', page:1 });
  if (!res.success) return;
  document.getElementById('attLogBody').innerHTML = res.data.students.map(s=>`
    <tr>
      <td class="reg-num">${esc(s.reg_number)}</td>
      <td>${esc(s.full_name)}</td>
      <td>${esc(s.class_name||'—')}</td>
      <td><span class="badge ${(s.status||'Absent').toLowerCase()}">${esc(s.status||'Absent')}</span></td>
      <td style="font-family:var(--font-mono);font-size:11px">${s.check_in_time||'—'}</td>
      <td style="font-size:11px;color:var(--purple)">${esc(s.method||'—')}</td>
      <td style="font-size:11px;color:var(--text-secondary)">Main Gate</td>
    </tr>`).join('');
}

// ═══════════════════════════════════════════════════════════
//   LIVE FEED
// ═══════════════════════════════════════════════════════════
async function initLiveFeed() {
  loadCameras();
  State.pollTimers.livefeed = setInterval(loadCameras, 6000);
}

async function loadCameras() {
  const res = await api('/api/safety.php', { action:'cameras' });
  if (!res.success) return;
  const cams = res.data.cameras;

  const online = cams.filter(c=>c.status==='Online').length;
  const status = document.getElementById('feedStatus');
  const camOnline = document.getElementById('camOnline');
  if (status)   status.textContent = `● ${online}/${cams.length} CAMERAS OPERATIONAL`;
  if (camOnline) camOnline.textContent = `${online}/${cams.length} ONLINE`;

  document.getElementById('feedsGrid').innerHTML = cams.map(c => {
    const isAlert = c.alert || c.status === 'Alert';
    const persons = c.simulated_persons || c.current_count || 0;
    return `
    <div class="feed-cell ${isAlert?'alert-feed':''}">
      <div class="feed-bg"></div>
      <div class="feed-sim"></div>
      <div class="feed-scan-line"></div>
      ${Array.from({length:Math.min(3,Math.max(1,Math.floor(persons/20)+1))}).map((_,i)=>
        `<div class="feed-person" style="left:${20+i*28}%;top:${38+i*12}%"></div>`).join('')}
      <div class="feed-crosshair"></div>
      <div class="feed-overlay"></div>
      <div class="feed-hud">
        <div class="feed-top">
          <div class="feed-cam-id">${esc(c.camera_code)}</div>
          <div class="feed-live"><div class="feed-live-dot"></div>${isAlert?'⚠ ALERT':'LIVE'}</div>
        </div>
        <div class="feed-bottom">
          <div class="feed-location">${esc(c.location)}</div>
          <div class="feed-count">${persons} person${persons!==1?'s':''} detected</div>
        </div>
      </div>
    </div>`;
  }).join('');

  // Detection log
  const now = new Date();
  document.getElementById('cameraDetLog').innerHTML = cams.map((c,i)=>`
    <div class="activity-item">
      <div class="act-dot ${c.status==='Alert'?'red':c.status==='Offline'?'amber':'cyan'}"></div>
      <div class="act-content">
        <div class="act-msg" style="font-size:11px;font-family:var(--font-mono)">${esc(c.camera_code)} · ${esc(c.location)}</div>
        <div class="act-msg" style="font-size:11px">${c.simulated_persons||c.current_count||0} persons · ${esc(c.status)}</div>
        <div class="act-time">${new Date(now-i*60000).toTimeString().slice(0,8)}</div>
      </div>
    </div>`).join('');

  // Occupancy chart
  makeChart('occupancyChart', {
    type:'doughnut',
    data:{ labels:cams.map(c=>c.location||c.zone_name),
      datasets:[{ data:cams.map(c=>c.simulated_persons||c.current_count||0),
        backgroundColor:['#00d4ffaa','#00e58aaa','#ffb800aa','#ff3855aa','#b44dffaa','#3a4d62'],
        borderWidth:2, borderColor:'#101828' }] },
    options:{ responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'right',labels:{boxWidth:10,padding:8,font:{size:10}}}},cutout:'60%' }
  });
}

// ═══════════════════════════════════════════════════════════
//   QR SCANNER
// ═══════════════════════════════════════════════════════════
function initQRPage() {
  loadQRLog();
}

async function generateQR() {
  const reg = document.getElementById('qrRegInput')?.value.trim();
  if (!reg) return alert('Please enter a registration number');

  const res = await api('/api/qr.php', { action:'generate', reg });
  if (!res.success) return showToast(res.error || 'Student not found', 'error');

  const s   = res.data.student;
  const out = document.getElementById('qrOutput');
  out.innerHTML = '';
  if (State.qrInstance) { try { State.qrInstance.clear(); } catch(e){} State.qrInstance = null; }

  const qrData = `EDUSHIELD:${s.reg_number}:${s.qr_token}:${new Date().toISOString().slice(0,10)}`;
  try {
    State.qrInstance = new QRCode(out, {
      text: qrData, width:100, height:100,
      colorDark:'#00d4ff', colorLight:'#060a12',
      correctLevel: QRCode.CorrectLevel.H
    });
  } catch(e) {
    out.innerHTML = `<div style="font-family:var(--font-mono);font-size:9px;color:var(--cyan);text-align:center;word-break:break-all;padding:4px">${esc(s.reg_number)}</div>`;
  }

  document.getElementById('qrStudentMeta').style.display = 'block';
  document.getElementById('qrSName').textContent  = s.full_name;
  document.getElementById('qrSClass').textContent = (s.class_name||'') + ' · ' + s.reg_number;
  document.getElementById('qrDlBtn').style.display = 'block';
  termLog('QR generated for '+reg+' — '+s.full_name, 'ok');
}

function downloadQR() {
  const canvas = document.querySelector('#qrOutput canvas');
  if (canvas) {
    const a = document.createElement('a');
    a.download = `qr_${document.getElementById('qrRegInput').value.replace(/[^a-zA-Z0-9-]/g,'_')}.png`;
    a.href = canvas.toDataURL();
    a.click();
  } else { alert('No QR to download. Generate one first.'); }
}

async function simulateScan() {
  const box    = document.getElementById('qrScanBox');
  const line   = document.getElementById('qrScanLine');
  const status = document.getElementById('scanStatus');
  box.classList.add('scanning');
  if (line) line.style.display = 'block';
  status.textContent = 'SCANNING...';

  // Pick a random student reg
  const regs = ['STU-2024-001','STU-2024-003','STU-2024-005','STU-2024-009','STU-2024-014','STU-2024-015'];
  const reg  = regs[State.scanIdx++ % regs.length];

  await new Promise(r => setTimeout(r, 1800));
  box.classList.remove('scanning');
  if (line) line.style.display = 'none';

  const r2 = await fetch(APP_BASE + '/api/qr.php?action=verify', {
    method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ reg, token:'', location:'Main Gate' })
  });
  const j = await r2.json();

  const fb = document.getElementById('scanFeedback');
  if (j.success) {
    const s = j.data.student;
    fb.innerHTML = `<div class="result-granted"><div style="font-size:26px">✅</div><div><div style="font-weight:700;color:var(--green)">${esc(s.full_name)}</div><div style="font-size:11px;font-family:var(--font-mono);color:var(--text-secondary)">${esc(s.reg_number)} · ${esc(s.class_name||'')} · ${new Date().toTimeString().slice(0,8)}</div></div></div>`;
    status.textContent = 'ACCESS GRANTED';
    termLog('QR scan: '+s.reg_number+' — ACCESS GRANTED', 'ok');
  } else {
    fb.innerHTML = `<div class="result-denied"><div style="font-size:26px">❌</div><div><div style="font-weight:700;color:var(--red)">ACCESS DENIED</div><div style="font-size:11px;font-family:var(--font-mono)">${esc(j.error||'Unknown token')}</div></div></div>`;
    status.textContent = 'SCAN FAILED';
  }
  setTimeout(() => { fb.innerHTML = ''; status.textContent = 'CLICK TO SIMULATE SCAN'; }, 4000);
  loadQRLog();
}

async function manualVerify() {
  const val = document.getElementById('manualQRInput')?.value.trim();
  if (!val) return;
  const r = await fetch(APP_BASE + '/api/qr.php?action=verify', {
    method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ reg:val, token:val, location:'Manual Verify' })
  });
  const j = await r.json();
  const el = document.getElementById('manualResult');
  if (j.success) {
    const s = j.data.student;
    el.innerHTML = `<div class="result-granted"><div style="font-size:20px">✅</div><div><b>${esc(s.full_name)}</b><br><span style="font-size:10px;font-family:var(--font-mono)">${esc(s.reg_number)} · ${esc(s.class_name||'')}</span></div></div>`;
  } else {
    el.innerHTML = `<div class="result-denied"><div style="font-size:20px">❌</div><div><b>Not Found</b><br><span style="font-size:10px;font-family:var(--font-mono)">${esc(j.error||'Invalid')}</span></div></div>`;
  }
  loadQRLog();
}

async function loadQRLog() {
  const res = await api('/api/qr.php', { action:'log' });
  if (!res.success) return;
  document.getElementById('qrLogBody').innerHTML = res.data.logs.map(q=>`
    <tr>
      <td style="font-family:var(--font-mono);font-size:10px">${q.scanned_at||'—'}</td>
      <td style="font-weight:600">${esc(q.full_name||'Unknown')}</td>
      <td class="reg-num">${esc(q.reg_number||'—')}</td>
      <td style="font-size:11px;color:var(--text-secondary)">${esc(q.location||'—')}</td>
      <td><span class="badge ${q.scan_result==='Granted'?'present':'absent'}">${esc(q.scan_result)}</span></td>
    </tr>`).join('');
}

// ═══════════════════════════════════════════════════════════
//   RFID
// ═══════════════════════════════════════════════════════════
async function initRFID() {
  loadRFIDStats();
  loadRFIDReaders();
  loadRFIDLogs();
  State.pollTimers.rfid = setInterval(() => { loadRFIDLogs(); loadRFIDReaders(); }, 10000);
}

async function loadRFIDStats() {
  const res = await api('/api/rfid.php', { action:'stats' });
  if (!res.success) return;
  const d = res.data;
  document.getElementById('rfidStats').innerHTML = `
    <div class="card stat-card"><div class="stat-icon cyan" style="margin-bottom:8px">📡</div><div class="stat-value" style="color:var(--cyan);font-size:24px">${d.online}</div><div class="stat-label">Active Readers</div></div>
    <div class="card stat-card"><div class="stat-icon green" style="margin-bottom:8px">✔</div><div class="stat-value" style="color:var(--green);font-size:24px">${d.auth}</div><div class="stat-label">Authorized Today</div></div>
    <div class="card stat-card"><div class="stat-icon red" style="margin-bottom:8px">✖</div><div class="stat-value" style="color:var(--red);font-size:24px">${d.unauth}</div><div class="stat-label">Unauthorized Attempts</div></div>
    <div class="card stat-card"><div class="stat-icon amber" style="margin-bottom:8px">⏳</div><div class="stat-value" style="color:var(--amber);font-size:24px">${d.pending}</div><div class="stat-label">Unregistered RFID</div></div>`;
}

async function loadRFIDReaders() {
  const res = await api('/api/rfid.php', { action:'readers' });
  if (!res.success) return;
  const colorMap = { Online:'var(--green)', Offline:'var(--red)', Degraded:'var(--amber)' };
  document.getElementById('rfidReaders').innerHTML = res.data.readers.map(r=>`
    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg-panel);border:1px solid var(--border);border-radius:8px">
      <div style="font-size:22px">📡</div>
      <div style="flex:1">
        <div style="font-size:12px;font-weight:600">${esc(r.reader_code)} — ${esc(r.location)}</div>
        <div style="font-family:var(--font-mono);font-size:10px;color:${colorMap[r.status]||'var(--text-secondary)'}">
          ${r.status==='Online'?'●':'✖'} ${esc(r.status)} · IP: ${esc(r.ip_address||'—')}
        </div>
      </div>
      <span class="badge ${r.status.toLowerCase()}">${esc(r.status)}</span>
    </div>`).join('');
}

async function loadRFIDLogs() {
  const q  = document.getElementById('rfidSearch')?.value  || '';
  const st = document.getElementById('rfidStatusFilter')?.value || '';
  const res = await api('/api/rfid.php', { action:'logs', q, status:st });
  if (!res.success) return;
  const logs = res.data.logs;

  const colorMap = { Authorized:'green', Unauthorized:'red', Pending:'amber' };
  document.getElementById('rfidLiveLog').innerHTML = logs.slice(0,10).map(l=>`
    <div class="activity-item">
      <div class="act-dot ${colorMap[l.status]||'cyan'}"></div>
      <div class="act-content">
        <div style="font-family:var(--font-mono);font-size:11px;color:var(--cyan)">${esc(l.rfid_tag)}</div>
        <div class="act-msg" style="font-size:11px">${esc(l.full_name||'UNKNOWN')} · ${esc(l.reader_location||'—')}</div>
        <div class="act-time">${esc(l.scanned_at)} · <span style="color:${l.status==='Authorized'?'var(--green)':'var(--red)'}">${esc(l.status)}</span></div>
      </div>
    </div>`).join('');

  document.getElementById('rfidRegistry').innerHTML = logs.slice(0,20).map(l=>`
    <div class="rfid-item ${esc(l.status)}">
      <div class="rfid-chip ${l.status==='Authorized'?'auth':l.status==='Unauthorized'?'unauth':'pend'}">
        <div class="rfid-chip-inner"></div>
      </div>
      <div class="rfid-info">
        <div class="rfid-tag">${esc(l.rfid_tag)}</div>
        <div class="rfid-name">${esc(l.full_name||'Unknown')}</div>
        <div class="rfid-meta">${esc(l.class_name||'—')} · ${esc(l.scanned_at?.slice(11,19)||'—')}</div>
      </div>
      <span class="badge ${l.status.toLowerCase()}">${esc(l.status)}</span>
    </div>`).join('');
}

// ═══════════════════════════════════════════════════════════
//   SAFETY
// ═══════════════════════════════════════════════════════════
async function loadSafety() {
  const res = await api('/api/safety.php', { action:'overview' });
  if (!res.success) return;
  const { zones, incidents, alerts } = res.data;

  // Alert banners (open critical incidents)
  const criticals = incidents.filter(i=>i.type==='critical'&&i.status==='Open');
  document.getElementById('alertBanners').innerHTML = criticals.map(inc=>`
    <div class="safety-alert-banner" id="banner-${inc.id}">
      <div style="font-size:20px">🚨</div>
      <div class="alert-msg-text">
        <div class="alert-msg-title">ACTIVE ALERT: ${esc(inc.title)}</div>
        <div class="alert-msg-desc">${esc(inc.description||'')} · ${esc(inc.created_at?.slice(11,19)||'')} · ${esc(inc.zone_name||'')}</div>
      </div>
      <button class="dismiss-btn" onclick="resolveIncident(${inc.id})">RESOLVE</button>
    </div>`).join('');

  // Zones
  document.getElementById('zoneGrid').innerHTML = zones.map(z=>`
    <div class="zone-card ${esc(z.status)}">
      <div class="zone-icon">${esc(z.icon||'🏫')}</div>
      <div class="zone-name">${esc(z.zone_name)}</div>
      <div class="zone-count" style="color:${z.status==='Breach'?'var(--red)':z.status==='Crowded'?'var(--amber)':'var(--green)'}">${z.current_count}</div>
      <div class="zone-status ${esc(z.status)}">${esc(z.status).toUpperCase()}</div>
    </div>`).join('');

  // Incidents
  const typeIcon = { critical:'🚨', warning:'⚠️', info:'ℹ️' };
  document.getElementById('incidentLog').innerHTML = incidents.map(inc=>`
    <div class="incident-item" id="inc-${inc.id}">
      <div class="inc-type ${esc(inc.type)}">${typeIcon[inc.type]||'ℹ️'}</div>
      <div class="inc-body">
        <div class="inc-id">${esc(inc.incident_ref)}</div>
        <div class="inc-desc">${esc(inc.title)}</div>
        <div class="inc-time">${esc(inc.zone_name||'—')} · ${esc(inc.created_at?.slice(0,16)||'')}</div>
      </div>
      ${inc.status==='Open'?`<button class="inc-resolve" onclick="resolveIncident(${inc.id})">RESOLVE</button>`:
        `<span style="font-size:9px;color:var(--text-dim);font-family:var(--font-mono)">${esc(inc.status)}</span>`}
    </div>`).join('');

  // Safety chart
  const zNames = zones.map(z=>z.zone_name.replace('Main ','').slice(0,10));
  const zCounts = zones.map(z=>+z.current_count);
  const zCaps   = zones.map(z=>+z.capacity);
  makeChart('safetyChart', {
    type:'radar',
    data:{ labels:zNames,
      datasets:[
        { label:'Occupancy', data:zCounts, borderColor:'#00d4ff', backgroundColor:'rgba(0,212,255,.1)', borderWidth:2, pointRadius:4 },
        { label:'Capacity',  data:zCaps,   borderColor:'#ff3855', backgroundColor:'rgba(255,56,85,.05)', borderWidth:1.5, pointRadius:3, borderDash:[4,4] }
      ] },
    options:{ responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'top',labels:{boxWidth:10}}},
      scales:{r:{grid:{color:'#1e2d42'},pointLabels:{font:{size:9}},ticks:{display:false}}} }
  });

  State.pollTimers.safety = setInterval(loadSafety, 20000);
}

async function resolveIncident(id) {
  if (!confirm('Mark this incident as Resolved?')) return;
  const r = await fetch(APP_BASE+'/api/safety.php?action=incidents', {
    method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ sub_action:'resolve', id })
  });
  const j = await r.json();
  if (j.success) { loadSafety(); termLog('Incident '+id+' resolved','ok'); }
}

// ═══════════════════════════════════════════════════════════
//   ADMIN
// ═══════════════════════════════════════════════════════════
async function initAdmin() {
  loadAdminStats();
  loadClassSelect('aClass');
  loadClassSelect('mClass');
  loadAdminNotifs();
  loadSysLog();
  initTerminal();
}

async function loadAdminStats() {
  const res = await api('/api/admin.php', { action:'stats' });
  if (!res.success) return;
  const d = res.data;
  document.getElementById('adminSystemStats').innerHTML = `
    <div class="enroll-stat"><div class="enroll-val" style="color:var(--cyan)">${d.students}</div><div class="enroll-lbl">STUDENTS</div></div>
    <div class="enroll-stat"><div class="enroll-val" style="color:var(--green)">${d.cameras}</div><div class="enroll-lbl">CAMERAS</div></div>
    <div class="enroll-stat"><div class="enroll-val" style="color:var(--amber)">${d.readers}</div><div class="enroll-lbl">RFID READERS</div></div>
    <div class="enroll-stat"><div class="enroll-val" style="color:var(--red)">${d.alerts}</div><div class="enroll-lbl">OPEN ALERTS</div></div>
    <div class="enroll-stat"><div class="enroll-val" style="color:var(--purple)">${d.users}</div><div class="enroll-lbl">ADMIN USERS</div></div>
    <div class="enroll-stat"><div class="enroll-val" style="color:var(--green)">99.8%</div><div class="enroll-lbl">UPTIME</div></div>`;
}

async function loadAdminNotifs() {
  const res = await api('/api/admin.php', { action:'notifications' });
  if (!res.success) return;
  const notifs = res.data.notifications;
  const unread = notifs.filter(n=>!n.is_read).length;
  if (unread > 0) { document.getElementById('alertPip').style.display = ''; }
  document.getElementById('adminNotifList').innerHTML = notifs.map(n=>`
    <div class="notif-item ${n.is_read=='0'?'unread':''}">
      <div class="notif-icon">${n.type==='danger'?'🚨':n.type==='warning'?'⚠️':n.type==='success'?'✅':'ℹ️'}</div>
      <div class="notif-body">
        <div class="notif-title">${esc(n.title)}</div>
        <div class="notif-sub">${esc(n.message||'')}</div>
      </div>
      <div class="notif-time">${timeAgo(n.created_at)}</div>
    </div>`).join('');
  renderNotifPanel(notifs);
}

async function loadSysLog() {
  const res = await api('/api/admin.php', { action:'logs' });
  if (!res.success) return;
  document.getElementById('sysLogBody').innerHTML = res.data.logs.map(l=>`
    <tr>
      <td style="font-family:var(--font-mono);font-size:10px;white-space:nowrap">${l.created_at?.slice(0,16)||''}</td>
      <td style="font-weight:600;font-size:11px">${esc(l.action)}</td>
      <td style="font-size:11px;color:var(--text-secondary)">${esc(l.details||'—')}</td>
      <td style="font-family:var(--font-mono);font-size:10px;color:var(--cyan)">${esc(l.username||'system')}</td>
      <td style="font-family:var(--font-mono);font-size:10px">${esc(l.ip_address||'—')}</td>
    </tr>`).join('');
}

async function registerStudent() {
  const reg     = document.getElementById('aReg')?.value.trim();
  const name    = document.getElementById('aName')?.value.trim();
  const classId = document.getElementById('aClass')?.value;
  const gender  = document.getElementById('aGender')?.value;
  const rfid    = document.getElementById('aRfid')?.value.trim();
  const phone   = document.getElementById('aPhone')?.value.trim();
  const guardian= document.getElementById('aGuardian')?.value.trim();
  const email   = document.getElementById('aEmail')?.value.trim();
  if (!reg||!name) return alert('Registration number and name are required');
  const r = await fetch(APP_BASE+'/api/students.php?action=add', {
    method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ reg_number:reg,full_name:name,class_id:classId,gender,rfid_tag:rfid,guardian_name:guardian,guardian_phone:phone,guardian_email:email })
  });
  const j = await r.json();
  if (j.success) {
    alert('✅ Student '+name+' ('+reg+') registered successfully!');
    ['aReg','aName','aRfid','aPhone','aGuardian','aEmail'].forEach(id=>{ const el=document.getElementById(id); if(el)el.value=''; });
    loadAdminStats();
    termLog('Student registered: '+reg+' — '+name,'ok');
  } else { alert('Error: '+(j.error||'Registration failed')); }
}

async function registerStudentModal() {
  const reg     = document.getElementById('mReg')?.value.trim();
  const name    = document.getElementById('mName')?.value.trim();
  const classId = document.getElementById('mClass')?.value;
  const gender  = document.getElementById('mGender')?.value;
  const rfid    = document.getElementById('mRfid')?.value.trim();
  const phone   = document.getElementById('mPhone')?.value.trim();
  if (!reg||!name) return alert('Registration number and name required');
  const r = await fetch(APP_BASE+'/api/students.php?action=add', {
    method:'POST', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ reg_number:reg,full_name:name,class_id:classId,gender,rfid_tag:rfid,guardian_phone:phone })
  });
  const j = await r.json();
  if (j.success) {
    closeModal('addStudentModal');
    alert('✅ '+name+' registered!');
    loadStudents();
    termLog('Student registered: '+reg,'ok');
  } else { alert(j.error||'Failed'); }
}

// ═══════════════════════════════════════════════════════════
//   NOTIFICATION PANEL
// ═══════════════════════════════════════════════════════════
function renderNotifPanel(notifs) {
  document.getElementById('notifList').innerHTML = notifs.slice(0,10).map(n=>`
    <div class="notif-item ${n.is_read=='0'?'unread':''}">
      <div class="notif-icon">${n.type==='danger'?'🚨':n.type==='warning'?'⚠️':n.type==='success'?'✅':'ℹ️'}</div>
      <div class="notif-body">
        <div class="notif-title">${esc(n.title)}</div>
        <div class="notif-sub">${timeAgo(n.created_at)}</div>
      </div>
    </div>`).join('');
}

function toggleNotifPanel() {
  const p = document.getElementById('notifPanel');
  State.notifOpen = !State.notifOpen;
  p.style.display = State.notifOpen ? 'block' : 'none';
  if (State.notifOpen) loadNotifPanel();
}

async function loadNotifPanel() {
  const res = await api('/api/admin.php', { action:'notifications' });
  if (res.success) renderNotifPanel(res.data.notifications);
}

async function markAllRead() {
  await api('/api/admin.php', { action:'mark_read' }, 'GET');
  document.getElementById('alertPip').style.display = 'none';
  loadNotifPanel();
}

// ═══════════════════════════════════════════════════════════
//   TERMINAL
// ═══════════════════════════════════════════════════════════
const termBootLines = [
  { cls:'term-cyan', t:'[SYS] EduShield v3.4.1 booting...' },
  { cls:'term-ok',   t:'[OK] Database connected: edushield@localhost' },
  { cls:'term-ok',   t:'[OK] 6 cameras online' },
  { cls:'term-ok',   t:'[OK] RFID subsystem: 3/4 readers active' },
  { cls:'term-warn', t:'[WARN] Reader D Lab Block — offline' },
  { cls:'term-ok',   t:'[OK] Attendance sync complete' },
  { cls:'term-ok',   t:'[OK] SMS gateway: connected' },
  { cls:'term-dim',  t:'[INFO] Backup scheduled 23:00 daily' },
];

function initTerminal() {
  const t = document.getElementById('adminTerminal');
  if (!t) return;
  t.innerHTML = '';
  termBootLines.forEach((l,i) => setTimeout(() => {
    const d = document.createElement('div');
    d.className = 'term-line';
    d.innerHTML = `<span class="${l.cls}">${l.t}</span>`;
    t.appendChild(d); t.scrollTop = t.scrollHeight;
  }, i * 200));
  setTimeout(() => {
    const d = document.createElement('div');
    d.className = 'term-line';
    d.innerHTML = '<span class="term-cyan">$ </span><span class="term-cursor">_</span>';
    t.appendChild(d);
  }, termBootLines.length * 200 + 300);
}

function termLog(msg, type = 'dim') {
  const t = document.getElementById('adminTerminal');
  if (!t) return;
  const clsMap = { ok:'term-ok', err:'term-err', warn:'term-warn', dim:'term-dim' };
  const d = document.createElement('div');
  d.className = 'term-line';
  d.innerHTML = `<span class="term-dim">[${new Date().toTimeString().slice(0,8)}]</span> <span class="${clsMap[type]||'term-dim'}">${esc(msg)}</span>`;
  t.appendChild(d); t.scrollTop = t.scrollHeight;
}

// ═══════════════════════════════════════════════════════════
//   MODALS
// ═══════════════════════════════════════════════════════════
function openModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'flex';
}
function closeModal(id, event) {
  if (event && event.target !== document.getElementById(id)) return;
  const m = document.getElementById(id);
  if (m) m.style.display = 'none';
}
function toggleSetting(el) { el.classList.toggle('on'); }

// ═══════════════════════════════════════════════════════════
//   HELPERS
// ═══════════════════════════════════════════════════════════
function esc(str) {
  if (str === null || str === undefined) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function timeAgo(dt) {
  if (!dt) return '';
  const d = new Date(dt.replace(' ','T'));
  const sec = Math.floor((Date.now() - d)/1000);
  if (sec < 60) return sec+'s ago';
  if (sec < 3600) return Math.floor(sec/60)+'m ago';
  if (sec < 86400) return Math.floor(sec/3600)+'h ago';
  return Math.floor(sec/86400)+'d ago';
}

function showToast(msg, type='info') {
  const t = document.createElement('div');
  t.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;padding:12px 18px;border-radius:8px;font-family:var(--font-mono);font-size:12px;background:${type==='error'?'var(--red-dim)':type==='success'?'var(--green-dim)':'var(--cyan-glow)'};border:1px solid ${type==='error'?'var(--red)':type==='success'?'var(--green)':'var(--cyan)'};color:${type==='error'?'var(--red)':type==='success'?'var(--green)':'var(--cyan)'};transition:opacity .3s`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(),400); }, 3000);
}

// Close notif panel on outside click
document.addEventListener('click', e => {
  if (State.notifOpen && !e.target.closest('#notifPanel') && !e.target.closest('#notifBtn')) {
    document.getElementById('notifPanel').style.display = 'none';
    State.notifOpen = false;
  }
});

// ═══════════════════════════════════════════════════════════
//   INIT
// ═══════════════════════════════════════════════════════════
document.querySelectorAll('.nav-item[data-page]').forEach(el => {
  el.addEventListener('click', e => { e.preventDefault(); navigate(el.dataset.page); });
});

window.addEventListener('hashchange', () => {
  const page = location.hash.slice(1);
  if (page && document.getElementById('page-'+page)) navigate(page);
});

startClock();
const initPage = location.hash.slice(1) || 'dashboard';
navigate(initPage.match(/^[a-z]+$/) ? initPage : 'dashboard');

// Load classes for admin selects on init
loadClassSelect('aClass');
loadClassSelect('mClass');
