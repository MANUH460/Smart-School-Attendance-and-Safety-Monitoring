<?php
// ============================================================
//  EduShield — Database Configuration
//  File: includes/config.php
//  Edit DB credentials below to match your XAMPP setup
// ============================================================

define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASS',     '');          // XAMPP default = blank
define('DB_NAME',     'edushield');
define('DB_CHARSET',  'utf8mb4');
define('APP_NAME',    'EduShield');
define('APP_VERSION', '3.4.1');
define('APP_BASE',    '/edushield'); // URL base path

// Session config
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// ── PDO Connection ──────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['success'=>false,'error'=>'Database connection failed: '.$e->getMessage()]));
        }
    }
    return $pdo;
}

// ── JSON helpers ────────────────────────────────────────────
function jsonSuccess(array $data = [], string $message = 'OK'): void {
    header('Content-Type: application/json');
    echo json_encode(['success'=>true,'message'=>$message,'data'=>$data]);
    exit;
}

function jsonError(string $message, int $code = 400): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'error'=>$message]);
    exit;
}

// ── Auth helpers ─────────────────────────────────────────────
function requireAuth(): void {
    if (empty($_SESSION['admin_id'])) {
        if (isApiRequest()) {
            jsonError('Unauthorized', 401);
        }
        header('Location: '.APP_BASE.'/index.php');
        exit;
    }
}

function isApiRequest(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
           strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
}

function currentUser(): array {
    return $_SESSION['admin_user'] ?? [];
}

// ── Log action ────────────────────────────────────────────────
function logAction(string $action, string $details = ''): void {
    try {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO system_logs (action,details,user_id,ip_address) VALUES (?,?,?,?)');
        $stmt->execute([$action, $details, $_SESSION['admin_id'] ?? null, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Exception $e) { /* silent */ }
}

// ── Sanitize ─────────────────────────────────────────────────
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}
