<?php
// ============================================================
// CONTACT.PHP — Contact Page
// Form Input, Branching, Looping, Session, Array, Function
// ============================================================

$page_title = "Contact Us";
include 'includes/header.php';
require_once 'includes/load_classes.php';

// ---- PHP VARIABLES ----
$form_submitted = false;
$form_errors    = [];
$form_values    = [];

// ---- FUNCTION: Bersihkan input ----
function bersihkanInput(string $input): string {
    return htmlspecialchars(trim(strip_tags($input)));
}

// ---- FUNCTION: Validasi panjang pesan ----
function validasiPesan(string $pesan, int $min = 10, int $max = 1000): array {
    $panjang = strlen($pesan);
    if ($panjang < $min) return ['valid' => false, 'msg' => "Pesan terlalu pendek (minimum {$min} karakter)."];
    if ($panjang > $max) return ['valid' => false, 'msg' => "Pesan terlalu panjang (maksimum {$max} karakter)."];
    return ['valid' => true, 'msg' => ''];
}

// ---- ARRAY: Topik pesan ----
$topik_list = [
    'umum'        => 'Pertanyaan Umum',
    'reservasi'   => 'Informasi Reservasi',
    'menu'        => 'Informasi Menu',
    'katering'    => 'Layanan Katering',
    'acara'       => 'Private Event',
    'masukan'     => 'Masukan & Saran',
    'komplain'    => 'Keluhan',
    'lainnya'     => 'Lainnya',
];

// ======== PROSES FORM KONTAK ========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_kontak'])) {

    $form_values = [
        'nama'   => bersihkanInput($_POST['nama'] ?? ''),
        'email'  => bersihkanInput($_POST['email'] ?? ''),
        'topik'  => bersihkanInput($_POST['topik'] ?? ''),
        'pesan'  => bersihkanInput($_POST['pesan'] ?? ''),
        'rating' => (int) ($_POST['rating'] ?? 0),
    ];

    // ---- BRANCHING #1: Validasi nama ----
    if (strlen($form_values['nama']) < 3) {
        $form_errors[] = "Nama minimal 3 karakter.";
    }

    // ---- BRANCHING #2: Validasi email ----
    if (!filter_var($form_values['email'], FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Format email tidak valid.";
    }

    // ---- BRANCHING #3: Validasi topik ----
    if (empty($form_values['topik']) || !array_key_exists($form_values['topik'], $topik_list)) {
        $form_errors[] = "Silakan pilih topik pesan.";
    }

    // Validasi panjang pesan menggunakan function
    $cek_pesan = validasiPesan($form_values['pesan']);
    if (!$cek_pesan['valid']) {
        $form_errors[] = $cek_pesan['msg'];
    }

    if (empty($form_errors)) {
        // ---- OOP: simpan pesan kontak via MessageModel (extends Database) ----
        $topik_label = $topik_list[$form_values['topik']] ?? $form_values['topik'];
        $messageModel = new MessageModel();
        $messageModel->create([
            'nama'   => $form_values['nama'],
            'email'  => $form_values['email'],
            'topik'  => $topik_label,
            'pesan'  => $form_values['pesan'],
            'rating' => $form_values['rating'],
        ]);

        // Simpan ke SESSION sebagai riwayat pesan (untuk ditampilkan di sidebar)
        if (!isset($_SESSION['riwayat_pesan'])) {
            $_SESSION['riwayat_pesan'] = [];
        }
        $_SESSION['riwayat_pesan'][] = [
            'nama'   => $form_values['nama'],
            'email'  => $form_values['email'],
            'topik'  => $topik_list[$form_values['topik']],
            'waktu'  => date('d M Y H:i'),
            'rating' => $form_values['rating'],
        ];
        $form_submitted = true;
        $form_values    = [];
    }
}

// Ambil riwayat pesan dari session
$riwayat = $_SESSION['riwayat_pesan'] ?? [];
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Get in Touch</div>
        <h1 class="section-title">Hubungi Kami</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<!-- CONTACT SECTION -->
<section class="section-dark py-5">
    <div class="container">
        <div class="row g-5">
            <!-- FORM -->
            <div class="col-lg-7" data-reveal>

                <!-- Sukses -->
                <?php if ($form_submitted): ?>
                    <div class="alert-success-dark mb-4 alert-auto-dismiss">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Pesan terkirim!</strong> Terima kasih telah menghubungi kami. Tim kami akan membalas dalam 1×24 jam.
                    </div>
                <?php endif; ?>

                <!-- Errors -->
                <?php if (!empty($form_errors)): ?>
                    <div class="alert-error-dark mb-4">
                        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Perbaiki kesalahan:</strong>
                        <ul class="mt-2 mb-0">
                            <?php
                            // ---- LOOPING: Tampilkan errors ----
                            foreach ($form_errors as $err): ?>
                                <li><?= $err ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-dark">
                    <h4 class="mb-4" style="font-family:var(--ff-heading);">
                        <i class="bi bi-envelope-fill text-gold me-2"></i>Kirim Pesan
                    </h4>

                    <form method="POST" action="contact.php">

                        <!-- Nama & Email -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-dark">Nama Lengkap *</label>
                                <input type="text" name="nama" class="form-control form-control-dark"
                                       placeholder="Nama Anda" required minlength="3"
                                       value="<?= htmlspecialchars($form_values['nama'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-dark">Email *</label>
                                <input type="email" name="email" class="form-control form-control-dark"
                                       placeholder="email@domain.com" required
                                       value="<?= htmlspecialchars($form_values['email'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Topik -->
                        <div class="mb-3">
                            <label class="form-label-dark">Topik Pesan *</label>
                            <select name="topik" class="form-control form-control-dark" required>
                                <option value="">-- Pilih Topik --</option>
                                <?php
                                // ---- LOOPING: Topik dropdown ----
                                foreach ($topik_list as $key => $label): ?>
                                    <option value="<?= $key ?>"
                                        <?= ($form_values['topik'] ?? '') === $key ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Pesan -->
                        <div class="mb-3">
                            <label class="form-label-dark">Pesan *</label>
                            <textarea name="pesan" class="form-control form-control-dark" rows="5"
                                      placeholder="Tuliskan pesan Anda (min. 10 karakter)..." required><?= htmlspecialchars($form_values['pesan'] ?? '') ?></textarea>
                        </div>

                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="form-label-dark">Rating Kunjungan (opsional)</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php
                                // ---- LOOPING: Rating 1-5 ----
                                for ($r = 1; $r <= 5; $r++): ?>
                                    <label class="d-flex align-items-center gap-2 small" style="cursor:pointer; color:var(--text-muted)">
                                        <input type="radio" name="rating" value="<?= $r ?>"
                                               <?= ($form_values['rating'] ?? 0) == $r ? 'checked' : '' ?>>
                                        <span style="color:var(--gold)"><?= str_repeat('★', $r) ?><?= str_repeat('☆', 5 - $r) ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <button type="submit" name="submit_kontak" class="btn-gold w-100 py-3">
                            <i class="bi bi-send-fill me-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>

            <!-- SIDEBAR INFO -->
            <div class="col-lg-5" data-reveal>
                <!-- Kontak Info -->
                <div class="card-dark p-4 mb-4">
                    <h5 style="font-family:var(--ff-heading)" class="mb-4">
                        <i class="bi bi-telephone text-gold me-2"></i>Informasi Kontak
                    </h5>
                    <?php
                    $kontak_info = [
                        ['icon' => 'bi-telephone-fill',    'label' => 'Telepon',  'val' => $restaurant_phone,   'link' => 'tel:' . preg_replace('/\s/', '', $restaurant_phone)],
                        ['icon' => 'bi-whatsapp',          'label' => 'WhatsApp', 'val' => $restaurant_phone,   'link' => 'https://wa.me/6281234567890'],
                        ['icon' => 'bi-envelope-fill',     'label' => 'Email',    'val' => $restaurant_email,   'link' => 'mailto:' . $restaurant_email],
                        ['icon' => 'bi-geo-alt-fill',      'label' => 'Alamat',   'val' => $restaurant_address, 'link' => '#'],
                    ];
                    // ---- LOOPING: Kontak info ----
                    foreach ($kontak_info as $item): ?>
                        <a href="<?= $item['link'] ?>" class="d-flex align-items-start gap-3 mb-3 text-decoration-none" style="color:var(--text-main)">
                            <div class="social-btn" style="min-width:38px">
                                <i class="<?= $item['icon'] ?> text-gold"></i>
                            </div>
                            <div>
                                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.1em"><?= $item['label'] ?></div>
                                <div class="small"><?= $item['val'] ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Map placeholder -->
                <div class="map-placeholder mb-4">
                    <i class="bi bi-map-fill text-gold fs-1"></i>
                    <div class="text-center">
                        <div><?= $restaurant_address ?></div>
                        <a href="https://maps.google.com" target="_blank" class="btn-gold mt-2 py-2 px-4" style="font-size:0.8rem">
                            <i class="bi bi-geo-alt me-1"></i>Buka di Maps
                        </a>
                    </div>
                </div>

                <!-- Riwayat Pesan (Session) -->
                <?php if (!empty($riwayat)): ?>
                    <div class="card-dark p-4">
                        <h6 style="color:var(--gold); font-family:var(--ff-display)">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Pesan Anda
                        </h6>
                        <?php
                        // ---- LOOPING: Riwayat pesan dari session ----
                        $riwayat_tampil = array_reverse($riwayat);
                        foreach (array_slice($riwayat_tampil, 0, 3) as $r): ?>
                            <div class="p-2 rounded mb-2" style="background:var(--dark-mid); border:1px solid var(--border-gold);">
                                <div class="d-flex justify-content-between">
                                    <strong class="small"><?= htmlspecialchars($r['nama']) ?></strong>
                                    <?php if ($r['rating'] > 0): ?>
                                        <span style="color:var(--gold); font-size:0.75rem"><?= str_repeat('★', $r['rating']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted"><?= htmlspecialchars($r['topik']) ?></div>
                                <div style="font-size:0.72rem; color:var(--text-muted)"><?= $r['waktu'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
