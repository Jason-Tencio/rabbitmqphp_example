<?php
require_once __DIR__ . '/db_connect.php';

if (empty($_SESSION['user_id'])) {
    flash('ok', 'Please log in to continue.');
    header('Location: login.php');
    exit;
}
