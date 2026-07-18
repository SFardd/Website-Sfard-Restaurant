<?php
// ============================================================
// PESAN.PHP — Halaman Pemesanan Online
// Wajib login supaya pesanan bisa tertaut ke akun & ada riwayat
// ============================================================
ob_start(); // buffer output biar setcookie() tetap bisa dipanggil setelah header.php nge-print HTML
require_once 'includes/functions.php';
cekLoginUser();

$page_title = "Pesan Makanan";
include 'includes/header.php';
require_once 'includes/load_classes.php';

// ---- OOP: pakai MenuModel & OrderModel (extends Database) ----
$menuModel  = new MenuModel();
$orderModel = new OrderModel();

// ---- DATABASE: ambil data menu (sinkron dengan menu.php) ----
$icon_kategori = [
    'makanan_berat'  => '🍛',
    'makanan_ringan' => '🥗',
    'minuman'        => '🥤',
    'dessert'        => '🍮',
];
$data_menu = [];
$menuQuery = $menuModel->getAvailable();
while ($row = mysqli_fetch_assoc($menuQuery)) {
    $data_menu[(int)$row['id']] = [
        'nama'     => $row['name'],
        'harga'    => (float) $row['price'],
        'kategori' => $row['kategori_nama'],
        'icon'     => $icon_kategori[$row['kategori_slug']] ?? '🍽️',
    ];
}

$id_preselect = isset($_GET['id']) ? (int)$_GET['id'] : 0;

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// ---- PROSES FORM ----
$pesan_sukses   = '';
$pesan_error    = '';
$total_pesanan  = 0;
$detail_pesanan = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan = trim($_POST['nama_pemesan'] ?? '');
    $telepon      = trim($_POST['telepon'] ?? '');
    $jenis        = $_POST['jenis'] ?? '';
    $alamat       = trim($_POST['alamat'] ?? '');
    $item_ids     = $_POST['item_ids'] ?? [];
    $quantities   = $_POST['quantities'] ?? [];
    $catatan      = trim($_POST['catatan'] ?? '');

    if (empty($nama_pemesan)) {
        $pesan_error = "Nama pemesan harus diisi!";
    } elseif (empty($telepon)) {
        $pesan_error = "Nomor telepon harus diisi!";
    } elseif (empty($jenis)) {
        $pesan_error = "Pilih jenis pesanan!";
    } elseif ($jenis === 'delivery' && empty($alamat)) {
        $pesan_error = "Alamat pengiriman harus diisi untuk pesanan delivery!";
    } elseif (empty($item_ids)) {
        $pesan_error = "Pilih minimal satu menu!";
    } else {
        // ---- FUNCTION: upload bukti transfer (WAJIB) ----
        $hasilUpload = uploadBuktiTransfer($_FILES['bukti_transfer'] ?? []);

        if (is_array($hasilUpload) && isset($hasilUpload['error'])) {
            $pesan_error = $hasilUpload['error'];
        }
    }

    if (empty($pesan_error) && !empty($item_ids)) {
        $bukti_transfer = $hasilUpload;

        foreach ($item_ids as $idx => $id) {
            $id  = (int)$id;
            $qty = max(1, (int)($quantities[$idx] ?? 1));
            if (isset($data_menu[$id])) {
                $m = $data_menu[$id];
                $sub = $m['harga'] * $qty;
                $total_pesanan += $sub;
                $detail_pesanan[] = [
                    'id'       => $id,
                    'nama'     => $m['nama'],
                    'harga'    => $m['harga'],
                    'qty'      => $qty,
                    'subtotal' => $sub,
                    'icon'     => $m['icon'],
                ];
            }
        }

        if ($jenis === 'dine_in') {
            $ongkir = 0; $estimasi = "15 – 30 menit"; $jenis_txt = "Makan di Tempat";
        } elseif ($jenis === 'take_away') {
            $ongkir = 0; $estimasi = "20 – 35 menit"; $jenis_txt = "Bawa Pulang";
        } else {
            $ongkir = 15000; $estimasi = "30 – 60 menit"; $jenis_txt = "Delivery";
        }

        $grand_total  = $total_pesanan + $ongkir;
        $kode_pesanan = "SFD-" . strtoupper(substr(md5(time()), 0, 6));

        // ---- OOP: simpan pesanan ke tabel orders + order_items via OrderModel ----
        // ---- SESSION: kalau lagi login, otomatis tautkan pesanan ke akun ----
        $orderId = $orderModel->create([
            'user_id'           => $_SESSION['user_id'] ?? null,
            'kode'              => $kode_pesanan,
            'nama_pemesan'      => $nama_pemesan,
            'telepon'           => $telepon,
            'jenis'             => $jenis,
            'alamat'            => $alamat,
            'catatan'           => $catatan,
            'ongkir'            => $ongkir,
            'total'             => $grand_total,
            'metode_pembayaran' => 'transfer',
            'bukti_transfer'    => $bukti_transfer,
        ]);

        foreach ($detail_pesanan as $d) {
            $orderModel->createItem($orderId, $d);
        }

        $_SESSION['pesanan_terakhir'] = [
            'kode'           => $kode_pesanan,
            'nama'           => $nama_pemesan,
            'jenis'          => $jenis_txt,
            'detail'         => $detail_pesanan,
            'ongkir'         => $ongkir,
            'total'          => $grand_total,
            'estimasi'       => $estimasi,
            'catatan'        => $catatan,
            'bukti_transfer' => $bukti_transfer,
        ];

        setcookie('sfard_nama', htmlspecialchars($nama_pemesan), time() + (86400 * 7), "/");
        $pesan_sukses = $kode_pesanan;
    }
}

// Kelompokkan menu per kategori
$menu_per_kat = [];
foreach ($data_menu as $id => $item) {
    $menu_per_kat[$item['kategori']][$id] = $item;
}
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Online Order</div>
        <h1 class="section-title">🛒 Pesan Makanan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="menu.php">Menu</a></li>
                <li class="breadcrumb-item active">Pesan</li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-5" style="background:#0f0c08;">
<div class="container">

    <?php if (!empty($pesan_sukses) && !empty($_SESSION['pesanan_terakhir'])):
        $p = $_SESSION['pesanan_terakhir']; ?>
    <!-- SUKSES -->
    <div class="pesan-sukses-box mb-5" data-reveal>
        <div class="text-center mb-4">
            <span style="font-size:3rem;">✅</span>
            <h3 style="color:var(--gold);font-family:var(--ff-heading);margin-top:.5rem;">Pesanan Berhasil Dikirim!</h3>
            <p style="color:var(--text-muted)">Kode pesanan Anda:</p>
            <span class="kode-pesan-badge"><?= $p['kode'] ?></span>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-sm-4 text-center">
                <div class="pesan-info-item">👤 <strong><?= htmlspecialchars($p['nama']) ?></strong></div>
            </div>
            <div class="col-sm-4 text-center">
                <div class="pesan-info-item">🛍️ <?= $p['jenis'] ?></div>
            </div>
            <div class="col-sm-4 text-center">
                <div class="pesan-info-item">⏱️ <?= $p['estimasi'] ?></div>
            </div>
        </div>
        <table class="table pesan-table">
            <thead>
                <tr><th>Menu</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($p['detail'] as $d): ?>
                <tr>
                    <td><?= $d['icon'] ?> <?= htmlspecialchars($d['nama']) ?></td>
                    <td class="text-end"><?= formatRupiah($d['harga']) ?></td>
                    <td class="text-center"><?= $d['qty'] ?>x</td>
                    <td class="text-end"><?= formatRupiah($d['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php if ($p['ongkir'] > 0): ?>
                <tr><td colspan="3" style="color:var(--text-muted)">Ongkos Kirim</td><td class="text-end"><?= formatRupiah($p['ongkir']) ?></td></tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3"><strong style="color:var(--gold)">Grand Total</strong></td>
                    <td class="text-end"><strong style="color:var(--gold);font-size:1.1rem;"><?= formatRupiah($p['total']) ?></strong></td>
                </tr>
            </tfoot>
        </table>
        <?php if (!empty($p['catatan'])): ?>
        <p class="small" style="color:var(--text-muted)"><i class="bi bi-chat-text me-1"></i>Catatan: <?= htmlspecialchars($p['catatan']) ?></p>
        <?php endif; ?>

        <!-- INFO METODE PEMBAYARAN -->
        <div class="payment-info-box mt-3 mb-2">
            <p class="mb-1"><i class="bi bi-bank me-2 text-gold"></i><strong>Pembayaran: Transfer Bank</strong></p>
            <p class="small mb-2" style="color:var(--text-muted)">
                Total tagihan <strong style="color:var(--gold)"><?= formatRupiah($p['total']) ?></strong> —
                bukti transfer kamu sudah kami terima, akan segera diverifikasi oleh admin.
            </p>
            <?php if (!empty($p['bukti_transfer'])): ?>
            <img src="img/<?= htmlspecialchars($p['bukti_transfer']) ?>" alt="Bukti Transfer" style="max-width:180px;border-radius:8px;border:1px solid var(--border-gold);">
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="menu.php" class="btn-gold me-2"><i class="bi bi-journal-richtext me-1"></i>Lihat Menu</a>
            <a href="pesan.php" class="btn-outline-gold"><i class="bi bi-plus-circle me-1"></i>Pesan Lagi</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($pesan_error)): ?>
    <div class="alert-error-dark mb-4"><i class="bi bi-exclamation-triangle me-2"></i><?= $pesan_error ?></div>
    <?php endif; ?>

    <form method="POST" action="pesan.php" enctype="multipart/form-data">
    <div class="row g-4">

        <!-- Kiri: Pilih Menu -->
        <div class="col-lg-7">
            <div class="pesan-card">
                <h5 class="pesan-card-title"><i class="bi bi-journal-richtext me-2"></i>Pilih Menu</h5>
                <?php foreach ($menu_per_kat as $kategori => $items): ?>
                <div class="mb-4">
                    <div class="pesan-kat-label">— <?= $kategori ?> —</div>
                    <?php foreach ($items as $id => $item): ?>
                    <div class="pesan-menu-row">
                        <input type="checkbox" class="form-check-input menu-checkbox me-2"
                               name="item_ids[]" value="<?= $id ?>" id="m<?= $id ?>"
                               data-price="<?= $item['harga'] ?>"
                               <?= ($id_preselect === $id) ? 'checked' : '' ?>>
                        <label for="m<?= $id ?>" class="flex-grow-1" style="cursor:pointer;">
                            <span style="font-size:1.2rem;"><?= $item['icon'] ?></span>
                            <span style="color:var(--text-main);margin-left:.4rem;"><?= $item['nama'] ?></span>
                            <span style="color:var(--gold);font-size:.85rem;margin-left:.5rem;"><?= formatRupiah($item['harga']) ?></span>
                        </label>
                        <input type="number" name="quantities[]" class="qty-input form-control form-control-sm"
                               value="1" min="1" max="10" style="width:68px;background:#1A1208;color:var(--text-main);border:1px solid var(--border-gold);border-radius:6px;text-align:center;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Kanan: Data Pemesan + Ringkasan -->
        <div class="col-lg-5">
            <div class="pesan-card mb-4">
                <h5 class="pesan-card-title"><i class="bi bi-person-fill me-2"></i>Data Pemesan</h5>
                <div class="mb-3">
                    <label class="form-label small" style="color:var(--text-muted)">Nama *</label>
                    <input type="text" name="nama_pemesan" class="pesan-input"
                           value="<?= htmlspecialchars($_COOKIE['sfard_nama'] ?? '') ?>"
                           placeholder="Nama lengkap" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small" style="color:var(--text-muted)">Nomor Telepon *</label>
                    <input type="tel" name="telepon" class="pesan-input" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small" style="color:var(--text-muted)">Jenis Pesanan *</label>
                    <select name="jenis" class="pesan-input" id="jenisPesanan" required>
                        <option value="">— Pilih Jenis —</option>
                        <option value="dine_in">🪑 Makan di Tempat</option>
                        <option value="take_away">🛍️ Bawa Pulang</option>
                        <option value="delivery">🛵 Delivery (+Rp 15.000)</option>
                    </select>
                </div>
                <div class="mb-3" id="alamatField" style="display:none">
                    <label class="form-label small" style="color:var(--text-muted)">Alamat Pengiriman</label>
                    <textarea name="alamat" class="pesan-input" rows="2" placeholder="Jalan, No. Rumah, RT/RW..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label small" style="color:var(--text-muted)">Catatan</label>
                    <textarea name="catatan" class="pesan-input" rows="2" placeholder="Tidak pedas, tambah nasi, dll."></textarea>
                </div>
            </div>

            <!-- Ringkasan Harga -->
            <div class="pesan-card">
                <h5 class="pesan-card-title"><i class="bi bi-receipt me-2"></i>Ringkasan</h5>
                <div class="d-flex justify-content-between mb-2 small">
                    <span style="color:var(--text-muted)">Subtotal</span>
                    <span id="subtotalDisplay" style="color:var(--text-main)">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small" id="ongkirRow" style="display:none!important">
                    <span style="color:var(--text-muted)">Ongkos Kirim</span>
                    <span id="ongkirDisplay" style="color:var(--text-main)">Rp 0</span>
                </div>
                <hr style="border-color:var(--border-gold);">
                <div class="d-flex justify-content-between">
                    <strong style="color:var(--text-main)">Total</strong>
                    <strong id="totalDisplay" style="color:var(--gold);font-size:1.1rem;">Rp 0</strong>
                </div>

                <!-- METODE PEMBAYARAN -->
                <div class="mt-3 mb-1">
                    <label class="form-label small" style="color:var(--text-muted)">Metode Pembayaran</label>
                    <div class="payment-info-box mb-3">
                        <p class="mb-1"><i class="bi bi-bank me-2 text-gold"></i><strong>Transfer Bank</strong></p>
                        <p class="small mb-0" style="color:var(--text-muted)">
                            Transfer ke rekening <strong style="color:var(--text-main)">BCA 1234567890 a.n. SFard Restaurant</strong>
                            sesuai total pesanan, lalu upload foto bukti transfernya di bawah ini.
                        </p>
                    </div>
                    <label class="form-label small" style="color:var(--text-muted)">Upload Bukti Transfer *</label>
                    <input type="file" name="bukti_transfer" class="pesan-input" accept="image/png, image/jpeg, image/webp" required>
                    <small style="color:var(--text-muted)">Format JPG/PNG/WEBP, maks 2MB.</small>
                </div>

                <button type="submit" class="btn-gold w-100 mt-3" style="width:100%;text-align:center;display:block;">
                    <i class="bi bi-bag-check me-2"></i>Kirim Pesanan
                </button>
            </div>
        </div>

    </div>
    </form>
</div>
</section>

<style>
/* ---- PESAN PAGE STYLES ---- */
.pesan-sukses-box {
    background: linear-gradient(135deg, rgba(26,18,8,.9), rgba(33,24,16,.95));
    border: 1px solid rgba(212,175,55,.4);
    border-radius: 16px;
    padding: 2rem;
}
.kode-pesan-badge {
    display: inline-block;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0D0A06;
    font-family: var(--ff-display);
    font-size: 1.3rem;
    font-weight: 700;
    padding: .5rem 1.6rem;
    border-radius: 8px;
    letter-spacing: .15em;
    margin-top: .4rem;
}
.pesan-info-item {
    background: rgba(212,175,55,.08);
    border: 1px solid var(--border-gold);
    border-radius: 10px;
    padding: .6rem 1rem;
    font-size: .9rem;
    color: var(--text-main);
}
.pesan-table { color: var(--text-main); }
.pesan-table thead th { color: var(--gold); border-bottom: 1px solid var(--border-gold); font-size:.85rem; }
.pesan-table td, .pesan-table th { border-color: rgba(255,255,255,.06); font-size:.9rem; }
.pesan-table tfoot td { border-top: 1px solid var(--border-gold); }

.pesan-card {
    background: #1A1208;
    border: 1px solid rgba(212,175,55,.2);
    border-radius: 16px;
    padding: 1.6rem;
}
.pesan-card-title {
    font-family: var(--ff-heading);
    color: var(--gold);
    font-size: 1rem;
    margin-bottom: 1.2rem;
    padding-bottom: .8rem;
    border-bottom: 1px solid var(--border-gold);
}
.pesan-kat-label {
    color: var(--gold);
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    margin-bottom: .6rem;
}
.pesan-menu-row {
    display: flex;
    align-items: center;
    padding: .65rem .5rem;
    border-radius: 8px;
    border-bottom: 1px solid rgba(255,255,255,.04);
    transition: background .15s;
}
.pesan-menu-row:hover { background: rgba(212,175,55,.05); }
.pesan-input {
    width: 100%;
    background: #0f0c08;
    color: var(--text-main);
    border: 1px solid rgba(212,175,55,.25);
    border-radius: 8px;
    padding: .55rem .9rem;
    font-family: var(--ff-body);
    font-size: .9rem;
    outline: none;
    transition: border-color .2s;
}
.pesan-input:focus { border-color: var(--gold); }
.pesan-input option { background: #1A1208; }
.alert-error-dark {
    background: rgba(231,76,60,.15);
    border: 1px solid rgba(231,76,60,.4);
    color: #ff8080;
    border-radius: 10px;
    padding: .9rem 1.2rem;
    font-size: .9rem;
}
.payment-option {
    display: flex;
    align-items: center;
    gap: .5rem;
    background: rgba(212,175,55,.05);
    border: 1px solid var(--border-gold);
    border-radius: 8px;
    padding: .6rem .9rem;
    cursor: pointer;
}
.payment-option label {
    color: var(--text-main);
    font-size: .9rem;
    cursor: pointer;
    margin: 0;
    flex-grow: 1;
}
.payment-option:has(input:checked) {
    background: rgba(212,175,55,.15);
    border-color: var(--gold);
}
.payment-info-box {
    background: rgba(212,175,55,.08);
    border: 1px solid var(--border-gold);
    border-radius: 10px;
    padding: 1rem 1.2rem;
}
</style>

<script>
function updateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.menu-checkbox:checked').forEach(cb => {
        const price  = parseInt(cb.dataset.price);
        const row    = cb.closest('.pesan-menu-row');
        const qty    = parseInt(row.querySelector('.qty-input').value) || 1;
        subtotal += price * qty;
    });
    const jenis  = document.getElementById('jenisPesanan').value;
    const ongkir = jenis === 'delivery' ? 15000 : 0;
    const total  = subtotal + ongkir;
    const fmt    = n => 'Rp ' + n.toLocaleString('id-ID');
    document.getElementById('subtotalDisplay').textContent = fmt(subtotal);
    document.getElementById('totalDisplay').textContent    = fmt(total);
    document.getElementById('ongkirDisplay').textContent   = fmt(ongkir);
    const ongkirRow = document.getElementById('ongkirRow');
    ongkir > 0 ? ongkirRow.style.removeProperty('display') : ongkirRow.style.setProperty('display','none','important');
}
document.querySelectorAll('.menu-checkbox, .qty-input').forEach(el => el.addEventListener('change', updateTotal));
document.getElementById('jenisPesanan').addEventListener('change', function() {
    document.getElementById('alamatField').style.display = this.value === 'delivery' ? 'block' : 'none';
    updateTotal();
});
updateTotal();
</script>

<?php include 'includes/footer.php'; ?>
