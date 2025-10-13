<?php
require_once __DIR__ . "/mq_client.php";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $e = trim($_POST['email'] ?? '');
    $p = $_POST['password'] ?? '';

    if ($u === '' || $e === '' || $p === '') {
        $error = "Please fill out all fields.";
    } else {
        $res = mq_rpc(['type' => 'register', 'username' => $u, 'email' => $e, 'password' => $p]);
        if (($res['status'] ?? '') === 'ok') {
            header('Location: login.php'); exit;
        } else {
            $error = $res['message'] ?? 'Registration failed.';
        }
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

    <?php if (!empty($error)): ?>
      <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" novalidate>
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Register</button>
    </form>

    <p style="margin-top:10px">
      Already have an account? <a href="login.php">Log in</a>
    </p>
  </div>
</body>
</html>
