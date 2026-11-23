<?php if (($showNavbar ?? true) === true): ?>
<header>
    <div class="header-inner">
        <h1>Presensi Siswa</h1>

        <button
            class="nav-toggle"
            type="button"
            aria-label="Buka menu navigasi"
            aria-expanded="false"
            aria-controls="site-nav"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav id="site-nav">
            <a href="<?= $baseUrl ?>index.php">Dashboard</a>
            <a href="<?= $baseUrl ?>pages/siswa/index.php">Data Siswa</a>
            <a href="<?= $baseUrl ?>pages/kelas/index.php">Data Kelas</a>
            <a href="<?= $baseUrl ?>pages/presensi/index.php">Rekap Presensi</a>
            <a href="<?= $baseUrl ?>pages/presensi/tap.php">Simulasi Tap</a>

            <?php if (isset($_SESSION['id_user'])): ?>
                <a href="<?= $baseUrl ?>pages/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>pages/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php endif; ?>