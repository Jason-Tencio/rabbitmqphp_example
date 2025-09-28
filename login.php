<?php
require_once __DIR__ . '/db_connect.php';

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Invalid credentials.';
        } else {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['user_id']  = (int)$user['id'];
            $_SESSION['username'] = $user['username'];

            // Generate a session key (for your future DB validator / RabbitMQ flow)
            $_SESSION['session_key'] = bin2hex(random_bytes(32));

            // OPTIONAL: persist in DB if you create a user_sessions table
            try {
                $pdo->prepare('INSERT INTO user_sessions (user_id, session_key, created_at)
                               VALUES (?, ?, NOW())')->execute([$user['id'], $_SESSION['session_key']]);
            } catch (Throwable $e) {
                // table may not exist yet; ignore in this learning phase
            }

            header('Location: home.php');
            exit;
        }
    }
}
$ok = flash('ok');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Login</h2>

  <?php if ($ok): ?>
    <div class="success"><?php echo h($ok); ?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="error"><?php echo h(implode("\n", $errors)); ?></div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
    <input type="text"     name="username" placeholder="Username"
           value="<?php echo h($username); ?>" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>

  <p style="margin-top:10px">
    New here? <a href="register.php">Create an account</a>
  </p>
</div>
</body>
</html>
