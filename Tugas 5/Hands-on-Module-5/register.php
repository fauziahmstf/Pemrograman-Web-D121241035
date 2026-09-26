<?php
declare(strict_types=1);

require_once './Student.php';

session_start();

// 1. Generate CSRF Token jika belum ada di session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$successMessage = '';

// 2. Pemrosesan HTTP POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifikasi CSRF Token
    $postToken = $_POST['csrf_token'] ?? '';

    if (!is_string($postToken) || !hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Kesalahan Keamanan: Token CSRF tidak cocok.');
    }

    // Mengambil input
    $nim = trim($_POST['nim'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // 3. Validasi NIM
    if (!preg_match('/^[0-9]{10}$/', $nim)) {
        $errors[] = 'NIM harus berupa angka sepanjang tepat 10 digit.';
    }

    // 4. Validasi Nama
    if (empty($name)) {
        $errors[] = 'Nama lengkap mahasiswa tidak boleh kosong.';
    }

    // 5. Validasi Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format alamat email tidak valid.';
    }

    // 6. Jika tidak ada error
    if (empty($errors)) {

        // Membuat object Student
        $student = new Student($nim, $name, $email);

        // Menyimpan ke session
        if ($student->saveToSession()) {
            $successMessage =
                'Pendaftaran mahasiswa ' .
                htmlspecialchars($student->getName(), ENT_QUOTES, 'UTF-8') .
                ' berhasil disimpan!';

            // Regenerasi CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pendaftaran Mahasiswa Baru</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"
    >
</head>

<body class="bg-light p-5">

    <div class="container" style="max-width: 600px;">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0">
                    Formulir Pendaftaran Mahasiswa Baru
                </h1>
            </div>

            <div class="card-body">

                <!-- Pesan Error -->
                <?php if (!empty($errors)): ?>

                    <div class="alert alert-danger">
                        <ul class="mb-0">

                            <?php foreach ($errors as $error): ?>

                                <li>
                                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                </li>

                            <?php endforeach; ?>

                        </ul>
                    </div>

                <?php endif; ?>


                <!-- Pesan Sukses -->
                <?php if (!empty($successMessage)): ?>

                    <div class="alert alert-success">
                        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>


                <!-- Form -->
                <form action="./register.php" method="POST">

                    <!-- CSRF Token -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <!-- NIM -->
                    <div class="mb-3">

                        <label for="nim" class="form-label">
                            Nomor Induk Mahasiswa (NIM)
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nim"
                            name="nim"
                            required
                            placeholder="Contoh: 1234567890"
                        >

                    </div>


                    <!-- Nama -->
                    <div class="mb-3">

                        <label for="name" class="form-label">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            required
                            placeholder="Masukkan nama lengkap Anda"
                        >

                    </div>


                    <!-- Email -->
                    <div class="mb-3">

                        <label for="email" class="form-label">
                            Alamat Email
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            required
                            placeholder="nama@mahasiswa.ac.id"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        Daftar Sekarang
                    </button>

                </form>

            </div>
        </div>


        <!-- Data Mahasiswa yang Tersimpan -->
        <?php if (!empty($_SESSION['registered_students'])): ?>

            <div class="card mt-4 shadow-sm">

                <div class="card-header bg-secondary text-white">

                    <h2 class="h5 mb-0">
                        Daftar Mahasiswa Terdaftar (Session)
                    </h2>

                </div>

                <div class="card-body">

                    <ul class="list-group">

                        <?php foreach ($_SESSION['registered_students'] as $student): ?>

                            <li class="list-group-item">

                                <strong>
                                    <?= htmlspecialchars($student['nim'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>

                                -

                                <?= htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?>

                                (

                                <em>
                                    <?= htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8') ?>
                                </em>

                                )

                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>