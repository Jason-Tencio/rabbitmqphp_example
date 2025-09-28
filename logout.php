<?php
require_once __DIR__ . '/db_connect.php';

if (!empty($_SESSION['user_id']) && !empty($_SESSION['session_key'])) {
    // OPTIONAL: mark the session as ended in DB if your table exists
    try {
        $pdo->prepare('UPDATE user_sessions SET ended_at = NOW()
                       WHERE user_id = ? AND session_key = ? AND ended_at IS NULL')
            ->execute([$_SESSION['user_id'], $_SESSION['session_key']]);
    } catch (Throwable $e) { /* ignore for now */ }
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'],
              $params['secure'], $params['httponly']);
}
session_destroy();

header('Location: login.php');
exit;
