<?php
// ============================================================
// REGISTER.PHP — Halaman Daftar Akun
// Form Input, Branching, Function, Database
// ============================================================
require_once 'includes/functions.php';
require_once 'includes/load_classes.php';

// ---- FUNCTION: validasi tujuan redirect biar aman (cegah open redirect) ----
function tujuanRedirectAmanRegister(string $url): string {
    if ($url !== '' && preg_match('#^[a-zA-Z0-9_\-/]+\.php(\?[a-zA-Z0-9_=&%\-\.]*)?$#', $url)) {
        return $url;
    }
    return '';
}

$redirect = tujuanRedirectAmanRegister($_GET['redirect'] ?? '');

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php'));
    exit;
}

// ---- FUNCTION: bersihkan input form register ----
function bersihkanInputRegister(string $data): string {
    return htmlspecialchars(trim(stripslashes($data)));
}

$error   = '';
$success = '';

// ======== PROSES FORM REGISTER ========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = bersihkanInputRegister($_POST['username'] ?? '');
    $email    = bersihkanInputRegister($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ---- BRANCHING: validasi berlapis ----
    if ($username === '' || $email === '' || $password === '') {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // ---- OOP: cek username & simpan user baru via UserModel (extends Database) ----
        $userModel = new UserModel();

        if ($userModel->usernameExists($username)) {
            $error = "Username sudah digunakan, silakan pilih username lain.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            if ($userModel->create($username, $hashed, $email)) {
                $success = "Akun berhasil dibuat! Silakan login.";
            } else {
                $error = "Terjadi kesalahan, silakan coba lagi.";
            }
        }
    }
}

$page_title = "Daftar Akun";
include 'includes/header.php';
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Bergabung Bersama Kami</div>
        <h1 class="section-title">Daftar Akun</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Daftar</li>
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

        <?php if ($success): ?>
            <div class="alert-success-dark mb-4">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                <a href="login.php<?= $redirect !== '' ? '?redirect=' . urlencode($redirect) : '' ?>" class="text-gold">Login sekarang</a>
            </div>
        <?php endif; ?>

        <div class="form-dark">
            <h4 class="mb-4 text-center" style="font-family:var(--ff-heading);">
                <i class="bi bi-person-plus-fill text-gold me-2"></i>Buat Akun Baru
            </h4>
            <form method="POST" action="register.php">
                <div class="mb-3">
                    <label class="form-label-dark">Username</label>
                    <input type="text" name="username" class="form-control form-control-dark" required placeholder="Pilih username">
                </div>
                <div class="mb-3">
                    <label class="form-label-dark">Email</label>
                    <input type="email" name="email" class="form-control form-control-dark" required placeholder="email@domain.com">
                </div>
                <div class="mb-3">
                    <label class="form-label-dark">Password</label>
                    <input type="password" name="password" class="form-control form-control-dark" required minlength="6" placeholder="Minimal 6 karakter">
                </div>
                <div class="mb-4">
                    <label class="form-label-dark">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="form-control form-control-dark" required placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn-gold w-100 py-3">
                    <i class="bi bi-person-plus me-2"></i>Daftar
                </button>
            </form>
            <p class="text-center mt-3 small" style="color:var(--text-muted)">
                Sudah punya akun? <a href="login.php<?= $redirect !== '' ? '?redirect=' . urlencode($redirect) : '' ?>" class="text-gold">Login di sini</a>
            </p>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
