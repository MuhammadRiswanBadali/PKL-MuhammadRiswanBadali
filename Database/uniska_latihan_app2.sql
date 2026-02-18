-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 03:24 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uniska_latihan_app2`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_dosen`
--

CREATE TABLE `data_dosen` (
  `nidn` char(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `no_telepon` char(12) NOT NULL,
  `jabatan` varchar(20) NOT NULL,
  `pangkat` varchar(20) NOT NULL,
  `fakultas` varchar(40) NOT NULL,
  `program_studi` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_dosen`
--

INSERT INTO `data_dosen` (`nidn`, `nama`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `jabatan`, `pangkat`, `fakultas`, `program_studi`) VALUES
('2011128393', 'Budi Gunawan', 'Banjarbaru', '2025-05-04', 'Telaga Langsat   ', '085932514233', 'Kajur', 'Pembina Utama Muda (', 'FAPERTA', 'Peternakan');

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `id` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `lokasi_gudang` varchar(200) NOT NULL,
  `luas_gudang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gudang`
--

INSERT INTO `gudang` (`id`, `nama_gudang`, `lokasi_gudang`, `luas_gudang`) VALUES
(1112, 'Sejahtera 1', 'Martapura Barat', 100);

-- --------------------------------------------------------

--
-- Table structure for table `karyawan1`
--

CREATE TABLE `karyawan1` (
  `nik` char(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `no_telepon` char(12) NOT NULL,
  `jabatan` varchar(15) NOT NULL,
  `status_id` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan1`
--

INSERT INTO `karyawan1` (`nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telepon`, `jabatan`, `status_id`) VALUES
('2090190117', 'Riswan', 'Martapura', '2009-05-08', 'Martapura ', '2090190117', 'Leader', 'Kontrak'),
('2206071100', 'Ahmad Syairozi Mubarak', 'Kandangan', '2003-11-16', 'Ida Manggala   ', '2206071100', 'Supervisor', 'Kontrak');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan_gudang`
--

CREATE TABLE `karyawan_gudang` (
  `id` int(11) NOT NULL,
  `nik` varchar(10) NOT NULL,
  `id_gudang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan_gudang`
--

INSERT INTO `karyawan_gudang` (`id`, `nik`, `id_gudang`) VALUES
(1, '2090190117', 1112);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_admin`, `nama`, `username`, `password`) VALUES
(3, 'riswan', 'badali', '73b51c920493709f22f9380ba5b9744c');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan1`
--
ALTER TABLE `karyawan1`
  ADD PRIMARY KEY (`nik`);

--
-- Indexes for table `karyawan_gudang`
--
ALTER TABLE `karyawan_gudang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1113;

--
-- AUTO_INCREMENT for table `karyawan_gudang`
--
ALTER TABLE `karyawan_gudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
