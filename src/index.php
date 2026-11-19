<?php

declare(strict_types=1);

use App\Models\Ebook;
use App\Models\JsonStorage;
use App\Models\Transaction;
use App\Models\User;

session_start();

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\Models\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/Models/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function generateNextId(string $prefix, array $items): string
{
    $max = 0;
    foreach ($items as $item) {
        $id = $item->getId();
        if (preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', $id, $matches)) {
            $num = (int) $matches[1];
            if ($num > $max) {
                $max = $num;
            }
        }
    }
    return sprintf('%s-%03d', $prefix, $max + 1);
}

$storage = new JsonStorage(__DIR__ . '/Data');

$ebooks = array_map(
    static fn(array $row): Ebook => Ebook::fromArray($row),
    $storage->loadAll('ebooks.json')
);

$users = array_map(
    static fn(array $row): User => User::fromArray($row),
    $storage->loadAll('users.json')
);

$transactions = array_map(
    static fn(array $row): Transaction => Transaction::fromArray($row),
    $storage->loadAll('transactions.json')
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'register_user') {
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $tier = trim((string) ($_POST['membership_tier'] ?? 'Standard'));

        if ($fullName === '' || strlen($fullName) < 2) {
            setFlash('error', 'Nama lengkap harus terdiri dari minimal 2 karakter.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Harap masukkan alamat email yang valid.');
        } else {
            $duplicate = false;
            foreach ($users as $existingUser) {
                if ($existingUser->getEmail() === $email) {
                    $duplicate = true;
                    break;
                }
            }

            if ($duplicate) {
                setFlash('error', 'Anggota dengan alamat email tersebut sudah terdaftar.');
            } else {
                $newUser = new User(
                    generateNextId('USR', $users),
                    $fullName,
                    $email,
                    $tier,
                    date('Y-m-d')
                );
                $users[] = $newUser;
                $saved = $storage->saveAll(
                    'users.json',
                    array_map(static fn(User $u): array => $u->toArray(), $users)
                );
                if (!$saved) {
                    setFlash('error', 'Gagal menyimpan data anggota ke file JSON. Periksa izin tulis folder Data.');
                }
            }
        }

        header('Location: index.php#members');
        exit;
    }

    if ($action === 'update_member') {
        $userId = trim((string) ($_POST['user_id'] ?? ''));
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $tier = trim((string) ($_POST['membership_tier'] ?? 'Standard'));

        if ($userId !== '' && $fullName !== '' && strlen($fullName) >= 2 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $duplicateEmail = false;
            foreach ($users as $existingUser) {
                if ($existingUser->getId() !== $userId && $existingUser->getEmail() === $email) {
                    $duplicateEmail = true;
                    break;
                }
            }

            if (!$duplicateEmail) {
                foreach ($users as $user) {
                    if ($user->getId() === $userId) {
                        $user->setFullName($fullName);
                        $user->setEmail($email);
                        $user->setMembershipTier($tier);
                        $storage->saveAll(
                            'users.json',
                            array_map(static fn(User $u): array => $u->toArray(), $users)
                        );
                        break;
                    }
                }
            }
        }

        header('Location: index.php#members');
        exit;
    }

    if ($action === 'delete_member') {
        $userId = trim((string) ($_POST['user_id'] ?? ''));

        if ($userId !== '') {
            $updatedUsers = [];
            $found = false;

            foreach ($users as $user) {
                if ($user->getId() === $userId) {
                    $found = true;
                    continue;
                }
                $updatedUsers[] = $user;
            }

            if ($found) {
                $users = array_values($updatedUsers);
                $storage->saveAll(
                    'users.json',
                    array_map(static fn(User $u): array => $u->toArray(), $users)
                );
            }
        }

        header('Location: index.php#members');
        exit;
    }

    if ($action === 'borrow_ebook') {
        $userId = trim((string) ($_POST['user_id'] ?? ''));
        $ebookId = trim((string) ($_POST['ebook_id'] ?? ''));
        $days = (int) ($_POST['rental_days'] ?? 14);
        $days = max(1, min(30, $days));

        $selectedUser = null;
        foreach ($users as $user) {
            if ($user->getId() === $userId) {
                $selectedUser = $user;
                break;
            }
        }

        $selectedEbook = null;
        foreach ($ebooks as $ebook) {
            if ($ebook->getId() === $ebookId) {
                $selectedEbook = $ebook;
                break;
            }
        }

        if ($selectedUser === null) {
            setFlash('error', 'Harap pilih anggota terdaftar yang valid.');
        } elseif ($selectedEbook === null) {
            setFlash('error', 'Harap pilih e-book yang valid dari katalog.');
        } elseif (!$selectedEbook->borrowCopy()) {
            setFlash('error', 'Saat ini tidak ada salinan yang tersedia untuk "' . $selectedEbook->getTitle() . '".');
        } else {
            $borrowedAt = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime('+' . $days . ' days'));
            $newTransaction = new Transaction(
                generateNextId('TRX', $transactions),
                $selectedUser->getId(),
                $selectedEbook->getId(),
                $borrowedAt,
                $dueDate,
                $selectedEbook->getRentalPrice(),
                'Active',
                null
            );

            $transactions[] = $newTransaction;

            $savedEbooks = $storage->saveAll(
                'ebooks.json',
                array_map(static fn(Ebook $b): array => $b->toArray(), $ebooks)
            );
            $savedTransactions = $storage->saveAll(
                'transactions.json',
                array_map(static fn(Transaction $t): array => $t->toArray(), $transactions)
            );

            if (!$savedEbooks || !$savedTransactions) {
                setFlash('error', 'Gagal menyimpan transaksi penyewaan ke file JSON. Periksa izin tulis folder Data.');
            }
        }

        header('Location: index.php#borrow');
        exit;
    }

    if ($action === 'return_ebook') {
        $transactionId = trim((string) ($_POST['transaction_id'] ?? ''));
        $targetTransaction = null;

        foreach ($transactions as $transaction) {
            if ($transaction->getId() === $transactionId) {
                $targetTransaction = $transaction;
                break;
            }
        }

        if ($targetTransaction === null || !$targetTransaction->isActive()) {
            setFlash('error', 'Transaksi tidak ditemukan atau sudah dikembalikan.');
        } else {
            $targetTransaction->markReturned(date('Y-m-d'));
            foreach ($ebooks as $ebook) {
                if ($ebook->getId() === $targetTransaction->getEbookId()) {
                    $ebook->returnCopy();
                    break;
                }
            }

            $savedEbooks = $storage->saveAll(
                'ebooks.json',
                array_map(static fn(Ebook $b): array => $b->toArray(), $ebooks)
            );
            $savedTransactions = $storage->saveAll(
                'transactions.json',
                array_map(static fn(Transaction $t): array => $t->toArray(), $transactions)
            );

            if (!$savedEbooks || !$savedTransactions) {
                setFlash('error', 'Gagal menyimpan pembaruan pengembalian ke file JSON. Periksa izin tulis folder Data.');
            }
        }

        header('Location: index.php#transactions');
        exit;
    }

    if ($action === 'delete_transaction') {
        $transactionId = trim((string) ($_POST['transaction_id'] ?? ''));

        if ($transactionId !== '') {
            $updatedTransactions = [];
            $found = false;

            foreach ($transactions as $transaction) {
                if ($transaction->getId() === $transactionId) {
                    $found = true;
                    continue;
                }
                $updatedTransactions[] = $transaction;
            }

            if ($found) {
                $transactions = array_values($updatedTransactions);
                $storage->saveAll(
                    'transactions.json',
                    array_map(static fn(Transaction $t): array => $t->toArray(), $transactions)
                );
            }
        }

        header('Location: index.php#transactions');
        exit;
    }
}

$userMap = [];
foreach ($users as $user) {
    $userMap[$user->getId()] = $user;
}

$editingUser = null;
$editMemberId = trim((string) ($_GET['edit_member'] ?? ''));
if ($editMemberId !== '' && isset($userMap[$editMemberId])) {
    $editingUser = $userMap[$editMemberId];
}

$ebookMap = [];
$categories = [];
$totalAvailableCopies = 0;
foreach ($ebooks as $ebook) {
    $ebookMap[$ebook->getId()] = $ebook;
    $totalAvailableCopies += $ebook->getAvailableCopies();
    $cat = $ebook->getCategory();
    if (!in_array($cat, $categories, true)) {
        $categories[] = $cat;
    }
}
sort($categories);

$activeRentalsCount = 0;
foreach ($transactions as $transaction) {
    if ($transaction->isActive()) {
        $activeRentalsCount++;
    }
}

$orderedTransactions = array_reverse($transactions);

require __DIR__ . '/Views/header.php';
?>
<main>
    <?php
    require __DIR__ . '/Views/hero.php';
    require __DIR__ . '/Views/catalog.php';
    require __DIR__ . '/Views/members.php';
    require __DIR__ . '/Views/borrow.php';
    ?>
</main>
<?php
require __DIR__ . '/Views/footer.php';

