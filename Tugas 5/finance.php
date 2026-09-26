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
        Rp<?= number_format($_SESSION['balance'], 2, ',', '.') ?>
    </p>

    <form method="post">
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

</body>
</html>