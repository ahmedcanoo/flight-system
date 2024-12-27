-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2024 at 02:45 PM
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
-- Database: `flight-booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `itinerary` text NOT NULL,
  `nu,_passengers_registered` int(11) NOT NULL,
  `num_passengers_pending` int(11) NOT NULL,
  `fees` decimal(10,0) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `completed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `type` enum('Company','Passenger') NOT NULL,
  `bio` text NOT NULL,
  `address` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `account_balance` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `type`, `bio`, `address`, `location`, `logo`, `account_balance`) VALUES
(6, 'sama', 'sama@gmail.com', '$2y$10$YxiB.7tOIiTvYHsBO/9f4OkEsIpEfdS0ZIwmmNDHgbJhlF0TStziO', '01234', 'Company', '', '', '', '', 0),
(7, 'Mostafa', 'mosstafaahmed11@gmail.com', '$2y$10$A2z.oaXkbKcL7jOCumUH1uZBjddbYVfWVpwSwowQlHjnfTGEpsKWm', '0123456', 'Company', '', '', '', '', 0),
(8, 'Mostafa', 'mostafa@gmail.com', '$2y$10$8Y.wLMuGV2S2x6zcGA6vbeTMT8cYgz2sCp7rw2pUZIb/5uyYODbcu', '0123456', 'Company', '', '', '', '', 0),
(9, 'sama', 'samaselim@gmail.com', '$2y$10$.Uh0xFhEtxMdKMAMezHgD.E21Ezz0opf34Iyi/cctv5WJWVka4XCK', '0123456', 'Company', '', '', '', '', 0),
(10, 'Sama Selim', 'samaselim18@icloud.com', '$2y$10$EY0z.47W.NyW1jTN0K5SeOSwvsiHudZBpTRgb0VWis.kxk0nWpCHy', '0123456', 'Company', '', '', '', '', 0),
(11, 'Sama Selim', 'samaselim@icloud.com', '$2y$10$Rtpa4UVcpzjtdKHX81ptEe73MoI/yAUOgPnX33tP9hi9Ai6t5dihC', '0123456', 'Company', '', '', '', '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
