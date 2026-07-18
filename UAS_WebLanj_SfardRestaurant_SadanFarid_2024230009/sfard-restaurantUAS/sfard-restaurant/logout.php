<?php
// ============================================================
// LOGOUT.PHP — Hapus session & cookie remember-me
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

if (isset($_COOKIE['sfard_remember_username'])) {
    setcookie('sfard_remember_username', '', time() - 3600, "/");
}

header("Location: login.php");
exit;
