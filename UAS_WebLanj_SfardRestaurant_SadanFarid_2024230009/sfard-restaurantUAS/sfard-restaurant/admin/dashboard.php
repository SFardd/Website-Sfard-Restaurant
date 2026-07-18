<?php
// ============================================================
// ADMIN/DASHBOARD.PHP
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

$page_title = "Dashboard";

// ---- OOP: pakai Model classes (extends Database) buat ambil statistik ----
$menuModel        = new MenuModel();
$reservationModel = new ReservationModel();
$orderModel       = new OrderModel();
$messageModel     = new MessageModel();
$userModel        = new UserModel();

$total_menu  = $menuModel->countAll();
$total_res   = $reservationModel->countAll();
$total_order = $orderModel->countAll();
$total_msg   = $messageModel->countAll();
$total_users = $userModel->countAll();

include 'includes/admin_header.php';
?>

<h2 class="admin-title"><i class="bi bi-speedometer2 text-gold me-2"></i>Dashboard Admin</h2>
<p class="mb-4" style="color:var(--text-muted)">Selamat datang kembali, <strong style="color:var(--gold)"><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>

<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Total Menu</div>
            <div class="stat-number"><?= $total_menu ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Reservasi</div>
            <div class="stat-number"><?= $total_res ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Pesanan Online</div>
            <div class="stat-number"><?= $total_order ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Pesan Masuk</div>
            <div class="stat-number"><?= $total_msg ?></div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="stat-card" style="max-width:280px;">
        <div class="stat-label mb-1">Pengguna Terdaftar</div>
        <div class="stat-number"><?= $total_users ?></div>
    </div>
</div>

<div class="mt-5 d-flex gap-2 flex-wrap">
    <a href="menu_list.php" class="btn-gold py-2 px-4"><i class="bi bi-journal-richtext me-2"></i>Kelola Menu</a>
    <a href="reservations.php" class="btn-outline-gold py-2 px-4"><i class="bi bi-calendar-check me-2"></i>Lihat Reservasi</a>
    <a href="orders.php" class="btn-outline-gold py-2 px-4"><i class="bi bi-bag-check me-2"></i>Lihat Pesanan</a>
    <a href="messages.php" class="btn-outline-gold py-2 px-4"><i class="bi bi-envelope me-2"></i>Lihat Pesan</a>
</div>

<?php include 'includes/admin_footer.php'; ?>
