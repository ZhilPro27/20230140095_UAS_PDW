-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Jul 2025 pada 15.37
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
-- Database: `uas_p_pdw`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `mata_praktikum`
--

CREATE TABLE `mata_praktikum` (
  `id` int(11) NOT NULL,
  `kode_matkul` varchar(20) NOT NULL,
  `nama_matkul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mata_praktikum`
--

INSERT INTO `mata_praktikum` (`id`, `kode_matkul`, `nama_matkul`, `deskripsi`, `gambar`) VALUES
(1, 'PW2025', 'Pemrograman Web', 'Praktikum untuk mempelajari dasar-dasar pengembangan web dengan HTML, CSS, PHP, dan MySQL.', '1751806337_images (1).jpeg'),
(2, 'JK2025', 'Jaringan Komputer', 'Praktikum untuk memahami konsep dasar jaringan, topologi, dan konfigurasi perangkat.', '1751806329_images.jpeg'),
(3, 'BD2025', 'Basis Data', 'Praktikum mengenai perancangan dan implementasi database relasional menggunakan SQL.', '1751806251_33c6f7_21da1e83b7ba4a7d88e6a40e3c2eda2f~mv2.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `modul`
--

CREATE TABLE `modul` (
  `id` int(11) NOT NULL,
  `id_matkul` int(11) NOT NULL,
  `nama_modul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_materi` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `modul`
--

INSERT INTO `modul` (`id`, `id_matkul`, `nama_modul`, `deskripsi`, `file_materi`, `gambar`, `created_at`) VALUES
(1, 1, 'Modul 1: HTML & CSS', 'Pengenalan struktur dasar HTML dan styling dengan CSS.', 'Modul_1_Web.pdf', '1751806660_gambar_images (2).jpeg', '2025-07-06 11:53:16'),
(2, 1, 'Modul 2: PHP Native', 'Dasar-dasar pemrograman server-side dengan PHP Native.', 'Modul_2_Web.pdf', NULL, '2025-07-06 11:53:16'),
(3, 1, 'Modul 3: Koneksi Database', 'Menghubungkan aplikasi PHP dengan database MySQL.', 'Modul_3_Web.pdf', NULL, '2025-07-06 11:53:16'),
(4, 2, 'Modul 1: Pengenalan Jaringan', 'Konsep dasar, topologi, dan media transmisi.', 'Modul_1_Jarkom.pdf', NULL, '2025-07-06 11:53:16'),
(5, 2, 'Modul 2: Subnetting', 'Teknik perhitungan subnet mask untuk efisiensi IP Address.', 'Modul_2_Jarkom.pdf', NULL, '2025-07-06 11:53:16'),
(6, 3, 'Modul 1: ERD & Normalisasi', 'Perancangan database menggunakan Entity Relationship Diagram dan normalisasi.', 'Modul_1_Basdat.pdf', NULL, '2025-07-06 11:53:16'),
(7, 3, 'Modul 2: DDL & DML', 'Perintah-perintah dasar SQL untuk manipulasi struktur dan data.', 'Modul_2_Basdat.pdf', NULL, '2025-07-06 11:53:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran_praktikum`
--

CREATE TABLE `pendaftaran_praktikum` (
  `id` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `id_matkul` int(11) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran_praktikum`
--

INSERT INTO `pendaftaran_praktikum` (`id`, `id_mahasiswa`, `id_matkul`, `tanggal_daftar`) VALUES
(1, 6, 2, '2025-07-06 12:23:18'),
(2, 6, 1, '2025-07-06 12:36:16'),
(3, 2, 3, '2025-07-06 13:19:58'),
(4, 2, 1, '2025-07-06 13:20:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumpulan`
--

CREATE TABLE `pengumpulan` (
  `id` int(11) NOT NULL,
  `id_modul` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `file_laporan` varchar(255) NOT NULL,
  `tanggal_kumpul` timestamp NOT NULL DEFAULT current_timestamp(),
  `nilai` int(3) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumpulan`
--

INSERT INTO `pengumpulan` (`id`, `id_modul`, `id_mahasiswa`, `file_laporan`, `tanggal_kumpul`, `nilai`, `feedback`) VALUES
(1, 1, 3, 'Laporan_Budi_Modul1_Web.pdf', '2025-07-01 03:20:30', 90, 'Kerja bagus! Penjelasan di laporan sangat detail dan rapi.'),
(2, 2, 3, 'Laporan_Budi_Modul2_Web.pdf', '2025-07-05 16:10:00', 85, 'Kode PHP sudah berjalan baik, namun bisa lebih dioptimalkan.'),
(3, 1, 4, 'Laporan_Citra_Modul1_Web.pdf', '2025-07-01 04:00:00', 95, 'Sangat baik! Penggunaan CSS modern dan responsif.'),
(4, 2, 4, 'Laporan_Citra_Modul2_Web.pdf', '2025-07-06 01:00:00', NULL, NULL),
(5, 4, 4, 'Laporan_Citra_Modul1_Jarkom.pdf', '2025-07-03 07:00:00', 88, 'Pemahaman konsep sudah bagus.'),
(6, 6, 5, 'Laporan_Doni_Modul1_Basdat.pdf', '2025-07-04 09:45:00', 78, 'ERD sudah benar, namun bagian normalisasi perlu diperbaiki.'),
(7, 7, 5, 'Laporan_Doni_Modul2_Basdat.pdf', '2025-07-06 08:00:00', NULL, NULL),
(8, 4, 6, '6_4_1751804810.pdf', '2025-07-06 12:26:50', 70, 'Testing');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Zhilal Fadiah Krisna', 'zhilal@mail.com', '$2y$10$Gbh2S6yjtAwYiAzrTcS5NOB5a3rNTNlA8dWCsrjpvSMc7R8qPGcSi', 'asisten', '2025-07-06 11:25:30'),
(2, 'Budi Santoso', 'budi@example.com', '$2y$10$5g35av3BILxM1VL.2H/9au./3aNVYJcK/Nrs25cJKqhs0NtNrt8le', 'mahasiswa', '2025-07-06 11:53:16'),
(3, 'Citra Lestari', 'citra@example.com', '$2y$10$DAzqUqP.yusK0QLI7cU.KODk0JNiLKbHJK0bMDCmmaIDCOnYJV3Qu', 'mahasiswa', '2025-07-06 11:53:16'),
(4, 'Doni Firmansyah', 'doni@example.com', '$2y$10$D62y2w0k09t5DoRwkIjjnuSm7nYTt4Qx2KeqHt0Vj0QcDl/RLvZJO', 'mahasiswa', '2025-07-06 11:53:16'),
(5, 'Eka Putri', 'eka@example.com', '$2y$10$2z6h3HLBbELR3Mc91JzuyO1o36mza5wLIgnMo8Yvoz2d8Ljhzf16G', 'mahasiswa', '2025-07-06 11:53:16'),
(6, 'mahasiswa1', 'mahasiswa1@mail.com', '$2y$10$eo4BIYyD9RpxnoUBPgNg2OCm5y3OxqegsAi5ihmGjiD/PaELjJdhy', 'mahasiswa', '2025-07-06 12:22:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `mata_praktikum`
--
ALTER TABLE `mata_praktikum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_matkul` (`kode_matkul`);

--
-- Indeks untuk tabel `modul`
--
ALTER TABLE `modul`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_matkul` (`id_matkul`);

--
-- Indeks untuk tabel `pendaftaran_praktikum`
--
ALTER TABLE `pendaftaran_praktikum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pendaftaran_unik` (`id_mahasiswa`,`id_matkul`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `id_matkul` (`id_matkul`);

--
-- Indeks untuk tabel `pengumpulan`
--
ALTER TABLE `pengumpulan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_modul` (`id_modul`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `mata_praktikum`
--
ALTER TABLE `mata_praktikum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `modul`
--
ALTER TABLE `modul`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran_praktikum`
--
ALTER TABLE `pendaftaran_praktikum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengumpulan`
--
ALTER TABLE `pengumpulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `modul`
--
ALTER TABLE `modul`
  ADD CONSTRAINT `modul_ibfk_1` FOREIGN KEY (`id_matkul`) REFERENCES `mata_praktikum` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran_praktikum`
--
ALTER TABLE `pendaftaran_praktikum`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`id_matkul`) REFERENCES `mata_praktikum` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengumpulan`
--
ALTER TABLE `pengumpulan`
  ADD CONSTRAINT `pengumpulan_ibfk_1` FOREIGN KEY (`id_modul`) REFERENCES `modul` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumpulan_ibfk_2` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
