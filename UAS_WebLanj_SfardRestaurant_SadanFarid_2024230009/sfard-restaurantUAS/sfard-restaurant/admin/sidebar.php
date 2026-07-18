<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-sidebar">
    <div class="p-4">
        <a href="dashboard.php" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="../img/LogoSfardRest.png" alt="Logo" height="38">
            <div>
                <div style="font-family:var(--ff-display); color:var(--text-main); font-size:1rem; letter-spacing:0.05em;">SFard <span class="text-gold">Admin</span></div>
            </div>
        </a>
    </div>
    <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="menu_list.php" class="<?= in_array($current, ['menu_list.php', 'menu_add.php', 'menu_edit.php']) ? 'active' : '' ?>"><i class="bi bi-journal-richtext me-2"></i>Kelola Menu</a>
    <a href="reservations.php" class="<?= $current === 'reservations.php' ? 'active' : '' ?>"><i class="bi bi-calendar-check me-2"></i>Reservasi</a>
    <a href="orders.php" class="<?= $current === 'orders.php' ? 'active' : '' ?>"><i class="bi bi-bag-check me-2"></i>Pesanan Online</a>
    <a href="messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>"><i class="bi bi-envelope me-2"></i>Pesan Masuk</a>
    <hr style="border-color: rgba(212,175,55,0.15); margin: 1rem 1.5rem;">
    <a href="../index.php"><i class="bi bi-globe me-2"></i>Lihat Website</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>
