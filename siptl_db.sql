-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 11:33 PM
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
-- Database: `siptl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `distribusi_rekomendasi`
--

CREATE TABLE `distribusi_rekomendasi` (
  `distribusi_id` int(11) NOT NULL,
  `rekomendasi_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `satker_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tugas` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distribusi_rekomendasi`
--

INSERT INTO `distribusi_rekomendasi` (`distribusi_id`, `rekomendasi_id`, `user_id`, `satker_id`, `created_at`, `tugas`) VALUES
(15, 11, 5, 3, '2024-11-06 22:32:32', 'aa');

-- --------------------------------------------------------

--
-- Table structure for table `lhp`
--

CREATE TABLE `lhp` (
  `lhp_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `bpk_issue_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lhp`
--

INSERT INTO `lhp` (`lhp_id`, `title`, `bpk_issue_date`) VALUES
(3, 'aass', '2024-11-06'),
(4, 'a', '2024-11-06');

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi`
--

CREATE TABLE `rekomendasi` (
  `rekomendasi_id` int(11) NOT NULL,
  `temuan_id` int(11) DEFAULT NULL,
  `recommendation_text` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rekomendasi`
--

INSERT INTO `rekomendasi` (`rekomendasi_id`, `temuan_id`, `recommendation_text`, `status`) VALUES
(11, 2, 'aa', 'a'),
(12, 2, 'b', 'b'),
(13, 2, 'a', 'a');

-- --------------------------------------------------------

--
-- Table structure for table `satker`
--

CREATE TABLE `satker` (
  `satker_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satker`
--

INSERT INTO `satker` (`satker_id`, `name`, `description`) VALUES
(3, 'ss', 'dd');

-- --------------------------------------------------------

--
-- Table structure for table `temuan`
--

CREATE TABLE `temuan` (
  `temuan_id` int(11) NOT NULL,
  `lhp_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temuan`
--

INSERT INTO `temuan` (`temuan_id`, `lhp_id`, `description`) VALUES
(2, 3, 'a'),
(5, 3, 'as');

-- --------------------------------------------------------

--
-- Table structure for table `tindak_lanjut`
--

CREATE TABLE `tindak_lanjut` (
  `tl_id` int(11) NOT NULL,
  `rekomendasi_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tl_text` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` enum('admin','inputer_inspektorat','inputer_satker') NOT NULL,
  `satker_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `name`, `role`, `satker_id`, `is_active`) VALUES
(5, 'admin@mail.com', '$2y$10$7wPvcR0goGviWgOI5RJCoum6FIhBnachM6Eo23tqGam0tpKu.T7Bq', NULL, 'admin', 3, 1),
(6, 'admin@example.com', '$2y$10$PsqszuFk14i8QLhwhmt0I.5U9wWMghAAiDrZyUKMuUKqKP2z.GILW', NULL, 'inputer_inspektorat', 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `distribusi_rekomendasi`
--
ALTER TABLE `distribusi_rekomendasi`
  ADD PRIMARY KEY (`distribusi_id`),
  ADD KEY `rekomendasi_id` (`rekomendasi_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `satker_id` (`satker_id`);

--
-- Indexes for table `lhp`
--
ALTER TABLE `lhp`
  ADD PRIMARY KEY (`lhp_id`);

--
-- Indexes for table `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD PRIMARY KEY (`rekomendasi_id`),
  ADD KEY `temuan_id` (`temuan_id`);

--
-- Indexes for table `satker`
--
ALTER TABLE `satker`
  ADD PRIMARY KEY (`satker_id`);

--
-- Indexes for table `temuan`
--
ALTER TABLE `temuan`
  ADD PRIMARY KEY (`temuan_id`),
  ADD KEY `lhp_id` (`lhp_id`);

--
-- Indexes for table `tindak_lanjut`
--
ALTER TABLE `tindak_lanjut`
  ADD PRIMARY KEY (`tl_id`),
  ADD KEY `rekomendasi_id` (`rekomendasi_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `distribusi_rekomendasi`
--
ALTER TABLE `distribusi_rekomendasi`
  MODIFY `distribusi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `lhp`
--
ALTER TABLE `lhp`
  MODIFY `lhp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rekomendasi`
--
ALTER TABLE `rekomendasi`
  MODIFY `rekomendasi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `satker`
--
ALTER TABLE `satker`
  MODIFY `satker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `temuan`
--
ALTER TABLE `temuan`
  MODIFY `temuan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tindak_lanjut`
--
ALTER TABLE `tindak_lanjut`
  MODIFY `tl_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `distribusi_rekomendasi`
--
ALTER TABLE `distribusi_rekomendasi`
  ADD CONSTRAINT `distribusi_rekomendasi_ibfk_1` FOREIGN KEY (`rekomendasi_id`) REFERENCES `rekomendasi` (`rekomendasi_id`),
  ADD CONSTRAINT `distribusi_rekomendasi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `distribusi_rekomendasi_ibfk_3` FOREIGN KEY (`satker_id`) REFERENCES `satker` (`satker_id`);

--
-- Constraints for table `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD CONSTRAINT `rekomendasi_ibfk_1` FOREIGN KEY (`temuan_id`) REFERENCES `temuan` (`temuan_id`) ON DELETE CASCADE;

--
-- Constraints for table `temuan`
--
ALTER TABLE `temuan`
  ADD CONSTRAINT `temuan_ibfk_1` FOREIGN KEY (`lhp_id`) REFERENCES `lhp` (`lhp_id`) ON DELETE CASCADE;

--
-- Constraints for table `tindak_lanjut`
--
ALTER TABLE `tindak_lanjut`
  ADD CONSTRAINT `tindak_lanjut_ibfk_1` FOREIGN KEY (`rekomendasi_id`) REFERENCES `rekomendasi` (`rekomendasi_id`),
  ADD CONSTRAINT `tindak_lanjut_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
