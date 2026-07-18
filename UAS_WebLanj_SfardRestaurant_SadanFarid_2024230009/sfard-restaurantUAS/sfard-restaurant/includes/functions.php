<?php
// ============================================================
// INCLUDES/FUNCTIONS.PHP
// Helper untuk sistem login & admin (dipakai di login.php,
// register.php, logout.php, dan seluruh halaman admin/*)
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------
// PROCEDURE: cek apakah user sudah login sebagai admin,
// kalau belum -> redirect ke halaman login
// ---------------------------------------------------------
function cekLoginAdmin()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit;
    }
}

// ---------------------------------------------------------
// PROCEDURE: cek apakah user sudah login (role apa saja),
// kalau belum -> redirect ke login, lalu balik lagi ke
// halaman ini setelah berhasil login
// ---------------------------------------------------------
function cekLoginUser()
{
    if (!isset($_SESSION['user_id'])) {
        $tujuan = $_SERVER['REQUEST_URI'] ?? '';
        header("Location: login.php?redirect=" . urlencode($tujuan));
        exit;
    }
}

// ---------------------------------------------------------
// FUNCTION: upload gambar menu (CRUD Gambar)
// $file      = elemen dari $_FILES['image_file']
// $oldImage  = nama file lama (untuk dihapus saat edit), boleh null
// Return: nama file baru jika sukses, atau array ['error' => pesan] jika gagal
// ---------------------------------------------------------
function uploadGambarMenu(array $file, ?string $oldImage = null)
{
    // Tidak ada file yang diupload -> bukan error, biarkan pemanggil pakai gambar lama/placeholder
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Gagal mengupload gambar (kode error: ' . $file['error'] . ').'];
    }

    // ---- BRANCHING: validasi tipe file ----
    $ekstensiDiizinkan = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiDiizinkan)) {
        return ['error' => 'Format gambar harus JPG, PNG, GIF, atau WEBP.'];
    }

    // ---- BRANCHING: validasi ukuran file (maks 2MB) ----
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['error' => 'Ukuran gambar maksimal 2MB.'];
    }

    // ---- Buat folder upload jika belum ada ----
    $folderUpload = __DIR__ . '/../img/menu/';
    if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0755, true);
    }

    // ---- Buat nama file unik ----
    $namaBaru = 'menu_' . uniqid() . '.' . $ekstensi;
    $tujuan   = $folderUpload . $namaBaru;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        return ['error' => 'Gagal menyimpan file gambar ke server.'];
    }

    // ---- Hapus gambar lama jika ada dan berada di folder img/menu ----
    // (pakai substr() bukan str_starts_with() biar tetap jalan di PHP 7.x saat hosting)
    if ($oldImage && substr($oldImage, 0, 5) === 'menu/') {
        $fileLama = __DIR__ . '/../img/' . $oldImage;
        if (is_file($fileLama)) {
            @unlink($fileLama);
        }
    }

    return 'menu/' . $namaBaru;
}

// ---------------------------------------------------------
// FUNCTION: upload bukti transfer (WAJIB ADA, tidak boleh kosong)
// Dipakai di pesan.php (bukti bayar pesanan) & reservation.php (bukti bayar DP reservasi)
// $file = elemen dari $_FILES['bukti_transfer']
// Return: nama file (path relatif) jika sukses, atau array ['error' => pesan] jika gagal
// ---------------------------------------------------------
function uploadBuktiTransfer(array $file)
{
    // ---- BRANCHING: wajib ada file, tidak boleh dikosongkan ----
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => 'Bukti transfer wajib diupload.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Gagal mengupload bukti transfer (kode error: ' . $file['error'] . ').'];
    }

    // ---- BRANCHING: validasi tipe file ----
    $ekstensiDiizinkan = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiDiizinkan)) {
        return ['error' => 'Bukti transfer harus berupa foto JPG, PNG, atau WEBP.'];
    }

    // ---- BRANCHING: validasi ukuran file (maks 2MB) ----
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['error' => 'Ukuran foto bukti transfer maksimal 2MB.'];
    }

    // ---- Buat folder upload jika belum ada ----
    $folderUpload = __DIR__ . '/../img/bukti_transfer/';
    if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0755, true);
    }

    // ---- Buat nama file unik ----
    $namaBaru = 'bukti_' . uniqid() . '.' . $ekstensi;
    $tujuan   = $folderUpload . $namaBaru;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        return ['error' => 'Gagal menyimpan foto bukti transfer ke server.'];
    }

    return 'bukti_transfer/' . $namaBaru;
}

