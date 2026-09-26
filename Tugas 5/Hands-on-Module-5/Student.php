<?php
declare(strict_types=1);

class Student
{
    public function __construct(
        private string $nim,
        private string $name,
        private string $email
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function saveToSession(): bool
    {
        $_SESSION['registered_students'][] = [
            'nim' => $this->nim,
            'name' => $this->name,
            'email' => $this->email
        ];

        return true;
    }
}