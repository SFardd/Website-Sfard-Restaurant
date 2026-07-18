<?php
// ============================================================
// ABOUT.PHP — About Us Page
// ============================================================

$page_title = "About Us";
include 'includes/header.php';

// ---- PHP VARIABLES ----
$tahun_berdiri  = 2009;
$tahun_sekarang = (int) date('Y');
$umur_restoran  = $tahun_sekarang - $tahun_berdiri;

// ---- FUNCTION: Hitung lama pengalaman ----
function hitungPengalaman(int $tahunBerdiri): string {
    $lama = (int) date('Y') - $tahunBerdiri;
    return $lama . '+ tahun pengalaman';
}

// ---- FUNCTION: Generate inisial avatar ----
function getInisial(string $nama): string {
    $parts = explode(' ', $nama);
    $inisial = '';
    foreach ($parts as $p) {
        $inisial .= strtoupper(substr($p, 0, 1));
    }
    return substr($inisial, 0, 2);
}

// ---- ARRAY: Tim kami ----
$team = [
    ['name' => 'Chef Hendra Wijaya',  'role' => 'Head Chef',          'exp' => '20 tahun',  'bio' => 'Lulusan Le Cordon Bleu Jakarta dengan spesialisasi masakan Nusantara.'],
    ['name' => 'Chef Dewi Lestari',   'role' => 'Sous Chef',          'exp' => '12 tahun',  'bio' => 'Ahli masakan Prancis dengan sentuhan modern yang memukau.'],
    ['name' => 'Ahmad Ridwan',        'role' => 'Restaurant Manager',  'exp' => '15 tahun',  'bio' => 'Berpengalaman dalam manajemen restoran fine dining kelas internasional.'],
    ['name' => 'Sari Indah Pertiwi',  'role' => 'Pastry Chef',         'exp' => '8 tahun',   'bio' => 'Kreator dessert dan kue internasional yang inovatif dan menggugah selera.'],
];

// ---- ARRAY: Penghargaan ----
$penghargaan = [
    ['tahun' => 2023, 'nama' => 'Best Indonesian Restaurant',   'lembaga' => 'Jakarta Food Awards'],
    ['tahun' => 2022, 'nama' => 'Top 10 Fine Dining Jakarta',   'lembaga' => 'Indonesia Culinary Guide'],
    ['tahun' => 2021, 'nama' => 'Most Authentic Cuisine Award', 'lembaga' => 'National Restaurant Association'],
    ['tahun' => 2020, 'nama' => 'Chef of the Year',             'lembaga' => 'Culinary Institute Indonesia'],
    ['tahun' => 2019, 'nama' => 'Excellence in Service',        'lembaga' => 'Jakarta Tourism Board'],
];

// ---- ARRAY: Nilai-nilai restoran ----
$nilai = [
    ['icon' => 'bi-heart-fill',    'judul' => 'Passion',   'desc' => 'Setiap hidangan dibuat dengan penuh cinta dan dedikasi.'],
    ['icon' => 'bi-shield-fill',   'judul' => 'Quality',   'desc' => 'Bahan-bahan terbaik dipilih langsung dari sumber terpercaya.'],
    ['icon' => 'bi-people-fill',   'judul' => 'Community', 'desc' => 'Mendukung petani dan produsen lokal Indonesia.'],
    ['icon' => 'bi-lightbulb-fill','judul' => 'Innovation','desc' => 'Mengkreasikan tradisi kuliner dengan sentuhan kontemporer.'],
];
?>

<!-- PAGE BANNER -->
<div class="page-banner">
    <div class="container">
        <div class="section-label">Our Story</div>
        <h1 class="section-title">Tentang Kami</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">About</li>
            </ol>
        </nav>
    </div>
</div>

<!-- ABOUT MAIN -->
<section class="section-dark py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5" data-reveal>
                <div class="about-img-wrap">
                    <img src="img/aboutus.jpg" alt="SFard Restaurant" class="img-fluid w-100 rounded">
                    <div class="about-badge">
                        <div style="font-size:1.6rem; font-weight:800;"><?= $umur_restoran ?>+</div>
                        <div style="font-size:0.65rem; letter-spacing:0.05em">Years of<br>Excellence</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-reveal>
                <div class="section-label">Since <?= $tahun_berdiri ?></div>
                <h2 class="section-title">Kisah di Balik<br>Setiap Hidangan</h2>
                <div class="section-divider ms-0 mb-4"></div>
                <p style="color:var(--text-muted)">
                    SFard Restaurant berdiri sejak tahun <?= $tahun_berdiri ?> dengan satu misi sederhana namun mulia: menghadirkan cita rasa autentik Indonesia yang sesungguhnya kepada setiap tamu yang datang.
                </p>
                <p style="color:var(--text-muted)">
                    Selama <?= hitungPengalaman($tahun_berdiri) ?>, kami telah melayani jutaan tamu dengan hidangan yang terinspirasi dari kekayaan kuliner 34 provinsi di Indonesia.
                </p>

                <?php
                // ---- BRANCHING #1: Tampilkan pesan berdasarkan usia restoran ----
                if ($umur_restoran >= 15): ?>
                    <div class="p-3 mb-4 rounded" style="background:rgba(212,175,55,0.08); border-left:3px solid var(--gold);">
                        <strong class="text-gold"><i class="bi bi-trophy-fill me-2"></i>Milestone:</strong>
                        <span style="color:var(--text-muted)"> Kami dengan bangga merayakan <?= $umur_restoran ?>+ tahun melayani dengan sepenuh hati!</span>
                    </div>
                <?php elseif ($umur_restoran >= 10): ?>
                    <div class="p-3 mb-4 rounded" style="background:rgba(212,175,55,0.08); border-left:3px solid var(--gold);">
                        <strong class="text-gold"><i class="bi bi-star-fill me-2"></i>Dekade Pertama:</strong>
                        <span style="color:var(--text-muted)"> Satu dekade penuh dedikasi dan semangat melayani.</span>
                    </div>
                <?php endif; ?>

                <div class="row g-3 mt-1">
                    <?php
                    // ---- LOOPING #1: Nilai-nilai restoran ----
                    foreach ($nilai as $v): ?>
                        <div class="col-6" data-reveal>
                            <div class="p-3 rounded h-100" style="background:var(--dark-card); border:1px solid var(--border-gold);">
                                <i class="<?= $v['icon'] ?> text-gold fs-5 mb-2 d-block"></i>
                                <h6 class="mb-1" style="font-family:var(--ff-heading);"><?= $v['judul'] ?></h6>
                                <p class="mb-0 small" style="color:var(--text-muted)"><?= $v['desc'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TEAM SECTION -->
<section class="section-mid py-5">
    <div class="container">
        <div class="text-center mb-5" data-reveal>
            <div class="section-label">Our People</div>
            <h2 class="section-title">Tim Profesional Kami</h2>
            <div class="section-divider mx-auto"></div>
        </div>
        <div class="row g-4">
            <?php
            // ---- LOOPING #2: Tim ----
            foreach ($team as $member): ?>
                <div class="col-md-6 col-lg-3" data-reveal>
                    <div class="card-dark h-100 text-center p-4">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:80px;height:80px;background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#0D0A06;font-size:1.5rem;font-weight:700;font-family:var(--ff-display);">
                            <?= getInisial($member['name']) ?>
                        </div>
                        <h6 style="font-family:var(--ff-heading); color:var(--text-main)"><?= $member['name'] ?></h6>
                        <div class="badge-gold mb-2"><?= $member['role'] ?></div>
                        <p class="small mb-2" style="color:var(--text-muted)"><?= $member['bio'] ?></p>
                        <div class="small text-gold">
                            <i class="bi bi-award me-1"></i><?= $member['exp'] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- AWARDS SECTION -->
<section class="section-dark py-5">
    <div class="container">
        <div class="text-center mb-5" data-reveal>
            <div class="section-label">Recognition</div>
            <h2 class="section-title">Penghargaan Kami</h2>
            <div class="section-divider mx-auto"></div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php
                // ---- LOOPING #3: Penghargaan ----
                foreach ($penghargaan as $i => $award):
                    // ---- BRANCHING #2: Tandai penghargaan terbaru ----
                    $isTerbaru = ($i === 0);
                ?>
                    <div class="d-flex align-items-center gap-4 p-3 mb-3 rounded" data-reveal
                         style="background:var(--dark-card); border:1px solid <?= $isTerbaru ? 'var(--gold)' : 'var(--border-gold)' ?>; position:relative;">
                        <?php if ($isTerbaru): ?>
                            <span class="badge-gold position-absolute" style="top:-10px; right:10px">Terbaru</span>
                        <?php endif; ?>
                        <div class="text-center" style="min-width:60px;">
                            <div style="font-family:var(--ff-display); color:var(--gold); font-size:1.3rem; font-weight:700;"><?= $award['tahun'] ?></div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0" style="color:var(--text-main)"><?= $award['nama'] ?></h6>
                            <small style="color:var(--text-muted)"><?= $award['lembaga'] ?></small>
                        </div>
                        <i class="bi bi-trophy-fill text-gold fs-4"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
