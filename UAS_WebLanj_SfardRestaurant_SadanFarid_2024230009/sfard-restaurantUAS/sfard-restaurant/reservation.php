<?php
// ============================================================
// RESERVATION.PHP — Reservation Form Page
// Form Input, Session, Branching, Looping, Function, Array
// Wajib login supaya reservasi bisa tertaut ke akun & ada riwayat
// ============================================================
ob_start(); // buffer output biar header()/redirect tetap bisa dipanggil setelah HTML nge-print
require_once 'includes/functions.php';
cekLoginUser();

$page_title = "Reservation";
include 'includes/header.php';
require_once 'includes/load_classes.php';

// ---- PHP VARIABLES ----
$pesan_sukses = '';
$pesan_error  = [];
$form_data    = [];

// ---- FUNCTION: Validasi email ----
function validasiEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ---- FUNCTION: Validasi tanggal reservasi ----
function validasiTanggal(string $tanggal): bool {
    $tgl = strtotime($tanggal);
    $hari_ini = strtotime(date('Y-m-d'));
    return $tgl !== false && $tgl >= $hari_ini;
}

// ---- FUNCTION: Format konfirmasi ----
function formatKonfirmasi(array $data): string {
    return "#{$data['nama'][0]}" . strtoupper(substr($data['nama'], -2)) . rand(100, 999);
}

// ---- ARRAY: Pilihan jam reservasi ----
$pilihan_jam = [
    '10:00', '11:00', '12:00', '13:00', '14:00',
    '17:00', '18:00', '19:00', '20:00', '21:00',
];

// ---- ARRAY: Pilihan jumlah tamu ----
$pilihan_tamu = [1, 2, 3, 4, 5, 6, 7, 8, 10, 12, 15, 20];

// ---- ARRAY: Pilihan meja ----
$tipe_meja = [
    'regular'  => 'Regular Table',
    'vip'      => 'VIP Table (+Rp 100.000)',
    'private'  => 'Private Dining Room (+Rp 300.000)',
    'outdoor'  => 'Outdoor Terrace',
];

// ---- ARRAY: Pilihan paket ----
$paket = [
    'none'   => 'Tanpa Paket',
    'basic'  => 'Paket Basic  - Rp 750.000/orang',
    'dinner' => 'Paket Dinner - Rp 850.000/orang',
    'vip'    => 'Paket VIP  - Rp 1.000.000/orang',
];

// ======== PROSES FORM ========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservasi'])) {

    // ---- Ambil data input ----
    $form_data = [
        'nama'      => trim($_POST['nama'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'telepon'   => trim($_POST['telepon'] ?? ''),
        'tanggal'   => trim($_POST['tanggal'] ?? ''),
        'jam'       => trim($_POST['jam'] ?? ''),
        'tamu'      => (int) ($_POST['tamu'] ?? 0),
        'meja'      => trim($_POST['meja'] ?? ''),
        'paket'     => trim($_POST['paket'] ?? ''),
        'catatan'   => trim($_POST['catatan'] ?? ''),
    ];

    // ---- BRANCHING #1: Validasi semua field ----
    if (empty($form_data['nama'])) {
        $pesan_error[] = "Nama tidak boleh kosong.";
    }
    if (empty($form_data['email']) || !validasiEmail($form_data['email'])) {
        $pesan_error[] = "Email tidak valid.";
    }
    if (empty($form_data['telepon'])) {
        $pesan_error[] = "Nomor telepon tidak boleh kosong.";
    }
    if (empty($form_data['tanggal']) || !validasiTanggal($form_data['tanggal'])) {
        $pesan_error[] = "Tanggal reservasi tidak valid (harus hari ini atau masa depan).";
    }
    if (empty($form_data['jam'])) {
        $pesan_error[] = "Silakan pilih jam reservasi.";
    }
    if ($form_data['tamu'] < 1) {
        $pesan_error[] = "Jumlah tamu harus minimal 1 orang.";
    }

    // ---- FUNCTION: upload bukti transfer DP reservasi (WAJIB) ----
    $bukti_transfer_reservasi = null;
    if (empty($pesan_error)) {
        $hasilUpload = uploadBuktiTransfer($_FILES['bukti_transfer'] ?? []);
        if (is_array($hasilUpload) && isset($hasilUpload['error'])) {
            $pesan_error[] = $hasilUpload['error'];
        } else {
            $bukti_transfer_reservasi = $hasilUpload;
        }
    }

    // ---- BRANCHING #2: Proses jika valid ----
    if (empty($pesan_error)) {
        $kode_reservasi = formatKonfirmasi($form_data);

        // ---- OOP: simpan reservasi via ReservationModel (extends Database) ----
        $meja_label  = $tipe_meja[$form_data['meja']] ?? $form_data['meja'];
        $paket_label = $paket[$form_data['paket']] ?? $form_data['paket'];
        $reservationModel = new ReservationModel();
        $reservationModel->create([
            'user_id'           => $_SESSION['user_id'] ?? null,
            'kode'              => $kode_reservasi,
            'nama'              => $form_data['nama'],
            'email'             => $form_data['email'],
            'telepon'           => $form_data['telepon'],
            'tanggal'           => $form_data['tanggal'],
            'jam'               => $form_data['jam'],
            'tamu'              => $form_data['tamu'],
            'meja'              => $meja_label,
            'paket'             => $paket_label,
            'catatan'           => $form_data['catatan'],
            'metode_pembayaran' => 'transfer',
            'bukti_transfer'    => $bukti_transfer_reservasi,
        ]);

        // Simpan ke SESSION juga (untuk ditampilkan di panel kanan)
        $_SESSION['reservasi'] = [
            'kode'      => $kode_reservasi,
            'nama'      => $form_data['nama'],
            'email'     => $form_data['email'],
            'telepon'   => $form_data['telepon'],
            'tanggal'   => $form_data['tanggal'],
            'jam'       => $form_data['jam'],
            'tamu'      => $form_data['tamu'],
            'meja'      => $tipe_meja[$form_data['meja']] ?? $form_data['meja'],
            'paket'     => $paket[$form_data['paket']] ?? $form_data['paket'],
            'catatan'   => $form_data['catatan'],
            'bukti_transfer' => $bukti_transfer_reservasi,
            'waktu_booking' => date('Y-m-d H:i:s'),
        ];

        $pesan_sukses = "Reservasi berhasil! Kode booking Anda: <strong>{$kode_reservasi}</strong>. Konfirmasi akan dikirim ke {$form_data['email']}.";
        $form_data = []; // Reset form
    }
}

// Ambil data reservasi terakhir dari session (jika ada)
$reservasi_session = $_SESSION['reservasi'] ?? null;
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Book a Table</div>
        <h1 class="section-title">Reservasi Meja</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Reservation</li>
            </ol>
        </nav>
    </div>
</div>

<!-- RESERVATION SECTION -->
<section class="section-dark py-5">
    <div class="container">
        <div class="row g-5">
            <!-- FORM KOLOM KIRI -->
            <div class="col-lg-7" data-reveal>

                <!-- Tampilkan pesan sukses -->
                <?php if ($pesan_sukses): ?>
                    <div class="alert-success-dark mb-4 alert-auto-dismiss">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $pesan_sukses ?>
                    </div>
                <?php endif; ?>

                <!-- Tampilkan pesan error -->
                <?php if (!empty($pesan_error)): ?>
                    <div class="alert-error-dark mb-4">
                        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php
                            // ---- LOOPING: Tampilkan semua error ----
                            foreach ($pesan_error as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-dark">
                    <h4 class="mb-4" style="font-family:var(--ff-heading);">
                        <i class="bi bi-calendar-check text-gold me-2"></i>Form Reservasi
                    </h4>

                    <form method="POST" action="reservation.php" enctype="multipart/form-data" novalidate>

                        <!-- BARIS 1: Nama & Email -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-dark">Nama Lengkap *</label>
                                <input type="text" name="nama" class="form-control form-control-dark"
                                       placeholder="Nama Anda" maxlength="100" required
                                       value="<?= htmlspecialchars($form_data['nama'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-dark">Email *</label>
                                <input type="email" name="email" class="form-control form-control-dark"
                                       placeholder="email@domain.com" required
                                       value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- BARIS 2: Telepon & Jumlah Tamu -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-dark">Nomor Telepon *</label>
                                <input type="tel" name="telepon" class="form-control form-control-dark"
                                       placeholder="+62 812..." required
                                       value="<?= htmlspecialchars($form_data['telepon'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-dark">Jumlah Tamu *</label>
                                <select name="tamu" class="form-control form-control-dark" required>
                                    <option value="">Pilih jumlah tamu</option>
                                    <?php
                                    // ---- LOOPING: Opsi jumlah tamu ----
                                    foreach ($pilihan_tamu as $jml): ?>
                                        <option value="<?= $jml ?>"
                                            <?= ($form_data['tamu'] ?? '') == $jml ? 'selected' : '' ?>>
                                            <?= $jml ?> Orang
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- BARIS 3: Tanggal & Jam -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-dark">Tanggal Reservasi *</label>
                                <input type="date" name="tanggal" class="form-control form-control-dark"
                                       min="<?= date('Y-m-d') ?>" required
                                       value="<?= htmlspecialchars($form_data['tanggal'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-dark">Jam Kedatangan *</label>
                                <select name="jam" class="form-control form-control-dark" required>
                                    <option value="">Pilih jam</option>
                                    <?php
                                    // ---- LOOPING: Opsi jam ----
                                    foreach ($pilihan_jam as $j): ?>
                                        <option value="<?= $j ?>"
                                            <?= ($form_data['jam'] ?? '') === $j ? 'selected' : '' ?>>
                                            <?= $j ?> WIB
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- BARIS 4: Tipe Meja & Paket -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-dark">Tipe Meja</label>
                                <select name="meja" class="form-control form-control-dark">
                                    <?php
                                    // ---- LOOPING: Tipe meja ----
                                    foreach ($tipe_meja as $key => $label): ?>
                                        <option value="<?= $key ?>"
                                            <?= ($form_data['meja'] ?? 'regular') === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-dark">Pilihan Paket</label>
                                <select name="paket" class="form-control form-control-dark">
                                    <?php
                                    // ---- LOOPING: Paket ----
                                    foreach ($paket as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-4">
                            <label class="form-label-dark">Catatan Khusus</label>
                            <textarea name="catatan" class="form-control form-control-dark" rows="3"
                                      placeholder="Alergi makanan, permintaan khusus, dll..."><?= htmlspecialchars($form_data['catatan'] ?? '') ?></textarea>
                        </div>

                        <!-- METODE PEMBAYARAN DP RESERVASI -->
                        <div class="mb-4">
                            <label class="form-label-dark mb-2">Pembayaran DP Reservasi</label>
                            <div class="p-3 mb-3" style="background:rgba(212,175,55,.08); border:1px solid var(--border-gold); border-radius:10px;">
                                <p class="mb-1"><i class="bi bi-bank me-2 text-gold"></i><strong>Transfer Bank</strong></p>
                                <p class="small mb-0" style="color:var(--text-muted)">
                                    Silakan transfer DP reservasi ke rekening <strong style="color:var(--text-main)">BCA 1234567890 a.n. SFard Restaurant</strong>,
                                    lalu upload foto bukti transfernya di bawah ini.
                                </p>
                            </div>
                            <label class="form-label-dark">Upload Bukti Transfer *</label>
                            <input type="file" name="bukti_transfer" class="form-control form-control-dark" accept="image/png, image/jpeg, image/webp" required>
                            <small style="color:var(--text-muted)">Format JPG/PNG/WEBP, maks 2MB.</small>
                        </div>

                        <button type="submit" name="submit_reservasi" class="btn-gold w-100 py-3">
                            <i class="bi bi-calendar-check me-2"></i>Konfirmasi Reservasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- INFO KOLOM KANAN -->
            <div class="col-lg-5" data-reveal>
                <!-- Info Kontak -->
                <div class="card-dark p-4 mb-4">
                    <h5 style="font-family:var(--ff-heading)" class="mb-4">
                        <i class="bi bi-info-circle text-gold me-2"></i>Informasi Reservasi
                    </h5>
                    <?php
                    $info_list = [
                        ['icon' => 'bi-telephone-fill',   'label' => 'Telepon',  'val' => $restaurant_phone],
                        ['icon' => 'bi-envelope-fill',    'label' => 'Email',    'val' => $restaurant_email],
                        ['icon' => 'bi-geo-alt-fill',     'label' => 'Alamat',   'val' => $restaurant_address],
                        ['icon' => 'bi-clock-fill',       'label' => 'Buka',     'val' => 'Sen–Kam: 10:00–21:00 | Jum–Sab: 10:00–22:30'],
                    ];
                    // ---- LOOPING: Info list ----
                    foreach ($info_list as $info): ?>
                        <div class="d-flex gap-3 mb-3">
                            <i class="<?= $info['icon'] ?> text-gold mt-1"></i>
                            <div>
                                <div class="small" style="color:var(--text-muted); text-transform:uppercase; font-size:0.72rem; letter-spacing:0.1em"><?= $info['label'] ?></div>
                                <div class="small"><?= $info['val'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Session: Reservasi terakhir -->
                <?php if ($reservasi_session): ?>
                    <div class="card-dark p-4" style="border-color:var(--gold);">
                        <h6 style="color:var(--gold); font-family:var(--ff-display); letter-spacing:0.1em">
                            <i class="bi bi-check-circle-fill me-2"></i>Reservasi Aktif Anda
                        </h6>
                        <div class="mt-3">
                            <?php
                            $detail_session = [
                                'Kode Booking' => $reservasi_session['kode'],
                                'Nama'         => $reservasi_session['nama'],
                                'Tanggal'      => $reservasi_session['tanggal'],
                                'Jam'          => $reservasi_session['jam'] . ' WIB',
                                'Tamu'         => $reservasi_session['tamu'] . ' Orang',
                                'Meja'         => $reservasi_session['meja'],
                            ];
                            // ---- LOOPING: Detail session ----
                            foreach ($detail_session as $k => $v): ?>
                                <div class="d-flex justify-content-between small border-bottom border-secondary py-1">
                                    <span style="color:var(--text-muted)"><?= $k ?></span>
                                    <span class="<?= $k === 'Kode Booking' ? 'text-gold fw-bold' : '' ?>"><?= htmlspecialchars($v) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($reservasi_session['bukti_transfer'])): ?>
                        <div class="mt-3">
                            <div class="small mb-1" style="color:var(--text-muted)"><i class="bi bi-bank me-1"></i>Bukti Transfer:</div>
                            <img src="img/<?= htmlspecialchars($reservasi_session['bukti_transfer']) ?>" alt="Bukti Transfer"
                                 style="max-width:140px;border-radius:8px;border:1px solid var(--border-gold);">
                        </div>
                        <?php endif; ?>
                        <a href="reservation.php?clear=1" class="btn-outline-gold w-100 text-center mt-3 py-2" style="font-size:0.8rem"
                           onclick="return confirm('Hapus reservasi ini?')">
                            <i class="bi bi-trash me-1"></i>Hapus Reservasi
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php
                // Clear session reservasi
                if (isset($_GET['clear'])) {
                    unset($_SESSION['reservasi']);
                    header('Location: reservation.php');
                    exit;
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
