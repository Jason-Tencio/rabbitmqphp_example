<?php
require_once __DIR__ . "/db_connect.php";
session_start();

/* Prevent PHP from turning MySQL warnings into fatal errors */
mysqli_report(MYSQLI_REPORT_OFF);

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        // Prepare safely and handle failures gracefully
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username=? LIMIT 1");
        if (!$stmt) {
            // Don’t leak DB details to users
            $error = "Login is temporarily unavailable. Please try again.";
        } else {
            $stmt->bind_param("s", $username);
            if (!$stmt->execute()) {
                $error = "Login is temporarily unavailable. Please try again.";
            } else {
                $stmt->bind_result($id, $hash);
                if ($stmt->fetch() && password_verify($password, $hash)) {
                    $_SESSION['user_id']  = $id;
                    $_SESSION['username'] = $username;
                    header("Location: home.php");
                    exit();
                } else {
                    // Unsuccessful login: show clean message (no 500)
                    $error = "Invalid username or password.";
                }
            }
            $stmt->close();
        }
    }
}
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

    <?php if (!empty($error)): ?>
      <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Login</button>
    </form>

    <!-- Simple Register button -->
    <div style="margin-top:10px">
      <a href="register.php"><button type="button">Register</button></a>
    </div>
  </div>
</body>
</html>
