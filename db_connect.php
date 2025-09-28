<?php
/* ---------- CONFIG ---------- */
// Prefer environment variables if you set them; fall back to defaults.
$DB_HOST = getenv('APP_DB_HOST') ?: 'dbvm.local';
$DB_NAME = getenv('APP_DB_NAME') ?: 'spotify_clone';
$DB_USER = getenv('APP_DB_USER') ?: 'testUser';
$DB_PASS = getenv('APP_DB_PASS') ?: '12345';
$DB_DSN  = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

/* ---------- DB CONNECTION (PDO) ---------- */
try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // throw exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // fetch arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                   // real prepares
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit("Database connection failed.");
}

/* ---------- SESSION (safe defaults) ---------- */
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,           // session cookie
    'path'     => '/',
    'secure'   => $https,      // true on HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ---------- TINY UTILITIES ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $t = $_POST['csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $t)) {
            http_response_code(400);
            exit('Invalid CSRF token.');
        }
    }
}

function flash(string $key, ?string $msg = null): ?string {
    // setter
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return null;
    }
    // getter (one-time)
    if (!empty($_SESSION['flash'][$key])) {
        $m = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    return null;
}

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
