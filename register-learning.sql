-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2025 at 04:28 PM
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
-- Database: `register-learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `year` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year`) VALUES
(1, '2025-2026'),
(2, '2026-2027'),
(3, '2027-2028');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`id`, `name`, `code`, `description`) VALUES
(1, 'ສາຍ ຄູພຸດທະສາດສະໜາ ແລະ ພາສາລາວ-ວັນນະຄະດີ', 'BL', NULL),
(2, 'ສາຍຄູ ພາສາອັງກິດ', 'ENG', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('ພຣະ','ສ.ນ','ຊາຍ','ຍິງ','ອຶ່ນໆ') NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `village` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `accommodation_type` enum('ຫາວັດໃຫ້','ມີວັດຢູ່ແລ້ວ') DEFAULT 'ມີວັດຢູ່ແລ້ວ',
  `photo` varchar(255) DEFAULT NULL,
  `registered_at` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `major_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `gender`, `dob`, `email`, `phone`, `village`, `district`, `province`, `accommodation_type`, `photo`, `registered_at`, `created_at`, `major_id`, `academic_year_id`, `previous_school`) VALUES
(33, 'ແກ້ວພຣະຈັນ', 'ແສງສະຫວັນ', 'ຍິງ', '2014-02-05', 'temples@gmail.com', '59750824', 'ອອກເມືອງ', 'ປາກເຊ', 'ຫຼວງພະບາງ', 'ມີວັດຢູ່ແລ້ວ', '686629bf428ee.png', '2025-07-03 13:57:03', '2025-07-03 11:01:30', 1, 1, 'ທ່າຫິນ'),
(34, 'ສຳລີ', 'ສຳລານ', 'ຊາຍ', '2019-02-28', 'hasyla@gmail.com', '2091213399', 'ໜອງບົວທອງໃຕ້', 'ສີໂຄດຕະບອງ', 'ນະຄອນຫຼວງວຽງຈັນ', 'ຫາວັດໃຫ້', '686635087a02d.png', '2025-07-03 14:45:12', '2025-07-03 11:01:30', 1, 1, 'ທ່າຫິນ'),
(36, 'ທອງສັນ', 'ວາລິນທອງ', 'ພຣະ', '2017-02-01', 'yla@gmail.com', '2031213322', 'ວັດຫລວງ', 'ສີໂຄດຕະບອງ', 'ນະຄອນຫຼວງວຽງຈັນ', 'ມີວັດຢູ່ແລ້ວ', '6866386324701.png', '2025-07-03 14:53:52', '2025-07-03 11:01:30', 1, 1, 'ໂຊກປາລວງ'),
(37, 'ວັດທະນາ', 'ຄຳມີໃຊ', 'ພຣະ', '2025-04-25', 'ffathasyla@gmail.com', '02077772565', 'ວັດຫລວງ', 'ສີໂຄດຕະບອງ', 'ນະຄອນຫຼວງ', 'ມີວັດຢູ່ແລ້ວ', '68663920e0573.png', '2025-07-03 15:02:40', '2025-07-03 11:01:30', 1, 1, 'ທ່າຫິນ'),
(38, 'ບຸນຫຼາຍ', 'ວິລະພາບ', 'ພຣະ', '2004-02-27', 'phathla@gmail.com', '91213355', 'ດອນ', 'ຊະນະສົມບູນ', 'ຈຳປາສັກ', 'ຫາວັດໃຫ້', '68663aef57aef.png', '2025-07-03 15:10:23', '2025-07-03 11:01:30', 1, 1, 'ມປ ສົງດົງໂດກ'),
(39, 'ບຸນຫຼາຍ', 'ວິລະພາບ', 'ຊາຍ', '2020-02-27', 'hathasyla@gmail.com', '777751888', 'ວັດຫລວງ', 'ໄຊເສດຖາ', 'ນະຄອນຫຼວງວຽງຈັນ', 'ມີວັດຢູ່ແລ້ວ', '68663c0e0a668.png', '2025-07-03 15:15:10', '2025-07-03 11:01:30', 1, 2, 'ໂຊກປາຫຼວງ'),
(40, 'ຄຳຝົນ', 'ກຸວໍລະວົງ', 'ສ.ນ', '1996-02-02', 'phat@gmail.com', '02091213344', 'ອອກເມືອງ', 'ຊະນະສົມບູນ', 'ຈຳປາສັກ', 'ຫາວັດໃຫ້', '6866448fadf66.png', '2025-07-03 15:51:27', '2025-07-03 11:01:30', 1, 1, 'ທ່າຫິນ'),
(41, 'ຄຳເສົາ', 'ສິດທິເດດ', 'ພຣະ', '2007-03-03', 'kham@gmail.com', '209121222', 'ວັດຫລວງ', 'ສີໂຄດຕະບອງ', 'ນະຄອນຫຼວງວຽງຈັນ', 'ມີວັດຢູ່ແລ້ວ', '68664ad9e8a26.png', '2025-07-03 16:15:54', '2025-07-03 11:01:30', 1, 1, 'ມປ ສົງດົງໂດກ'),
(42, 'ພຣະສັນຕິ', 'ຄຳມົນທາ', 'ຍິງ', '2000-03-02', 'phatsyla@gmail.com', '203232562', 'ອອກເມືອງ', 'ສີໂຄດຕະບອງ', 'ສະຫວັນນະເຂດ', 'ຫາວັດໃຫ້', '68665ccea7557.png', '2025-07-03 17:34:54', '2025-07-03 11:01:30', 1, 1, 'ມປ ສົງດົງໂດກ'),
(43, 'ສຳນຽງ', 'ພັດທະສີລາ', 'ສ.ນ', '1989-01-01', 'phatha@gmia.com', '201255122', 'ອອກເມືອງ', 'ໂຂງ', 'ສະຫວັນນະເຂດ', 'ຫາວັດໃຫ້', '68665edeb29aa.png', '2025-07-03 17:43:42', '2025-07-03 11:01:30', 1, 3, 'ທ່າຫິນ'),
(44, 'ວັນຄຳ', 'ວົງສາສຸລິນ', 'ພຣະ', '1999-01-01', 'phatasyla@gmail.com', '077752338', 'ຫພດເຫກເ', 'ສີໂຄດຕະບອງ', 'ສາລະວັນ', 'ມີວັດຢູ່ແລ້ວ', '68668fda337f8.png', '2025-07-03 21:12:42', '2025-07-03 14:12:42', 1, 1, 'ທ່າຫິນ'),
(46, 'พำเหกดเ', 'หกดหกด', 'ພຣະ', '1999-12-01', 'phathasya@gmail.com', '020777723585', 'ອອກເມືອງ', 'ປາກຂອງ', 'ບໍລິຄໍາໄຊ', 'ມີວັດຢູ່ແລ້ວ', '6866924ca8de3.png', '2025-07-03 21:23:08', '2025-07-03 14:23:08', 2, 1, 'ມປ ສົງດົງໂດກ'),
(48, 'ພັດທະສີລາ', 'ອາັນນທະສັກ', 'ສ.ນ', '2003-12-01', 'ph@gmail.com', '2077772337', 'ຮ່ອງຄ້າ', 'ຈັນທະບູລີ', 'ສະຫວັນນະເຂດ', 'ມີວັດຢູ່ແລ້ວ', '68669d2578fd4.png', '2025-07-03 22:09:25', '2025-07-03 15:09:25', 1, 2, 'ຫຼວງພະບາງ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year` (`year`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `major_id` (`major_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`major_id`) REFERENCES `majors` (`id`),
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
