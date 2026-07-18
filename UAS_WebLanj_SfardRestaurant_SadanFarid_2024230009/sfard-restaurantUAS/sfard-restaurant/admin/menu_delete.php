<?php
// ============================================================
// ADMIN/MENU_DELETE.PHP — CRUD: Delete
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

// ---- OOP: pakai MenuModel (extends Database) ----
$menuModel = new MenuModel();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $item = $menuModel->find($id);

    // ---- Hapus file gambar fisik dulu (kalau hasil upload, bukan gambar bawaan/placeholder) ----
    // pakai substr() bukan str_starts_with() biar tetap jalan di PHP 7.x saat hosting
    if ($item && substr($item['image'], 0, 5) === 'menu/') {
        $filePath = __DIR__ . '/../img/' . $item['image'];
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }

    $menuModel->delete($id);
}

header("Location: menu_list.php?msg=" . urlencode("Menu berhasil dihapus."));
exit;
