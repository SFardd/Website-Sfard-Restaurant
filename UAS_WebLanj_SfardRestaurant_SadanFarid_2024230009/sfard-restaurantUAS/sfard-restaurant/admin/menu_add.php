<?php
// ============================================================
// ADMIN/MENU_ADD.PHP — CRUD: Create
// ============================================================
require_once '../includes/functions.php';
require_once '../includes/load_classes.php';
cekLoginAdmin();

$page_title = "Tambah Menu";
$error = '';

// ---- OOP: pakai MenuModel (extends Database) ----
$menuModel = new MenuModel();

$categories = [];
$catResult = $menuModel->getCategories();
while ($row = mysqli_fetch_assoc($catResult)) { $categories[] = $row; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $badge       = trim($_POST['badge'] ?? '');
    $rating      = (float) ($_POST['rating'] ?? 5);
    $is_spicy    = isset($_POST['is_spicy']) ? 1 : 0;
    $is_veg      = isset($_POST['is_veg']) ? 1 : 0;
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if ($name === '' || $price <= 0 || $category_id <= 0) {
        $error = "Nama, kategori, dan harga wajib diisi dengan benar.";
    } else {
        // ---- FUNCTION: upload gambar (CRUD Gambar) ----
        $hasilUpload = uploadGambarMenu($_FILES['image_file'] ?? []);

        if (is_array($hasilUpload) && isset($hasilUpload['error'])) {
            $error = $hasilUpload['error'];
        } else {
            $image = $hasilUpload ?? ("https://placehold.co/600x400/1A1208/D4AF37?text=" . urlencode($name));

            $menuModel->create([
                'category_id'  => $category_id,
                'name'         => $name,
                'description'  => $description,
                'price'        => $price,
                'image'        => $image,
                'badge'        => $badge,
                'rating'       => $rating,
                'is_spicy'     => $is_spicy,
                'is_veg'       => $is_veg,
                'is_available' => $is_available,
            ]);
            header("Location: menu_list.php?msg=" . urlencode("Menu '$name' berhasil ditambahkan."));
            exit;
        }
    }
}

include 'includes/admin_header.php';
?>

<h2 class="admin-title"><i class="bi bi-plus-circle text-gold me-2"></i>Tambah Menu Baru</h2>

<?php if ($error): ?>
    <div class="alert-error-dark mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="form-dark" style="max-width:640px;">
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label-dark">Nama Menu</label>
            <input type="text" name="name" class="form-control form-control-dark" required>
        </div>
        <div class="mb-3">
            <label class="form-label-dark">Kategori</label>
            <select name="category_id" class="form-control form-control-dark" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label-dark">Deskripsi</label>
            <textarea name="description" rows="3" class="form-control form-control-dark"></textarea>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-dark">Harga (Rp)</label>
                <input type="number" name="price" class="form-control form-control-dark" required min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label-dark">Rating (1-5)</label>
                <input type="number" name="rating" class="form-control form-control-dark" value="5.0" step="0.1" min="1" max="5">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label-dark">Pilih Gambar</label>
            <input type="file" name="image_file" class="form-control form-control-dark" accept="image/png, image/jpeg, image/gif, image/webp">
            <small style="color:var(--text-muted)">Kosongkan jika ingin pakai gambar placeholder. Format JPG/PNG/GIF/WEBP, maks 2MB.</small>
        </div>
        <div class="mb-3">
            <label class="form-label-dark">Badge (opsional)</label>
            <input type="text" name="badge" class="form-control form-control-dark" placeholder="Bestseller / New / Signature">
        </div>
        <div class="d-flex gap-4 mb-4 flex-wrap">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_spicy" id="is_spicy">
                <label class="form-check-label small" for="is_spicy" style="color:var(--text-muted)">Pedas</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_veg" id="is_veg">
                <label class="form-check-label small" for="is_veg" style="color:var(--text-muted)">Vegetarian</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_available" id="is_available" checked>
                <label class="form-check-label small" for="is_available" style="color:var(--text-muted)">Tersedia</label>
            </div>
        </div>
        <button type="submit" class="btn-gold px-4 py-2">Simpan</button>
        <a href="menu_list.php" class="btn-outline-gold px-4 py-2">Batal</a>
    </form>
</div>

<?php include 'includes/admin_footer.php'; ?>
