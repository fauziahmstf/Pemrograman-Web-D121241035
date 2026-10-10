<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/GuestBook.php';

// Membuat CSRF token untuk sesi pengguna.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = '';

$nama = '';
$email = '';
$pesan = '';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $guestBook = new GuestBook($pdo);

    // Memproses formulir ketika dikirim.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pesan = trim($_POST['pesan'] ?? '');
        $csrfToken = $_POST['csrf_token'] ?? '';

        // Memeriksa token CSRF.
        if (
            !is_string($csrfToken) ||
            !hash_equals($_SESSION['csrf_token'], $csrfToken)
        ) {
            $errors[] = 'Permintaan tidak valid. Silakan muat ulang halaman.';
        }

        // Validasi nama.
        if ($nama === '') {
            $errors[] = 'Nama tidak boleh kosong.';
        } elseif (strlen($nama) > 100) {
            $errors[] = 'Nama maksimal 100 karakter.';
        }

        // Validasi email.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        } elseif (strlen($email) > 150) {
            $errors[] = 'Email maksimal 150 karakter.';
        }

        // Validasi pesan.
        if (strlen($pesan) < 5) {
            $errors[] = 'Pesan minimal 5 karakter.';
        }

        // Simpan pesan hanya jika seluruh validasi lolos.
        if (empty($errors)) {
            $guestBook->addMessage($nama, $email, $pesan);

            // Token baru setelah pengiriman berhasil.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Mengosongkan formulir setelah sukses.
            $nama = '';
            $email = '';
            $pesan = '';

            $success = 'Pesan berhasil dikirim!';
        }
    }

    $messages = $guestBook->getMessages();
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    $messages = [];
    $errors[] = 'Koneksi atau proses database gagal. Periksa konfigurasi database.';
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 35px auto;
            padding: 0 18px;
            background: #f4f6f9;
            color: #243447;
        }

        h1, h2 { color: #243b53; }

        .panel {
            background: white;
            padding: 24px;
            margin-bottom: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000012;
        }

        label {
            display: block;
            margin: 14px 0 6px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccd3dc;
            border-radius: 5px;
            font: inherit;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        button {
            margin-top: 16px;
            padding: 11px 20px;
            border: 0;
            border-radius: 5px;
            background: #2457a7;
            color: white;
            cursor: pointer;
        }

        .success {
            background: #e6f6e9;
            padding: 12px;
            border-radius: 5px;
        }

        .error {
            background: #fdeaea;
            padding: 12px;
            border-radius: 5px;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th { background: #243b53; color: white; }

        .message {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
    </style>
</head>
<body>
    <h1>Buku Tamu</h1>
    <p>Silakan isi formulir untuk meninggalkan pesan.</p>

    <section class="panel">
        <h2>Formulir Buku Tamu</h2>

        <?php if ($success !== ''): ?>
            <p class="success"><?= escapeHtml($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error" role="alert">
                <strong>Periksa kembali:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escapeHtml($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= escapeHtml($_SESSION['csrf_token']) ?>"
            >

            <label for="nama">Nama</label>
            <input
                type="text"
                id="nama"
                name="nama"
                maxlength="100"
                value="<?= escapeHtml($nama) ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                value="<?= escapeHtml($email) ?>"
                required
            >

            <label for="pesan">Pesan</label>
            <textarea
                id="pesan"
                name="pesan"
                minlength="5"
                required
            ><?= escapeHtml($pesan) ?></textarea>

            <button type="submit">Kirim Pesan</button>
        </form>
    </section>

    <section class="panel">
        <h2>Daftar Pesan</h2>

        <?php if (empty($messages)): ?>
            <p>Belum ada pesan di buku tamu.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Pesan</th>
                            <th>Tanggal Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $index => $message): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= escapeHtml($message['nama']) ?></td>
                                <td><?= escapeHtml($message['email']) ?></td>
                                <td class="message"><?= escapeHtml($message['pesan']) ?></td>
                                <td><?= escapeHtml($message['tanggal_kirim']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</body>
</html>