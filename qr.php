<?php
// ============================================================
//  EduShield — Login Page
//  File: index.php
// ============================================================
require_once __DIR__.'/includes/config.php';

// Already logged in → redirect
if (!empty($_SESSION['admin_id'])) {
    header('Location: '.APP_BASE.'/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM admin_users WHERE username=? AND is_active=1 LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // password_verify handles bcrypt; also allow plain "admin123" for demo ease
        $valid = $user && (password_verify($password, $user['password']) || $password === 'admin123');
        if ($valid) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_user'] = $user;
            $db->prepare('UPDATE admin_users SET last_login=NOW() WHERE id=?')->execute([$user['id']]);
            logAction('LOGIN', 'User '.$username.' logged in');
            header('Location: '.APP_BASE.'/dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>EduShield — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Exo+2:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{--bg:#060a12;--panel:#0c1220;--card:#101828;--border:#1e2d42;--cyan:#00d4ff;--green:#00e58a;--red:#ff3855;--text:#e8f0fe;--dim:#6b82a0;}
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Exo 2',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;}
  body::before{content:'';position:fixed;inset:0;background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,0,0,.06) 2px,rgba(0,0,0,.06) 4px);pointer-events:none;z-index:10;}
  .bg-grid{position:fixed;inset:0;background-image:linear-gradient(rgba(0,212,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,212,255,.03) 1px,transparent 1px);background-size:40px 40px;}
  .orb{position:fixed;border-radius:50%;filter:blur(80px);opacity:.12;}
  .orb1{width:500px;height:500px;background:var(--cyan);top:-100px;left:-100px;}
  .orb2{width:400px;height:400px;background:#b44dff;bottom:-80px;right:-80px;}
  .login-wrap{position:relative;z-index:20;width:100%;max-width:420px;padding:20px;}
  .logo-block{text-align:center;margin-bottom:32px;}
  .logo-icon{width:64px;height:64px;background:linear-gradient(135deg,var(--cyan),#b44dff);border-radius:16px;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:30px;box-shadow:0 0 40px rgba(0,212,255,.3);}
  .logo-name{font-size:28px;font-weight:800;letter-spacing:3px;}
  .logo-name span{color:var(--cyan);}
  .logo-sub{font-family:'Share Tech Mono',monospace;font-size:10px;color:var(--dim);letter-spacing:4px;margin-top:4px;}
  .card{background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:32px;box-shadow:0 20px 60px rgba(0,0,0,.5);}
  .card-title{font-size:16px;font-weight:700;margin-bottom:6px;}
  .card-sub{font-size:12px;color:var(--dim);font-family:'Share Tech Mono',monospace;margin-bottom:24px;}
  .form-group{margin-bottom:16px;}
  label{display:block;font-size:10px;letter-spacing:2px;color:var(--dim);font-family:'Share Tech Mono',monospace;margin-bottom:7px;}
  input{width:100%;background:#060a12;border:1px solid var(--border);border-radius:8px;padding:12px 16px;color:var(--text);font-size:14px;font-family:'Exo 2',sans-serif;outline:none;transition:all .2s;}
  input:focus{border-color:rgba(0,212,255,.4);box-shadow:0 0 0 3px rgba(0,212,255,.1);}
  .btn-login{width:100%;background:var(--cyan);border:none;border-radius:8px;padding:13px;color:#060a12;font-weight:800;font-size:14px;letter-spacing:2px;cursor:pointer;font-family:'Exo 2',sans-serif;transition:all .2s;margin-top:8px;}
  .btn-login:hover{background:#33dcff;box-shadow:0 0 30px rgba(0,212,255,.3);}
  .error{background:rgba(255,56,85,.12);border:1px solid rgba(255,56,85,.3);border-radius:8px;padding:10px 14px;font-size:12px;color:var(--red);margin-bottom:16px;}
  .demo-hint{margin-top:16px;text-align:center;font-family:'Share Tech Mono',monospace;font-size:10px;color:var(--dim);}
  .demo-hint strong{color:var(--cyan);}
  .status-bar{display:flex;align-items:center;gap:8px;justify-content:center;margin-top:20px;font-family:'Share Tech Mono',monospace;font-size:10px;color:var(--dim);}
  .dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 2s infinite;}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="orb orb1"></div>
<div class="orb orb2"></div>
<div class="login-wrap">
  <div class="logo-block">
    <div class="logo-icon">🛡</div>
    <div class="logo-name">Edu<span>Shield</span></div>
    <div class="logo-sub">STUDENT SAFETY COMMAND SYSTEM</div>
  </div>
  <div class="card">
    <div class="card-title">Administrator Login</div>
    <div class="card-sub">SECURE ACCESS · AUTHORIZED PERSONNEL ONLY</div>
    <?php if($error): ?><div class="error">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>USERNAME</label>
        <input type="text" name="username" placeholder="Enter your username" value="<?= htmlspecialchars($_POST['username']??'') ?>" required autocomplete="username">
      </div>
      <div class="form-group">
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-login">AUTHENTICATE →</button>
    </form>
    <div class="demo-hint">Demo credentials: <strong>admin</strong> / <strong>admin123</strong></div>
  </div>
  <div class="status-bar"><div class="dot"></div>SYSTEM ONLINE · <?= APP_NAME ?> v<?= APP_VERSION ?></div>
</div>
</body>
</html>
