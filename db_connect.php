<?php
$host = "dbvm.local";     // change if not using hosts file
$user = "testUser";       // your DB user
$pass = "12345";          // your DB password
$db   = "spotify_clone";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connect error: " . $conn->connect_error);
}
?>
