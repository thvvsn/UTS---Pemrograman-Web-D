<?php
use App\Models\Ebook;
use App\Models\Transaction;
use App\Models\User;

/** @var User[] $users */
$users ??= [];
/** @var Ebook[] $ebooks */
$ebooks ??= [];
/** @var Transaction[] $orderedTransactions */
$orderedTransactions ??= [];
/** @var array<string, User> $userMap */
$userMap ??= [];
/** @var array<string, Ebook> $ebookMap */
$ebookMap ??= [];
$tierLabels = [
    'Standard' => 'Standar',
    'Student' => 'Pelajar',
    'Premium' => 'Premium',
];
?>
<section id="borrow" class="section">
    <div class="container split-layout">
        <div class="panel form-panel">
            <span class="section-kicker">Meja Penyewaan</span>
            <h2>Pinjam E-Book</h2>
            <p class="panel-subtitle">Pilih anggota terdaftar dan judul yang tersedia untuk mencatat transaksi penyewaan.</p>

            <form id="borrow-form" action="index.php" method="POST" novalidate>
                <input type="hidden" name="action" value="borrow_ebook">

                <div class="form-group">
                    <label for="borrow_user_id">Anggota</label>
                    <select id="borrow_user_id" name="user_id" required>
                        <option value="">Pilih anggota...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= e($user->getId()) ?>">
                                <?= e($user->getId() . ' - ' . $user->getFullName() . ' (' . ($tierLabels[$user->getMembershipTier()] ?? $user->getMembershipTier()) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="field-error" id="error-borrow_user_id"></span>
                </div>

                <div class="form-group">
                    <label for="borrow_ebook_id">Judul E-Book</label>
                    <select id="borrow_ebook_id" name="ebook_id" required>
                        <option value="">Pilih e-book...</option>
                        <?php foreach ($ebooks as $ebook): ?>
                            <option
                                value="<?= e($ebook->getId()) ?>"
                                <?= !$ebook->isAvailable() ? 'disabled' : '' ?>
                            >
                                <?= e(sprintf(
                                    '%s - Rp %s (%d/%d tersedia)',
                                    $ebook->getTitle(),
                                    number_format($ebook->getRentalPrice(), 0, ',', '.'),
                                    $ebook->getAvailableCopies(),
                                    $ebook->getTotalCopies()
                                )) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="field-error" id="error-borrow_ebook_id"></span>
                </div>

                <div class="form-group">
                    <label for="rental_days">Durasi Sewa (Hari)</label>
                    <input
                        type="number"
                        id="rental_days"
                        name="rental_days"
                        value="14"
                        min="1"
                        max="30"
                        required
                    >
                    <span class="field-error" id="error-rental_days"></span>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Konfirmasi Sewa</button>
            </form>
        </div>

        <div class="panel list-panel" id="transactions">
            <span class="section-kicker">Catatan</span>
            <h2>Transaksi Penyewaan (<?= count($orderedTransactions) ?>)</h2>
            <p class="panel-subtitle">Aktivitas peminjaman dan pengembalian terbaru yang disinkronkan dengan penyimpanan JSON.</p>

            <?php if (empty($orderedTransactions)): ?>
                <p class="empty-state">Belum ada transaksi penyewaan yang tercatat.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="col-nowrap">ID</th>
                                <th>Peminjam</th>
                                <th>E-Book</th>
                                <th class="col-nowrap">Jatuh Tempo</th>
                                <th class="col-nowrap">Status</th>
                                <th class="col-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderedTransactions as $trx): ?>
                                <?php
                                $memberName = isset($userMap[$trx->getUserId()])
                                    ? $userMap[$trx->getUserId()]->getFullName()
                                    : $trx->getUserId();
                                $bookTitle = isset($ebookMap[$trx->getEbookId()])
                                    ? $ebookMap[$trx->getEbookId()]->getTitle()
                                    : $trx->getEbookId();
                                ?>
                                <tr>
                                    <td class="col-nowrap"><code><?= e($trx->getId()) ?></code></td>
                                    <td><?= e($memberName) ?></td>
                                    <td>
                                        <?= e($bookTitle) ?><br>
                                        <small class="muted-text">Rp <?= number_format($trx->getRentalFee(), 0, ',', '.') ?></small>
                                    </td>
                                    <td class="col-nowrap">
                                        <?= e($trx->getDueDate()) ?>
                                        <?php if ($trx->getReturnedAt() !== null): ?>
                                            <br><small class="muted-text">Dikembalikan: <?= e($trx->getReturnedAt()) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-nowrap">
                                        <span class="badge <?= $trx->isActive() ? 'badge-active' : 'badge-returned' ?>">
                                            <?= e($trx->isActive() ? 'Aktif' : 'Dikembalikan') ?>
                                        </span>
                                    </td>
                                    <td class="col-nowrap">
                                        <?php if ($trx->isActive()): ?>
                                            <form action="index.php" method="POST" class="inline-form">
                                                <input type="hidden" name="action" value="return_ebook">
                                                <input type="hidden" name="transaction_id" value="<?= e($trx->getId()) ?>">
                                                <button type="submit" class="btn btn-small btn-secondary">Kembalikan</button>
                                            </form>
                                        <?php else: ?>
                                            <form action="index.php" method="POST" class="inline-form">
                                                <input type="hidden" name="action" value="delete_transaction">
                                                <input type="hidden" name="transaction_id" value="<?= e($trx->getId()) ?>">
                                                <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

