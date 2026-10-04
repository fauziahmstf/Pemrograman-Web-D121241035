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