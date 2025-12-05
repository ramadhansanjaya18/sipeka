-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Des 2025 pada 04.57
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `lowongan`
--

CREATE TABLE `lowongan` (
  `id_lowongan` int(11) NOT NULL,
  `id_hrd` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `posisi_lowongan` varchar(100) NOT NULL,
  `deskripsi_singkat` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `persyaratan` text DEFAULT NULL,
  `tanggal_buka` date NOT NULL,
  `tanggal_tutup` date NOT NULL,
  `status_lowongan` enum('Aktif','Tutup') NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lowongan`
--

INSERT INTO `lowongan` (`id_lowongan`, `id_hrd`, `judul`, `posisi_lowongan`, `deskripsi_singkat`, `deskripsi`, `persyaratan`, `tanggal_buka`, `tanggal_tutup`, `status_lowongan`) VALUES
(9, 1, 'Barista Syjura Coffee', 'Barista', 'Bertanggung jawab untuk meracik dan menyajikan minuman kopi & non-kopi berkualitas tinggi, melayani pelanggan dengan ramah (menerima pesanan, menjelaskan menu, menerima pembayaran).', 'Tanggung Jawab Utama:\r\n•	Meracik dan menyajikan minuman kopi sesuai standar resep.\r\n•	Menjaga kualitas dan konsistensi rasa.\r\n•	Menjaga kebersihan area bar dan peralatan.\r\n•	Melayani pelanggan dengan ramah dan profesional.\r\n•	Membantu operasional lain saat dibutuhkan.\r\n', '•	Pendidikan minimal SMA/SMK Sederajat, diutamakan jurusan Tata Boga.\r\n•	Memiliki pengetahuan dasar tentang jenis kopi, brewing, dan latte art.\r\n•	Pengalaman minimal 6 bulan sebagai barista menjadi nilai tambah.\r\n•	Bersedia bekerja dengan sistem shift.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(10, 1, 'Waitress Syjura Coffee', 'Waitress', 'Bertanggung jawab untuk memberikan pelayanan pelanggan yang prima, mulai dari menyambut tamu, mengantar ke meja, menawarkan/mencatat pesanan, menyajikan makanan dan minuman dengan tepat waktu.', 'Tanggung Jawab Utama:\r\n•	Menyambut pelanggan dan mencatat pesanan dengan sopan.\r\n•	Menyajikan makanan/minuman sesuai pesanan.\r\n•	Menjaga kebersihan meja dan area pelayanan.\r\n•	Memberikan rekomendasi menu kepada pelanggan.\r\n•	Membantu proses pembayaran di kasir jika dibutuhkan.\r\n', '•	Pendidikan minimal SMA/SMK Sederajat.\r\n•	Berpenampilan rapi dan sopan.\r\n•	Komunikatif, cepat tanggap, dan memiliki etika pelayanan yang baik.\r\n•	Pengalaman di bidang pelayanan makanan menjadi nilai tambah.', '2025-11-06', '2026-02-28', 'Aktif'),
(11, 1, 'Kitchen Staff Syjura Coffe', 'Kitchen Staff', 'Bertanggung jawab untuk persiapan bahan, memasak sesuai standar, menjaga kebersihan dan kehigienisan dapur, mengelola persediaan, serta membantu penyajian makanan agar operasional dapur berjalan efisien.', 'Tanggung Jawab Utama:\r\n•	Menyiapkan bahan makanan dan memasak sesuai standar resep.\r\n•	Menjaga kebersihan dan higienitas dapur.\r\n•	Mengatur stok bahan baku dan melakukan pengecekan kualitas.\r\n•	Mendukung inovasi menu baru bersama tim dapur.\r\n', '•	Pendidikan minimal SMA/SMK Tata Boga.\r\n•	Mengerti tentang standar kebersihan makanan (food safety).\r\n•	Mampu bekerja cepat dan rapi di lingkungan dapur.\r\n•	Pengalaman minimal 1 tahun di bidang kuliner lebih disukai.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(12, 1, 'Cleaning Service Syjura Coffe', 'Cleaning Service', 'Bertanggung jawab untuk membersihkan, merawat, dan menjaga kebersihan lingkungan seperti kantor, rumah sakit, atau fasilitas umum lainnya. agar menciptakan lingkungan yang bersih, sehat, dan nyaman bagi penghuni atau penggunanya. ', 'Tanggung Jawab Utama:\r\n•	Menjaga kebersihan area kafe, toilet, dapur, dan ruang karyawan.\r\n•	Membantu penataan meja dan kursi sebelum serta setelah jam operasional.\r\n•	Melakukan pembersihan rutin sesuai jadwal yang ditentukan.\r\n', '•	Pendidikan minimal SMP/SMA sederajat.\r\n•	Teliti, jujur, dan bertanggung jawab.\r\n•	Memiliki fisik yang kuat dan terbiasa bekerja dengan mobilitas tinggi.\r\n•	Bersedia bekerja dengan sistem shift.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(13, 1, 'Operational Supervisor Syjura Coffee', 'Operational Supervisor', 'Bertanggung jawab untuk mengawasi dan mengoordinasikan kegiatan operasional sehari-hari suatu departemen atau unit kerja untuk memastikan efisiensi dan kelancaran operasional.', 'Tanggung Jawab Utama:\r\n• Mengawasi aktivitas harian di area kafe.\r\n• Mengatur jadwal kerja karyawan dan memastikan pelayanan berjalan lancar.\r\n• Menangani keluhan pelanggan dan menyelesaikan masalah operasional.\r\n• Mengontrol stok bahan baku, peralatan, dan kebutuhan operasional lainnya.\r\n• Memberikan laporan harian kepada General Manager.\r\n', '•	Pendidikan minimal D3 / S1 Manajemen, Perhotelan, atau Pariwisata.\r\n•	Pengalaman minimal 2 tahun di bidang operasional kafe atau restoran.\r\n•	Memahami standar pelayanan pelanggan (customer service excellence).\r\n•	Memiliki kemampuan memimpin dan mengawasi tim.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(14, 1, 'Finance & Accounting Staff Syjura Coffee', 'Finance & Accounting Staff', 'Bertanggung jawab untuk pencatatan, pengelolaan, dan pelaporan transaksi keuangan perusahaan agar kesehatan finansialnya terpantau baik.', 'Tanggung Jawab Utama:\r\n• Mengelola dan mencatat transaksi keuangan harian.\r\n• Membuat laporan keuangan bulanan dan tahunan.\r\n• Mengontrol arus kas (cash flow) dan memastikan keseimbangan antara pemasukan dan pengeluaran.\r\n• Mengelola gaji, pajak, serta laporan pembukuan lainnya.\r\n• Melakukan audit internal sederhana untuk memastikan ketepatan data keuangan.\r\n', '•	Pendidikan minimal S1 Akuntansi / Keuangan.\r\n•	Menguasai Microsoft Excel dan software akuntansi seperti MYOB / Accurate / Jurnal.id.\r\n•	Teliti, jujur, dan mampu bekerja dengan tenggat waktu.\r\n•	Memahami dasar-dasar perpajakan dan manajemen keuangan.\r\n•	Pengalaman minimal 1 tahun di bidang keuangan (lebih disukai).\r\n', '2025-11-06', '2026-02-28', 'Aktif');

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
  `ijasah` varchar(255) DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL COMMENT 'Diisi jika role = hrd',
  `role` enum('hrd','pelamar') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `email`, `password`, `no_telepon`, `alamat`, `tempat_tanggal_lahir`, `riwayat_pendidikan`, `pengalaman_kerja`, `keahlian`, `ringkasan_pribadi`, `dokumen_cv`, `foto_profil`, `surat_lamaran`, `sertifikat_pendukung`, `ijasah`, `jabatan`, `role`) VALUES
(1, 'Ramadhan Sanjaya', 'hrdsyjuracoffe@gmail.com', '$2y$10$rMYdcvaVEu.keopsVRia4Os9H3Tcu0Wkwd1K2pAoaEp6F8QWKLaNu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'HRD', 'hrd'),
(91, 'Adelita nur mu\'izah', 'adelita@gmail.com', '$2y$10$kzcVRJc1J1NEQMt7uruA3upK02KxU0ILRyvizjrWu9iMJL3rKWNfe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(92, 'Syahrul Hannan Ramdhani', 'arul@gmail.com', '$2y$10$6ggwmM0POOzdhGXrB51I1uvIp3asdNxB6v6u3s2oHqurp30UrVJ/G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(93, 'Eka mayputri.Y', 'ekamay@gmail.com', '$2y$10$RDqxHTQk/SW3rgGMRrVHLuUNQV/k.u1uqlAT8pGgnhl1FN2IkaPEG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(94, 'syifa awaliyah', 'nonok@gmail.com', '$2y$10$fp4nbXD7j09GUfnDX08.7.ZAdx.7ibm5sa4ElSu59D03tu/QjLVSu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(95, 'Restu Septio', 'restu@gmail.com', '$2y$10$/5V3Gpb.qCqU8L5fh05T6ekeen29K2uswmGZ8.rNiWAeKo76.ukqu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(96, 'Hafiz Muzakar Addin', 'hafiz@gmail.com', '$2y$10$PZ0I7BUVHH5tetEWgczKh.2gVO.NRmcamkLft41wAOi/rcf4opg5K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(97, 'Selsa Nabila Putri', 'selsa@gmail.com', '$2y$10$a1dFDzpVHdQMLG7tLlCbaeeY/3rts81DMz66d3O0LpAK.N3/I8nNa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(98, 'junanti', 'junanti@gmail.com', '$2y$10$EVXO3olEgROyc.OuSX98h.op6kpTje2w5R9aZmSTh.2E5Wt9Dqcmm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(99, 'arul', 'arulimut@gmail.com', '$2y$10$/s/RphmCWNBqeQYVU7giQ.Bdd4ANDZXSJU5l1JC0SNRXUVz28QZuO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar'),
(100, 'diyan', 'diyan@gmail.com', '$2y$10$XLpWEpITTxlzjaCoWuXptudX4Mo1kNiS0Guuf3IJt7fXXFkJ9y22W', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pelamar');

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
  MODIFY `id_lamaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id_lowongan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `profil_pelamar`
--
ALTER TABLE `profil_pelamar`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  MODIFY `id_wawancara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
