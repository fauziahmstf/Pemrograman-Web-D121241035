<?php

declare(strict_types=1);

require_once './Transaction.php';

session_start();

if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 0.0;
}

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Kesalahan Keamanan: Token CSRF tidak cocok.');
    }

    $type = $_POST['type'] ?? '';
    
    $amountInput = trim($_POST['amount'] ?? '');

    $transactionType = match ($type) {
        'deposit' => 'deposit',
        'withdrawal' => 'withdrawal',
        default => null
    };

    if ($transactionType === null) {
        $errors[] = 'Jenis transaksi tidak valid.';
    }

    if (
        $amountInput === '' ||
        !preg_match('/^\d+(?:\.\d+)?$/', $amountInput) ||
        (float) $amountInput <= 0
    ) {
        $errors[] = 'Jumlah transaksi harus berupa angka desimal positif.';
    }

    if (empty($errors)) {
        $transaction = new Transaction(
            uniqid('TRX-', true),
            $transactionType,
            (float) $amountInput
        );

        try {
            $transaction->process();

            $success = 'Transaksi berhasil diproses.';
        } catch (RuntimeException | InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Keuangan</title>
</head>
<body>

    <h1>Sistem Manajemen Keuangan</h1>

    <p>
        Saldo:
        Rp<?= htmlspecialchars(
            number_format(
                $_SESSION['balance'],
                2,
                ',',
                '.'
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>

    <?php if ($success !== ''): ?>
        <p>
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="type">Jenis Transaksi:</label>

        <select name="type" id="type">
            <option value="deposit">Deposit</option>
            <option value="withdrawal">Penarikan</option>
        </select>

        <br><br>

        <label for="amount">Jumlah:</label>

        <input
            type="number"
            name="amount"
            id="amount"
            step="0.01"
            min="0.01"
            required
        >

        <br><br>

        <button type="submit">Proses Transaksi</button>
    </form>

    <h2>Riwayat Transaksi</h2>

    <?php if (empty($_SESSION['transactions'])): ?>

        <p>Belum ada transaksi.</p>

    <?php else: ?>

        <ul>
            <?php foreach ($_SESSION['transactions'] as $transaction): ?>

                <li>
                    <?= htmlspecialchars(
                        $transaction['id'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                    -

                    <?= htmlspecialchars(
                        $transaction['type'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                    -

                    Rp<?= htmlspecialchars(
                        number_format(
                            (float) $transaction['amount'],
                            2,
                            ',',
                            '.'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </li>

            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

</body>
</html>