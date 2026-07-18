<?php
// ============================================================
// LOGIN.PHP — Halaman Login
// Session, Cookie, Branching, Function, Database
// ============================================================
require_once 'includes/functions.php';
require_once 'includes/load_classes.php';

// ---- FUNCTION: validasi tujuan redirect biar aman (cegah open redirect) ----
function tujuanRedirectAman(string $url): string {
    // hanya izinkan path relatif ke file .php di dalam situs sendiri
    if ($url !== '' && preg_match('#^[a-zA-Z0-9_\-/]+\.php(\?[a-zA-Z0-9_=&%\-\.]*)?$#', $url)) {
        return $url;
    }
    return '';
}

$redirect = tujuanRedirectAman($_POST['redirect'] ?? $_GET['redirect'] ?? '');

// Kalau sudah login, langsung arahkan sesuai role (atau ke tujuan asal kalau ada)
if (isset($_SESSION['user_id'])) {
    if ($redirect !== '') {
        header("Location: " . $redirect);
    } else {
        header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php'));
    }
    exit;
}

// ---- FUNCTION: bersihkan input form login ----
function bersihkanInputLogin(string $data): string {
    return htmlspecialchars(trim(stripslashes($data)));
}

$error = '';

// ======== PROSES FORM LOGIN ========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = bersihkanInputLogin($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi.";
    } else {
        // ---- OOP: cari user via UserModel (extends Database) ----
        $userModel = new UserModel();
        $user = $userModel->findByUsername($username);

        if ($user) {
            // ---- BRANCHING: verifikasi password ----
            if (password_verify($password, $user['password'])) {
                // ---- SESSION: simpan data login ----
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                // ---- COOKIE: remember me selama 7 hari ----
                if (isset($_POST['remember'])) {
                    setcookie('sfard_remember_username', $username, time() + (86400 * 7), "/");
                } else {
                    setcookie('sfard_remember_username', '', time() - 3600, "/");
                }

                // ---- BRANCHING: arahkan ke tujuan asal (kalau ada), atau sesuai role ----
                if ($redirect !== '') {
                    header("Location: " . $redirect);
                } elseif ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    }
}

// ---- COOKIE: ambil username yang diingat (jika ada) ----
$remembered = $_COOKIE['sfard_remember_username'] ?? '';

$page_title = "Login";
include 'includes/header.php';
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Selamat Datang Kembali</div>
        <h1 class="section-title">Login</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Login</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-dark py-5">
    <div class="container" style="max-width:480px;">

        <?php if ($error): ?>
            <div class="alert-error-dark mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($redirect !== '' && !$error): ?>
            <div class="alert-error-dark mb-4" style="background:rgba(212,175,55,.12); border-color:var(--border-gold); color:var(--text-main);">
                <i class="bi bi-info-circle-fill me-2 text-gold"></i>Silakan login terlebih dahulu untuk melanjutkan pemesanan/reservasi.
            </div>
        <?php endif; ?>

        <div class="form-dark">
            <h4 class="mb-4 text-center" style="font-family:var(--ff-heading);">
                <i class="bi bi-box-arrow-in-right text-gold me-2"></i>Masuk ke Akun Anda
            </h4>
            <form method="POST" action="login.php">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <div class="mb-3">
                    <label class="form-label-dark">Username</label>
                    <input type="text" name="username" class="form-control form-control-dark" required
                           value="<?= htmlspecialchars($remembered) ?>" placeholder="Username Anda">
                </div>
                <div class="mb-3">
                    <label class="form-label-dark">Password</label>
                    <input type="password" name="password" class="form-control form-control-dark" required placeholder="Password Anda">
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" <?= $remembered ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="remember" style="color:var(--text-muted)">Ingat username saya</label>
                </div>
                <button type="submit" class="btn-gold w-100 py-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
            <p class="text-center mt-3 small" style="color:var(--text-muted)">
                Belum punya akun? <a href="register.php<?= $redirect !== '' ? '?redirect=' . urlencode($redirect) : '' ?>" class="text-gold">Daftar di sini</a>
            </p>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
