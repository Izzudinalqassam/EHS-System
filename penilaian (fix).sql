-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 10:00 AM
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
-- Database: `penilaian`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_guru`
--

CREATE TABLE `data_guru` (
  `id` int(11) NOT NULL,
  `nip` char(15) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `no_telfon` char(15) NOT NULL,
  `email` varchar(20) NOT NULL,
  `mapel_guru` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_guru`
--

INSERT INTO `data_guru` (`id`, `nip`, `nama`, `no_telfon`, `email`, `mapel_guru`) VALUES
(24, '1234567', 'GILANG RAMADHAN DEWANTORO', '098765432', 'gilangrd38@gmail.com', 'tahfidz'),
(25, '123', 'Mochamad Dwiyan Robiansyah ', '09876453', 'xpremiumisme@gmail.c', 'pjok');

-- --------------------------------------------------------

--
-- Table structure for table `data_siswa`
--

CREATE TABLE `data_siswa` (
  `nisn` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `kelas` varchar(11) NOT NULL,
  `jk` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_siswa`
--

INSERT INTO `data_siswa` (`nisn`, `nama`, `kelas`, `jk`) VALUES
(1121212, 'Bayu Kristanto', '8A', 'Laki-laki');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL,
  `role` enum('guru','siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `role`) VALUES
(32323, 'dwiyan', 'dwiyan', 'guru'),
(32324, '1121212', '1121212', 'siswa');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `nisn` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `surah` varchar(15) NOT NULL,
  `ayat` int(30) NOT NULL,
  `nilai` varchar(30) NOT NULL,
  `guru_penguji` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`nisn`, `nama`, `kelas`, `tanggal`, `surah`, `ayat`, `nilai`, `guru_penguji`) VALUES
(1121212, 'Bayu Kristanto', '8A', '2025-04-22', 'Al-Fatihah', 23, 'C (70-79)', '');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat`
--

CREATE TABLE `riwayat` (
  `nisn` int(11) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `Tanggal` date NOT NULL,
  `surah` varchar(15) NOT NULL,
  `nilai` varchar(30) NOT NULL,
  `guru_penguji` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_guru`
--
ALTER TABLE `data_guru`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_siswa`
--
ALTER TABLE `data_siswa`
  ADD PRIMARY KEY (`nisn`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`nisn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_guru`
--
ALTER TABLE `data_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32325;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
