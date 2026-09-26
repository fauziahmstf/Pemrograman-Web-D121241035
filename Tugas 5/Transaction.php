<?php

declare(strict_types=1);

// membuat kelas Transaction dengan properti private id, type, dan amount menggunakan constructor property promotion.
class Transaction
{
    public function __construct(
        // private karena OOP terenkapsulasi.
        // Properti tidak boleh diakses sembarangan dari luar class
        private string $id, 
        private string $type,
        private float $amount
    ) {
    }

        public function process(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['balance'])) {
            $_SESSION['balance'] = 0.0;
        }

        // menggunakan ekspresi match untuk mencocokkan jenis transaksi
        match ($this->type) {
            'deposit' => $_SESSION['balance'] += $this->amount,

            'withdrawal' => $this->processWithdrawal(),

            default => throw new InvalidArgumentException(
                'Jenis transaksi tidak valid.'
            )
        };

        $_SESSION['transactions'][] = [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount
        ];

        return true;
    }

    // processWithdrawal() dibuat private
    // Supaya logika penarikan tetap berada di dalam class Transaction.
    private function processWithdrawal(): void
    {
        if ($_SESSION['balance'] < $this->amount) {
            throw new RuntimeException(
                'Saldo tidak mencukupi untuk melakukan penarikan.'
            );
        }

        $_SESSION['balance'] -= $this->amount;
    }
}