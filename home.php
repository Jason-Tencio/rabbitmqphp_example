<?php
require_once __DIR__ . '/session_check.php'; // also starts session & has helpers
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Welcome, <?php echo h($_SESSION['username'] ?? ''); ?>!</h2>
  <p>Your session key (for DB validation later) is set.</p>
  <a href="logout.php"><button>Logout</button></a>
</div>
</body>
</html>
