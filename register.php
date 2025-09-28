<?php
require_once __DIR__ . '/db_connect.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim((string)($_POST['username'] ?? ''));
    $email    = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    // Basic validation
    if ($username === '' || strlen($username) < 3 || strlen($username) > 32) {
        $errors[] = 'Username must be 3–32 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    // Uniqueness checks
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already registered.';
        }
    }

    // Insert
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at)
                               VALUES (?, ?, ?, NOW())');
        $stmt->execute([$username, $email, $hash]);

        flash('ok', 'Account created! Please log in.');
        header('Location: login.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Create Account</h2>

  <?php if ($errors): ?>
    <div class="error"><?php echo h(implode("\n", $errors)); ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <input type="hidden" name="csrf" value="<?php echo h(csrf_token()); ?>">
    <input type="text"     name="username" placeholder="Username"
           value="<?php echo h($username); ?>" required>
    <input type="email"    name="email"    placeholder="Email"
           value="<?php echo h($email); ?>" required>
    <input type="password" name="password" placeholder="Password (min 8)" required>
    <button type="submit">Register</button>
  </form>

  <p style="margin-top:10px">
    Already have an account? <a href="login.php">Log in</a>
  </p>
</div>
</body>
</html>
