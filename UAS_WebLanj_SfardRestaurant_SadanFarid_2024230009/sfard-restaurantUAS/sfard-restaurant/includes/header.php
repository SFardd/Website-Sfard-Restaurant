<?php
// ============================================================
// INCLUDES/HEADER.PHP - Global Header & Navigation
// Session & Cookie Management
// ============================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- COOKIE: Track visitor count & popup ----
$visitor_count = 1;
$show_cookie_popup = false;
if (isset($_COOKIE['sfard_visits'])) {
    $visitor_count = (int)$_COOKIE['sfard_visits'] + 1;
} else {
    // Pengunjung baru — tampilkan popup
    $show_cookie_popup = true;
}
setcookie('sfard_visits', $visitor_count, time() + (86400 * 30), "/"); // 30 hari

// ---- SESSION: Last visited page ----
$current_page = basename($_SERVER['PHP_SELF']);
$_SESSION['last_page'] = $current_page;
if (!isset($_SESSION['visit_start'])) {
    $_SESSION['visit_start'] = date('Y-m-d H:i:s');
}

// ---- PHP VARIABLE: Restaurant info ----
$restaurant_name   = "SFard Restaurant";
$restaurant_tagline = "Taste the Authentic Flavors";
$restaurant_phone  = "+62 851-5613-5851";
$restaurant_email  = "sadanfarid28@gmail.com";
$restaurant_address = "Jl.Tipar Cakung No.10 Jakarta Timur";

// Navigation menu array
$nav_items = [
    ['label' => 'Home',      'href' => 'index.php',     'icon' => 'bi-house-fill'],
    ['label' => 'About',     'href' => 'about.php',     'icon' => 'bi-info-circle-fill'],
    ['label' => 'Menu',      'href' => 'menu.php',      'icon' => 'bi-journal-richtext'],
    ['label' => 'Gallery',   'href' => 'gallery.php',   'icon' => 'bi-images'],
    ['label' => 'Reservation','href' => 'reservation.php','icon' => 'bi-calendar-check-fill'],
    ['label' => 'Contact',   'href' => 'contact.php',   'icon' => 'bi-envelope-fill'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? $restaurant_name) ?> | <?= $restaurant_name ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&family=Cinzel:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if ($show_cookie_popup): ?>
<!-- COOKIE CONSENT POPUP -->
<div id="cookiePopup" style="
    position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
    z-index:9999;width:min(480px,calc(100vw - 32px));
    background:linear-gradient(135deg,#1A1208,#211810);
    border:1px solid rgba(212,175,55,0.4);border-radius:8px;
    box-shadow:0 8px 40px rgba(0,0,0,0.7);
    padding:1.4rem 1.6rem;
    animation:slideUpPopup 0.5s ease both;
">
    <div style="display:flex;align-items:flex-start;gap:1rem;">
        <span style="font-size:1.8rem;line-height:1;">🍪</span>
        <div style="flex:1;">
            <h6 style="color:#D4AF37;font-family:'Cinzel',serif;margin:0 0 0.4rem;letter-spacing:0.08em;">Cookie Notice</h6>
            <p style="color:#A89880;font-size:0.82rem;margin:0 0 1rem;line-height:1.6;">
                Kami menggunakan cookie untuk meningkatkan pengalaman kunjungan Anda di <strong style="color:#F5EDD8;">SFard Restaurant</strong>. Dengan melanjutkan, Anda menyetujui penggunaan cookie kami.
            </p>
            <div style="display:flex;gap:0.6rem;flex-wrap:wrap;">
                <button onclick="acceptCookie()" style="
                    background:linear-gradient(135deg,#D4AF37,#A88520);
                    color:#0D0A06;border:none;border-radius:4px;
                    padding:0.45rem 1.2rem;font-size:0.78rem;font-weight:700;
                    letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                ">✓ Terima</button>
                <button onclick="closeCookiePopup()" style="
                    background:transparent;color:#A89880;
                    border:1px solid rgba(212,175,55,0.3);border-radius:4px;
                    padding:0.45rem 1.2rem;font-size:0.78rem;cursor:pointer;
                    letter-spacing:0.08em;
                ">Tutup</button>
            </div>
        </div>
        <button onclick="closeCookiePopup()" style="
            background:none;border:none;color:#A89880;cursor:pointer;
            font-size:1.2rem;padding:0;line-height:1;
        ">×</button>
    </div>
</div>
<style>
@keyframes slideUpPopup {
    from { opacity:0; transform:translateX(-50%) translateY(20px); }
    to   { opacity:1; transform:translateX(-50%) translateY(0); }
}
</style>
<script>
function acceptCookie() {
    document.cookie = "sfard_consent=1;max-age=" + (86400*365) + ";path=/";
    closeCookiePopup();
}
function closeCookiePopup() {
    var p = document.getElementById('cookiePopup');
    if (p) { p.style.transition='opacity 0.4s'; p.style.opacity='0'; setTimeout(()=>p.remove(),400); }
}
</script>
<?php endif; ?>

<!-- TOP BAR -->
<div class="topbar py-2">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-3 small">
            <span><i class="bi bi-telephone-fill me-1 text-gold"></i><?= $restaurant_phone ?></span>
            <span><i class="bi bi-envelope-fill me-1 text-gold"></i><?= $restaurant_email ?></span>
        </div>
        <div class="d-flex gap-3 small">
            <span><i class="bi bi-clock-fill me-1 text-gold"></i>Mon–Sun: 10:00 – 22:00</span>
            <!-- Cookie visitor count display -->
            <span class="text-gold"><i class="bi bi-eye-fill me-1"></i>Visit ke-<?= $visitor_count ?></span>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img src="img/LogoSfardRest.png" alt="Logo" height="45" class="logo-img">
            <div>
                <div class="brand-name"><?= $restaurant_name ?></div>
                <div class="brand-tagline"><?= $restaurant_tagline ?></div>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto gap-1">
                <?php foreach ($nav_items as $item): ?>
                    <?php $is_active = ($current_page === $item['href']) ? 'active' : ''; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $is_active ?>" href="<?= $item['href'] ?>">
                            <i class="<?= $item['icon'] ?> me-1"></i><?= $item['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown ms-3">
                    <a class="btn btn-outline-gold dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['username']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="background:var(--dark-card); border:1px solid var(--border-gold);">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a class="dropdown-item text-gold" href="admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                        <li><hr class="dropdown-divider" style="border-color:var(--border-gold);"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="riwayat_pesanan.php" style="color:var(--text-main)"><i class="bi bi-bag-check me-2"></i>Riwayat Pesanan</a></li>
                        <li><a class="dropdown-item" href="riwayat_reservasi.php" style="color:var(--text-main)"><i class="bi bi-calendar-check me-2"></i>Riwayat Reservasi</a></li>
                        <li><hr class="dropdown-divider" style="border-color:var(--border-gold);"></li>
                        <li><a class="dropdown-item" href="logout.php" style="color:var(--text-main)"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-gold ms-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
            <?php endif; ?>
            <a href="reservation.php" class="btn btn-gold ms-2">
                <i class="bi bi-calendar-check me-1"></i>Book Now
            </a>
        </div>
    </div>
</nav>
