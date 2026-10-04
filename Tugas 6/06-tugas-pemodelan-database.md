# Tugas Modul 6: Perancangan ERD E-Library Kampus

**Nama:** Nurul Fauziah Mustafa
**NIM:** D121241035

## 1. Skenario

Basis data relasional untuk sistem peminjaman buku perpustakaan kampus.
Sistem mencatat data mahasiswa, buku, penerbit, serta riwayat peminjaman
dan pengembalian.

**Aturan bisnis:**
- Satu penerbit dapat menerbitkan banyak buku, satu buku hanya punya satu penerbit.
- Satu mahasiswa dapat melakukan banyak transaksi peminjaman.
- Satu transaksi dapat memuat lebih dari satu buku.
- Satu buku dapat dipinjam berkali-kali pada transaksi yang berbeda.
- Setiap buku dalam transaksi punya tanggal kembali sendiri-sendiri.

## 2. Identifikasi Entitas dan Atribut

| Entitas | Atribut | PK | FK |
|---|---|---|---|
| Mahasiswa | nim, nama, prodi, angkatan, email, no_hp | nim | - |
| Penerbit | id_penerbit, nama_penerbit, kota, alamat, telepon | id_penerbit | - |
| Buku | id_buku, isbn, judul, pengarang, tahun_terbit, stok, id_penerbit | id_buku | id_penerbit |
| Transaksi Peminjaman (header) | id_peminjaman, nim, tgl_pinjam, tgl_jatuh_tempo | id_peminjaman | nim |
| Transaksi Peminjaman (detail) | id_peminjaman, id_buku, tgl_kembali, denda, status | (id_peminjaman, id_buku) | id_peminjaman, id_buku |

**Relasi antar entitas:**
- Penerbit 1 : N Buku
- Mahasiswa 1 : N Peminjaman
- Peminjaman 1 : N Detail Peminjaman
- Buku 1 : N Detail Peminjaman
- Maka Peminjaman dan Buku berelasi M:N melalui Detail Peminjaman.