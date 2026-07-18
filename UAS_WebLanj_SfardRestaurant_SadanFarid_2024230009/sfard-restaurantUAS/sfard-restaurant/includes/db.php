<?php
// ============================================================
// INCLUDES/DB.PHP — Koneksi Database
// Dipakai di semua halaman yang butuh akses ke MySQL
// ============================================================

$host   = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "sfardresto_db";

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
