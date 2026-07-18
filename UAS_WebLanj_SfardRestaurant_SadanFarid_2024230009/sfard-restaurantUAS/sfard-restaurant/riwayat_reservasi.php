<?php
// ============================================================
// RIWAYAT_RESERVASI.PHP — Riwayat reservasi milik akun yang login
// ============================================================
require_once 'includes/functions.php';
require_once 'includes/load_classes.php';
cekLoginUser(); // wajib login buat lihat riwayat reservasi sendiri

$page_title = "Riwayat Reservasi";

// ---- OOP: pakai ReservationModel (extends Database) ----
$reservationModel = new ReservationModel();
$reservations = $reservationModel->getByUser((int) $_SESSION['user_id']);

include 'includes/header.php';
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Akun Saya</div>
        <h1 class="section-title">Riwayat Reservasi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Riwayat Reservasi</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-dark py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <p class="mb-0" style="color:var(--text-muted)">
                Halo, <strong style="color:var(--gold)"><?= htmlspecialchars($_SESSION['username']) ?></strong>. Berikut riwayat reservasi meja kamu.
            </p>
            <a href="riwayat_pesanan.php" class="btn-outline-gold px-3 py-2"><i class="bi bi-bag-check me-2"></i>Riwayat Pesanan</a>
        </div>

        <?php $jumlah = 0; while ($row = mysqli_fetch_assoc($reservations)): $jumlah++; ?>
        <div class="riwayat-card mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <span class="badge-role-admin"><?= htmlspecialchars($row['kode']) ?></span>
                <span style="color:var(--text-muted)" class="small"><?= htmlspecialchars($row['created_at']) ?></span>
            </div>
            <p class="mb-1 small"><i class="bi bi-calendar-event me-2 text-gold"></i><?= htmlspecialchars($row['tanggal']) ?> — <?= htmlspecialchars($row['jam']) ?> WIB</p>
            <p class="mb-1 small"><i class="bi bi-people me-2 text-gold"></i><?= (int) $row['tamu'] ?> tamu — Meja: <?= htmlspecialchars($row['meja']) ?></p>
            <?php if (!empty($row['paket']) && $row['paket'] !== '-'): ?>
            <p class="mb-1 small"><i class="bi bi-gift me-2 text-gold"></i>Paket: <?= htmlspecialchars($row['paket']) ?></p>
            <?php endif; ?>
            <?php if (!empty($row['catatan'])): ?>
            <p class="mb-0 small"><i class="bi bi-chat-text me-2 text-gold"></i><?= htmlspecialchars($row['catatan']) ?></p>
            <?php endif; ?>
            <?php if (!empty($row['bukti_transfer'])): ?>
            <div class="mt-2">
                <div class="small mb-1" style="color:var(--text-muted)"><i class="bi bi-bank me-1"></i>Bukti Transfer DP:</div>
                <a href="img/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank">
                    <img src="img/<?= htmlspecialchars($row['bukti_transfer']) ?>" width="70" height="70" style="object-fit:cover;border-radius:8px;border:1px solid var(--border-gold);">
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>

        <?php if ($jumlah === 0): ?>
        <div class="text-center py-5" style="color:var(--text-muted)">
            <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
            <p class="mt-3">Belum ada riwayat reservasi.</p>
            <a href="reservation.php" class="btn-gold px-4 py-2 mt-2"><i class="bi bi-calendar-check me-2"></i>Reservasi Sekarang</a>
        </div>
        <?php endif; ?>

    </div>
</section>

<style>
.riwayat-card {
    background: var(--dark-card);
    border: 1px solid var(--border-gold);
    border-radius: 10px;
    padding: 1.2rem 1.4rem;
}
</style>

<?php include 'includes/footer.php'; ?>
