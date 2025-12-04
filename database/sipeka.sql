-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Okt 2025 pada 17.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipeka`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `lamaran`
--

CREATE TABLE `lamaran` (
  `id_lamaran` int(11) NOT NULL,
  `id_lowongan` int(11) NOT NULL,
  `id_pelamar` int(11) NOT NULL,
  `posisi_dilamar` varchar(100) NOT NULL COMMENT 'Snapshot posisi saat melamar',
  `tanggal_lamaran` datetime NOT NULL DEFAULT current_timestamp(),
  `status_lamaran` enum('Diproses','Diterima','Ditolak','Wawancara') NOT NULL DEFAULT 'Diproses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lamaran`
--

INSERT INTO `lamaran` (`id_lamaran`, `id_lowongan`, `id_pelamar`, `posisi_dilamar`, `tanggal_lamaran`, `status_lamaran`) VALUES
(1, 1, 3, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(2, 1, 4, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(3, 1, 5, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(4, 1, 6, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(5, 1, 7, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(6, 1, 8, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(7, 1, 9, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(8, 1, 10, 'Barista', '2025-10-18 13:40:11', 'Wawancara'),
(9, 2, 11, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(10, 2, 12, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(11, 2, 13, 'Waiters', '2025-10-19 13:40:11', 'Diterima'),
(12, 2, 14, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(13, 2, 15, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(14, 2, 16, 'Waiters', '2025-10-19 13:40:11', 'Diterima'),
(15, 2, 17, 'Waiters', '2025-10-19 13:40:11', 'Diterima'),
(16, 2, 18, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(17, 2, 19, 'Waiters', '2025-10-19 13:40:11', 'Diterima'),
(18, 2, 20, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(19, 2, 21, 'Waiters', '2025-10-19 13:40:11', 'Diproses'),
(20, 2, 22, 'Waiters', '2025-10-19 13:40:11', 'Wawancara'),
(21, 2, 23, 'Waiters', '2025-10-16 13:40:11', 'Diproses'),
(22, 6, 24, 'Cleaning Service', '2025-10-17 13:40:11', 'Ditolak'),
(23, 6, 25, 'Cleaning Service', '2025-10-16 13:40:11', 'Diterima'),
(24, 2, 26, 'Waiters', '2025-10-11 13:40:11', 'Ditolak'),
(25, 1, 27, 'Barista', '2025-10-08 13:40:11', 'Diproses'),
(26, 1, 28, 'Barista', '2025-10-16 13:40:11', 'Ditolak'),
(27, 4, 29, 'Cashier', '2025-10-12 13:40:11', 'Diterima'),
(28, 5, 30, 'Supervisor', '2025-10-17 13:40:11', 'Ditolak'),
(29, 6, 31, 'Cleaning Service', '2025-10-08 13:40:11', 'Diterima'),
(30, 5, 32, 'Supervisor', '2025-10-17 13:40:11', 'Ditolak'),
(31, 3, 33, 'Kitchen Staff', '2025-10-09 13:40:11', 'Ditolak'),
(32, 5, 34, 'Supervisor', '2025-10-09 13:40:11', 'Ditolak'),
(33, 3, 35, 'Kitchen Staff', '2025-10-12 13:40:11', 'Diproses'),
(34, 6, 36, 'Cleaning Service', '2025-10-08 13:40:11', 'Diproses'),
(35, 2, 37, 'Waiters', '2025-10-17 13:40:11', 'Diproses'),
(36, 1, 38, 'Barista', '2025-10-09 13:40:11', 'Ditolak'),
(37, 2, 39, 'Waiters', '2025-10-08 13:40:11', 'Ditolak'),
(38, 5, 40, 'Supervisor', '2025-10-17 13:40:11', 'Diproses'),
(39, 5, 41, 'Supervisor', '2025-10-17 13:40:11', 'Diproses'),
(40, 6, 42, 'Cleaning Service', '2025-10-13 13:40:11', 'Ditolak'),
(41, 1, 43, 'Barista', '2025-10-14 13:40:11', 'Diterima'),
(42, 3, 44, 'Kitchen Staff', '2025-10-15 13:40:11', 'Diterima'),
(43, 5, 45, 'Supervisor', '2025-10-16 13:40:11', 'Diterima'),
(44, 1, 46, 'Barista', '2025-10-12 13:40:11', 'Diterima'),
(45, 2, 47, 'Waiters', '2025-10-08 13:40:11', 'Ditolak'),
(46, 3, 48, 'Kitchen Staff', '2025-10-15 13:40:11', 'Ditolak'),
(47, 2, 49, 'Waiters', '2025-10-12 13:40:11', 'Ditolak'),
(48, 3, 50, 'Kitchen Staff', '2025-10-16 13:40:11', 'Diterima'),
(49, 3, 51, 'Kitchen Staff', '2025-10-13 13:40:11', 'Diproses'),
(50, 2, 52, 'Waiters', '2025-10-12 13:40:11', 'Diterima'),
(51, 5, 53, 'Supervisor', '2025-10-14 13:40:11', 'Ditolak'),
(52, 3, 54, 'Kitchen Staff', '2025-10-09 13:40:11', 'Diterima'),
(53, 1, 55, 'Barista', '2025-10-13 13:40:11', 'Diproses'),
(54, 1, 56, 'Barista', '2025-10-17 13:40:11', 'Diterima'),
(55, 1, 57, 'Barista', '2025-10-17 13:40:11', 'Ditolak'),
(56, 6, 58, 'Cleaning Service', '2025-10-10 13:40:11', 'Ditolak'),
(57, 3, 59, 'Kitchen Staff', '2025-10-11 13:40:11', 'Diterima'),
(58, 2, 60, 'Waiters', '2025-10-09 13:40:11', 'Diterima'),
(59, 3, 61, 'Kitchen Staff', '2025-10-16 13:40:11', 'Diterima'),
(60, 2, 62, 'Waiters', '2025-10-14 13:40:11', 'Diterima'),
(61, 5, 63, 'Supervisor', '2025-10-17 13:40:11', 'Ditolak'),
(62, 5, 64, 'Supervisor', '2025-10-09 13:40:11', 'Diproses'),
(63, 4, 65, 'Cashier', '2025-10-16 13:40:11', 'Diproses'),
(64, 4, 66, 'Cashier', '2025-10-12 13:40:11', 'Diproses'),
(65, 6, 67, 'Cleaning Service', '2025-10-10 13:40:11', 'Diproses'),
(66, 2, 68, 'Waiters', '2025-10-13 13:40:11', 'Diterima'),
(67, 6, 69, 'Cleaning Service', '2025-10-14 13:40:11', 'Diproses'),
(68, 5, 70, 'Supervisor', '2025-10-15 13:40:11', 'Diproses'),
(69, 5, 71, 'Supervisor', '2025-10-11 13:40:11', 'Diproses'),
(70, 6, 72, 'Cleaning Service', '2025-10-15 13:40:11', 'Diproses'),
(71, 5, 73, 'Supervisor', '2025-10-15 13:40:11', 'Diterima'),
(72, 3, 74, 'Kitchen Staff', '2025-10-13 13:40:11', 'Ditolak'),
(73, 5, 75, 'Supervisor', '2025-10-15 13:40:11', 'Diproses'),
(74, 5, 76, 'Supervisor', '2025-10-15 13:40:11', 'Diterima'),
(75, 6, 77, 'Cleaning Service', '2025-10-10 13:40:11', 'Diproses'),
(76, 5, 78, 'Supervisor', '2025-10-12 13:40:11', 'Ditolak'),
(77, 2, 79, 'Waiters', '2025-10-17 13:40:11', 'Diproses'),
(78, 3, 80, 'Kitchen Staff', '2025-10-12 13:40:11', 'Ditolak'),
(79, 4, 81, 'Cashier', '2025-10-15 13:40:11', 'Ditolak'),
(80, 3, 82, 'Kitchen Staff', '2025-10-13 13:40:11', 'Ditolak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `lowongan`
--

CREATE TABLE `lowongan` (
  `id_lowongan` int(11) NOT NULL,
  `id_hrd` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `posisi_lowongan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `persyaratan` text DEFAULT NULL,
  `tanggal_buka` date NOT NULL,
  `tanggal_tutup` date NOT NULL,
  `status_lowongan` enum('Aktif','Tutup') NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lowongan`
--

INSERT INTO `lowongan` (`id_lowongan`, `id_hrd`, `judul`, `posisi_lowongan`, `deskripsi`, `persyaratan`, `tanggal_buka`, `tanggal_tutup`, `status_lowongan`) VALUES
(1, 2, 'Barista Profesional', 'Barista', 'Membuat dan menyajikan kopi berbasis espresso dan manual brew.', 'Pria/Wanita, Usia maks 28 thn, Pengalaman min 1 thn, Jujur dan teliti.', '2025-10-01', '2025-10-19', 'Aktif'),
(2, 2, 'Waiters / Pelayan', 'Waiters', 'Melayani pelanggan, mencatat pesanan, dan menyajikan makanan/minuman.', 'Pria/Wanita, Ramah, Komunikatif, Berpenampilan menarik.', '2025-10-01', '2025-11-01', 'Aktif'),
(3, 2, 'Staf Dapur', 'Kitchen Staff', 'Membantu koki dalam persiapan bahan baku dan memasak menu.', 'Pria/Wanita, Bisa memasak masakan dasar, Bersih dan rapi.', '2025-10-05', '2025-11-05', 'Aktif'),
(4, 2, 'Kasir', 'Cashier', 'Melayani proses transaksi dan pembayaran pelanggan.', 'Wanita, Usia maks 26 thn, Jujur, teliti, menguasai mesin POS.', '2025-10-05', '2025-11-05', 'Aktif'),
(5, 2, 'Supervisor Outlet', 'Supervisor', 'Mengawasi operasional harian coffee shop dan memimpin tim.', 'Pria/Wanita, Pengalaman min 1 thn sebagai supervisor F&B.', '2025-10-09', '2025-11-13', 'Aktif'),
(6, 2, 'Staf Kebersihan', 'Cleaning Service', 'Menjaga kebersihan seluruh area indoor dan outdoor coffee shop.', 'Pria, Rajin, Bersih, Siap bekerja shift.', '2025-10-10', '2025-11-10', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_pelamar`
--

CREATE TABLE `profil_pelamar` (
  `id_profil` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tempat_tanggal_lahir` varchar(100) DEFAULT NULL,
  `riwayat_pendidikan` text DEFAULT NULL,
  `pengalaman_kerja` text DEFAULT NULL,
  `keahlian` text DEFAULT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL COMMENT 'Menyimpan nama file dokumen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Di-hash menggunakan password_hash()',
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tempat_tanggal_lahir` varchar(100) DEFAULT NULL,
  `riwayat_pendidikan` text DEFAULT NULL,
  `pengalaman_kerja` text DEFAULT NULL,
  `keahlian` text DEFAULT NULL,
  `ringkasan_pribadi` text DEFAULT NULL,
  `dokumen_cv` varchar(255) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `surat_lamaran` varchar(255) DEFAULT NULL,
  `sertifikat_pendukung` varchar(255) DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL COMMENT 'Diisi jika role = hrd',
  `role` enum('hrd','pelamar') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `email`, `password`, `no_telepon`, `alamat`, `tempat_tanggal_lahir`, `riwayat_pendidikan`, `pengalaman_kerja`, `keahlian`, `ringkasan_pribadi`, `dokumen_cv`, `foto_profil`, `surat_lamaran`, `sertifikat_pendukung`, `jabatan`, `role`) VALUES
(2, 'Ramadhan Sanjaya', 'rama@hrd.syjura.co.id', '$2y$10$rMYdcvaVEu.keopsVRia4Os9H3Tcu0Wkwd1K2pAoaEp6F8QWKLaNu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'HRD', 'hrd'),
(3, 'Pelamar Contoh 1', 'pelamar1@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(4, 'Pelamar Contoh 2', 'pelamar2@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(5, 'Pelamar Contoh 3', 'pelamar3@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(6, 'Pelamar Contoh 4', 'pelamar4@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000004', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(7, 'Pelamar Contoh 5', 'pelamar5@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000005', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(8, 'Pelamar Contoh 6', 'pelamar6@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(9, 'Pelamar Contoh 7', 'pelamar7@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000007', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(10, 'Pelamar Contoh 8', 'pelamar8@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000008', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(11, 'Pelamar Contoh 9', 'pelamar9@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(12, 'Pelamar Contoh 10', 'pelamar10@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000010', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(13, 'Pelamar Contoh 11', 'pelamar11@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(14, 'Pelamar Contoh 12', 'pelamar12@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000012', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(15, 'Pelamar Contoh 13', 'pelamar13@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000013', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(16, 'Pelamar Contoh 14', 'pelamar14@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000014', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(17, 'Pelamar Contoh 15', 'pelamar15@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(18, 'Pelamar Contoh 16', 'pelamar16@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000016', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(19, 'Pelamar Contoh 17', 'pelamar17@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000017', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(20, 'Pelamar Contoh 18', 'pelamar18@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(21, 'Pelamar Contoh 19', 'pelamar19@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(22, 'Pelamar Contoh 20', 'pelamar20@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(23, 'Pelamar Contoh 21', 'pelamar21@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(24, 'Pelamar Contoh 22', 'pelamar22@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(25, 'Pelamar Contoh 23', 'pelamar23@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000023', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(26, 'Pelamar Contoh 24', 'pelamar24@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000024', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(27, 'Pelamar Contoh 25', 'pelamar25@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(28, 'Pelamar Contoh 26', 'pelamar26@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000026', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(29, 'Pelamar Contoh 27', 'pelamar27@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000027', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(30, 'Pelamar Contoh 28', 'pelamar28@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000028', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(31, 'Pelamar Contoh 29', 'pelamar29@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000029', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(32, 'Pelamar Contoh 30', 'pelamar30@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(33, 'Pelamar Contoh 31', 'pelamar31@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000031', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(34, 'Pelamar Contoh 32', 'pelamar32@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000032', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(35, 'Pelamar Contoh 33', 'pelamar33@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000033', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(36, 'Pelamar Contoh 34', 'pelamar34@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000034', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(37, 'Pelamar Contoh 35', 'pelamar35@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(38, 'Pelamar Contoh 36', 'pelamar36@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000036', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(39, 'Pelamar Contoh 37', 'pelamar37@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000037', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(40, 'Pelamar Contoh 38', 'pelamar38@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000038', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(41, 'Pelamar Contoh 39', 'pelamar39@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000039', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(42, 'Pelamar Contoh 40', 'pelamar40@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000040', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(43, 'Pelamar Contoh 41', 'pelamar41@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000041', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(44, 'Pelamar Contoh 42', 'pelamar42@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000042', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(45, 'Pelamar Contoh 43', 'pelamar43@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000043', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(46, 'Pelamar Contoh 44', 'pelamar44@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(47, 'Pelamar Contoh 45', 'pelamar45@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000045', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(48, 'Pelamar Contoh 46', 'pelamar46@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000046', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(49, 'Pelamar Contoh 47', 'pelamar47@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000047', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(50, 'Pelamar Contoh 48', 'pelamar48@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000048', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(51, 'Pelamar Contoh 49', 'pelamar49@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000049', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(52, 'Pelamar Contoh 50', 'pelamar50@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(53, 'Pelamar Contoh 51', 'pelamar51@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000051', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(54, 'Pelamar Contoh 52', 'pelamar52@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000052', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(55, 'Pelamar Contoh 53', 'pelamar53@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000053', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(56, 'Pelamar Contoh 54', 'pelamar54@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(57, 'Pelamar Contoh 55', 'pelamar55@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000055', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(58, 'Pelamar Contoh 56', 'pelamar56@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000056', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(59, 'Pelamar Contoh 57', 'pelamar57@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000057', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(60, 'Pelamar Contoh 58', 'pelamar58@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000058', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(61, 'Pelamar Contoh 59', 'pelamar59@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000059', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(62, 'Pelamar Contoh 60', 'pelamar60@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(63, 'Pelamar Contoh 61', 'pelamar61@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000061', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(64, 'Pelamar Contoh 62', 'pelamar62@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000062', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(65, 'Pelamar Contoh 63', 'pelamar63@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000063', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(66, 'Pelamar Contoh 64', 'pelamar64@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000064', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(67, 'Pelamar Contoh 65', 'pelamar65@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000065', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(68, 'Pelamar Contoh 66', 'pelamar66@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000066', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(69, 'Pelamar Contoh 67', 'pelamar67@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000067', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(70, 'Pelamar Contoh 68', 'pelamar68@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000068', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(71, 'Pelamar Contoh 69', 'pelamar69@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000069', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(72, 'Pelamar Contoh 70', 'pelamar70@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000070', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(73, 'Pelamar Contoh 71', 'pelamar71@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000071', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(74, 'Pelamar Contoh 72', 'pelamar72@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000072', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(75, 'Pelamar Contoh 73', 'pelamar73@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000073', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(76, 'Pelamar Contoh 74', 'pelamar74@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000074', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(77, 'Pelamar Contoh 75', 'pelamar75@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000075', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(78, 'Pelamar Contoh 76', 'pelamar76@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000076', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(79, 'Pelamar Contoh 77', 'pelamar77@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000077', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(80, 'Pelamar Contoh 78', 'pelamar78@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000078', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(81, 'Pelamar Contoh 79', 'pelamar79@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000079', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(82, 'Pelamar Contoh 80', 'pelamar80@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08120000080', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(83, 'Ramadhan Sanjaya', 'ramadhan@gmail.com', '$2y$10$7.8tOUTWffZHnFTDpoOrBuoRWwxDCc0p7rFCUPLtv/bvjtSNV9vES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(84, 'arul', 'arul@gmail.com', '$2y$10$NaWEO0l/W5w8bIpJgvvTpuTEG9.GJ8IQALXuo1FC.wjrvw1/1o/s2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wawancara`
--

CREATE TABLE `wawancara` (
  `id_wawancara` int(11) NOT NULL,
  `id_lamaran` int(11) NOT NULL,
  `jadwal` datetime NOT NULL COMMENT 'Menyimpan tanggal dan jam',
  `lokasi` varchar(255) NOT NULL,
  `status_wawancara` enum('Terjadwal','Selesai','Batal') NOT NULL DEFAULT 'Terjadwal',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wawancara`
--

INSERT INTO `wawancara` (`id_wawancara`, `id_lamaran`, `jadwal`, `lokasi`, `status_wawancara`, `catatan`) VALUES
(3, 3, '2025-10-22 13:44:00', 'Online Via Zoom / Kantor Syjura Coffe', 'Terjadwal', 'Catatan wawancara untuk lamaran ID 3'),
(4, 4, '2025-10-23 13:40:11', 'Online Via Zoom / Kantor Syjura Coffe', 'Terjadwal', 'Catatan wawancara untuk lamaran ID 4'),
(6, 6, '2025-10-25 13:40:11', 'Online Via Zoom / Kantor Syjura Coffe', 'Terjadwal', 'Catatan wawancara untuk lamaran ID 6'),
(7, 7, '2025-10-26 13:40:11', 'Online Via Zoom / Kantor Syjura Coffe', 'Terjadwal', 'Catatan wawancara untuk lamaran ID 7'),
(8, 8, '2025-10-27 13:40:11', 'Online Via Zoom / Kantor Syjura Coffe', 'Terjadwal', 'Catatan wawancara untuk lamaran ID 8');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  ADD PRIMARY KEY (`id_lamaran`),
  ADD KEY `id_lowongan` (`id_lowongan`),
  ADD KEY `id_pelamar` (`id_pelamar`);

--
-- Indeks untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id_lowongan`),
  ADD KEY `id_hrd` (`id_hrd`);

--
-- Indeks untuk tabel `profil_pelamar`
--
ALTER TABLE `profil_pelamar`
  ADD PRIMARY KEY (`id_profil`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  ADD PRIMARY KEY (`id_wawancara`),
  ADD UNIQUE KEY `id_lamaran` (`id_lamaran`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  MODIFY `id_lamaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id_lowongan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `profil_pelamar`
--
ALTER TABLE `profil_pelamar`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  MODIFY `id_wawancara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `lamaran`
--
ALTER TABLE `lamaran`
  ADD CONSTRAINT `lamaran_ibfk_1` FOREIGN KEY (`id_lowongan`) REFERENCES `lowongan` (`id_lowongan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lamaran_ibfk_2` FOREIGN KEY (`id_pelamar`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  ADD CONSTRAINT `lowongan_ibfk_1` FOREIGN KEY (`id_hrd`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `profil_pelamar`
--
ALTER TABLE `profil_pelamar`
  ADD CONSTRAINT `profil_pelamar_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  ADD CONSTRAINT `wawancara_ibfk_1` FOREIGN KEY (`id_lamaran`) REFERENCES `lamaran` (`id_lamaran`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
