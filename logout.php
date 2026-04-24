<?php
session_start();
session_destroy();
require_once __DIR__ . '/config/database.php';
header("Location: " . BASE_URL . "/index.php");
exit;
