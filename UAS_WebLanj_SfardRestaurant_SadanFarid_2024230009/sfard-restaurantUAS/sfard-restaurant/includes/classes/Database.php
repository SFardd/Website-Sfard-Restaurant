<?php
// ============================================================
// INCLUDES/CLASSES/DATABASE.PHP
// PARENT CLASS — semua Model (MenuModel, ReservationModel, dst)
// akan EXTENDS class ini supaya otomatis dapat koneksi database
// tanpa perlu nulis ulang kode koneksi di tiap class.
// ============================================================

class Database
{
    protected $conn;

    // ---- CONSTRUCTOR ----
    // Otomatis dipanggil setiap kali ada object baru dibuat,
    // baik langsung "new Database()" maupun lewat class anak
    // (MenuModel, ReservationModel, dll) yang extends Database.
    public function __construct()
    {
        $host   = "localhost";
        $dbuser = "root";
        $dbpass = "";
        $dbname = "sfardresto_db";

        $this->conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

        if (!$this->conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->conn, "utf8mb4");
    }

    // ---- DESTRUCTOR ----
    // Otomatis dipanggil saat object tidak dipakai lagi / script
    // selesai berjalan. Menutup koneksi database secara otomatis
    // supaya tidak perlu manggil mysqli_close() manual di tiap halaman.
    public function __destruct()
    {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
