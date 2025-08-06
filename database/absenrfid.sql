-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Aug 05, 2025 at 02:30 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absenrfid`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int NOT NULL,
  `nokartu` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `nokartu`, `tanggal`, `jam_masuk`, `jam_pulang`, `status`, `last_update`) VALUES
(792, '05004278E6', '2025-07-26', '23:31:47', '23:33:31', 'OUT', '2025-07-26 16:33:31'),
(793, '8300653F1E', '2025-07-27', '00:21:04', '10:32:48', 'OUT', '2025-07-28 03:32:48'),
(794, '8300653F1E', '2025-07-28', '12:15:27', '12:15:33', 'OUT', '2025-07-28 05:15:33'),
(795, '8300653F1E', '2025-07-29', '13:59:11', '09:29:28', 'OUT', '2025-08-05 02:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int NOT NULL,
  `nokartu` varchar(20) NOT NULL,
  `NIK` varchar(12) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `departmen` varchar(50) NOT NULL,
  `no_wa` char(20) NOT NULL,
  `nopol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nokartu`, `NIK`, `nama`, `departmen`, `no_wa`, `nopol`) VALUES
(33434650, '05004278E6', '1233232', 'Mochamad Dwiyan Robiansyah ', 'IT', '089614225323', 'b 1928 fvz'),
(33434651, '0500250A25', '123456755', 'karyawan 1', 'HR & GA', '089614225323', 'b 197 fvg'),
(33434652, '3A00140AB2', '230600', 'magang 1', 'Magang', '089614225323', 'b 4444 fvz'),
(33434653, '8300654179', '123456786', 'karyawan 2', 'HR & GA', '089614225323', 'b 1928ww fvz'),
(33434655, '8300653F1E', '321', 'alqa', 'IT', '085693268094', 'B 2567 RFQ');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` int NOT NULL,
  `tanggal_input` date DEFAULT NULL,
  `petugas` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_kendaraan` enum('Mobil','Motor') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nopol` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT '00:00:00',
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `tanggal_input`, `petugas`, `nama`, `jenis_kendaraan`, `nopol`, `jam_masuk`, `jam_keluar`, `status`) VALUES
(13, '2025-07-28', 'M. SUBUR', 'alqa', 'Mobil', 'B 2567 RFQ', '11:28:19', '11:31:15', NULL),
(14, '2025-07-28', 'M. SUBUR', 'alqa', 'Motor', 'B 2567 RFQ', '11:31:24', '11:40:05', NULL),
(15, '2025-07-28', 'M. SUBUR', 'karyawan 1', 'Mobil', 'b 197 fvg', '11:31:47', '11:40:03', NULL),
(16, '2025-07-28', 'M. SUBUR', 'karyawan 2', 'Mobil', 'b 1928ww fvz', '11:35:28', '11:36:16', NULL),
(17, '2025-07-28', 'M. SUBUR', 'magang 1', 'Mobil', 'b 4444 fvz', '11:37:04', '11:40:01', NULL),
(18, '2025-07-28', 'SUSANTO', 'alqa', 'Mobil', 'B 2567 RFQ', '11:40:27', '11:44:14', NULL),
(19, '2025-07-28', 'M. SUBUR', 'alqa', 'Mobil', 'B 2567 RFQ', '11:44:25', '11:44:30', NULL),
(20, '2025-08-05', 'SUSANTO', 'alqa', 'Mobil', 'B 2567 RFQ', '08:31:39', '08:31:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','security') COLLATE utf8mb4_general_ci NOT NULL,
  `nik` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `role`, `nik`) VALUES
(15, 'dwiyan_', 'dwiyan', 'admin', '1233232'),
(16, 'satpam', 'satpam', 'security', '123456755'),
(17, 'alqa', 'alqa', 'admin', '321');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat`
--

CREATE TABLE `riwayat` (
  `id` int NOT NULL,
  `nokartu` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat`
--

INSERT INTO `riwayat` (`id`, `nokartu`, `tanggal`, `jam_masuk`, `jam_pulang`) VALUES
(1885, '05004278E6', '2025-07-26', '23:31:47', '23:33:31'),
(1886, '8300653F1E', '2025-07-27', '00:21:04', '10:32:48'),
(1887, '8300653F1E', '2025-07-28', '12:15:27', '12:15:33'),
(1888, '8300653F1E', '2025-07-29', '13:59:11', '09:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `mode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`mode`) VALUES
(2);

-- --------------------------------------------------------

--
-- Table structure for table `status2`
--

CREATE TABLE `status2` (
  `mode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status2`
--

INSERT INTO `status2` (`mode`) VALUES
(2);

-- --------------------------------------------------------

--
-- Table structure for table `status3`
--

CREATE TABLE `status3` (
  `mode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status3`
--

INSERT INTO `status3` (`mode`) VALUES
(2);

-- --------------------------------------------------------

--
-- Table structure for table `status4`
--

CREATE TABLE `status4` (
  `mode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status4`
--

INSERT INTO `status4` (`mode`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `tamu`
--

CREATE TABLE `tamu` (
  `id` int NOT NULL,
  `petugas` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_tamu` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_perusahaan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah_tamu` int DEFAULT NULL,
  `keperluan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ingin_bertemu` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_masuk_tamu` time DEFAULT NULL,
  `jam_keluar_tamu` time DEFAULT NULL,
  `tanggal_tamu` date DEFAULT NULL,
  `nopol` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tamu`
--

INSERT INTO `tamu` (`id`, `petugas`, `nama_tamu`, `nama_perusahaan`, `jumlah_tamu`, `keperluan`, `ingin_bertemu`, `jam_masuk_tamu`, `jam_keluar_tamu`, `tanggal_tamu`, `nopol`) VALUES
(31, 'ADE RESA', 'TEST', 'TEST', 37, 'meeting', 'magang 1', '23:39:21', '23:39:32', '2025-07-26', 'ETEW'),
(32, 'SUSANTO', 'test', 'test', 15, 'meeting', 'alqa', '00:21:35', '11:52:31', '2025-07-27', 'B 2567 RFQ'),
(33, 'M. SUBUR', 'alqa', 'nodeflux', 5, 'meeting', 'alqa', '11:45:05', '11:52:37', '2025-07-28', 'RFK'),
(34, 'M. SUBUR', 'beta', 'nodeflux', 5, 'meeting', 'alqa', '11:53:07', '12:06:37', '2025-07-28', 'RFK'),
(35, 'M. SUBUR', 'alkka', 'nodeflux', 5, 'meeting', 'karyawan 1', '13:59:34', '08:35:31', '2025-07-29', 'B RTT');

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid`
--

CREATE TABLE `tmprfid` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid2`
--

CREATE TABLE `tmprfid2` (
  `nokartu` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid3`
--

CREATE TABLE `tmprfid3` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid4`
--

CREATE TABLE `tmprfid4` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_pendaftaran`
--

CREATE TABLE `tmp_pendaftaran` (
  `id` int NOT NULL,
  `nokartu` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nokartu` (`nokartu`),
  ADD KEY `nokartu_2` (`nokartu`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nokartu` (`nokartu`),
  ADD KEY `nokartu_2` (`nokartu`),
  ADD KEY `nokartu_3` (`nokartu`),
  ADD KEY `nokartu_4` (`nokartu`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riwayat`
--
ALTER TABLE `riwayat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nokartu` (`nokartu`),
  ADD KEY `nokartu_2` (`nokartu`),
  ADD KEY `nokartu_3` (`nokartu`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`mode`);

--
-- Indexes for table `status2`
--
ALTER TABLE `status2`
  ADD PRIMARY KEY (`mode`);

--
-- Indexes for table `tamu`
--
ALTER TABLE `tamu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tmprfid`
--
ALTER TABLE `tmprfid`
  ADD PRIMARY KEY (`nokartu`);

--
-- Indexes for table `tmprfid2`
--
ALTER TABLE `tmprfid2`
  ADD PRIMARY KEY (`nokartu`);

--
-- Indexes for table `tmp_pendaftaran`
--
ALTER TABLE `tmp_pendaftaran`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=796;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33434656;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `riwayat`
--
ALTER TABLE `riwayat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1889;

--
-- AUTO_INCREMENT for table `tamu`
--
ALTER TABLE `tamu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tmp_pendaftaran`
--
ALTER TABLE `tmp_pendaftaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_karyawan` FOREIGN KEY (`nokartu`) REFERENCES `karyawan` (`nokartu`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
