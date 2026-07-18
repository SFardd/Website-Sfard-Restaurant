<?php
// ============================================================
// GALLERY.PHP — Gallery Page
// ============================================================

$page_title = "Gallery";
include 'includes/header.php';

// ---- PHP VARIABLE ----
$jumlah_foto = 6;

// ---- ARRAY: Gallery items ----
$gallery_items = [
    ['img' => 'gallery_main.jpg', 'judul' => 'Main Dining Area',    'desc' => 'Ruang makan utama yang elegan dan nyaman'],
    ['img' => 'gallery_private.jpg', 'judul' => 'Private Dining',      'desc' => 'Ruang privat untuk acara eksklusif'],
    ['img' => 'gallery_open.jpg', 'judul' => 'Open Kitchen',        'desc' => 'Dapur terbuka modern kami'],
    ['img' => 'gallery_outdoor.jpg', 'judul' => 'Outdoor Terrace',     'desc' => 'Area outdoor yang asri dan sejuk'],
    ['img' => 'gallery_chef.jpg', 'judul' => 'Chef\'s Table',       'desc' => 'Pengalaman makan di meja chef'],
    ['img' => 'gallery_lounge.jpg', 'judul' => 'Lounge Area',         'desc' => 'Area lounge nyaman untuk bersantai'],
];

// ---- ARRAY: Kategori filter ----
$filter_gallery = $_GET['type'] ?? 'all';
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Visual Journey</div>
        <h1 class="section-title">Gallery</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
    </div>
</div>

<!-- GALLERY -->
<section class="section-dark py-5">
    <div class="container">
        <div class="text-center mb-5" data-reveal>
            <div class="section-label">Our Space</div>
            <h2 class="section-title">Jelajahi Restoran Kami</h2>
            <div class="section-divider mx-auto"></div>
            <p class="mt-3" style="color:var(--text-muted); max-width:500px; margin:0 auto;">
                Setiap sudut SFard Restaurant dirancang untuk memberikan pengalaman kuliner yang tak terlupakan.
            </p>
        </div>

        <!-- Branching: tampilkan jumlah foto yang terfilter -->
        <div class="text-center mb-4 small" style="color:var(--text-muted)" data-reveal>
            <?php
            // ---- BRANCHING #1: Pesan berdasarkan jumlah foto ----
            if ($jumlah_foto >= 6): ?>
                <i class="bi bi-images me-1 text-gold"></i>Menampilkan <?= $jumlah_foto ?> foto terpilih dari koleksi kami
            <?php elseif ($jumlah_foto >= 3): ?>
                <i class="bi bi-images me-1 text-gold"></i>Menampilkan <?= $jumlah_foto ?> foto pilihan
            <?php else: ?>
                <i class="bi bi-images me-1 text-gold"></i>Menampilkan <?= $jumlah_foto ?> foto
            <?php endif; ?>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid" data-reveal>
            <?php
            // ---- LOOPING: Gallery items ----
            foreach ($gallery_items as $foto): ?>
                <div class="gallery-item">
                    <img src="img/<?= $foto['img'] ?>" alt="<?= $foto['judul'] ?>">
                    <div class="gallery-overlay">
                        <i class="bi bi-zoom-in"></i>
                    </div>
                    <div class="position-absolute bottom-0 start-0 w-100 p-3"
                         style="background:linear-gradient(transparent, rgba(0,0,0,0.8));">
                        <div class="small fw-bold"><?= $foto['judul'] ?></div>
                        <div class="small" style="color:var(--text-muted)"><?= $foto['desc'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- BRANCHING #2: Pesan CTA -->
        <?php
        $has_private_room = true;
        if ($has_private_room): ?>
            <div class="text-center mt-5 p-4 rounded" style="background:var(--dark-card); border:1px solid var(--border-gold);" data-reveal>
                <h5 style="font-family:var(--ff-heading)">Ingin melihat langsung?</h5>
                <p style="color:var(--text-muted)" class="mb-3">Reservasi meja Anda dan rasakan sendiri keindahan SFard Restaurant</p>
                <a href="reservation.php" class="btn-gold"><i class="bi bi-calendar-check me-2"></i>Buat Reservasi</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
