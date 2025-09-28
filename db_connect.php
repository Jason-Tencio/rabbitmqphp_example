<?php
$host = "100.115.110.102";     // change if not using hosts file
$user = "webuser";       // your DB user
$pass = "12345";          // your DB password
$db   = "tune";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connect error: " . $conn->connect_error);
}
?>
