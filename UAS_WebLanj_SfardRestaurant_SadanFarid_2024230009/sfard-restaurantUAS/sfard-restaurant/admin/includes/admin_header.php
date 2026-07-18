<?php
// ============================================================
// ADMIN/INCLUDES/ADMIN_HEADER.PHP
// Head + pembuka layout khusus untuk halaman admin
// (terpisah dari includes/header.php milik halaman publik)
// ============================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin') ?> | SFard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&family=Cinzel:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background:var(--dark-bg);">

<?php if (isset($_GET['msg'])): ?>
<div class="container-fluid p-0">
    <div class="alert-success-dark m-3 mb-0" style="border-radius:8px;">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_GET['msg']) ?>
    </div>
</div>
<?php endif; ?>

<div class="d-flex">
    <?php include __DIR__ . '/../sidebar.php'; ?>
    <div class="flex-grow-1 admin-content">
