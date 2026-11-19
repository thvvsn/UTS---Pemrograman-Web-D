<?php
use App\Models\Ebook;
use App\Models\User;

/** @var Ebook[] $ebooks */
$ebooks ??= [];
/** @var User[] $users */
$users ??= [];
/** @var int $totalAvailableCopies */
$totalAvailableCopies ??= 0;
/** @var int $activeRentalsCount */
$activeRentalsCount ??= 0;
?>
<section id="hero" class="hero-section">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1>Pinjam e-book teknis dan desain dengan mudah.</h1>
            <p class="hero-lead">
                Jelajahi koleksi pilihan judul rekayasa perangkat lunak, arsitektur web, dan desain.
                Daftar sebagai anggota, pesan salinan digital, dan kelola penyewaan aktif di satu tempat.
            </p>
            <div class="hero-actions">
                <a href="#catalog" class="btn btn-primary">Jelajahi Katalog</a>
                <a href="#borrow" class="btn btn-secondary">Sewa E-Book</a>
            </div>
        </div>

        <aside class="hero-stats" aria-label="Ringkasan Perpustakaan">
            <div class="stat-card">
                <span class="stat-value"><?= count($ebooks) ?></span>
                <span class="stat-label">Judul Katalog</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?= $totalAvailableCopies ?></span>
                <span class="stat-label">Salinan Tersedia</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?= count($users) ?></span>
                <span class="stat-label">Anggota Terdaftar</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?= $activeRentalsCount ?></span>
                <span class="stat-label">Penyewaan Aktif</span>
            </div>
        </aside>
    </div>
</section>

