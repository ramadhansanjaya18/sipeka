-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Des 2025 pada 15.54
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
(1, 2, 4, 'Waitress', '2025-12-21 14:22:40', 'Diterima'),
(2, 3, 4, 'Kitchen Staff', '2025-12-21 14:23:43', 'Ditolak');

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
(1, 1, 'Barista Syjura Coffee', 'Barista', 'Bertanggung jawab untuk meracik dan menyajikan minuman kopi & non-kopi berkualitas tinggi, melayani pelanggan dengan ramah (menerima pesanan, menjelaskan menu, menerima pembayaran).', 'Tanggung Jawab Utama:\r\n•	Meracik dan menyajikan minuman kopi sesuai standar resep.\r\n•	Menjaga kualitas dan konsistensi rasa.\r\n•	Menjaga kebersihan area bar dan peralatan.\r\n•	Melayani pelanggan dengan ramah dan profesional.\r\n•	Membantu operasional lain saat dibutuhkan.\r\n', '•	Pendidikan minimal SMA/SMK Sederajat, diutamakan jurusan Tata Boga.\r\n•	Memiliki pengetahuan dasar tentang jenis kopi, brewing, dan latte art.\r\n•	Pengalaman minimal 6 bulan sebagai barista menjadi nilai tambah.\r\n•	Bersedia bekerja dengan sistem shift.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(2, 1, 'Waitress Syjura Coffee', 'Waitress', 'Bertanggung jawab untuk memberikan pelayanan pelanggan yang prima, mulai dari menyambut tamu, mengantar ke meja, menawarkan/mencatat pesanan, menyajikan makanan dan minuman dengan tepat waktu.', 'Tanggung Jawab Utama:\r\n•	Menyambut pelanggan dan mencatat pesanan dengan sopan.\r\n•	Menyajikan makanan/minuman sesuai pesanan.\r\n•	Menjaga kebersihan meja dan area pelayanan.\r\n•	Memberikan rekomendasi menu kepada pelanggan.\r\n•	Membantu proses pembayaran di kasir jika dibutuhkan.\r\n', '•	Pendidikan minimal SMA/SMK Sederajat.\r\n•	Berpenampilan rapi dan sopan.\r\n•	Komunikatif, cepat tanggap, dan memiliki etika pelayanan yang baik.\r\n•	Pengalaman di bidang pelayanan makanan menjadi nilai tambah.', '2025-11-06', '2026-02-28', 'Aktif'),
(3, 1, 'Kitchen Staff Syjura Coffe', 'Kitchen Staff', 'Bertanggung jawab untuk persiapan bahan, memasak sesuai standar, menjaga kebersihan dan kehigienisan dapur, mengelola persediaan, serta membantu penyajian makanan agar operasional dapur berjalan efisien.', 'Tanggung Jawab Utama:\r\n•	Menyiapkan bahan makanan dan memasak sesuai standar resep.\r\n•	Menjaga kebersihan dan higienitas dapur.\r\n•	Mengatur stok bahan baku dan melakukan pengecekan kualitas.\r\n•	Mendukung inovasi menu baru bersama tim dapur.\r\n', '•	Pendidikan minimal SMA/SMK Tata Boga.\r\n•	Mengerti tentang standar kebersihan makanan (food safety).\r\n•	Mampu bekerja cepat dan rapi di lingkungan dapur.\r\n•	Pengalaman minimal 1 tahun di bidang kuliner lebih disukai.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(4, 1, 'Cleaning Service Syjura Coffe', 'Cleaning Service', 'Bertanggung jawab untuk membersihkan, merawat, dan menjaga kebersihan lingkungan seperti kantor, rumah sakit, atau fasilitas umum lainnya. agar menciptakan lingkungan yang bersih, sehat, dan nyaman bagi penghuni atau penggunanya. ', 'Tanggung Jawab Utama:\r\n•	Menjaga kebersihan area kafe, toilet, dapur, dan ruang karyawan.\r\n•	Membantu penataan meja dan kursi sebelum serta setelah jam operasional.\r\n•	Melakukan pembersihan rutin sesuai jadwal yang ditentukan.\r\n', '•	Pendidikan minimal SMP/SMA sederajat.\r\n•	Teliti, jujur, dan bertanggung jawab.\r\n•	Memiliki fisik yang kuat dan terbiasa bekerja dengan mobilitas tinggi.\r\n•	Bersedia bekerja dengan sistem shift.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(5, 1, 'Operational Supervisor Syjura Coffee', 'Operational Supervisor', 'Bertanggung jawab untuk mengawasi dan mengoordinasikan kegiatan operasional sehari-hari suatu departemen atau unit kerja untuk memastikan efisiensi dan kelancaran operasional.', 'Tanggung Jawab Utama:\r\n• Mengawasi aktivitas harian di area kafe.\r\n• Mengatur jadwal kerja karyawan dan memastikan pelayanan berjalan lancar.\r\n• Menangani keluhan pelanggan dan menyelesaikan masalah operasional.\r\n• Mengontrol stok bahan baku, peralatan, dan kebutuhan operasional lainnya.\r\n• Memberikan laporan harian kepada General Manager.\r\n', '•	Pendidikan minimal D3 / S1 Manajemen, Perhotelan, atau Pariwisata.\r\n•	Pengalaman minimal 2 tahun di bidang operasional kafe atau restoran.\r\n•	Memahami standar pelayanan pelanggan (customer service excellence).\r\n•	Memiliki kemampuan memimpin dan mengawasi tim.\r\n', '2025-11-06', '2026-02-28', 'Aktif'),
(6, 1, 'Finance & Accounting Staff Syjura Coffee', 'Finance & Accounting Staff', 'Bertanggung jawab untuk pencatatan, pengelolaan, dan pelaporan transaksi keuangan perusahaan agar kesehatan finansialnya terpantau baik.', 'Tanggung Jawab Utama:\r\n• Mengelola dan mencatat transaksi keuangan harian.\r\n• Membuat laporan keuangan bulanan dan tahunan.\r\n• Mengontrol arus kas (cash flow) dan memastikan keseimbangan antara pemasukan dan pengeluaran.\r\n• Mengelola gaji, pajak, serta laporan pembukuan lainnya.\r\n• Melakukan audit internal sederhana untuk memastikan ketepatan data keuangan.\r\n', '•	Pendidikan minimal S1 Akuntansi / Keuangan.\r\n•	Menguasai Microsoft Excel dan software akuntansi seperti MYOB / Accurate / Jurnal.id.\r\n•	Teliti, jujur, dan mampu bekerja dengan tenggat waktu.\r\n•	Memahami dasar-dasar perpajakan dan manajemen keuangan.\r\n•	Pengalaman minimal 1 tahun di bidang keuangan (lebih disukai).\r\n', '2025-11-06', '2026-02-02', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_pelamar`
--

CREATE TABLE `profil_pelamar` (
  `id_profil` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
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
  `ijasah` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil_pelamar`
--

INSERT INTO `profil_pelamar` (`id_profil`, `id_user`, `nama_lengkap`, `no_telepon`, `alamat`, `tempat_tanggal_lahir`, `riwayat_pendidikan`, `pengalaman_kerja`, `keahlian`, `ringkasan_pribadi`, `dokumen_cv`, `foto_profil`, `surat_lamaran`, `sertifikat_pendukung`, `ijasah`) VALUES
(1, 4, 'Ramadhan Sanjaya', '0897659329', 'indramayu', 'indramayu,12 agustus 2022', 'masih kuliah', 'saya pernah kerjain tugas', 'ngempromt', 'saya seorang laki laki', 'CV_4_69479fdd294c5.pdf', 'FOTO_4_69479a3493a3e.png', 'SL_4_69479fe7e5e4a.pdf', 'SERTIFIKAT_4_6947a015c3366.pdf', 'IJASAH_4_6947a02d02b22.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(10) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Di-hash menggunakan password_hash()',
  `role` enum('hrd','pelamar') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `email`, `password`, `role`) VALUES
(1, 'Ramadhan', 'hrdsyjuracoffe@gmail.com', '$2a$12$Z/70JBaIm2Q6Of.7Hhltju2iWRi3tJkg7e1Z8sHWZjigRe7X4Z2Re', 'hrd'),
(2, 'Junanti', 'antitwvinterb@gmail.com', '$2a$12$Z/70JBaIm2Q6Of.7Hhltju2iWRi3tJkg7e1Z8sHWZjigRe7X4Z2Re', 'hrd'),
(3, 'Syahrul', 'syahrulhannan07@gmail.com', '$2a$12$Z/70JBaIm2Q6Of.7Hhltju2iWRi3tJkg7e1Z8sHWZjigRe7X4Z2Re', 'hrd'),
(4, 'Rama', 'ramadhansanjaya18@gmail.com', '$2y$10$uqiuDjaV50bIdRJZbA3bnOlng8UmZSaSdXpQKHROlD7QB5L9yDAS.', 'pelamar');

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
(1, 1, '2025-12-22 16:26:00', 'Kantor Syjura Coffe', 'Terjadwal', 'test');

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
  MODIFY `id_lamaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id_lowongan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `profil_pelamar`
--
ALTER TABLE `profil_pelamar`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  MODIFY `id_wawancara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
