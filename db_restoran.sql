-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 11:29 AM
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
-- Database: `db_restoran`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir','koki','pelayan') DEFAULT 'pelayan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `restaurant_id`, `name`, `username`, `password`, `role`, `created_at`) VALUES
(7, 2, 'Kiral Nevan', 'Kiral', 'Kiral', 'admin', '2025-12-01 03:57:58'),
(8, 3, 'Naufal Alghifari', 'Nagari', 'Nagari', 'admin', '2025-12-01 04:01:04'),
(9, 4, 'Tyas Sekar A.', 'Tyas', 'Tyas', 'admin', '2025-12-01 04:01:24'),
(10, 5, 'Iswanda Febriantoro', 'Toro', 'Toro', 'admin', '2025-12-01 04:01:49'),
(11, 2, 'Adi', 'Adi', 'Adi', 'kasir', '2025-12-07 09:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('makanan','minuman','snack') NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_id`, `name`, `description`, `category`, `image_url`, `base_price`) VALUES
(4, 'Es kopi susu gula aren', 'espresso yang ditambah susu dan gula aren&#039;t', 'minuman', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRApkoIaHdpwuWR5ugxh7rszP9ns522zzWdZg&amp;s', 25000.00),
(5, 'Americano', 'espresso dengan air es', 'minuman', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRYC-M6OQdczqmaE9Dvbn8wVxP3fGQw-ZHKw&amp;s', 15000.00),
(6, 'Shushi', 'Nasi dengan nigiri sushi', 'makanan', 'https://png.pngtree.com/background/20230512/original/pngtree-japanese-foods-on-a-plate-picture-image_2503434.jpg', 500000.00),
(7, 'Ayam Geprek', 'Ayam Geprek', 'makanan', 'https://tse4.mm.bing.net/th/id/OIP.EXzDnyUhfXL_m5PmdX637AHaHP?pid=Api&amp;P=0&amp;h=180', 15000.00),
(8, 'Mie Ayam Yamin Bakso', '', 'makanan', 'https://up.yimg.com/ib/th/id/OIP.Ybk1lgv1MFvbHLNN-mKQFwHaHa?pid=Api&amp;rs=1&amp;c=1&amp;qlt=95&amp;w=107&amp;h=107', 30000.00),
(9, 'Es Teh Manis', '', 'minuman', 'https://tse3.mm.bing.net/th/id/OIP.B3vRNgfGNSIAltUZHIKThgHaE8?pid=Api&amp;P=0&amp;h=180', 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `order_type` enum('dine_in','take_away') NOT NULL DEFAULT 'dine_in',
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `number_of_people` int(11) DEFAULT 1,
  `reservation_time` datetime DEFAULT current_timestamp(),
  `estimasi_waktu` int(11) DEFAULT 0,
  `status` enum('pending','cooking','served','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('tunai','qris','debit') DEFAULT NULL,
  `cashier_name` varchar(100) DEFAULT '-',
  `amount_received` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `restaurant_id`, `user_id`, `table_number`, `customer_name`, `customer_phone`, `order_type`, `total_amount`, `number_of_people`, `reservation_time`, `estimasi_waktu`, `status`, `created_at`, `payment_method`, `cashier_name`, `amount_received`, `change_amount`) VALUES
(11, 2, NULL, '1', 'Kiral Nevan', NULL, 'dine_in', 10000.00, 1, '2025-12-01 10:04:06', 15, 'completed', '2025-11-30 14:37:56', 'qris', '-', 10000.00, 0.00),
(13, 2, NULL, '1', 'Kiral', NULL, 'dine_in', 50000.00, 1, '2025-12-01 10:04:06', 15, 'completed', '2025-12-01 00:55:40', 'tunai', '-', 100000.00, 50000.00),
(14, 2, NULL, '15', 'Kiral Nevan', '083892851545', 'dine_in', 25000.00, 1, '2025-12-01 11:10:00', 15, 'completed', '2025-12-01 03:11:06', 'debit', '-', 25000.00, 0.00),
(15, 2, NULL, '17', 'Kiral Nevan', '083892851545', 'dine_in', 40000.00, 2, '2025-12-01 14:00:00', 0, 'completed', '2025-12-01 03:50:40', 'qris', '-', 40000.00, 0.00),
(16, 3, NULL, '10', 'Kiral Nevan', '83892851545', 'dine_in', 500000.00, 2, '2025-12-01 12:31:00', 0, 'completed', '2025-12-01 04:31:48', 'debit', '-', 500000.00, 0.00),
(17, 3, NULL, '12', 'rasya', '00000', 'dine_in', 40000.00, 2, '2025-12-01 12:38:00', 0, 'completed', '2025-12-01 04:38:12', 'qris', '-', 40000.00, 0.00),
(18, 2, NULL, '16', 'fariz', '089394029939', 'dine_in', 90000.00, 4, '2025-12-01 13:12:00', 0, 'completed', '2025-12-01 05:12:58', 'qris', '-', 90000.00, 0.00),
(19, 3, NULL, '8', 'fariz', '089394029939', 'dine_in', 500000.00, 10, '2025-12-01 13:15:00', 0, 'completed', '2025-12-01 05:15:37', 'debit', '-', 500000.00, 0.00),
(20, 2, NULL, '01', 'fariz', '089394029939', 'dine_in', 30000.00, 1, '2025-12-01 13:38:00', 0, 'completed', '2025-12-01 05:38:41', 'tunai', '-', 75000.00, 45000.00),
(21, 3, NULL, 'L-365 (Lar', 'fariz', '089394029939', 'dine_in', 500000.00, 6, '2025-12-01 13:41:00', 0, 'completed', '2025-12-01 05:42:06', 'debit', '-', 500000.00, 0.00),
(22, 2, NULL, 'C-233 (Cou', 'fariz', '089394029939', 'dine_in', 30000.00, 1, '2025-12-01 13:48:00', 0, 'completed', '2025-12-01 05:48:57', 'qris', '-', 30000.00, 0.00),
(23, 2, NULL, 'C-233 (Cou', 'Nagari', '911', 'dine_in', 40000.00, 2, '2025-12-01 13:50:00', 0, 'completed', '2025-12-01 05:50:07', 'debit', '-', 40000.00, 0.00),
(24, 2, NULL, 'C-233 (Cou', 'Nagari', '911', 'dine_in', 15000.00, 1, '2025-12-02 13:55:00', 0, 'completed', '2025-12-02 05:55:05', 'qris', '-', 15000.00, 0.00),
(25, 2, NULL, 'C-233 (Cou', 'Nagari', '911', 'dine_in', 25000.00, 1, '2025-12-02 13:56:00', 0, 'completed', '2025-12-01 05:56:11', 'qris', '-', 25000.00, 0.00),
(26, 5, NULL, 'F-221 (Fam', 'kiral', '089231239174', 'dine_in', 45000.00, 4, '2025-12-01 15:50:00', 0, 'completed', '2025-12-01 07:50:18', 'qris', '-', 45000.00, 0.00),
(27, 2, NULL, 'C-476 (Cou', 'Jason Susanto', '1234567890', 'dine_in', 15000.00, 1, '2025-12-07 15:11:00', 0, 'completed', '2025-12-07 07:11:55', 'tunai', '-', 20000.00, 5000.00),
(28, 3, NULL, 'C-648 (Cou', 'Jason Susanto', '1234567890', 'dine_in', 15000.00, 1, '2025-12-07 15:18:00', 0, 'completed', '2025-12-07 07:18:18', 'qris', '-', 15000.00, 0.00),
(29, 3, NULL, 'C-648 (Cou', 'Jason Susanto', '1234567890', 'dine_in', 30000.00, 2, '2025-12-07 15:19:00', 0, 'completed', '2025-12-07 07:19:20', 'qris', '-', 30000.00, 0.00),
(30, 3, NULL, 'C-648 (Cou', 'Jason Susanto', '1234567890', 'dine_in', 15000.00, 1, '2025-12-07 15:21:00', 0, 'completed', '2025-12-07 07:21:22', 'qris', '-', 15000.00, 0.00),
(31, 3, NULL, 'L-365 (Lar', 'Jason Susanto', '1234567890', 'dine_in', 500000.00, 5, '2025-12-07 15:26:00', 0, 'completed', '2025-12-07 07:26:40', 'debit', '-', 500000.00, 0.00),
(32, 2, NULL, 'C-476 (Cou', 'Jason Susanto', '1234567890', 'dine_in', 15000.00, 1, '2025-12-07 15:29:00', 0, 'completed', '2025-12-07 07:29:28', 'qris', '-', 15000.00, 0.00),
(33, 2, NULL, '20', 'Jason Susanto', '1234567890', 'dine_in', 25000.00, 1, '2025-12-07 15:32:00', 0, 'completed', '2025-12-07 07:32:07', 'qris', '-', 25000.00, 0.00),
(34, 2, NULL, '11', 'Jason Susanto', '1234567890', 'dine_in', 40000.00, 2, '2025-12-07 15:48:00', 0, 'completed', '2025-12-07 07:49:02', NULL, '-', 0.00, 0.00),
(35, 3, NULL, '6', 'Kiral Nevan', '83892851545', 'dine_in', 15000.00, 1, '2025-12-07 17:43:00', 0, 'completed', '2025-12-07 09:44:09', 'qris', '-', 15000.00, 0.00),
(36, 2, NULL, '8', 'Jason Susanto', '080808080808', 'dine_in', 15000.00, 1, '2025-12-07 17:45:00', 0, 'completed', '2025-12-07 09:45:52', 'tunai', '-', 50000.00, 35000.00),
(37, 2, NULL, '6', 'Jason Susanto', '080808080808', 'dine_in', 15000.00, 1, '2025-12-07 17:55:00', 0, 'completed', '2025-12-07 09:55:11', 'qris', 'Kiral Nevan', 15000.00, 0.00),
(38, 2, NULL, '1', 'Jason Susanto', '080808080808', 'dine_in', 40000.00, 1, '2025-12-07 17:56:00', 0, 'completed', '2025-12-07 09:56:10', 'qris', 'Kiral Nevan', 40000.00, 0.00),
(39, 5, NULL, '4', 'Jason Susanto', '080808080808', 'dine_in', 90000.00, 3, '2025-12-07 18:04:00', 0, 'completed', '2025-12-07 10:04:45', 'qris', 'Tyas Sekar A.', 90000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`, `subtotal`, `notes`) VALUES
(15, 13, 4, 2, 50000.00, NULL),
(16, 14, 4, 1, 25000.00, NULL),
(17, 15, 4, 1, 25000.00, NULL),
(18, 15, 5, 1, 15000.00, NULL),
(19, 16, 6, 1, 500000.00, NULL),
(20, 17, 5, 1, 15000.00, NULL),
(21, 17, 4, 1, 25000.00, NULL),
(22, 18, 5, 1, 15000.00, NULL),
(23, 18, 4, 3, 75000.00, NULL),
(24, 19, 6, 1, 500000.00, NULL),
(25, 20, 5, 2, 30000.00, NULL),
(26, 21, 6, 1, 500000.00, NULL),
(27, 22, 5, 2, 30000.00, NULL),
(28, 23, 4, 1, 25000.00, NULL),
(29, 23, 5, 1, 15000.00, NULL),
(30, 24, 5, 1, 15000.00, NULL),
(31, 25, 4, 1, 25000.00, NULL),
(32, 26, 7, 1, 15000.00, NULL),
(33, 26, 8, 1, 30000.00, NULL),
(34, 27, 5, 1, 15000.00, NULL),
(35, 28, 7, 1, 15000.00, NULL),
(36, 29, 7, 2, 30000.00, NULL),
(37, 30, 7, 1, 15000.00, NULL),
(38, 31, 6, 1, 500000.00, NULL),
(39, 32, 5, 1, 15000.00, NULL),
(40, 33, 4, 1, 25000.00, 'no sugar, less ice'),
(41, 34, 5, 1, 15000.00, ''),
(42, 34, 4, 1, 25000.00, 'no sugar, less ice'),
(43, 35, 5, 1, 15000.00, 'less ice'),
(44, 36, 5, 1, 15000.00, ''),
(45, 37, 5, 1, 15000.00, 'tambahin saus tabasco'),
(46, 38, 5, 1, 15000.00, ''),
(47, 38, 4, 1, 25000.00, ''),
(48, 39, 8, 3, 90000.00, 'Mie nya setenga mateng');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`restaurant_id`, `name`, `address`, `phone`) VALUES
(2, 'Seruput', 'jl taman siswa, no 11', '080808080808'),
(3, 'Naga Geprek', 'Jalan Uranus, no 10', '089121087562'),
(4, 'Ayam Bakar OK', 'Jalan Mars, no 19', '089231239174'),
(5, 'Mie Ayam JOM ', 'Jalan Saturnus, no 7\r\n', '087261781212'),
(7, 'Sate Ayam 12', 'Jl Bumi, No 13', '123');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menus`
--

CREATE TABLE `restaurant_menus` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_menus`
--

INSERT INTO `restaurant_menus` (`id`, `restaurant_id`, `menu_id`, `price`, `is_available`) VALUES
(20, 2, 10, NULL, 1),
(22, 2, 11, NULL, 1),
(24, 2, 12, NULL, 1),
(26, 2, 13, NULL, 1),
(28, 2, 14, NULL, 1),
(76, 3, 7, NULL, 1),
(77, 5, 8, NULL, 1),
(80, 3, 9, NULL, 1),
(81, 4, 9, NULL, 1),
(82, 5, 9, NULL, 1),
(83, 2, 4, NULL, 1),
(84, 2, 5, NULL, 1),
(85, 3, 6, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `table_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`table_id`, `restaurant_id`, `table_name`, `capacity`, `is_active`) VALUES
(1, 1, 'Meja A1 (Couple)', 2, 1),
(2, 1, 'Meja A2 (Couple)', 2, 1),
(3, 1, 'Meja B1 (Family)', 4, 1),
(4, 1, 'Meja B2 (Family)', 4, 1),
(5, 1, 'Meja C1 (Large)', 8, 1),
(7, 3, 'C-648 (Couple)', 2, 1),
(8, 3, 'C-543 (Couple)', 2, 1),
(9, 3, 'C-267 (Couple)', 2, 1),
(10, 3, 'C-699 (Couple)', 2, 1),
(11, 3, 'C-732 (Couple)', 2, 1),
(12, 3, 'F-551 (Family)', 4, 1),
(13, 3, 'F-514 (Family)', 4, 1),
(14, 3, 'F-892 (Family)', 4, 1),
(15, 3, 'F-448 (Family)', 4, 1),
(16, 3, 'F-146 (Family)', 4, 1),
(17, 3, 'L-365 (Large)', 8, 1),
(18, 3, 'L-595 (Large)', 8, 1),
(24, 4, 'C-741 (Couple)', 2, 1),
(25, 4, 'C-112 (Couple)', 2, 1),
(26, 4, 'C-815 (Couple)', 2, 1),
(27, 4, 'C-722 (Couple)', 2, 1),
(28, 4, 'F-939 (Family)', 4, 1),
(29, 4, 'F-911 (Family)', 4, 1),
(30, 4, 'F-574 (Family)', 4, 1),
(31, 4, 'F-703 (Family)', 4, 1),
(32, 4, 'F-804 (Family)', 4, 1),
(33, 4, 'F-709 (Family)', 4, 1),
(34, 4, 'F-783 (Family)', 4, 1),
(35, 4, 'F-396 (Family)', 4, 1),
(36, 5, 'F-221 (Family)', 4, 1),
(37, 5, 'F-912 (Family)', 4, 1),
(38, 5, 'F-114 (Family)', 4, 1),
(39, 5, 'F-599 (Family)', 4, 1),
(56, 2, 'C-476 (Couple)', 2, 1),
(57, 2, 'C-910 (Couple)', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`restaurant_id`);

--
-- Indexes for table `restaurant_menus`
--
ALTER TABLE `restaurant_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`table_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `restaurant_menus`
--
ALTER TABLE `restaurant_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu_items` (`menu_id`);

--
-- Constraints for table `restaurant_menus`
--
ALTER TABLE `restaurant_menus`
  ADD CONSTRAINT `restaurant_menus_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_menus_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu_items` (`menu_id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD CONSTRAINT `restaurant_tables_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
