-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2026 at 02:08 PM
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
  `tagihan_listrik` decimal(12,2) DEFAULT 0.00,
  `tagihan_wifi` decimal(12,2) DEFAULT 0.00,
  `tagihan_air` decimal(12,2) DEFAULT 0.00,
  `tagihan_sampah` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_tagihan`
--

INSERT INTO `detail_tagihan` (`id`, `tagihan_id`, `penghuni_id`, `bobot`, `nominal_tagihan`, `status_bayar`, `tagihan_listrik`, `tagihan_wifi`, `tagihan_air`, `tagihan_sampah`) VALUES
(1, 7, 1, 1.0, 20000.00, 'Belum Bayar', 1000.00, 1000.00, 1000.00, 1000.00),
(22, 10, 3, 0.5, 25000.00, 'Belum Bayar', 0.00, 8333.33, 8333.33, 8333.33),
(23, 10, 1, 1.0, 37500.00, 'Belum Bayar', 12500.00, 8333.33, 8333.33, 8333.33),
(24, 10, 4, 1.0, 37500.00, 'Belum Bayar', 12500.00, 8333.33, 8333.33, 8333.33),
(25, 11, 3, 0.5, 25000.00, 'Belum Bayar', 0.00, 8333.33, 8333.33, 8333.33),
(26, 11, 1, 1.0, 37500.00, 'Belum Bayar', 12500.00, 8333.33, 8333.33, 8333.33),
(27, 11, 4, 1.0, 37500.00, 'Belum Bayar', 12500.00, 8333.33, 8333.33, 8333.33);

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
(1, 3, '3301123131', 'Irvan Maulana', 'Purbalingga', '0845646', 'Aktif', 'Belum Lunas'),
(3, 4, '333215', 'Deko Wirayuda', 'Majenang', '0548464', 'Tidak Aktif', 'Belum Lunas'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
