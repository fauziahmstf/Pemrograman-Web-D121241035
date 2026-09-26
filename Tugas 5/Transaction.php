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
        private string $amount
    ) {
    }
}