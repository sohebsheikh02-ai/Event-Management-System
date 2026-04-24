<?php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_user']);
require_once __DIR__ . '/../config/database.php';
header("Location: " . BASE_URL . "/admin/login.php");
exit;
