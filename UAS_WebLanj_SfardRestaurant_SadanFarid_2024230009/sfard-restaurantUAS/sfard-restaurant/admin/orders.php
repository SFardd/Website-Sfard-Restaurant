<?php
// ============================================================
// ADMIN/ORDERS.PHP — Lihat & update status pesanan online (pesan.php)
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

$page_title = "Pesanan Online";

// ---- OOP: pakai OrderModel (extends Database) ----
$orderModel = new OrderModel();

// ---- Update status pesanan ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId    = (int) $_POST['order_id'];
    $statusBaru = $_POST['status'];
    $validStatus = ['pending', 'diproses', 'selesai', 'dibatalkan'];
    if (in_array($statusBaru, $validStatus)) {
        $orderModel->updateStatus($orderId, $statusBaru);
    }
    header("Location: orders.php?msg=" . urlencode("Status pesanan berhasil diperbarui."));
    exit;
}

$orders = $orderModel->getAll();

include 'includes/admin_header.php';
?>

<h2 class="admin-title"><i class="bi bi-bag-check text-gold me-2"></i>Pesanan Online</h2>

<div class="table-responsive">
    <table class="table table-dark-admin align-middle mb-0">
        <thead>
            <tr>
                <th>Kode</th><th>Pemesan</th><th>Akun</th><th>Jenis</th><th>Item</th><th>Total</th><th>Bukti Transfer</th><th>Status</th><th>Waktu</th><th>Ubah Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $jumlah = 0; while ($row = mysqli_fetch_assoc($orders)): $jumlah++;
                $itemsResult = $orderModel->getItems((int) $row['id']);
                $items = [];
                while ($it = mysqli_fetch_assoc($itemsResult)) { $items[] = $it['nama_menu'] . ' x' . $it['qty']; }
                $jenisLabel = ['dine_in' => 'Makan di Tempat', 'take_away' => 'Bawa Pulang', 'delivery' => 'Delivery'];
            ?>
            <tr>
                <td><span class="badge-role-admin"><?= htmlspecialchars($row['kode']) ?></span></td>
                <td><?= htmlspecialchars($row['nama_pemesan']) ?><br><small style="color:var(--text-muted)"><?= htmlspecialchars($row['telepon']) ?></small></td>
                <td><?= $row['username'] ? htmlspecialchars($row['username']) : '<span style="color:var(--text-muted)">Tamu</span>' ?></td>
                <td><?= $jenisLabel[$row['jenis']] ?? $row['jenis'] ?></td>
                <td class="small"><?= htmlspecialchars(implode(', ', $items)) ?></td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                <td>
                    <?php if (!empty($row['bukti_transfer'])): ?>
                    <a href="../img/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank">
                        <img src="../img/<?= htmlspecialchars($row['bukti_transfer']) ?>" width="50" height="50" style="object-fit:cover;border-radius:6px;border:1px solid var(--border-gold);">
                    </a>
                    <?php else: ?>
                    <span class="small" style="color:var(--text-muted)">-</span>
                    <?php endif; ?>
                </td>
                <td><span class="status-badge status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                <td class="small"><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <form method="POST" class="d-flex gap-1">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <select name="status" class="form-select form-select-sm" style="background:#0f0c08;color:var(--text-main);border-color:var(--border-gold);">
                            <?php foreach (['pending','diproses','selesai','dibatalkan'] as $st): ?>
                                <option value="<?= $st ?>" <?= $row['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:6px;">Simpan</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($jumlah === 0): ?>
            <tr><td colspan="10" class="text-center py-4" style="color:var(--text-muted)">Belum ada pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/admin_footer.php'; ?>
