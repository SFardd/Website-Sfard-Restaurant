<?php
// ============================================================
// RIWAYAT_PESANAN.PHP — Riwayat pesanan milik akun yang login
// ============================================================
require_once 'includes/functions.php';
require_once 'includes/load_classes.php';
cekLoginUser(); // wajib login buat lihat riwayat pesanan sendiri

$page_title = "Riwayat Pesanan";

// ---- OOP: pakai OrderModel (extends Database) ----
$orderModel = new OrderModel();
$orders = $orderModel->getByUser((int) $_SESSION['user_id']);

include 'includes/header.php';
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Akun Saya</div>
        <h1 class="section-title">Riwayat Pesanan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Riwayat Pesanan</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-dark py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <p class="mb-0" style="color:var(--text-muted)">
                Halo, <strong style="color:var(--gold)"><?= htmlspecialchars($_SESSION['username']) ?></strong>. Berikut riwayat pesanan makanan kamu.
            </p>
            <a href="riwayat_reservasi.php" class="btn-outline-gold px-3 py-2"><i class="bi bi-calendar-check me-2"></i>Riwayat Reservasi</a>
        </div>

        <?php $jumlah = 0; while ($row = mysqli_fetch_assoc($orders)): $jumlah++;
            $itemsResult = $orderModel->getItems((int) $row['id']);
            $items = [];
            while ($it = mysqli_fetch_assoc($itemsResult)) { $items[] = $it['nama_menu'] . ' x' . $it['qty']; }
            $jenisLabel = ['dine_in' => 'Makan di Tempat', 'take_away' => 'Bawa Pulang', 'delivery' => 'Delivery'];
        ?>
        <div class="riwayat-card mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                    <span class="badge-role-admin me-2"><?= htmlspecialchars($row['kode']) ?></span>
                    <span style="color:var(--text-muted)" class="small"><?= htmlspecialchars($row['created_at']) ?></span>
                </div>
                <span class="status-badge status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span>
            </div>
            <p class="mb-1 small"><i class="bi bi-bag me-2 text-gold"></i><?= $jenisLabel[$row['jenis']] ?? $row['jenis'] ?> — <?= htmlspecialchars(implode(', ', $items)) ?></p>
            <p class="mb-1 small">
                <i class="bi bi-<?= $row['metode_pembayaran'] === 'transfer' ? 'bank' : 'cash-coin' ?> me-2 text-gold"></i>
                Pembayaran: <?= $row['metode_pembayaran'] === 'transfer' ? 'Transfer Bank' : 'Tunai' ?>
            </p>
            <?php if (!empty($row['alamat'])): ?>
            <p class="mb-1 small"><i class="bi bi-geo-alt me-2 text-gold"></i><?= htmlspecialchars($row['alamat']) ?></p>
            <?php endif; ?>
            <p class="mb-0 mt-2"><strong style="color:var(--gold)">Total: Rp <?= number_format($row['total'], 0, ',', '.') ?></strong></p>
            <?php if (!empty($row['bukti_transfer'])): ?>
            <div class="mt-2">
                <a href="img/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank">
                    <img src="img/<?= htmlspecialchars($row['bukti_transfer']) ?>" width="70" height="70" style="object-fit:cover;border-radius:8px;border:1px solid var(--border-gold);">
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>

        <?php if ($jumlah === 0): ?>
        <div class="text-center py-5" style="color:var(--text-muted)">
            <i class="bi bi-bag-x" style="font-size:2.5rem;"></i>
            <p class="mt-3">Belum ada riwayat pesanan.</p>
            <a href="pesan.php" class="btn-gold px-4 py-2 mt-2"><i class="bi bi-bag-check me-2"></i>Pesan Sekarang</a>
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
