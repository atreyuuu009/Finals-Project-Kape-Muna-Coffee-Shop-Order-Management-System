-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 02:34 PM
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
-- Database: `kape_muna_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Hot Coffee'),
(2, 'Cold Coffee'),
(3, 'Tea'),
(4, 'Dessert');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`item_id`, `category_id`, `item_name`, `description`, `price`, `image_url`, `is_available`) VALUES
(1, 2, 'Ice Americano', 'Fresh and Cool.', 150.00, 'images/iceamericano.jpg', 1),
(2, 1, 'Lyam Coffee', 'Lyam Signature Coffee. Aromatic.', 160.00, 'images/Cup-Of-Creamy-Coffee.png', 1),
(3, 1, 'Edward Coffee', 'Eds', 210.00, 'images/Cup-Of-Creamy-Coffee.png', 1),
(4, 1, 'Chin Kape', 'Kape ni Chin ', 1600.00, 'images/menu_69898c929b26c6.62473645.webp', 1),
(6, 1, 'Atreyu\'s Coffee', 'Atreyu\'s Coffeee', 150.00, 'images/menu_698b3f4ce77e47.06863188.webp', 1),
(8, 1, 'Creamy Joshua', 'Made with love ni Joshua <3 ', 160.00, 'images/menu_698c76c554f585.97568994.jpg', 1),
(9, 4, 'RJ\'s Specialty', 'Pie ni Nalda', 1800.00, 'images/menu_698c83557dc079.60348641.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(20) DEFAULT NULL,
  `order_date` date NOT NULL,
  `order_time` time NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `order_summary` text NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `source` varchar(50) DEFAULT NULL,
  `payment` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `order_time`, `customer_name`, `order_summary`, `status`, `source`, `payment`, `total_price`) VALUES
(1, 'ORD-000001', '2026-02-08', '15:38:00', 'Atreyu', 'Ice Americano x1', 'Completed', 'Online', 'Cash', 150.00),
(2, 'ORD-000002', '2026-02-08', '16:28:13', 'Atreyu', 'Edward Coffee x1', 'Payment Failed', 'Online', 'Cash', 210.00),
(6, 'ORD-000006', '2026-02-09', '08:28:57', 'Chin', 'Chin Kape x2', 'Completed', 'Online', 'Cash', 3200.00),
(7, 'ORD-000007', '2026-02-09', '09:13:09', 'Chin', 'Chin Kape x1', 'Payment Failed', 'Online', 'Cash', 1600.00),
(8, 'ORD-000008', '2026-02-09', '09:34:49', 'Atreyu Tagaban', 'Edward Coffee x2', 'Completed', 'Online', 'Cash', 420.00),
(9, 'ORD-000009', '2026-02-09', '10:14:03', 'Atreyu', 'Lyam Coffee x2, Edward Coffee x1, Ice Americano x3', 'Payment Failed', 'Online', 'Cash', 980.00),
(10, 'ORD-000010', '2026-02-09', '10:20:59', 'Gil Atreyu', 'Chin Kape x1', 'Payment Failed', 'In-Store', 'PayMaya', 1600.00),
(11, 'ORD-000011', '2026-02-09', '10:21:48', 'Joshua', 'Lyam Coffee x2, Chin Kape x10', 'Completed', 'In-Store', 'GCash', 16320.00),
(12, 'ORD-000012', '2026-02-09', '10:23:24', 'Gada', 'Ice Americano x3', 'Completed', 'Online', 'Cash', 450.00),
(13, 'ORD-000013', '2026-02-09', '10:25:33', 'Gil Atreyu', 'Edward Coffee x3, Ice Americano x1', 'Completed', 'In-Store', 'PayMaya', 780.00),
(14, 'ORD-000014', '2026-02-09', '10:26:25', 'ad', 'Edward Coffee x1', 'Completed', 'Online', 'Cash', 210.00),
(15, 'ORD-000015', '2026-02-09', '10:26:33', 'adadawdaw', 'Ice Americano x1', 'Completed', 'Online', 'Cash', 150.00),
(16, 'ORD-000016', '2026-02-09', '10:36:52', 'Josh', 'Ice Americano x1', 'Pending', 'In-Store', 'GCash', 150.00),
(17, 'ORD-000017', '2026-02-10', '15:22:26', 'Atreyu', 'Pending items...', 'Cancelled', 'Online', 'Cash', 0.00),
(18, 'ORD-000018', '2026-02-10', '15:23:33', 'Atreyu', 'Atreyu\'s Coffee x2', 'Completed', 'Online', 'Cash', 300.00),
(19, 'ORD-000019', '2026-02-10', '16:32:22', 'Riza Marie', 'Atreyu\'s Coffee x1', 'Completed', 'Online', 'PayMaya', 150.00),
(20, 'ORD-000020', '2026-02-10', '16:34:48', 'Lyam', 'Lyam Coffee x22', 'Completed', 'Online', 'Cash', 3520.00),
(21, 'ORD-000021', '2026-02-10', '16:37:40', 'AKDnkawd', 'Pending items...', 'Payment Failed', 'Online', 'Cash', 0.00),
(22, 'ORD-000022', '2026-02-11', '13:25:56', 'Pou', 'Edward Coffee x10, Lyam Coffee x12', 'Completed', 'In-Store', 'GCash', 4020.00),
(23, 'ORD-000023', '2026-02-11', '13:33:10', 'awdawd', 'Pending items...', 'Cancelled', 'Online', 'Cash', 0.00),
(24, 'ORD-000024', '2026-02-11', '14:27:05', 'RJ Nalda', 'RJ\'s Specialty x1, Edward Coffee x1', 'Completed', 'In-Store', 'Online Card', 2010.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `item_name`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 'Ice Americano', 1, 150.00),
(2, 2, 3, 'Edward Coffee', 1, 210.00),
(6, 6, 4, 'Chin Kape', 2, 1600.00),
(7, 7, 4, 'Chin Kape', 1, 1600.00),
(8, 8, 3, 'Edward Coffee', 2, 210.00),
(9, 9, 2, 'Lyam Coffee', 2, 160.00),
(10, 9, 3, 'Edward Coffee', 1, 210.00),
(11, 9, 1, 'Ice Americano', 3, 150.00),
(12, 10, 4, 'Chin Kape', 1, 1600.00),
(13, 11, 2, 'Lyam Coffee', 2, 160.00),
(14, 11, 4, 'Chin Kape', 10, 1600.00),
(15, 12, 1, 'Ice Americano', 3, 150.00),
(16, 13, 3, 'Edward Coffee', 3, 210.00),
(17, 13, 1, 'Ice Americano', 1, 150.00),
(18, 14, 3, 'Edward Coffee', 1, 210.00),
(19, 15, 1, 'Ice Americano', 1, 150.00),
(20, 16, 1, 'Ice Americano', 1, 150.00),
(21, 18, 6, 'Atreyu\'s Coffee', 2, 150.00),
(22, 19, 6, 'Atreyu\'s Coffee', 1, 150.00),
(23, 20, 2, 'Lyam Coffee', 22, 160.00),
(25, 22, 3, 'Edward Coffee', 10, 210.00),
(26, 22, 2, 'Lyam Coffee', 12, 160.00),
(28, 24, 9, 'RJ\'s Specialty', 1, 1800.00),
(29, 24, 3, 'Edward Coffee', 1, 210.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('staff','cashier','manager','admin') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'User', 'admin@kape.com', '09123456789', 'admin', '$2y$10$QeN1LED2WvWOpuq2JGDV4uqAYEwx1ESa.UpOaGLzXttBfV182P9Nu', 'admin', '2026-02-10 12:33:09'),
(2, 'Atreyu', 'Tagaban', 'atreyutagaban009@gmail.com', '09510751931', 'qatreyu', '$2y$10$HvAj.ZKXunMAHj8wHs7Zbekc8SK5isoVeuPhWP0l1K2YHRmh0WErK', 'manager', '2026-02-10 12:46:45'),
(3, 'Lyam', 'Tesalona', 'lyamtesalona@gmail.com', '09510751931', 'lyams', '$2y$10$yYXugbZH55dTdtcP6Lq2w.j.fOcIIWtaknZoHbS66pC7/X2wnicyS', 'manager', '2026-02-10 15:13:23'),
(4, 'Kyroll', 'Vallester', 'kyroll@gmail.com', '09510751931', 'kyrolls', '$2y$10$iITNRoKzkbD37AZdtJi6IeAPw9lqbhjitqqYw3GppaUZgoQtn8ZJq', 'staff', '2026-02-10 15:30:12'),
(5, 'Gabriel', 'Chin', 'chin@gmail.com', '09510751931', 'chins', '$2y$10$Bwv/5RfB7QY.x7s27DvLF.Yt6Usbg.K9BMRPOeeoRNc4Offk/2Lau', 'cashier', '2026-02-10 15:31:24'),
(6, 'Touch', 'Me', 'touchmenot@gmail.com', '', 'touchme', '$2y$10$vj3NvInpNNdjUQKfnZcPAeGB8Vpkho6yw/FTh8rTdTTzbxezMMbBC', 'cashier', '2026-02-11 12:34:59'),
(7, 'Gil Atreyu', 'Tagaban', 'atortysu@gmail.com', '09510751931', 'btreyu', '$2y$10$1xBcdPEfaZyavXbB0i6Ecu8gTitukNLpPuvQGTRgtYDiWyfkSkuXy', 'staff', '2026-02-11 12:46:22'),
(8, 'RJ', 'Nalda', 'nalda@gmail.com', '09510751931', 'naldas', '$2y$10$ebwzmO/Lqi1OWoQcAf/koOX7VTgr3.Ns0xsACY2WQYjK3Du53koJq', 'manager', '2026-02-11 13:23:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
