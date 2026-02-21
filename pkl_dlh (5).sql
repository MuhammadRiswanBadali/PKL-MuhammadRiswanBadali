-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 26, 2025 at 03:18 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pkl_dlh`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id_berita` int NOT NULL,
  `judul_berita` varchar(200) NOT NULL,
  `isi_berita` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal_posting` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id_berita`, `judul_berita`, `isi_berita`, `gambar`, `tanggal_posting`, `id_user`) VALUES
(2, 'Apel Siaga Pengendalian Kebakaran Lahan dan Rapat Koordinasi Kebakaran Hutan dan Lahan', 'Banjarbaru – Menteri Lingkungan Hidup sekaligus Kepala Badan Pengendalian Lingkungan Hidup Republik Indonesia, Hanif Faisol Nurafiq, memimpin langsung Apel Siaga Pengendalian Kebakaran Lahan dan Hutan (Karhutla) yang diselenggarakan pada hari Kamis, 7 Agustus 2025, bertempat di Lapangan Bola Lanud Syamsudin Noor, Banjarbaru, Kalimantan Selatan. Kegiatan ini dihadiri oleh berbagai unsur penting, termasuk TNI/Polri, Badan Penanggulangan Bencana Daerah (BPBD), Manggala Agni, perwakilan instansi pemerintah daerah, serta relawan lingkungan.\r\nApel siaga ini dilaksanakan sebagai bentuk kesiapsiagaan nasional dalam menghadapi ancaman kebakaran hutan dan lahan, khususnya di wilayah Kalimantan Selatan yang menjadi salah satu daerah rawan kebakaran akibat musim kemarau berkepanjangan. Dalam kesempatan tersebut, Menteri Hanif Faisol Nurafiq menekankan pentingnya kerja sama dan koordinasi lintas sektor, baik dari pemerintah pusat, daerah, hingga masyarakat dalam mencegah dan mengendalikan karhutla.\r\n“Seluruh pemangku kepentingan (stakeholder) harus bersatu dan bekerja bersama secara sinergis dalam menghadapi ancaman kebakaran hutan dan lahan. Pemerintah pusat akan terus memberikan dukungan, baik secara kebijakan, operasional, maupun logistik, untuk memperkuat upaya pencegahan dan pengendalian karhutla di Kalimantan Selatan dan wilayah lain yang rawan,” ujar Menteri Hanif dalam arahannya.\r\nSetelah apel siaga, kegiatan dilanjutkan dengan Rapat Koordinasi Kebakaran Hutan dan Lahan (Rakor Karhutla) yang diselenggarakan di Hotel Novotel Banjarbaru. Rapat tersebut dihadiri oleh para pemangku kebijakan, termasuk perwakilan dari Kementerian dan Lembaga terkait, serta seluruh kepala daerah di lingkungan Provinsi Kalimantan Selatan.\r\nDalam forum Rakor tersebut, Gubernur Kalimantan Selatan, H. Muhidin, menyampaikan arahan kepada seluruh Kepala Daerah, baik Bupati maupun Wali Kota se-Kalimantan Selatan, agar segera mengambil langkah-langkah strategis di wilayah masing-masing. Gubernur menegaskan bahwa upaya penanganan karhutla harus dilakukan secara komprehensif, tidak hanya fokus pada aspek penanggulangan saat terjadi kebakaran, tetapi juga meliputi pencegahan dini dan pemulihan pascakebakaran.\r\n“Saya meminta seluruh kepala daerah untuk meningkatkan kewaspadaan serta menyiapkan rencana aksi yang terukur dan terstruktur. Pencegahan harus menjadi prioritas utama, tetapi saat kejadian terjadi, penanggulangan harus cepat dan terkoordinasi. Selain itu, upaya pemulihan lingkungan pascakebakaran juga harus diperhatikan agar ekosistem yang terdampak dapat segera pulih,” ujar Gubernur H. Muhidin.\r\nMelalui kegiatan ini, diharapkan koordinasi antara pemerintah pusat dan daerah dalam penanganan karhutla semakin kuat, serta mendorong peningkatan kesadaran dan kesiapsiagaan seluruh elemen masyarakat. Pemerintah juga menegaskan komitmennya untuk terus memperkuat kebijakan dan aksi nyata dalam melindungi lingkungan hidup dari ancaman kebakaran hutan dan lahan.', '1762755814_bbbb.jpeg', '2025-11-10 06:23:34', 3),
(3, 'Apel Siaga Pengendalian Kebakaran Lahan dan Rapat Koordinasi Kebakaran Hutan dan Lahan', 'Banjarbaru – Menteri Lingkungan Hidup sekaligus Kepala Badan Pengendalian Lingkungan Hidup Republik Indonesia, Hanif Faisol Nurafiq, memimpin langsung Apel Siaga Pengendalian Kebakaran Lahan dan Hutan (Karhutla) yang diselenggarakan pada hari Kamis, 7 Agustus 2025, bertempat di Lapangan Bola Lanud Syamsudin Noor, Banjarbaru, Kalimantan Selatan. Kegiatan ini dihadiri oleh berbagai unsur penting, termasuk TNI/Polri, Badan Penanggulangan Bencana Daerah (BPBD), Manggala Agni, perwakilan instansi pemerintah daerah, serta relawan lingkungan.\r\nApel siaga ini dilaksanakan sebagai bentuk kesiapsiagaan nasional dalam menghadapi ancaman kebakaran hutan dan lahan, khususnya di wilayah Kalimantan Selatan yang menjadi salah satu daerah rawan kebakaran akibat musim kemarau berkepanjangan. Dalam kesempatan tersebut, Menteri Hanif Faisol Nurafiq menekankan pentingnya kerja sama dan koordinasi lintas sektor, baik dari pemerintah pusat, daerah, hingga masyarakat dalam mencegah dan mengendalikan karhutla.\r\n“Seluruh pemangku kepentingan (stakeholder) harus bersatu dan bekerja bersama secara sinergis dalam menghadapi ancaman kebakaran hutan dan lahan. Pemerintah pusat akan terus memberikan dukungan, baik secara kebijakan, operasional, maupun logistik, untuk memperkuat upaya pencegahan dan pengendalian karhutla di Kalimantan Selatan dan wilayah lain yang rawan,” ujar Menteri Hanif dalam arahannya.\r\nSetelah apel siaga, kegiatan dilanjutkan dengan Rapat Koordinasi Kebakaran Hutan dan Lahan (Rakor Karhutla) yang diselenggarakan di Hotel Novotel Banjarbaru. Rapat tersebut dihadiri oleh para pemangku kebijakan, termasuk perwakilan dari Kementerian dan Lembaga terkait, serta seluruh kepala daerah di lingkungan Provinsi Kalimantan Selatan.\r\nDalam forum Rakor tersebut, Gubernur Kalimantan Selatan, H. Muhidin, menyampaikan arahan kepada seluruh Kepala Daerah, baik Bupati maupun Wali Kota se-Kalimantan Selatan, agar segera mengambil langkah-langkah strategis di wilayah masing-masing. Gubernur menegaskan bahwa upaya penanganan karhutla harus dilakukan secara komprehensif, tidak hanya fokus pada aspek penanggulangan saat terjadi kebakaran, tetapi juga meliputi pencegahan dini dan pemulihan pascakebakaran.\r\n“Saya meminta seluruh kepala daerah untuk meningkatkan kewaspadaan serta menyiapkan rencana aksi yang terukur dan terstruktur. Pencegahan harus menjadi prioritas utama, tetapi saat kejadian terjadi, penanggulangan harus cepat dan terkoordinasi. Selain itu, upaya pemulihan lingkungan pascakebakaran juga harus diperhatikan agar ekosistem yang terdampak dapat segera pulih,” ujar Gubernur H. Muhidin.\r\nMelalui kegiatan ini, diharapkan koordinasi antara pemerintah pusat dan daerah dalam penanganan karhutla semakin kuat, serta mendorong peningkatan kesadaran dan kesiapsiagaan seluruh elemen masyarakat. Pemerintah juga menegaskan komitmennya untuk terus memperkuat kebijakan dan aksi nyata dalam melindungi lingkungan hidup dari ancaman kebakaran hutan dan lahan.', '1762744704_aaaaaaa.jpeg', '2025-11-10 03:18:24', 3),
(4, 'Apel Siaga Pengendalian Kebakaran Lahan dan Rapat Koordinasi Kebakaran Hutan dan Lahan', 'Banjarbaru – Menteri Lingkungan Hidup sekaligus Kepala Badan Pengendalian Lingkungan Hidup Republik Indonesia, Hanif Faisol Nurafiq, memimpin langsung Apel Siaga Pengendalian Kebakaran Lahan dan Hutan (Karhutla) yang diselenggarakan pada hari Kamis, 7 Agustus 2025, bertempat di Lapangan Bola Lanud Syamsudin Noor, Banjarbaru, Kalimantan Selatan. Kegiatan ini dihadiri oleh berbagai unsur penting, termasuk TNI/Polri, Badan Penanggulangan Bencana Daerah (BPBD), Manggala Agni, perwakilan instansi pemerintah daerah, serta relawan lingkungan.\r\nApel siaga ini dilaksanakan sebagai bentuk kesiapsiagaan nasional dalam menghadapi ancaman kebakaran hutan dan lahan, khususnya di wilayah Kalimantan Selatan yang menjadi salah satu daerah rawan kebakaran akibat musim kemarau berkepanjangan. Dalam kesempatan tersebut, Menteri Hanif Faisol Nurafiq menekankan pentingnya kerja sama dan koordinasi lintas sektor, baik dari pemerintah pusat, daerah, hingga masyarakat dalam mencegah dan mengendalikan karhutla.\r\n“Seluruh pemangku kepentingan (stakeholder) harus bersatu dan bekerja bersama secara sinergis dalam menghadapi ancaman kebakaran hutan dan lahan. Pemerintah pusat akan terus memberikan dukungan, baik secara kebijakan, operasional, maupun logistik, untuk memperkuat upaya pencegahan dan pengendalian karhutla di Kalimantan Selatan dan wilayah lain yang rawan,” ujar Menteri Hanif dalam arahannya.\r\nSetelah apel siaga, kegiatan dilanjutkan dengan Rapat Koordinasi Kebakaran Hutan dan Lahan (Rakor Karhutla) yang diselenggarakan di Hotel Novotel Banjarbaru. Rapat tersebut dihadiri oleh para pemangku kebijakan, termasuk perwakilan dari Kementerian dan Lembaga terkait, serta seluruh kepala daerah di lingkungan Provinsi Kalimantan Selatan.\r\nDalam forum Rakor tersebut, Gubernur Kalimantan Selatan, H. Muhidin, menyampaikan arahan kepada seluruh Kepala Daerah, baik Bupati maupun Wali Kota se-Kalimantan Selatan, agar segera mengambil langkah-langkah strategis di wilayah masing-masing. Gubernur menegaskan bahwa upaya penanganan karhutla harus dilakukan secara komprehensif, tidak hanya fokus pada aspek penanggulangan saat terjadi kebakaran, tetapi juga meliputi pencegahan dini dan pemulihan pascakebakaran.\r\n“Saya meminta seluruh kepala daerah untuk meningkatkan kewaspadaan serta menyiapkan rencana aksi yang terukur dan terstruktur. Pencegahan harus menjadi prioritas utama, tetapi saat kejadian terjadi, penanggulangan harus cepat dan terkoordinasi. Selain itu, upaya pemulihan lingkungan pascakebakaran juga harus diperhatikan agar ekosistem yang terdampak dapat segera pulih,” ujar Gubernur H. Muhidin.\r\nMelalui kegiatan ini, diharapkan koordinasi antara pemerintah pusat dan daerah dalam penanganan karhutla semakin kuat, serta mendorong peningkatan kesadaran dan kesiapsiagaan seluruh elemen masyarakat. Pemerintah juga menegaskan komitmennya untuk terus memperkuat kebijakan dan aksi nyata dalam melindungi lingkungan hidup dari ancaman kebakaran hutan dan lahan.', '1762744718_bbbb.jpeg', '2025-11-10 03:18:38', 3),
(5, 'Aksi World Cleanup Day (WCD) 2025', 'Banjarbaru – Menteri Lingkungan Hidup sekaligus Kepala Badan Pengendalian Lingkungan Hidup Republik Indonesia, Hanif Faisol Nurafiq, memimpin langsung Apel Siaga Pengendalian Kebakaran Lahan dan Hutan (Karhutla) yang diselenggarakan pada hari Kamis, 7 Agustus 2025, bertempat di Lapangan Bola Lanud Syamsudin Noor, Banjarbaru, Kalimantan Selatan. Kegiatan ini dihadiri oleh berbagai unsur penting, termasuk TNI/Polri, Badan Penanggulangan Bencana Daerah (BPBD), Manggala Agni, perwakilan instansi pemerintah daerah, serta relawan lingkungan.\r\nApel siaga ini dilaksanakan sebagai bentuk kesiapsiagaan nasional dalam menghadapi ancaman kebakaran hutan dan lahan, khususnya di wilayah Kalimantan Selatan yang menjadi salah satu daerah rawan kebakaran akibat musim kemarau berkepanjangan. Dalam kesempatan tersebut, Menteri Hanif Faisol Nurafiq menekankan pentingnya kerja sama dan koordinasi lintas sektor, baik dari pemerintah pusat, daerah, hingga masyarakat dalam mencegah dan mengendalikan karhutla.\r\n“Seluruh pemangku kepentingan (stakeholder) harus bersatu dan bekerja bersama secara sinergis dalam menghadapi ancaman kebakaran hutan dan lahan. Pemerintah pusat akan terus memberikan dukungan, baik secara kebijakan, operasional, maupun logistik, untuk memperkuat upaya pencegahan dan pengendalian karhutla di Kalimantan Selatan dan wilayah lain yang rawan,” ujar Menteri Hanif dalam arahannya.\r\nSetelah apel siaga, kegiatan dilanjutkan dengan Rapat Koordinasi Kebakaran Hutan dan Lahan (Rakor Karhutla) yang diselenggarakan di Hotel Novotel Banjarbaru. Rapat tersebut dihadiri oleh para pemangku kebijakan, termasuk perwakilan dari Kementerian dan Lembaga terkait, serta seluruh kepala daerah di lingkungan Provinsi Kalimantan Selatan.\r\nDalam forum Rakor tersebut, Gubernur Kalimantan Selatan, H. Muhidin, menyampaikan arahan kepada seluruh Kepala Daerah, baik Bupati maupun Wali Kota se-Kalimantan Selatan, agar segera mengambil langkah-langkah strategis di wilayah masing-masing. Gubernur menegaskan bahwa upaya penanganan karhutla harus dilakukan secara komprehensif, tidak hanya fokus pada aspek penanggulangan saat terjadi kebakaran, tetapi juga meliputi pencegahan dini dan pemulihan pascakebakaran.\r\n“Saya meminta seluruh kepala daerah untuk meningkatkan kewaspadaan serta menyiapkan rencana aksi yang terukur dan terstruktur. Pencegahan harus menjadi prioritas utama, tetapi saat kejadian terjadi, penanggulangan harus cepat dan terkoordinasi. Selain itu, upaya pemulihan lingkungan pascakebakaran juga harus diperhatikan agar ekosistem yang terdampak dapat segera pulih,” ujar Gubernur H. Muhidin.\r\nMelalui kegiatan ini, diharapkan koordinasi antara pemerintah pusat dan daerah dalam penanganan karhutla semakin kuat, serta mendorong peningkatan kesadaran dan kesiapsiagaan seluruh elemen masyarakat. Pemerintah juga menegaskan komitmennya untuk terus memperkuat kebijakan dan aksi nyata dalam melindungi lingkungan hidup dari ancaman kebakaran hutan dan lahan.', '1762755799_aaaaaaa.jpeg', '2025-11-10 06:23:19', 3);

-- --------------------------------------------------------

--
-- Table structure for table `edukasi`
--

CREATE TABLE `edukasi` (
  `id_edukasi` int NOT NULL,
  `judul_edukasi` varchar(200) NOT NULL,
  `isi_edukasi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal_posting` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_user` int NOT NULL,
  `tipe_konten` enum('file','video') NOT NULL DEFAULT 'file',
  `file_path` varchar(255) DEFAULT NULL,
  `link_video` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `edukasi`
--

INSERT INTO `edukasi` (`id_edukasi`, `judul_edukasi`, `isi_edukasi`, `gambar`, `tanggal_posting`, `id_user`, `tipe_konten`, `file_path`, `link_video`) VALUES
(1, 'Race Highlights | 2025 Sao Paulo Grand Prix', 'Lando Norris led the field away for a classic Interlagos race as the reigning champion Max Verstappen started from the pit lane', NULL, '2025-11-10 05:43:30', 3, 'video', '', 'https://youtu.be/MK83clSv6-k?si=viELCksCEO7mx52S'),
(2, 'CURHATAN SEORANG DOKTER', 'tes tes tes', '', '2025-11-10 06:19:03', 3, 'video', '', 'https://youtu.be/zyqpFFfMO_g?si=xW1FJRpTOosQnQ2d'),
(3, 'ARSENIK DI BALIK HILIRISASI NIKEL - Temuan jejak logam berat, di tubuh warga Teluk Weda', 'Di tengah gencarnya program hilirisasi nikel dan geliat pertumbuhan ekonomi di wilayah Teluk Weda, Halmahera Tengah, hasil uji riset laboratorium dari Nexus3 Foundation bersama Universitas Tadulako mengungkap hal sebaliknya. Peneliti mendapati temuan logam berat terkandung dalam sampel darah warga, pekerja tambang, hingga nelayan.\r\n\r\nTak hanya itu, konsentrasi logam berat arsenik juga ditemukan dari sampel ikan yang ditangkap di sekitar perairan Teluk Weda. Padahal, ikan menjadi salah satu sumber pangan utama bagi masyarakat pesisir di Weda.\r\n\r\nDi balik angka pertumbuhan ekonomi dan lonjakan ekspor nikel, ada kenyataan yang tak pernah tercatat dalam neraca perdagangan. Warga dan buruh tambang terdampak karena kualitas kesehatan yang kian menurun, hingga beragam permasalahan penyakit tak menular akibat akumulasi kadar logam berat yang mencemari air, tanah, dan udara.\r\n\r\nMelalui liputan mendalam di berbagai wilayah tambang nikel, khususnya Sulawesi Tenggara, Sulawesi Tengah, dan Maluku Utara, kami merekam temuan para peneliti tentang; seberapa besar dampak tambang nikel berpengaruh pada kesehatan warga di sekitarnya.', NULL, '2025-11-10 06:21:22', 3, 'video', '', 'https://youtu.be/mgnS-VBgNsc?si=GoqSBRk9OQ0Ijini'),
(10, 'MEKANISME ', 'sosialiasi penyelenggaraan walidata dan statistik', '1762760872_89.png', '2025-11-10 07:38:36', 3, 'file', '1762760316_1762757709_Metadata_dan_Standar_data_LH.pptx', ''),
(11, 'Cerita dari Ruang IGD', 'uji coba', '', '2025-11-10 07:49:59', 3, 'video', '', 'https://youtu.be/W595BkxQMMc?si=BTWox4h3NbST3M2j');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_pemantauan`
--

CREATE TABLE `hasil_pemantauan` (
  `id_hasil` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_pemantauan` int NOT NULL,
  `no2` decimal(10,2) DEFAULT NULL,
  `so2` decimal(10,2) DEFAULT NULL,
  `pm25` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hasil_pemantauan`
--

INSERT INTO `hasil_pemantauan` (`id_hasil`, `id_user`, `id_pemantauan`, `no2`, `so2`, `pm25`) VALUES
(3, 8, 1, 4.65, 3.29, 0.00),
(4, 8, 3, 7.05, 9.59, 0.00),
(5, 8, 5, 6.87, 8.52, 0.00),
(7, 8, 7, 9.60, 5.03, 0.00),
(9, 8, 11, 6.85, 3.75, 0.00),
(10, 8, 12, 16.25, 14.35, 0.00),
(11, 8, 13, 12.27, 8.16, 0.00),
(12, 8, 14, 7.02, 5.62, 0.00),
(13, 8, 15, 5.77, 3.65, 0.00),
(14, 8, 16, 9.52, 8.33, 0.00),
(17, 8, 19, 7.51, 6.75, 0.00),
(18, 8, 20, 4.76, 5.13, 0.00),
(19, 8, 21, 2.71, 3.65, 0.00),
(20, 8, 22, 8.82, 7.52, 0.00),
(21, 8, 23, 6.92, 6.69, 0.00),
(22, 8, 24, 7.85, 5.79, 0.00),
(23, 8, 25, 3.42, 4.71, 0.00),
(24, 8, 26, 11.97, 10.75, 0.00),
(25, 8, 27, 8.06, 7.32, 0.00),
(26, 8, 28, 3.78, 5.09, 0.00),
(27, 8, 29, 3.19, 4.67, 0.00),
(28, 8, 30, 13.22, 10.28, 0.00),
(29, 8, 31, 7.55, 8.10, 0.00),
(34, 8, 36, 4.17, 5.61, 0.00),
(36, 8, 38, 13.63, 10.21, 0.00),
(39, 8, 41, 3.94, 5.62, 0.00),
(40, 8, 42, 2.83, 3.16, 0.00),
(41, 8, 43, 7.13, 8.04, 0.00),
(42, 8, 44, 5.31, 6.88, 0.00),
(43, 8, 45, 3.16, 4.11, 0.00),
(65, 3, 65, 4.18, 5.45, 0.00),
(66, 3, 66, 2.82, 3.12, 0.00),
(67, 3, 67, 6.05, 8.91, 0.00),
(68, 3, 68, 5.35, 7.23, 0.00),
(69, 3, 69, 4.41, 5.01, 0.00),
(70, 3, 70, 2.73, 3.81, 0.00),
(71, 3, 71, 9.48, 10.34, 0.00),
(72, 3, 72, 7.58, 6.11, 0.00),
(73, 3, 73, 2.18, 4.12, 0.00),
(74, 3, 74, 1.84, 2.95, 0.00),
(75, 3, 75, 7.77, 8.05, 0.00),
(76, 3, 76, 4.56, 6.63, 0.00),
(77, 3, 77, 4.68, 5.91, 0.00),
(78, 3, 78, 2.04, 3.48, 0.00),
(79, 3, 79, 10.63, 8.73, 0.00),
(80, 3, 80, 7.24, 6.12, 0.00),
(81, 3, 81, 3.94, 5.62, 0.00),
(82, 3, 82, 2.09, 3.21, 0.00),
(83, 3, 83, 5.33, 6.04, 0.00),
(84, 3, 84, 6.05, 7.11, 0.00),
(85, 3, 85, 4.97, 5.19, 0.00),
(86, 3, 86, 3.72, 2.73, 0.00),
(87, 3, 87, 12.62, 6.16, 0.00),
(88, 3, 88, 5.29, 9.28, 0.00),
(89, 3, 89, 10.76, 4.42, 0.00),
(90, 3, 90, 11.17, 6.66, 0.00),
(91, 3, 91, 15.93, 8.24, 0.00),
(92, 3, 92, 7.00, 8.35, 0.00),
(93, 3, 93, 23.37, 4.26, 0.00),
(94, 3, 94, 9.97, 10.59, 0.00),
(95, 3, 95, 4.65, 2.47, 0.00),
(96, 3, 96, 8.16, 5.00, 0.00),
(97, 3, 97, 9.13, 6.25, 0.00),
(98, 3, 98, 10.37, 3.83, 0.00),
(99, 3, 99, 3.16, 4.52, 0.00),
(100, 3, 100, 6.04, 4.72, 0.00),
(101, 3, 101, 4.73, 4.11, 0.00),
(102, 3, 102, 4.71, 8.97, 0.00),
(103, 3, 103, 10.39, 7.41, 0.00),
(104, 3, 104, 9.08, 16.07, 0.00),
(105, 3, 105, 11.89, 5.20, 0.00),
(106, 3, 106, 9.03, 7.36, 0.00),
(107, 3, 107, 17.99, 6.26, 0.00),
(108, 3, 108, 8.64, 5.83, 0.00),
(109, 3, 109, 25.87, 7.34, 0.00),
(110, 3, 110, 11.32, 8.95, 0.00),
(111, 3, 111, 8.92, 4.02, 0.00),
(112, 3, 112, 11.54, 6.93, 0.00),
(113, 3, 113, 16.39, 5.08, 0.00),
(114, 3, 114, 14.15, 4.24, 0.00),
(115, 3, 115, 3.86, 4.34, 0.00),
(116, 3, 116, 7.14, 5.18, 0.00),
(117, 3, 117, 5.27, 5.75, 0.00),
(118, 3, 118, 6.23, 10.52, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `lokasi_pemantauan`
--

CREATE TABLE `lokasi_pemantauan` (
  `id_lokasi` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `kode_lokasi` varchar(50) NOT NULL,
  `nama_lokasi` varchar(100) DEFAULT NULL,
  `alamat_lokasi` text,
  `kabupaten_kota` varchar(100) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `peruntukan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lokasi_pemantauan`
--

INSERT INTO `lokasi_pemantauan` (`id_lokasi`, `id_user`, `kode_lokasi`, `nama_lokasi`, `alamat_lokasi`, `kabupaten_kota`, `provinsi`, `latitude`, `longitude`, `peruntukan`) VALUES
(2, 8, 'U1-KS-72-004', 'Perkantoran/Komersial', 'Kantor Walikota Jl. Panglima Batur No. 1 Banjarbaru', 'Kota Banjarbaru', 'Kalimantan Selatan', -3.439028, 114.831444, 'PERKANTORAN'),
(3, 8, 'U1-KS-72-002', 'Industri/Agro Industri', 'Lingkungan Industri Kecil Liang Anggang Jl. A. Yani Jurusan Pelaihari Liang Anggana', 'Kota Banjarbaru', 'Kalimantan Selatan', -3.455917, 114.711278, 'INDUSTRI'),
(4, 8, 'U1-KS-72-001', 'Transportasi', 'Panti Sosial Budi Mulia Jl. A. Yani km 27.5 Landasan Ulin', 'Kota Banjarbaru', 'Kalimantan Selatan', -3.446333, 114.768667, 'TRANSPORTASI'),
(6, 8, 'U1-KS-71-004', 'Perkantoran/Komersial', 'Dinas PUPR Kota Banjarmasin Kel. Pangeran Kec. Banjarmasin Utara Jl. Brigjen H. Hasan Basri', 'Kota Banjarmasin', 'Kalimantan Selatan', -3.295820, 114.589250, 'PERKANTORAN'),
(7, 8, 'U1-KS-71-003', 'Perumahan', 'Jl. Sultan Adam Komplek Manugai Kel. Sungai Jingah Kec. Banjarmasin Utara', 'Kota Banjarmasin', 'Kalimantan Selatan', -3.305160, 114.604650, 'PEMUKIMAN'),
(8, 8, 'U1-KS-71-002', 'Industri/Agro Industri', 'PT Pelindo III Jl. Barito Hilir Kel. Telaga Biru Kec. Banjarmasin Barat', 'Kota Banjarmasin', 'Kalimantan Selatan', -3.328620, 114.559650, 'INDUSTRI'),
(9, 8, 'U1-KS-71-001', 'Transportasi', 'Universitas Terbuka Banjarmasin Jl. Sultan Adam No. 128 Kel. Surgi Mufti Kec. Banjarmasin Utara', 'Kota Banjarmasin', 'Kalimantan Selatan', -3.300040, 114.605040, 'TRANSPORTASI'),
(11, 8, 'U1-KS-11-004', 'Perkantoran/Komersial', 'Kantor Dinas Lingkungan Hidup Kab. Balangan', 'Kabupaten Balangan', 'Kalimantan Selatan', -2.361139, 115.470806, 'PERKANTORAN'),
(12, 8, 'U1-KS-11-003', 'Perumahan', 'Perumahan Citra Permai SKB Paringin', 'Kabupaten Balangan', 'Kalimantan Selatan', -2.331805, 115.463666, 'PEMUKIMAN'),
(13, 8, 'U1-KS-11-002', 'Industri/Agro Industri', 'Simpang Paringin Over Pass', 'Kabupaten Balangan', 'Kalimantan Selatan', -2.296725, 115.475278, 'INDUSTRI'),
(14, 8, 'U1-KS-11-001', 'Transportasi', 'Depan Terminal Besar Paringin', 'Kabupaten Balangan', 'Kalimantan Selatan', -2.336167, 115.459667, 'TRANSPORTASI'),
(15, 8, 'U1-KS-10-004', 'Perkantoran/Komersial', 'Kantor Bupati Kab. Tanah Bumbu, Jl. Dharma Praja No. 03 Gunung Tinggi Kec. Batulicin Kab. Tanah Bumbu', 'Kabupaten Tanah Bumbu', 'Kalimantan Selatan', -3.483361, 115.948500, 'PERKANTORAN'),
(16, 8, 'U1-KS-10-003', 'Perumahan', 'Perumahan Bumi Berujud, Desa Barokah Kec. Simpang Empat', 'Kabupaten Tanah Bumbu', 'Kalimantan Selatan', -3.409944, 115.980611, 'PEMUKIMAN'),
(17, 8, 'U1-KS-10-002', 'Industri/Agro Industri', 'Komp. Industri Jhonlin Agro Mandiri Desa Sungai Kecil. Kec. Simpang Empat', 'Kabupaten Tanah Bumbu', 'Kalimantan Selatan', -3.299833, 116.006361, 'INDUSTRI'),
(18, 8, 'U1-KS-10-001', 'Transportasi', 'Jl. Raya Batulicin, Kec. Batulicin Kab. Tanah Bumbu', 'Kabupaten Tanah Bumbu', 'Kalimantan Selatan', -3.451917, 116.001500, 'TRANSPORTASI'),
(19, 8, 'U1-KS-09-004', 'Perkantoran/Komersial', 'Kantor Sekertariat Daerah Kab Tabalong, Jl. P. Antasari No 1 Kel. Tanjung', 'Kabupaten Tabalong', 'Kalimantan Selatan', -2.165747, 115.381888, 'PERKANTORAN'),
(20, 8, 'U1-KS-09-003', 'Perumahan', 'Komp Swadarma LestariJl. Gelantik Kel. Mabuun', 'Kabupaten Tabalong', 'Kalimantan Selatan', -2.180075, 115.414402, 'PEMUKIMAN'),
(21, 8, 'U1-KS-09-002', 'Industri/Agro Industri', 'PT. Bumi Jaya Desa Kasiau, Kec. Murung Pudak', 'Kabupaten Tabalong', 'Kalimantan Selatan', -2.128327, 115.454452, 'INDUSTRI'),
(22, 8, 'U1-KS-09-001', 'Transportasi', 'Jl. Ir. PHM Noor PembataanKec. Murung Pudak', 'Kabupaten Tabalong', 'Kalimantan Selatan', -2.171150, 115.399577, 'TRANSPORTASI'),
(23, 8, 'U1-KS-08-004', 'Perkantoran/Komersial', 'Kantor Bupati Hulu Sungai Utara Jl. Ahmad Yani No. 12 Kec. Amuntai Tengah Kel. Murung Sari', 'Kabupaten Hulu Sungai Utara', 'Kalimantan Selatan', -2.419417, 115.254028, 'PERKANTORAN'),
(24, 8, 'U1-KS-08-003', 'Perumahan', 'Perumahan Dinas Pemerintah Daerah Kec. Amuntai Tengah Kel. Sungai Malang', 'Kabupaten Hulu Sungai Utara', 'Kalimantan Selatan', -2.415616, 115.248050, 'PEMUKIMAN'),
(25, 8, 'U1-KS-08-002', 'Industri/Agro Industri', 'PT Karias Tabing Kencana JI. Jermani Husein RT06/RW 03 Kec. Banjang Kel. Lok Bangkai', 'Kabupaten Hulu Sungai Utara', 'Kalimantan Selatan', -2.418383, 115.274817, 'INDUSTRI'),
(26, 8, 'U1-KS-08-001', 'Transportasi', 'Simpang Amuntai Lampu Merah Banua Lima Jl. Brigjen H. Hasan Baseri Kec. Amuntai Tengah Kel. Kota Raden Hilir', 'Kabupaten Hulu Sungai Utara', 'Kalimantan Selatan', -2.428361, 115.246778, 'TRANSPORTASI'),
(27, 8, 'U1-KS-07-004', 'Perkantoran/Komersial', 'Depan Kantor PU Barabai', 'Kabupaten Hulu Sungai Tengah', 'Kalimantan Selatan', -2.585550, 115.385510, 'PERKANTORAN'),
(28, 8, 'U1-KS-07-003', 'Perumahan', 'Komplek Bawan Permai Bukat Barabai', 'Kabupaten Hulu Sungai Tengah', 'Kalimantan Selatan', -2.591880, 115.367960, 'PEMUKIMAN'),
(29, 8, 'U1-KS-07-002', 'Industri/Agro Industri', 'Desa Telang Batang Alai Utara', 'Kabupaten Hulu Sungai Tengah', 'Kalimantan Selatan', -2.483410, 115.403950, 'INDUSTRI'),
(30, 8, 'U1-KS-07-001', 'Transportasi', 'Simpang 10 Jl. Murakata Barabai', 'Kabupaten Hulu Sungai Tengah', 'Kalimantan Selatan', -2.587550, 115.325310, 'TRANSPORTASI'),
(31, 8, 'U1-KS-06-004', 'Perkantoran/Komersial', 'Dinas Perdagangan (Jln. Anggrek No.65 Kandangan)', 'Kabupaten Hulu Sungai Selatan', 'Kalimantan Selatan', -2.780303, 115.269575, 'PERKANTORAN'),
(32, 8, 'U1-KS-06-003', 'Perumahan', 'Komplek Muara Banta RT. 01 RW 02', 'Kabupaten Hulu Sungai Selatan', 'Kalimantan Selatan', -2.789190, 115.281440, 'PEMUKIMAN'),
(33, 8, 'U1-KS-06-002', 'Industri/Agro Industri', 'Sentra Industri Gerabah,Jl. Hanyar, Desa Banyaran, Kec. Daha Selatan', 'Kabupaten Hulu Sungai Selatan', 'Kalimantan Selatan', -2.636890, 115.111866, 'INDUSTRI'),
(47, 3, 'U1-KS-72-003', 'Perumahan', 'Kawasan Perumahan Kehutanan Jl. Binawa', 'Kota Banjarbaru', 'Kalimantan Selatan', -3.458667, 114.842944, 'PEMUKIMAN');

-- --------------------------------------------------------

--
-- Table structure for table `pemantauan_udara`
--

CREATE TABLE `pemantauan_udara` (
  `id_pemantauan` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_lokasi` int NOT NULL,
  `level` varchar(50) DEFAULT NULL,
  `tanggal_pemantauan` date DEFAULT NULL,
  `periode_pemantauan` varchar(20) DEFAULT NULL,
  `durasi_pemantauan` varchar(20) DEFAULT NULL,
  `metode_pemantauan` varchar(100) DEFAULT NULL,
  `shu` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemantauan_udara`
--

INSERT INTO `pemantauan_udara` (`id_pemantauan`, `id_user`, `id_lokasi`, `level`, `tanggal_pemantauan`, `periode_pemantauan`, `durasi_pemantauan`, `metode_pemantauan`, `shu`) VALUES
(1, 8, 2, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(3, 8, 3, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(5, 8, 4, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(7, 8, 6, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(11, 8, 7, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(12, 8, 8, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(13, 8, 9, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(14, 8, 11, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(15, 8, 12, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(16, 8, 13, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(19, 8, 14, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(20, 8, 15, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(21, 8, 16, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(22, 8, 17, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(23, 8, 18, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(24, 8, 19, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(25, 8, 20, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(26, 8, 21, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(27, 8, 22, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(28, 8, 23, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(29, 8, 24, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(30, 8, 25, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(31, 8, 26, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(36, 8, 31, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(38, 8, 33, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(41, 8, 27, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(42, 8, 28, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(43, 8, 29, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(44, 8, 30, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(45, 8, 32, 'Pusat', '2024-09-03', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(65, 3, 2, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(66, 3, 47, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(67, 3, 3, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(68, 3, 4, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(69, 3, 11, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(70, 3, 12, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(71, 3, 13, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(72, 3, 14, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(73, 3, 15, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(74, 3, 16, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(75, 3, 17, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(76, 3, 18, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(77, 3, 23, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(78, 3, 24, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(79, 3, 25, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(80, 3, 26, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(81, 3, 27, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(82, 3, 28, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(83, 3, 29, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(84, 3, 30, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(85, 3, 31, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(86, 3, 32, 'Pusat', '2024-07-11', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(87, 3, 2, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(88, 3, 47, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(89, 3, 3, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(90, 3, 4, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(91, 3, 6, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(92, 3, 7, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(93, 3, 8, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(94, 3, 9, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(95, 3, 11, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(96, 3, 12, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(97, 3, 13, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(98, 3, 14, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(99, 3, 15, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(100, 3, 16, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(101, 3, 17, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(102, 3, 18, 'Pusat', '2023-07-04', '1', '14 Hari', 'Manual Passive', 'ADA SHU'),
(103, 3, 2, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(104, 3, 47, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(105, 3, 3, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(106, 3, 4, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(107, 3, 6, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(108, 3, 7, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(109, 3, 8, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(110, 3, 9, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(111, 3, 11, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(112, 3, 12, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(113, 3, 13, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(114, 3, 14, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(115, 3, 15, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(116, 3, 16, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(117, 3, 17, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU'),
(118, 3, 18, 'Pusat', '2023-09-12', '2', '14 Hari', 'Manual Passive', 'ADA SHU');

-- --------------------------------------------------------

--
-- Table structure for table `saran`
--

CREATE TABLE `saran` (
  `id_saran` int NOT NULL,
  `nama_pengirim` varchar(100) NOT NULL,
  `email_pengirim` varchar(100) DEFAULT NULL,
  `isi_saran` text NOT NULL,
  `tanggal_saran` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('baru','dibaca','ditindaklanjuti') DEFAULT 'baru',
  `id_user_admin` int DEFAULT NULL,
  `tanggapan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','petugas') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'petugas',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_protected` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `created_at`, `is_protected`) VALUES
(3, 'riswan', '$2y$10$8Lmf6vy.bLTewdhXe7iQqeH/qBoQBD9rhOOfOUQGl.fPweK0z8r.2', 'riswan badali', 'admin', '2025-11-06 03:11:42', 1),
(8, 'badali', '$2y$10$Gofltx5mayl3KBsdNfNfJO5fws/XF4mUevNY9CB.Egble1uxiNkTS', 'riswan badali', 'petugas', '2025-11-20 02:29:55', 0),
(34, 'aaa', '$2y$10$FC7dYOtHZrRwokaHwrpijuj/8PrvbCRvl460Q0yucYDctmJIQSMi2', 'aaa', 'admin', '2025-12-24 11:44:00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_berita`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `edukasi`
--
ALTER TABLE `edukasi`
  ADD PRIMARY KEY (`id_edukasi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `hasil_pemantauan`
--
ALTER TABLE `hasil_pemantauan`
  ADD PRIMARY KEY (`id_hasil`),
  ADD KEY `id_pemantauan` (`id_pemantauan`),
  ADD KEY `fk_hasil_user` (`id_user`);

--
-- Indexes for table `lokasi_pemantauan`
--
ALTER TABLE `lokasi_pemantauan`
  ADD PRIMARY KEY (`id_lokasi`),
  ADD KEY `fk_lokasi_user` (`id_user`);

--
-- Indexes for table `pemantauan_udara`
--
ALTER TABLE `pemantauan_udara`
  ADD PRIMARY KEY (`id_pemantauan`),
  ADD KEY `id_lokasi` (`id_lokasi`),
  ADD KEY `fk_pemantauan_user` (`id_user`);

--
-- Indexes for table `saran`
--
ALTER TABLE `saran`
  ADD PRIMARY KEY (`id_saran`),
  ADD KEY `id_user_admin` (`id_user_admin`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id_berita` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `edukasi`
--
ALTER TABLE `edukasi`
  MODIFY `id_edukasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hasil_pemantauan`
--
ALTER TABLE `hasil_pemantauan`
  MODIFY `id_hasil` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `lokasi_pemantauan`
--
ALTER TABLE `lokasi_pemantauan`
  MODIFY `id_lokasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `pemantauan_udara`
--
ALTER TABLE `pemantauan_udara`
  MODIFY `id_pemantauan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `saran`
--
ALTER TABLE `saran`
  MODIFY `id_saran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `berita`
--
ALTER TABLE `berita`
  ADD CONSTRAINT `berita_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `edukasi`
--
ALTER TABLE `edukasi`
  ADD CONSTRAINT `edukasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hasil_pemantauan`
--
ALTER TABLE `hasil_pemantauan`
  ADD CONSTRAINT `fk_hasil_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `hasil_pemantauan_ibfk_1` FOREIGN KEY (`id_pemantauan`) REFERENCES `pemantauan_udara` (`id_pemantauan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lokasi_pemantauan`
--
ALTER TABLE `lokasi_pemantauan`
  ADD CONSTRAINT `fk_lokasi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `pemantauan_udara`
--
ALTER TABLE `pemantauan_udara`
  ADD CONSTRAINT `fk_pemantauan_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pemantauan_udara_ibfk_1` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi_pemantauan` (`id_lokasi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saran`
--
ALTER TABLE `saran`
  ADD CONSTRAINT `saran_ibfk_1` FOREIGN KEY (`id_user_admin`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
