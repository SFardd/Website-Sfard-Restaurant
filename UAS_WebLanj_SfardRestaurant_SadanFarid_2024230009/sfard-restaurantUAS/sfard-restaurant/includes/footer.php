<?php
// ============================================================
// INCLUDES/FOOTER.PHP - Global Footer
// ============================================================

// ---- SESSION INFO ----
$visit_start = $_SESSION['visit_start'] ?? 'Unknown';
$last_page   = $_SESSION['last_page'] ?? 'index.php';
?>

<!-- FOOTER -->
<footer class="footer pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- Brand -->
            <div class="col-lg-4">
                <h4 class="footer-brand"><?= $restaurant_name ?></h4>
                <p class="footer-text"><?= $restaurant_tagline ?>. Kami menghadirkan cita rasa autentik dengan bahan-bahan premium pilihan terbaik.</p>
                <h3> Sa'dan Farid - 2024230009</h3>
                <div class="social-links d-flex gap-3 mt-3">
                    <a href="https://www.instagram.com/_sadanfr/?utm_source=ig_web_button_share_sheet" class="social-btn"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.tiktok.com/@_sadanfr?lang=en" class="social-btn"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="footer-links list-unstyled">
                    <?php foreach ($nav_items as $link): ?>
                        <li><a href="<?= $link['href'] ?>"><i class="bi bi-chevron-right me-1"></i><?= $link['label'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading">Contact Us</h6>
                <ul class="footer-links list-unstyled">
                    <li><i class="bi bi-geo-alt-fill me-2 text-gold"></i><?= $restaurant_address ?></li>
                    <li><i class="bi bi-telephone-fill me-2 text-gold"></i><?= $restaurant_phone ?></li>
                    <li><i class="bi bi-envelope-fill me-2 text-gold"></i><?= $restaurant_email ?></li>
                    <li><i class="bi bi-clock-fill me-2 text-gold"></i>Senin – Minggu: 10:00–22:00</li>
                </ul>
            </div>

            <!-- Opening Hours -->
            <div class="col-lg-3">
                <h6 class="footer-heading">Opening Hours</h6>
                <?php
                // ---- LOOPING: Jam operasional ----
                $jam_operasional = [
                    'Senin – Kamis' => '10:00 – 21:00',
                    'Jumat – Sabtu' => '10:00 – 22:30',
                    'Minggu'        => '11:00 – 21:00',
                ];
                foreach ($jam_operasional as $hari => $jam):
                ?>
                <div class="d-flex justify-content-between border-bottom border-secondary py-1 small footer-text">
                    <span><?= $hari ?></span>
                    <span class="text-gold"><?= $jam ?></span>
                </div>
                <?php endforeach; ?>

                <!-- Session info -->
                <div class="mt-3 small footer-text opacity-50">
                    <i class="bi bi-clock me-1"></i>Session dimulai: <?= $visit_start ?>
                </div>
            </div>
        </div>

        <hr class="footer-divider mt-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start small footer-text">
                &copy; <?= date('Y') ?> <?= $restaurant_name ?>. All rights reserved.
            </div>
           </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>
</body>
</html>
