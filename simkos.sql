-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2026 at 12:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simkos`
--

-- ========================================
-- Hapus table yang ada (opsional)
-- ========================================
DROP TABLE IF EXISTS `riwayat_tagihan`;
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `detail_tagihan`;
DROP TABLE IF EXISTS `tagihan_utilitas`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `penghuni`;

-- ========================================
-- CREATE TABLE USERS
-- ========================================

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `status` varchar(50) DEFAULT 'Aktif',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `terakhir_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- CREATE TABLE PENGHUNI
-- ========================================

CREATE TABLE `penghuni` (
  `no` int(11) NOT NULL,
  `no_kamar` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status_kamar` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  `status_pembayaran` enum('Lunas','Menunggak','Belum Lunas') DEFAULT 'Belum Lunas',
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `nama_emergency` varchar(100) DEFAULT NULL,
  `telepon_emergency` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penghuni`
--

INSERT INTO `penghuni` (`no`, `no_kamar`, `nik`, `nama_lengkap`, `alamat`, `no_hp`, `email`, `status_kamar`, `status_pembayaran`, `tanggal_masuk`, `tanggal_keluar`) VALUES
(1, 3, '3301123131', 'Irvan Maulana', 'Purbalingga', '0845646', NULL, 'Aktif', 'Lunas', '2026-01-01', NULL),
(3, 3, '333215', 'Deko Wirayuda', 'Majenang', '0548464', NULL, 'Tidak Aktif', 'Lunas', '2026-01-15', '2026-05-15'),
(4, 2, '222222', 'Subhy', 'aaa', '085176751204', NULL, 'Aktif', 'Lunas', '2026-02-01', NULL);

-- ========================================
-- CREATE TABLE TAGIHAN_UTILITAS
-- ========================================

CREATE TABLE `tagihan_utilitas` (
  `id` int(11) NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `tahun` year(4) NOT NULL,
  `biaya_listrik` decimal(12,2) DEFAULT 0.00,
  `biaya_air` decimal(12,2) DEFAULT 0.00,
  `biaya_wifi` decimal(12,2) DEFAULT 0.00,
  `biaya_sampah` decimal(12,2) DEFAULT 0.00,
  `total_penghuni` int(11) NOT NULL,
  `total_tagihan` decimal(12,2) NOT NULL,
  `total_bobot` decimal(10,2) NOT NULL,
  `tarif_per_bobot` decimal(12,2) NOT NULL,
  `tenggat_pembayaran` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- CREATE TABLE DETAIL_TAGIHAN
-- ========================================

CREATE TABLE `detail_tagihan` (
  `id` int(11) NOT NULL,
  `tagihan_id` int(11) NOT NULL,
  `penghuni_id` int(11) NOT NULL,
  `bobot` decimal(3,1) NOT NULL,
  `nominal_tagihan` decimal(12,2) NOT NULL,
  `status_bayar` enum('Belum Bayar','Lunas','Sebagian') DEFAULT 'Belum Bayar',
  `tagihan_listrik` decimal(12,2) DEFAULT 0.00,
  `tagihan_wifi` decimal(12,2) DEFAULT 0.00,
  `tagihan_air` decimal(12,2) DEFAULT 0.00,
  `tagihan_sampah` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- CREATE TABLE PEMBAYARAN
-- ========================================

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `penghuni_id` int(11) NOT NULL,
  `tagihan_id` int(11) DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `tanggal_pembayaran` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Verifikasi',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- CREATE TABLE RIWAYAT_TAGIHAN
-- ========================================

CREATE TABLE `riwayat_tagihan` (
  `id` int(11) NOT NULL,
  `tagihan_id` int(11) DEFAULT NULL,
  `penghuni_id` int(11) DEFAULT NULL,
  `perubahan_dari` text DEFAULT NULL,
  `perubahan_ke` text DEFAULT NULL,
  `tipe_perubahan` varchar(50) DEFAULT NULL,
  `diubah_oleh` varchar(100) DEFAULT NULL,
  `tanggal_perubahan` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- INDEXES
-- ========================================

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`no`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `tagihan_utilitas`
--
ALTER TABLE `tagihan_utilitas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tagihan_bulan` (`bulan`,`tahun`);

--
-- Indexes for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tagihan_id` (`tagihan_id`),
  ADD KEY `penghuni_id` (`penghuni_id`),
  ADD UNIQUE KEY `unique_detail_tagihan` (`tagihan_id`,`penghuni_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penghuni_id` (`penghuni_id`),
  ADD KEY `tagihan_id` (`tagihan_id`);

--
-- Indexes for table `riwayat_tagihan`
--
ALTER TABLE `riwayat_tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tagihan_id` (`tagihan_id`),
  ADD KEY `penghuni_id` (`penghuni_id`);

-- ========================================
-- AUTO_INCREMENT
-- ========================================

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tagihan_utilitas`
--
ALTER TABLE `tagihan_utilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `riwayat_tagihan`
--
ALTER TABLE `riwayat_tagihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- ========================================
-- FOREIGN KEY CONSTRAINTS
-- ========================================

--
-- Constraints for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD CONSTRAINT `detail_tagihan_ibfk_1` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan_utilitas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_tagihan_ibfk_2` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`no`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`no`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`tagihan_id`) REFERENCES `detail_tagihan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `riwayat_tagihan`
--
ALTER TABLE `riwayat_tagihan`
  ADD CONSTRAINT `riwayat_tagihan_ibfk_1` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan_utilitas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `riwayat_tagihan_ibfk_2` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`no`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
