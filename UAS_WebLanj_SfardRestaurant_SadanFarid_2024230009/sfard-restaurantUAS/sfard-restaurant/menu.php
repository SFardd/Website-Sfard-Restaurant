<?php
// ============================================================
// MENU.PHP — Menu Page
// ============================================================

$page_title = "Our Menu";
include 'includes/header.php';
require_once 'includes/load_classes.php';

$currency = "IDR";

function formatHarga(float $harga): string {
    return 'Rp ' . number_format($harga, 0, ',', '.');
}

function tampilBintang(float $rating): string {
    $bintang = "";
    $bulat = round($rating);
    for ($i = 1; $i <= 5; $i++) {
        $bintang .= ($i <= $bulat) ? "★" : "☆";
    }
    return $bintang;
}

function getBadgeKategori(string $cat): string {
    $map = [
        'makanan_berat'  => '🍛 Makanan Berat',
        'makanan_ringan' => '🥗 Makanan Ringan',
        'minuman'        => '🥤 Minuman',
        'dessert'        => '🍮 Dessert',
    ];
    return $map[$cat] ?? $cat;
}

// ---- OOP: pakai MenuModel (extends Database) untuk ambil data ----
$menuModel = new MenuModel();

$semua_menu = [];
$menuQuery = $menuModel->getShopMenu();
while ($row = mysqli_fetch_assoc($menuQuery)) {
    $semua_menu[] = [
        'id'        => $row['id'],
        'nama'      => $row['name'],
        'harga'     => (float) $row['price'],
        'deskripsi' => $row['description'],
        'img'       => $row['image'],
        'kategori'  => $row['kategori_slug'],
        'badge'     => $row['badge'],
        'spicy'     => (bool) $row['is_spicy'],
        'veg'       => (bool) $row['is_veg'],
        'rating'    => (float) $row['rating'],
        'tersedia'  => (bool) $row['is_available'],
    ];
}

// ---- DATABASE: ambil label kategori ----
$kategori_labels = [];
$catQuery = $menuModel->getCategories();
while ($row = mysqli_fetch_assoc($catQuery)) {
    $kategori_labels[$row['slug']] = $row['name'];
}

$filter_aktif = $_GET['cat'] ?? 'all';
$valid_filters = ['all', 'makanan_berat', 'makanan_ringan', 'minuman', 'dessert'];
if (!in_array($filter_aktif, $valid_filters)) $filter_aktif = 'all';

$menu_tampil = [];
$count_per_kat = [];
foreach ($semua_menu as $item) {
    $k = $item['kategori'];
    $count_per_kat[$k] = ($count_per_kat[$k] ?? 0) + 1;
    if ($filter_aktif === 'all' || $item['kategori'] === $filter_aktif) {
        $menu_tampil[] = $item;
    }
}

$total_tersedia = count(array_filter($semua_menu, fn($m) => $m['tersedia']));
$total_harga = array_sum(array_column($semua_menu, 'harga'));
$rata_harga = count($semua_menu) ? $total_harga / count($semua_menu) : 0;
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Culinary Experience</div>
        <h1 class="section-title">Menu Kami</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Menu</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5" style="background:#0f0c08;">
    <div class="container">

        <!-- Statistik -->
        <div class="row g-3 mb-5" data-reveal>
            <div class="col-md-4 text-center">
                <div class="nusa-stat-box">
                    <span class="nusa-stat-num"><?= count($semua_menu) ?></span>
                    <span class="nusa-stat-label">Total Menu</span>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="nusa-stat-box">
                    <span class="nusa-stat-num"><?= $total_tersedia ?></span>
                    <span class="nusa-stat-label">Menu Tersedia</span>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="nusa-stat-box">
                    <span class="nusa-stat-num" style="font-size:1.3rem;"><?= formatHarga($rata_harga) ?></span>
                    <span class="nusa-stat-label">Rata-rata Harga</span>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="text-center mb-5" data-reveal>
            <a href="menu.php" class="nusa-btn-filter <?= $filter_aktif === 'all' ? 'active' : '' ?>">
                Semua (<?= count($semua_menu) ?>)
            </a>
            <?php foreach ($kategori_labels as $key => $label): ?>
                <a href="menu.php?cat=<?= $key ?>" class="nusa-btn-filter <?= $filter_aktif === $key ? 'active' : '' ?>">
                    <?= $label ?> (<?= $count_per_kat[$key] ?? 0 ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Info harga -->
        <div class="text-center mb-4 small" style="color:var(--text-muted)">
            <i class="bi bi-info-circle me-1"></i>Harga sudah termasuk pajak &bull; Semua menu dibuat segar setiap hari
        </div>

        <!-- Menu Cards -->
        <div class="row g-4">
            <?php if (empty($menu_tampil)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-gold mb-3 d-block"></i>
                    <p style="color:var(--text-muted)">Tidak ada menu untuk kategori ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($menu_tampil as $item): ?>
                <div class="col-sm-6 col-lg-4" data-reveal>
                    <div class="nusa-menu-card <?= !$item['tersedia'] ? 'nusa-habis' : '' ?>">

                        <?php if (!$item['tersedia']): ?>
                            <div class="nusa-habis-badge">Habis</div>
                        <?php endif; ?>

                        <?php if ($item['badge']): ?>
                            <div class="nusa-top-badge"><?= $item['badge'] ?></div>
                        <?php endif; ?>

                        <!-- Image area -->
                        <div class="nusa-img-wrap">
                            <img src="img/<?= $item['img'] ?>" alt="<?= $item['nama'] ?>" class="nusa-img">
                            <?php if ($item['spicy']): ?>
                                <span class="nusa-spicy-tag">🌶 Pedas</span>
                            <?php endif; ?>
                            <?php if ($item['veg']): ?>
                                <span class="nusa-veg-tag">🌿 Veg</span>
                            <?php endif; ?>
                        </div>

                        <!-- Body -->
                        <div class="nusa-card-body">
                            <span class="nusa-badge-kat"><?= getBadgeKategori($item['kategori']) ?></span>
                            <h5 class="nusa-nama"><?= $item['nama'] ?></h5>
                            <p class="nusa-desc"><?= $item['deskripsi'] ?></p>
                            <div class="nusa-rating mb-3">
                                <span class="nusa-stars"><?= tampilBintang($item['rating']) ?></span>
                                <span class="nusa-rating-num"><?= $item['rating'] ?></span>
                            </div>
                            <div class="nusa-footer">
                                <span class="nusa-harga"><?= formatHarga($item['harga']) ?></span>
                                <?php if ($item['tersedia']): ?>
                                    <a href="pesan.php?id=<?= $item['id'] ?>" class="nusa-btn-pesan">
                                        <i class="bi bi-bag-plus me-1"></i>Pesan
                                    </a>
                                <?php else: ?>
                                    <span class="nusa-btn-habis">Habis</span>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Summary -->
        <div class="text-center mt-5" data-reveal>
            <p style="color:var(--text-muted)">
                Menampilkan <strong class="text-gold"><?= count($menu_tampil) ?></strong>
                dari <strong class="text-gold"><?= count($semua_menu) ?></strong> menu
            </p>
            <a href="reservation.php" class="btn-gold mt-2">
                <i class="bi bi-calendar-check me-2"></i>Reservasi & Pesan Sekarang
            </a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
