<?php
// ============================================================
// INDEX.PHP — Home Page
// PHP Variables, Branching, Looping, Array, Function
// ============================================================

$page_title = "Home";
include 'includes/header.php';

// ---- PHP VARIABLE ----
$hero_tagline   = "Cita Rasa Autentik ";
$welcome_text   = "Selamat datang di SFard Restaurant, tempat di mana setiap hidangan menceritakan kisah kuliner beberapa negara yang kaya dan beragam.";
$promo_active   = true;   // Branching #1
$is_weekend     = in_array(date('N'), [6, 7]); // Branching #2

// ---- ARRAY: Featured menu ----
$featured_menu = [
    ['name' => 'Lobster Thermidor',  'desc' => 'Hidangan mewah khas Prancis berupa lobster yang dagingnya dimasak dengan saus krim kaya rasa', 'price' => 355000,  'img' => 'lobster.jpg', 'badge' => 'Bestseller', 'spicy' => false],
    ['name' => 'Beef Rendang',    'desc' => 'Daging sapi empuk dengan rempah-rempah Minangkabau',  'price' => 275000,  'img' => 'rendang.jpg', 'badge' => 'Signature', 'spicy' => true],
    ['name' => 'Salmon Grilled','desc' => 'Salmon premium seperti Cajun Salmon with Lemon Butter Sauce',       'price' => 250000,  'img' => 'salmon.jpg', 'badge' => 'Popular',   'spicy' => false],
    ['name' => 'Chicken Cordon Bleu', 'desc' => 'hidangan dada ayam fillet yang diisi smoked beef atau ham serta lelehan keju cheddar, kemudian dibalut tepung roti renyah dan digoreng hingga keemasan.',       'price' => 240000,  'img' => 'chicken.jpg', 'badge' => '',          'spicy' => false],
];

// ---- FUNCTION: Format harga Rupiah ----
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// ---- FUNCTION: Hitung rating bintang ----
function renderStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating) ? '★' : '☆';
    }
    return $stars;
}

// ---- ARRAY: Testimonials ----
$testimonials = [
    ['name' => 'Budi Santoso',    'rating' => 5, 'city' => 'Jakarta',   'text' => 'Rendang di sini benar-benar autentik! Dagingnya empuk dan bumbunya meresap sempurna. Wajib dicoba!'],
    ['name' => 'Siti Rahayu',     'rating' => 5, 'city' => 'Bandung',   'text' => 'Suasana restoran sangat nyaman dan elegan. Pelayanannya ramah dan makanannya luar biasa enak.'],
    ['name' => 'Ahmad Fauzi',     'rating' => 4, 'city' => 'Surabaya',  'text' => 'Salmon Grilled-nya bikin ketagihan! Porsi cukup besar dan harganya sangat worth it.'],
];

// ---- ARRAY: Stats ----
$stats = [
    ['number' => 15,   'suffix' => '+', 'label' => 'Years Experience'],
    ['number' => 50,   'suffix' => '+', 'label' => 'Signature Dishes'],
    ['number' => 2500, 'suffix' => '+', 'label' => 'Happy Customers'],
    ['number' => 12,   'suffix' => '',  'label' => 'Awards Won'],
];
?>

<!-- ======== HERO SECTION ======== -->
<section class="hero-section">
    <div class="hero-bg"><img src="img/Heroo.jpg" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:-1;"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-7">
                <div class="hero-eyebrow">
                    <i class="bi bi-star-fill me-2"></i><?= $hero_tagline ?>
                </div>
                <h1 class="hero-title">
                    Experience<br><span>Authentic</span><br>Flavors
                </h1>
                <p class="hero-subtitle mt-3 mb-4"><?= $welcome_text ?></p>

                <?php
                // ---- BRANCHING #1: Tampilkan promo jika aktif ----
                if ($promo_active): ?>
                    <div class="d-inline-flex align-items-center gap-2 border border-warning rounded px-3 py-2 mb-4 small"
                         style="color:var(--gold-light); background:rgba(212,175,55,0.08);">
                        <i class="bi bi-tag-fill text-warning"></i>
                        <strong>Promo Spesial:</strong> Diskon 15% untuk reservasi online!
                    </div>
                <?php endif; ?>

                <?php
                // ---- BRANCHING #2: Weekend special message ----
                if ($is_weekend): ?>
                    <div class="d-block mb-3 small text-warning">
                        <i class="bi bi-calendar-event me-1"></i>Selamat datang di <strong>Weekend Special</strong> — Menu eksklusif tersedia hari ini!
                    </div>
                <?php else: ?>
                    <div class="d-block mb-3 small" style="color:var(--text-muted)">
                        <i class="bi bi-calendar me-1"></i>Buka setiap hari — Weekend Special tersedia Sabtu & Minggu!
                    </div>
                <?php endif; ?>

                <div class="hero-buttons d-flex gap-3 flex-wrap">
                    <a href="menu.php" class="btn-gold"><i class="bi bi-journal-richtext me-2"></i>Explore Menu</a>
                    <a href="reservation.php" class="btn-outline-gold"><i class="bi bi-calendar-check me-2"></i>Reserve Table</a>
                </div>
            </div>
        </div>
    </div>
    <a href="#featured" class="hero-scroll">
        <i class="bi bi-chevron-double-down fs-4"></i>
    </a>
</section>

<!-- ======== STATS SECTION ======== -->
<section class="section-mid py-5" id="featured">
    <div class="container">
        <div class="row g-4 text-center">
            <?php
            // ---- LOOPING #1: Render stats ----
            foreach ($stats as $index => $stat): ?>
                <div class="col-6 col-md-3" data-reveal>
                    <div class="stat-item">
                        <div class="stat-number">
                            <span data-count="<?= $stat['number'] ?>" data-suffix="<?= $stat['suffix'] ?>">0</span>
                        </div>
                        <div class="stat-label mt-1"><?= $stat['label'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======== FEATURED MENU ======== -->
<section class="section-dark py-6 py-5">
    <div class="container">
        <div class="text-center mb-5" data-reveal>
            <div class="section-label"><i class="bi bi-star me-2"></i>Our Specialties</div>
            <h2 class="section-title">Menu Unggulan</h2>
            <div class="section-divider mx-auto"></div>
            <p class="mt-3" style="color:var(--text-muted); max-width:550px; margin:0 auto;">
                Nikmati pilihan hidangan terbaik kami yang dimasak dengan resep turun-temurun dan bahan-bahan segar pilihan.
            </p>
        </div>

        <div class="row g-4">
            <?php
            // ---- LOOPING #2: Featured menu cards ----
            foreach ($featured_menu as $item): ?>
                <div class="col-md-6 col-lg-3" data-reveal>
                    <div class="card-dark h-100">
                        <div class="card-img-wrap">
                            <img src="img/<?= $item['img'] ?>" alt="<?= $item['name'] ?>">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0" style="color:var(--text-main); font-family:var(--ff-heading);">
                                    <?= $item['name'] ?>
                                </h6>
                                <?php if ($item['badge']): ?>
                                    <span class="badge-gold ms-2"><?= $item['badge'] ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($item['spicy']): ?>
                                <span class="badge-spicy d-inline-block mb-2" style="width:fit-content">
                                    <i class="bi bi-fire me-1"></i>Pedas
                                </span>
                            <?php endif; ?>
                            <p class="small mb-3 flex-grow-1" style="color:var(--text-muted)"><?= $item['desc'] ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="menu-price"><?= formatRupiah($item['price']) ?></span>
                                <a href="menu.php" class="btn-outline-gold py-1 px-3" style="font-size:0.75rem;">Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="menu.php" class="btn-gold"><i class="bi bi-journal-richtext me-2"></i>Lihat Semua Menu</a>
        </div>
    </div>
</section>

<!-- ======== ABOUT PREVIEW ======== -->
<section class="section-mid py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5" data-reveal>
                <div class="about-img-wrap pe-3">
                    <img src="img/aboutus.jpg" alt="About SFard" class="img-fluid w-100">
                    <div class="about-badge">
                        <div style="font-size:1.4rem; font-weight:800;">15+</div>
                        <div style="font-size:0.65rem; letter-spacing:0.05em">Years of<br>Excellence</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-reveal>
                <div class="section-label">Our Story</div>
                <h2 class="section-title">Warisan Kuliner<br>yang Tak Tertandingi</h2>
                <div class="section-divider ms-0 mb-4"></div>
                <p style="color:var(--text-muted)">
                    SFard Restaurant lahir dari kecintaan mendalam terhadap kekayaan kuliner Dunia. Didirikan tahun 2009, kami telah melayani ribuan tamu dengan sajian autentik yang memadukan tradisi dan keahlian modern.
                </p>
                <p style="color:var(--text-muted)">
                    Setiap hidangan kami disiapkan dengan bahan-bahan segar pilihan dan premium, memastikan cita rasa yang sesungguhnya hadir di setiap suapan.
                </p>
                <div class="row g-3 mt-2">
                    <?php
                    $keunggulan = ['Bahan Segar Premium', 'Resep Autentik', 'Chef Berpengalaman', 'Layanan Bintang 5'];
                    // ---- LOOPING #3 ----
                    foreach ($keunggulan as $k): ?>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-gold"></i>
                                <span class="small"><?= $k ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4">
                    <a href="about.php" class="btn-gold"><i class="bi bi-info-circle me-2"></i>Read Our Story</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======== TESTIMONIALS ======== -->
<section class="section-dark py-5">
    <div class="container">
        <div class="text-center mb-5" data-reveal>
            <div class="section-label">Testimonials</div>
            <h2 class="section-title">Kata Pelanggan Kami</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php
            // ---- LOOPING #4: Testimonials ----
            foreach ($testimonials as $t): ?>
                <div class="col-md-4" data-reveal>
                    <div class="testimonial-card h-100">
                        <div class="testimonial-stars mb-2"><?= renderStars($t['rating']) ?></div>
                        <p class="mb-3" style="color:var(--text-muted)"><?= $t['text'] ?></p>
                        <div class="d-flex align-items-center gap-2 mt-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:42px;height:42px;background:var(--gold);color:#0D0A06;font-weight:700">
                                <?= strtoupper(substr($t['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-bold small"><?= $t['name'] ?></div>
                                <div class="text-muted" style="font-size:0.75rem"><i class="bi bi-geo-alt-fill me-1"></i><?= $t['city'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======== CTA RESERVATION ======== -->
<section class="py-5" style="background: linear-gradient(135deg, var(--dark-card), var(--dark-mid)); border-top:1px solid var(--border-gold); border-bottom:1px solid var(--border-gold);">
    <div class="container text-center" data-reveal>
        <div class="section-label">Book a Table</div>
        <h2 class="section-title">Reservasi Sekarang</h2>
        <p class="mb-4" style="color:var(--text-muted); max-width:500px; margin:0 auto;">
            Amankan meja Anda dan nikmati pengalaman makan malam yang tak terlupakan bersama orang-orang terkasih.
        </p>
        <a href="reservation.php" class="btn-gold px-5 py-3" style="font-size:1rem">
            <i class="bi bi-calendar-check me-2"></i>Buat Reservasi
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
