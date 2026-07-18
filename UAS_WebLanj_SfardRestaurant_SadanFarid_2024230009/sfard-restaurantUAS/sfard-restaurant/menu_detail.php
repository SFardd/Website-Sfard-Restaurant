<?php
// ============================================================
// MENU_DETAIL.PHP — Menu Detail / Special Promo Page
// Extra PHP: Array, Function, Branching, Looping, Session
// ============================================================

$page_title = "Promo & Detail Menu";
include 'includes/header.php';

// ---- PHP VARIABLES ----
$hari_ini     = date('l');   // Nama hari Inggris
$jam_sekarang = (int) date('H');
$bulan_ini    = date('F Y');

// ---- FUNCTION: Hitung diskon ----
function hitungDiskon(float $harga, float $persen): array {
    $diskon = $harga * ($persen / 100);
    $setelah = $harga - $diskon;
    return [
        'original' => $harga,
        'diskon'   => $diskon,
        'setelah'  => $setelah,
        'persen'   => $persen,
    ];
}

// ---- FUNCTION: Status dapur ----
function statusDapur(int $jam): array {
    if ($jam >= 10 && $jam < 14) {
        return ['status' => 'Sibuk', 'color' => 'text-warning', 'icon' => 'bi-fire', 'msg' => 'Waktu makan siang — antrian lebih panjang'];
    } elseif ($jam >= 14 && $jam < 17) {
        return ['status' => 'Santai', 'color' => 'text-success', 'icon' => 'bi-check-circle-fill', 'msg' => 'Waktu terbaik untuk kunjungan!'];
    } elseif ($jam >= 17 && $jam < 22) {
        return ['status' => 'Ramai', 'color' => 'text-warning', 'icon' => 'bi-people-fill', 'msg' => 'Makan malam — Reservasi disarankan'];
    } else {
        return ['status' => 'Tutup', 'color' => 'text-danger', 'icon' => 'bi-x-circle-fill', 'msg' => 'Restoran sedang tutup'];
    }
}

// ---- ARRAY: Promo harian ----
$promo_harian = [
    'Monday'    => ['nama' => 'Monday Blues Buster',    'menu' => 'Lobster Thermidor',    'diskon' => 20],
    'Tuesday'   => ['nama' => 'Taco Tuesday (Nusantara)','menu' => 'Beef Rendang',       'diskon' => 15],
    'Wednesday' => ['nama' => 'Midweek Treat',           'menu' => 'Salmon Grilled',          'diskon' => 10],
    'Thursday'  => ['nama' => 'Throwback Thursday',      'menu' => 'Truffle Fries',       'diskon' => 25],
    'Friday'    => ['nama' => 'TGIF Special',            'menu' => 'Molten Chocolate Lava Cake',     'diskon' => 18],
    'Saturday'  => ['nama' => 'Weekend Feast',           'menu' => 'Paket Keluarga (4 orang)','diskon' => 30],
    'Sunday'    => ['nama' => 'Sunday Brunch',           'menu' => 'All-You-Can-Eat Buffet',  'diskon' => 20],
];

// ---- ARRAY: Harga referensi ----
$harga_referensi = [
    'Nasi Goreng Special'       => 55000,
    'Sate Ayam Madura'          => 45000,
    'Rendang Padang'            => 75000,
    'Gado-Gado Jakarta'         => 40000,
    'Ayam Bakar Taliwang'       => 65000,
    'Paket Keluarga (4 orang)'  => 280000,
    'All-You-Can-Eat Buffet'    => 150000,
];

// ---- BRANCHING #1: Cek promo hari ini ----
$promo_hari_ini = $promo_harian[$hari_ini] ?? null;
$info_dapur     = statusDapur($jam_sekarang);

// ---- BRANCHING #2: Hitung diskon hari ini ----
if ($promo_hari_ini) {
    $harga_asli = $harga_referensi[$promo_hari_ini['menu']] ?? 50000;
    $detail_diskon = hitungDiskon($harga_asli, $promo_hari_ini['diskon']);
}

// Simpan kunjungan halaman ini ke session
$_SESSION['halaman_detail_dikunjungi'] = date('Y-m-d H:i:s');
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Special Offers</div>
        <h1 class="section-title">Promo & Paket Katering</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="menu.php">Menu</a></li>
                <li class="breadcrumb-item active">Promo & Katering</li>
            </ol>
        </nav>
    </div>
</div>

<!-- PROMO HARI INI -->
<section class="section-dark py-5">
    <div class="container">

        <!-- Status Dapur -->
        <div class="text-center mb-5" data-reveal>
            <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill mb-3"
                 style="background:var(--dark-card); border:1px solid var(--border-gold)">
                <i class="<?= $info_dapur['icon'] ?> <?= $info_dapur['color'] ?>"></i>
                <span>Status Dapur: <strong class="<?= $info_dapur['color'] ?>"><?= $info_dapur['status'] ?></strong></span>
                <span class="text-muted small">— <?= $info_dapur['msg'] ?></span>
            </div>
            <div class="section-label">Daily Promo</div>
            <h2 class="section-title">Promo <?= $hari_ini ?></h2>
            <div class="section-divider mx-auto"></div>
        </div>

        <!-- Card Promo Hari Ini -->
        <?php if ($promo_hari_ini): ?>
            <div class="row justify-content-center mb-5">
                <div class="col-lg-6">
                    <div class="card-dark p-4 text-center" style="border-color:var(--gold);" data-reveal>
                        <div class="badge-gold mb-3" style="font-size:1rem; padding:0.5rem 1.5rem;">
                            <?= $promo_hari_ini['diskon'] ?>% OFF TODAY
                        </div>
                        <h3 style="font-family:var(--ff-heading); color:var(--gold)"><?= $promo_hari_ini['nama'] ?></h3>
                        <p style="color:var(--text-muted)" class="mb-3"><?= $promo_hari_ini['menu'] ?></p>

                        <?php
                        // ---- BRANCHING #3: Tampilkan detail harga diskon ----
                        if (isset($detail_diskon)): ?>
                            <div class="d-flex justify-content-center align-items-center gap-4 my-3">
                                <div>
                                    <div style="text-decoration:line-through; color:var(--text-muted); font-size:1rem">
                                        Rp <?= number_format($detail_diskon['original'], 0, ',', '.') ?>
                                    </div>
                                    <div style="font-family:var(--ff-display); font-size:1.8rem; color:var(--gold); font-weight:700;">
                                        Rp <?= number_format($detail_diskon['setelah'], 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:70px;height:70px;background:rgba(212,175,55,0.15);border:2px solid var(--gold);">
                                    <span style="font-family:var(--ff-display); color:var(--gold); font-size:1rem; font-weight:700;">
                                        -<?= $detail_diskon['persen'] ?>%
                                    </span>
                                </div>
                            </div>
                            <p class="small" style="color:var(--text-muted)">
                                Hemat Rp <?= number_format($detail_diskon['diskon'], 0, ',', '.') ?> hari ini!
                            </p>
                        <?php endif; ?>

                        <a href="reservation.php" class="btn-gold mt-2 px-5">
                            <i class="bi bi-calendar-check me-2"></i>Klaim Promo Ini
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Semua Promo Mingguan -->
        <div class="text-center mb-4" data-reveal>
            <div class="section-label">Weekly Schedule</div>
            <h3 class="section-title" style="font-size:2rem;">Promo Mingguan</h3>
        </div>
        <div class="row g-3">
            <?php
            // ---- LOOPING #1: Promo setiap hari ----
            foreach ($promo_harian as $hari => $promo):
                $is_today = ($hari === $hari_ini);
                $hrg = $harga_referensi[$promo['menu']] ?? 50000;
                $d   = hitungDiskon($hrg, $promo['diskon']);
            ?>
                <div class="col-md-6 col-lg-3" data-reveal>
                    <div class="card-dark p-3 h-100" style="border-color:<?= $is_today ? 'var(--gold)' : 'var(--border-gold)' ?>">
                        <?php if ($is_today): ?>
                            <span class="badge-gold d-block text-center mb-2">HARI INI</span>
                        <?php endif; ?>
                        <div class="small" style="color:var(--text-muted); text-transform:uppercase; letter-spacing:0.1em"><?= $hari ?></div>
                        <h6 class="mt-1 mb-1" style="color:var(--text-main); font-family:var(--ff-heading)"><?= $promo['nama'] ?></h6>
                        <div class="small mb-2" style="color:var(--text-muted)"><?= $promo['menu'] ?></div>
                        <div style="color:var(--gold); font-family:var(--ff-display); font-weight:700;">
                            Diskon <?= $promo['diskon'] ?>%
                        </div>
                        <div class="small text-muted mt-1">
                            <s>Rp <?= number_format($d['original'], 0, ',', '.') ?></s>
                            → <strong style="color:var(--text-main)">Rp <?= number_format($d['setelah'], 0, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PAKET KATERING -->
<section class="section-mid py-5">
    <div class="container">
        <div class="text-center mb-5" data-reveal>
            <div class="section-label">Catering Service</div>
            <h2 class="section-title">Paket Katering</h2>
            <div class="section-divider mx-auto"></div>
            <p class="mt-3" style="color:var(--text-muted); max-width:500px; margin:0 auto;">
                Percayakan momen spesial Anda kepada SFard. Layanan katering untuk acara perusahaan, pernikahan, dan gathering keluarga.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php
            // ---- LOOPING #2: Paket katering ----
            foreach ($paket_katering as $pkt): ?>
                <div class="col-md-6 col-lg-4" data-reveal>
                    <div class="card-dark p-4 h-100 text-center position-relative"
                         style="border-color:<?= $pkt['popular'] ? 'var(--gold)' : 'var(--border-gold)' ?>">
                        <?php if ($pkt['popular']): ?>
                            <div style="position:absolute; top:-14px; left:50%; transform:translateX(-50%)">
                                <span class="badge-gold px-3 py-2">PALING POPULER</span>
                            </div>
                        <?php endif; ?>
                        <h5 class="mt-3" style="font-family:var(--ff-display); color:var(--gold); letter-spacing:0.1em"><?= $pkt['nama'] ?></h5>
                        <div class="my-3">
                            <span style="font-family:var(--ff-display); font-size:2rem; color:var(--text-main); font-weight:700;">
                                Rp <?= number_format($pkt['harga'], 0, ',', '.') ?>
                            </span>
                            <span style="color:var(--text-muted)"><?= $pkt['per'] ?></span>
                        </div>
                        <div class="small text-muted mb-3">Min. <?= $pkt['min'] ?> orang</div>
                        <ul class="list-unstyled text-start">
                            <?php
                            // ---- LOOPING #3: Fitur per paket ----
                            foreach ($pkt['fitur'] as $f): ?>
                                <li class="py-1 border-bottom d-flex align-items-center gap-2"
                                    style="border-color:var(--border-gold)!important; color:var(--text-muted); font-size:0.88rem">
                                    <i class="bi bi-check-circle-fill text-gold small"></i><?= $f ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="contact.php" class="<?= $pkt['popular'] ? 'btn-gold' : 'btn-outline-gold' ?> w-100 text-center mt-4 py-2">
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
