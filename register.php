<?php
require_once __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    if (!$stmt) {
        $error = "Registration failed. Please try again.";
    } else {
        $stmt->bind_param("sss", $username, $email, $password_hash);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            if ($stmt->errno === 1062 || stripos($stmt->error, 'Duplicate') !== false) {
                $error = "That username or email is already taken.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
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
