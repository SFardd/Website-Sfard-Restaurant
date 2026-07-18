<?php
// ============================================================
// ADMIN/RESERVATIONS.PHP — Lihat semua reservasi dari database
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

$page_title = "Reservasi";

// ---- OOP: pakai ReservationModel (extends Database) ----
$reservationModel = new ReservationModel();
$result = $reservationModel->getAll();

include 'includes/admin_header.php';
?>

<h2 class="admin-title"><i class="bi bi-calendar-check text-gold me-2"></i>Daftar Reservasi</h2>

<div class="table-responsive">
    <table class="table table-dark-admin align-middle mb-0">
        <thead>
            <tr>
                <th>Kode</th><th>Nama</th><th>Akun</th><th>Kontak</th><th>Tanggal</th><th>Jam</th><th>Tamu</th><th>Meja</th><th>Paket</th><th>Bukti Transfer</th><th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php $jumlah = 0; while ($row = mysqli_fetch_assoc($result)): $jumlah++; ?>
            <tr>
                <td><span class="badge-role-admin"><?= htmlspecialchars($row['kode']) ?></span></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= $row['username'] ? htmlspecialchars($row['username']) : '<span style="color:var(--text-muted)">Tamu</span>' ?></td>
                <td><?= htmlspecialchars($row['email']) ?><br><small style="color:var(--text-muted)"><?= htmlspecialchars($row['telepon']) ?></small></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['jam']) ?> WIB</td>
                <td><?= (int) $row['tamu'] ?></td>
                <td><?= htmlspecialchars($row['meja']) ?></td>
                <td><?= htmlspecialchars($row['paket']) ?></td>
                <td>
                    <?php if (!empty($row['bukti_transfer'])): ?>
                    <a href="../img/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank">
                        <img src="../img/<?= htmlspecialchars($row['bukti_transfer']) ?>" width="50" height="50" style="object-fit:cover;border-radius:6px;border:1px solid var(--border-gold);">
                    </a>
                    <?php else: ?>
                    <span class="small" style="color:var(--text-muted)">-</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['catatan']) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($jumlah === 0): ?>
            <tr><td colspan="11" class="text-center py-4" style="color:var(--text-muted)">Belum ada reservasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/admin_footer.php'; ?>
