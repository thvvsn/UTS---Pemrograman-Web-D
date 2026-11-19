<?php
use App\Models\Ebook;

/** @var string[] $categories */
$categories ??= [];
/** @var Ebook[] $ebooks */
$ebooks ??= [];
?>
<section id="catalog" class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-kicker">Koleksi</span>
                <h2>Katalog E-Book</h2>
            </div>
            <div class="catalog-controls">
                <label class="sr-only" for="catalog-search">Cari katalog</label>
                <input
                    type="search"
                    id="catalog-search"
                    class="control-input"
                    placeholder="Cari judul atau penulis..."
                >
                <label class="sr-only" for="catalog-category">Filter berdasarkan kategori</label>
                <select id="catalog-category" class="control-select">
                    <option value="all">Semua Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category) ?>"><?= e($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="catalog-grid" id="catalog-grid">
            <?php foreach ($ebooks as $ebook): ?>
                <?php $available = $ebook->isAvailable(); ?>
                <article
                    class="ebook-card"
                    data-title="<?= e(strtolower($ebook->getTitle())) ?>"
                    data-author="<?= e(strtolower($ebook->getAuthor())) ?>"
                    data-isbn="<?= e(strtolower($ebook->getIsbn())) ?>"
                    data-category="<?= e($ebook->getCategory()) ?>"
                >
                    <div class="ebook-card-top">
                        <span class="badge badge-category"><?= e($ebook->getCategory()) ?></span>
                        <span class="badge <?= $available ? 'badge-available' : 'badge-unavailable' ?>">
                            <?= $available ? e($ebook->getAvailableCopies() . ' dari ' . $ebook->getTotalCopies() . ' Tersedia') : 'Stok Habis' ?>
                        </span>
                    </div>

                    <h3 class="ebook-title"><?= e($ebook->getTitle()) ?></h3>
                    <p class="ebook-author">Oleh <?= e($ebook->getAuthor()) ?> &middot; <?= e((string) $ebook->getPublishedYear()) ?></p>
                    <p class="ebook-description"><?= e($ebook->getDescription()) ?></p>

                    <div class="ebook-card-footer">
                        <div class="ebook-price">
                            <span class="price-amount">Rp <?= number_format($ebook->getRentalPrice(), 0, ',', '.') ?></span>
                            <span class="price-period">/ sewa</span>
                        </div>
                        <?php if ($available): ?>
                            <button
                                type="button"
                                class="btn btn-accent btn-select-ebook"
                                data-ebook-id="<?= e($ebook->getId()) ?>"
                            >
                                Pinjam E-Book
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-disabled" disabled>Tidak Tersedia</button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p id="catalog-empty" class="empty-state" hidden>Tidak ada e-book yang cocok dengan pencarian atau filter kategori Anda saat ini.</p>
    </div>
</section>

