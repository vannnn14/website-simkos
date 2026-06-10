-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2026 at 03:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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

-- --------------------------------------------------------

--
-- Table structure for table `detail_tagihan`
--

CREATE TABLE `detail_tagihan` (
  `id` int(11) NOT NULL,
  `tagihan_id` int(11) NOT NULL,
  `penghuni_id` int(11) NOT NULL,
  `bobot` decimal(3,1) NOT NULL,
  `nominal_tagihan` decimal(12,2) NOT NULL,
  `status_bayar` enum('Belum Bayar','Lunas') DEFAULT 'Belum Bayar',
  `midtrans_order_id` varchar(100) DEFAULT NULL,
  `midtrans_va_number` varchar(50) DEFAULT NULL,
  `midtrans_va_bank` varchar(20) DEFAULT NULL,
  `midtrans_transaction_id` varchar(100) DEFAULT NULL,
  `midtrans_expiry` datetime DEFAULT NULL,
  `tagihan_listrik` decimal(12,2) DEFAULT 0.00,
  `tagihan_wifi` decimal(12,2) DEFAULT 0.00,
  `tagihan_air` decimal(12,2) DEFAULT 0.00,
  `tagihan_sampah` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_tagihan`
--

INSERT INTO `detail_tagihan` (`id`, `tagihan_id`, `penghuni_id`, `bobot`, `nominal_tagihan`, `status_bayar`, `midtrans_order_id`, `midtrans_va_number`, `midtrans_va_bank`, `midtrans_transaction_id`, `midtrans_expiry`, `tagihan_listrik`, `tagihan_wifi`, `tagihan_air`, `tagihan_sampah`) VALUES
(1, 7, 1, 1.0, 20000.00, 'Belum Bayar', 'SIMKOS-1-1781093038', '55418982881075280706174', 'bca', '4279667c-008a-4734-ab81-54d7f5b1cf1f', '2026-06-11 19:03:58', 1000.00, 1000.00, 1000.00, 1000.00),
(22, 10, 3, 0.5, 25000.00, 'Belum Bayar', 'SIMKOS-22-1781092733', '55418675608666882594545', 'bca', 'fbfec79f-02e9-4d2c-a694-4d1d203c105d', '2026-06-11 18:58:53', 0.00, 8333.33, 8333.33, 8333.33),
(23, 10, 1, 1.0, 37500.00, 'Belum Bayar', 'SIMKOS-23-1781025962', '55418674412241676448265', 'bca', 'd2970ec1-6f61-431f-a3d3-18c5ee3cbedf', '2026-06-11 00:26:03', 12500.00, 8333.33, 8333.33, 8333.33),
(24, 10, 4, 1.0, 37500.00, 'Belum Bayar', 'SIMKOS-24-1781093036', '55418353003493312461356', 'bca', '32e09a9c-efe0-42ec-8935-cd7c8cf771b3', '2026-06-11 19:03:57', 12500.00, 8333.33, 8333.33, 8333.33),
(25, 11, 3, 0.5, 25000.00, 'Belum Bayar', 'SIMKOS-25-1781024986', '55418062223423807443537', 'bca', '61bcc864-7a5b-4981-9e76-c3de3c3283d8', '2026-06-11 00:09:46', 0.00, 8333.33, 8333.33, 8333.33),
(26, 11, 1, 1.0, 37500.00, 'Belum Bayar', 'SIMKOS-26-1781024060', '55418876158430433636818', 'bca', '83d41a91-094b-4384-91f6-f32dc39cc29f', '2026-06-10 23:54:20', 12500.00, 8333.33, 8333.33, 8333.33),
(27, 11, 4, 1.0, 37500.00, 'Belum Bayar', 'SIMKOS-27-1781093167', '5540074913087420', 'permata', 'd3783ea8-e27a-4d4d-93d0-fdc20cc5b8b3', '2026-06-11 19:06:07', 12500.00, 8333.33, 8333.33, 8333.33);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `penghuni_id` int(11) NOT NULL,
  `detail_tagihan_id` int(11) NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `tanggal_pembayaran` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Verifikasi',
  `keterangan` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `penghuni_id`, `detail_tagihan_id`, `jumlah_bayar`, `metode_pembayaran`, `tanggal_pembayaran`, `bukti_pembayaran`, `status`, `keterangan`, `updated_at`) VALUES
(1, 3, 22, 25000.00, 'Tunai', '2026-06-10 12:19:16', NULL, 'Ditolak', 'Dibatalkan admin', '2026-06-10 12:58:39'),
(2, 1, 23, 37500.00, 'Tunai', '2026-06-10 12:19:16', NULL, 'Ditolak', 'Dibatalkan admin', '2026-06-10 12:58:40'),
(3, 3, 25, 25000.00, 'Tunai', '2026-06-10 12:19:16', NULL, 'Ditolak', 'Dibatalkan admin', '2026-06-10 12:58:31'),
(4, 1, 1, 20000.00, 'Tunai', '2026-06-10 12:58:04', NULL, 'Ditolak', 'Dibatalkan admin', '2026-06-10 12:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `penghuni`
--

CREATE TABLE `penghuni` (
  `no` int(11) NOT NULL,
  `no_kamar` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `status_kamar` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  `status_pembayaran` enum('Lunas','Menunggak','Belum Lunas') DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penghuni`
--

INSERT INTO `penghuni` (`no`, `no_kamar`, `nik`, `nama_lengkap`, `alamat`, `no_hp`, `status_kamar`, `status_pembayaran`) VALUES
(1, 3, '3301123131', 'Irvan Maulana', 'Purbalingga', '6285641887122', 'Aktif', 'Belum Lunas'),
(3, 4, '333215', 'Deko Wirayuda', 'Majenang', '6285641887122', 'Tidak Aktif', 'Belum Lunas'),
(4, 2, '222222', 'Subhy', 'aaa', '085176751204', 'Aktif', 'Belum Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `tagihan_utilitas`
--

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

--
-- Dumping data for table `tagihan_utilitas`
--

INSERT INTO `tagihan_utilitas` (`id`, `bulan`, `tahun`, `biaya_listrik`, `biaya_air`, `biaya_wifi`, `biaya_sampah`, `total_penghuni`, `total_tagihan`, `total_bobot`, `tarif_per_bobot`, `tenggat_pembayaran`, `created_at`) VALUES
(7, 'May', '2026', 25000.00, 20000.00, 20000.00, 15000.00, 3, 80000.00, 2.50, 26666.67, '2026-06-30', '2026-06-05 18:41:10'),
(10, 'June', '2026', 25000.00, 25000.00, 25000.00, 25000.00, 3, 100000.00, 2.50, 33333.33, '2026-06-30', '2026-06-07 15:25:21'),
(11, 'July', '2026', 25000.00, 25000.00, 25000.00, 25000.00, 3, 100000.00, 2.50, 33333.33, '2026-06-15', '2026-06-07 15:39:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tagihan_id` (`tagihan_id`),
  ADD KEY `penghuni_id` (`penghuni_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penghuni_id` (`penghuni_id`),
  ADD KEY `detail_tagihan_id` (`detail_tagihan_id`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `tagihan_utilitas`
--
ALTER TABLE `tagihan_utilitas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tagihan_utilitas`
--
ALTER TABLE `tagihan_utilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD CONSTRAINT `detail_tagihan_ibfk_1` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan_utilitas` (`id`),
  ADD CONSTRAINT `detail_tagihan_ibfk_2` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`no`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`penghuni_id`) REFERENCES `penghuni` (`no`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`detail_tagihan_id`) REFERENCES `detail_tagihan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
