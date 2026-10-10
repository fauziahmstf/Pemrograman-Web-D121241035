<?php
require_once __DIR__ . '/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$sql = "
    SELECT
        m.nim,
        m.nama_mhs,
        mk.kode_mk,
        mk.nama_mk,
        mk.sks,
        d.nama_dosen,
        k.tanggal_ambil
    FROM krs k
    INNER JOIN mahasiswa m ON k.nim = m.nim
    INNER JOIN mata_kuliah mk ON k.kode_mk = mk.kode_mk
    LEFT JOIN dosen d ON mk.nidn = d.nidn
    ORDER BY m.nim, mk.kode_mk
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$dataKrs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar KRS Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f6f9;
        }

        h1 {
            color: #243b53;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #243b53;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Daftar KRS Mahasiswa</h1>

    <table>
        <thead>
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th>SKS</th>
                <th>Dosen</th>
                <th>Tanggal Ambil</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataKrs)): ?>
                <tr>
                    <td colspan="7">Belum ada data KRS.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($dataKrs as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['nama_mhs'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['kode_mk'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['nama_mk'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $row['sks'] ?></td>
                        <td><?= htmlspecialchars($row['nama_dosen'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['tanggal_ambil'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
