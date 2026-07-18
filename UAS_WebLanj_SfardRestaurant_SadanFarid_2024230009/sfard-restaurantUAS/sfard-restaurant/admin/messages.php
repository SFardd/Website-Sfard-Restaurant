<?php
// ============================================================
// ADMIN/MESSAGES.PHP — Lihat pesan masuk dari form kontak
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

$page_title = "Pesan Masuk";

// ---- OOP: pakai MessageModel (extends Database) ----
$messageModel = new MessageModel();
$result = $messageModel->getAll();

include 'includes/admin_header.php';
?>

<h2 class="admin-title"><i class="bi bi-envelope text-gold me-2"></i>Pesan Masuk</h2>

<div class="row g-3">
    <?php $jumlah = 0; while ($row = mysqli_fetch_assoc($result)): $jumlah++; ?>
    <div class="col-md-6">
        <div class="stat-card" style="border-left:4px solid var(--gold);">
            <div class="d-flex justify-content-between mb-1">
                <strong><?= htmlspecialchars($row['nama']) ?></strong>
                <small style="color:var(--text-muted)"><?= htmlspecialchars($row['created_at']) ?></small>
            </div>
            <p class="small mb-1" style="color:var(--text-muted)"><?= htmlspecialchars($row['email']) ?></p>
            <p class="mb-1"><em class="text-gold"><?= htmlspecialchars($row['topik']) ?></em></p>
            <p class="mb-2"><?= htmlspecialchars($row['pesan']) ?></p>
            <?php if ($row['rating'] > 0): ?>
                <span style="color:var(--gold)"><?= str_repeat('★', (int)$row['rating']) . str_repeat('☆', 5 - (int)$row['rating']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
    <?php if ($jumlah === 0): ?>
    <div class="col-12 text-center py-5" style="color:var(--text-muted)">Belum ada pesan masuk.</div>
    <?php endif; ?>
</div>

<?php include 'includes/admin_footer.php'; ?>
