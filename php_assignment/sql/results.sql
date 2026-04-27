-- phpMyAdmin SQL Dump
-- version 5.2.3-1.el9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 06, 2026 at 8:05 PM
-- Server version: 9.1.0-commercial
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sandhu3_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `result_id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `final_cash` int NOT NULL,
  `cart_level` int NOT NULL,
  `popularity` int NOT NULL,
  `played_date` date NOT NULL,
  `played_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`result_id`, `email`, `final_cash`, `cart_level`, `popularity`, `played_date`, `played_time`) VALUES
(1, 'alex@campuscart.ca', 248, 2, 3, '2026-04-01', '12:10:00'),
(2, 'maya@campuscart.ca', 314, 3, 5, '2026-04-01', '13:45:00'),
(3, 'samir@campuscart.ca', 201, 2, 2, '2026-04-02', '14:05:00'),
(4, 'zoe@campuscart.ca', 279, 3, 4, '2026-04-02', '15:30:00'),
(5, 'liam@campuscart.ca', 188, 2, 1, '2026-04-03', '10:25:00'),
(6, 'priya@campuscart.ca', 336, 4, 5, '2026-04-03', '11:55:00'),
(7, 'alex@campuscart.ca', 261, 3, 4, '2026-04-04', '09:40:00'),
(8, 'maya@campuscart.ca', 298, 3, 4, '2026-04-04', '16:20:00'),
(9, 'sandhu3@mcmaster.ca', 228, 1, 1, '2026-04-06', '21:22:37'),
(10, 'alex@campuscart.ca', 228, 1, 1, '2026-04-06', '21:29:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `fk_results_players` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `result_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `fk_results_players` FOREIGN KEY (`email`) REFERENCES `players` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
