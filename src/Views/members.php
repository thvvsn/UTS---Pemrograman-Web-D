<?php
use App\Models\User;

/** @var User[] $users */
$users ??= [];
/** @var User|null $editingUser */
$editingUser ??= null;

$isEditing = $editingUser instanceof User;
$editUserId = $editingUser instanceof User ? $editingUser->getId() : '';
$editFullName = $editingUser instanceof User ? $editingUser->getFullName() : '';
$editEmail = $editingUser instanceof User ? $editingUser->getEmail() : '';
$selectedTier = $editingUser instanceof User ? $editingUser->getMembershipTier() : 'Standard';

$tierLabels = [
    'Standard' => 'Standar',
    'Student' => 'Pelajar',
    'Premium' => 'Premium',
];
?>
<section id="members" class="section section-tinted">
    <div class="container split-layout">
        <div class="panel form-panel" id="member-form-panel">
            <span class="section-kicker">Keanggotaan</span>
            <h2 id="member-form-title"><?= $isEditing ? 'Edit Anggota' : 'Daftar Anggota Baru' ?></h2>
            <p class="panel-subtitle" id="member-form-subtitle">
                <?= $isEditing
                    ? 'Perbarui nama, alamat email, atau paket keanggotaan anggota.'
                    : 'Buat profil anggota untuk mulai meminjam judul digital.' ?>
            </p>

            <form id="member-form" action="index.php" method="POST" novalidate>
                <input type="hidden" id="member_action" name="action" value="<?= $isEditing ? 'update_member' : 'register_user' ?>">
                <input type="hidden" id="member_user_id" name="user_id" value="<?= e($editUserId) ?>">

                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="contoh: Maya Santoso"
                        value="<?= e($editFullName) ?>"
                        required
                        minlength="2"
                    >
                    <span class="field-error" id="error-full_name"></span>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="contoh: maya@example.com"
                        value="<?= e($editEmail) ?>"
                        required
                    >
                    <span class="field-error" id="error-email"></span>
                </div>

                <div class="form-group">
                    <label for="membership_tier">Paket Keanggotaan</label>
                    <select id="membership_tier" name="membership_tier" required>
                        <option value="Standard" <?= $selectedTier === 'Standard' ? 'selected' : '' ?>>Standar</option>
                        <option value="Student" <?= $selectedTier === 'Student' ? 'selected' : '' ?>>Pelajar</option>
                        <option value="Premium" <?= $selectedTier === 'Premium' ? 'selected' : '' ?>>Premium</option>
                    </select>
                    <span class="field-error" id="error-membership_tier"></span>
                </div>

                <div class="form-actions">
                    <button type="submit" id="member-submit-btn" class="btn btn-primary">
                        <?= $isEditing ? 'Simpan Perubahan' : 'Daftarkan Anggota' ?>
                    </button>
                    <a
                        href="index.php#members"
                        id="cancel-edit-member"
                        class="btn btn-secondary<?= $isEditing ? '' : ' is-hidden' ?>"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <div class="panel list-panel">
            <span class="section-kicker">Direktori</span>
            <h2>Anggota Terdaftar (<?= count($users) ?>)</h2>
            <p class="panel-subtitle">Akun perpustakaan aktif yang tersimpan dalam penyimpanan JSON.</p>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-nowrap">ID</th>
                            <th>Anggota</th>
                            <th class="col-nowrap">Paket</th>
                            <th class="col-nowrap">Bergabung</th>
                            <th class="col-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="col-nowrap"><code><?= e($user->getId()) ?></code></td>
                                <td>
                                    <strong><?= e($user->getFullName()) ?></strong><br>
                                    <small class="muted-text"><?= e($user->getEmail()) ?></small>
                                </td>
                                <td class="col-nowrap"><span class="badge badge-tier"><?= e($tierLabels[$user->getMembershipTier()] ?? $user->getMembershipTier()) ?></span></td>
                                <td class="col-nowrap"><?= e($user->getJoinedAt()) ?></td>
                                <td class="col-nowrap">
                                    <div class="table-actions">
                                        <a
                                            href="index.php?edit_member=<?= e(urlencode($user->getId())) ?>#members"
                                            class="btn btn-small btn-secondary btn-edit-member"
                                            data-user-id="<?= e($user->getId()) ?>"
                                            data-full-name="<?= e($user->getFullName()) ?>"
                                            data-email="<?= e($user->getEmail()) ?>"
                                            data-tier="<?= e($user->getMembershipTier()) ?>"
                                        >
                                            Edit
                                        </a>
                                        <form action="index.php" method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="delete_member">
                                            <input type="hidden" name="user_id" value="<?= e($user->getId()) ?>">
                                            <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

