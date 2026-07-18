<?php
// ============================================================
// ADMIN/MENU_LIST.PHP — CRUD: Read + Search
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

$page_title = "Kelola Menu";

// ---- OOP: pakai MenuModel (extends Database) ----
$menuModel = new MenuModel();

// ---- SEARCH: cari menu berdasarkan nama ----
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$result  = $menuModel->getAll($keyword);

include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="admin-title mb-0"><i class="bi bi-journal-richtext text-gold me-2"></i>Kelola Menu</h2>
    <a href="menu_add.php" class="btn-gold py-2 px-4"><i class="bi bi-plus-lg me-1"></i>Tambah Menu</a>
</div>

<form method="GET" class="mb-4 d-flex gap-2" style="max-width:420px;">
    <input type="text" name="q" class="form-control form-control-dark" placeholder="Cari nama menu..." value="<?= htmlspecialchars($keyword) ?>">
    <button class="btn-outline-gold px-3" type="submit"><i class="bi bi-search"></i></button>
    <?php if ($keyword): ?><a href="menu_list.php" class="btn-outline-gold px-3">Reset</a><?php endif; ?>
</form>

<div class="table-responsive">
    <table class="table table-dark-admin align-middle mb-0">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $jumlah = 0; while ($row = mysqli_fetch_assoc($result)): $jumlah++; ?>
            <tr>
                <td><img src="../img/<?= htmlspecialchars($row['image']) ?>" width="60" height="45" style="object-fit:cover; border-radius:6px;" onerror="this.src='https://placehold.co/60x45/1A1208/D4AF37?text=%20'"></td>
                <td>
                    <?= htmlspecialchars($row['name']) ?>
                    <?php if ($row['badge']): ?><span class="badge-role-admin ms-1"><?= htmlspecialchars($row['badge']) ?></span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                <td><i class="bi bi-star-fill text-warning"></i> <?= $row['rating'] ?></td>
                <td>
                    <?php if ($row['is_available']): ?>
                        <span class="status-badge status-selesai">Tersedia</span>
                    <?php else: ?>
                        <span class="status-badge status-dibatalkan">Habis</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="menu_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary" style="border-radius:6px;">Edit</a>
                    <a href="menu_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius:6px;" onclick="return confirm('Yakin hapus menu ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($jumlah === 0): ?>
            <tr><td colspan="7" class="text-center py-4" style="color:var(--text-muted)">Tidak ada menu ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/admin_footer.php'; ?>
