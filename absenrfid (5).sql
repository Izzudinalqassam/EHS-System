-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 08:33 AM
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
-- Database: `absenrfid`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `nokartu` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nokartu` varchar(20) NOT NULL,
  `NIK` varchar(12) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `departmen` varchar(50) NOT NULL,
  `no_wa` char(20) NOT NULL,
  `nopol` varchar(50) NOT NULL,
  `departemen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nokartu`, `NIK`, `nama`, `departmen`, `no_wa`, `nopol`, `departemen`) VALUES
(33434650, '05004278E6', '1233232', 'Mochamad Dwiyan Robiansyah ', 'IT', '089614225323', 'b 1928 fvz', ''),
(33434651, '0500250A25', '123456755', 'karyawan 1', 'HR & GA', '089614225323', 'b 197 fvg', ''),
(33434652, '3A00140AB2', '230600', 'magang 1', 'Magang', '089614225323', 'b 4444 fvz', ''),
(33434653, '8300654179', '123456786', 'karyawan 2', 'HR & GA', '089614225323', 'b 1928ww fvz', '');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` int(11) NOT NULL,
  `tanggal_input` date DEFAULT NULL,
  `petugas` varchar(30) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `jenis_kendaraan` enum('Mobil','Motor') DEFAULT NULL,
  `nopol` varchar(20) DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT '00:00:00',
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `tanggal_input`, `petugas`, `nama`, `jenis_kendaraan`, `nopol`, `jam_masuk`, `jam_keluar`, `status`) VALUES
(10, '2025-05-09', 'SUSANTO', 'Mochamad Dwiyan Robiansyah ', 'Motor', 'b 1928 fvz', '12:59:53', '13:01:16', NULL),
(11, '2025-05-09', 'M. SUBUR', 'Mochamad Dwiyan Robiansyah ', 'Mobil', 'b 1928 fvz', '13:45:40', '13:45:53', NULL),
(12, '2025-05-09', 'SUSANTO', 'Mochamad Dwiyan Robiansyah ', 'Mobil', 'b 1928 fvz', '13:51:08', '00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','security') NOT NULL,
  `nik` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `role`, `nik`) VALUES
(15, 'dwiyan_', 'dwiyan', 'admin', '1233232'),
(16, 'satpam', 'satpam', 'security', '123456755');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat`
--

CREATE TABLE `riwayat` (
  `id` int(11) NOT NULL,
  `nokartu` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `mode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `mode` int(11) NOT NULL
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
  `mode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `mode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `id` int(11) NOT NULL,
  `petugas` varchar(30) DEFAULT NULL,
  `nama_tamu` varchar(30) DEFAULT NULL,
  `nama_perusahaan` varchar(50) DEFAULT NULL,
  `jumlah_tamu` int(11) DEFAULT NULL,
  `keperluan` varchar(50) DEFAULT NULL,
  `ingin_bertemu` varchar(30) DEFAULT NULL,
  `jam_masuk_tamu` time DEFAULT NULL,
  `jam_keluar_tamu` time DEFAULT NULL,
  `tanggal_tamu` date DEFAULT NULL,
  `nopol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid`
--

CREATE TABLE `tmprfid` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid2`
--

CREATE TABLE `tmprfid2` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid3`
--

CREATE TABLE `tmprfid3` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmprfid4`
--

CREATE TABLE `tmprfid4` (
  `nokartu` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_pendaftaran`
--

CREATE TABLE `tmp_pendaftaran` (
  `id` int(11) NOT NULL,
  `nokartu` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tmp_pendaftaran`
--

INSERT INTO `tmp_pendaftaran` (`id`, `nokartu`, `created_at`) VALUES
(299, '8300653F1E', '2025-05-13 13:42:44');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=792;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33434654;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `riwayat`
--
ALTER TABLE `riwayat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1885;

--
-- AUTO_INCREMENT for table `tamu`
--
ALTER TABLE `tamu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tmp_pendaftaran`
--
ALTER TABLE `tmp_pendaftaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

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
