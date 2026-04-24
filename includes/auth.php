<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function is_logged_in() { return isset($_SESSION['user_id']); }
function is_admin()     { return isset($_SESSION['admin_id']); }

function require_login() {
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}
function require_admin() {
    if (!is_admin()) {
        header("Location: " . BASE_URL . "/admin/login.php");
        exit;
    }
}
function sanitize($v) { return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8'); }
