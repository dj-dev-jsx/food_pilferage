-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2025 at 08:23 AM
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
-- Database: `food_pilferage_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_types`
--

CREATE TABLE `action_types` (
  `action_type_id` int(11) NOT NULL,
  `action_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Meat Products'),
(2, 'Vegetables'),
(3, 'Seasonings'),
(4, 'Dry Goods'),
(5, 'Beverages');

-- --------------------------------------------------------

--
-- Table structure for table `daily_inventory`
--

CREATE TABLE `daily_inventory` (
  `record_id` int(11) NOT NULL,
  `item_id` varchar(10) DEFAULT NULL,
  `starting_quantity` decimal(10,2) DEFAULT NULL,
  `additions` decimal(10,2) DEFAULT 0.00,
  `usage_quantity` decimal(10,2) DEFAULT 0.00,
  `sales` decimal(10,2) DEFAULT 0.00,
  `expected_quantity` decimal(10,2) DEFAULT NULL,
  `actual_quantity` decimal(10,2) DEFAULT NULL,
  `discrepancy` decimal(10,2) DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_inventory`
--

INSERT INTO `daily_inventory` (`record_id`, `item_id`, `starting_quantity`, `additions`, `usage_quantity`, `sales`, `expected_quantity`, `actual_quantity`, `discrepancy`, `record_date`, `last_updated`) VALUES
(4, '001', 20.00, 1.00, 0.00, 0.00, 21.00, 35.00, -14.00, '2024-11-30', '2024-11-30 09:43:45'),
(5, '003', 30.00, 0.00, 0.00, 0.00, 30.00, 35.00, -5.00, '2024-11-30', '2024-11-30 09:43:16'),
(6, '002', 25.00, 0.00, 0.00, 0.00, 25.00, 15.00, 10.00, '2024-11-30', '2024-11-30 13:22:52'),
(7, '001', 21.00, 0.00, 0.00, 0.00, 21.00, 2.00, 19.00, '2024-12-06', '2024-12-06 14:40:12'),
(8, '003', 28.00, 12.00, 0.00, 0.00, 40.00, NULL, NULL, '2024-12-06', '2024-12-06 14:31:56'),
(9, '002', 0.00, 20.00, 0.00, 0.00, 20.00, 10.00, 10.00, '2024-12-06', '2024-12-06 14:58:25'),
(10, '001', 4.00, 4.00, 0.00, 0.00, 8.00, 10.00, -2.00, '2024-12-07', '2024-12-06 22:24:26'),
(11, '003', 40.00, 0.00, 0.00, 0.00, 40.00, 30.00, 10.00, '2024-12-07', '2024-12-06 22:24:50');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL,
  `item_id` varchar(10) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `previous_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`log_id`, `item_id`, `action_type`, `previous_value`, `new_value`, `user_id`, `timestamp`) VALUES
(72, '001', 'NEW_ITEM_ADDED', '0', '20', 18, '2024-11-29 00:39:08'),
(73, '002', 'NEW_ITEM_ADDED', '0', '20', 18, '2024-11-29 00:39:31'),
(74, '003', 'NEW_ITEM_ADDED', '0', '30', 18, '2024-11-30 11:48:35'),
(75, '001', 'START_DAY', '0', '20', 18, '2024-11-30 16:15:07'),
(76, '001', 'STOCK_ADDED', '20', '21', 18, '2024-11-30 16:15:12'),
(77, '001', 'END_DAY_COUNT', '21', '21', 18, '2024-11-30 16:15:21'),
(78, '001', 'END_DAY_COUNT', '21', '22', 18, '2024-11-30 16:15:36'),
(79, '001', 'END_DAY_COUNT', '21', '21', 18, '2024-11-30 16:18:46'),
(80, '003', 'END_DAY_COUNT', NULL, '2', 18, '2024-11-30 16:19:03'),
(81, '001', 'END_DAY_COUNT', '21.00', '22', 18, '2024-11-30 16:43:28'),
(82, '003', 'START_DAY', '0', '30', 18, '2024-11-30 16:55:33'),
(83, '003', 'END_DAY_COUNT', '30.00', '45', 18, '2024-11-30 16:55:50'),
(84, '003', 'END_DAY_COUNT', '30.00', '35', 18, '2024-11-30 16:56:50'),
(85, '003', 'END_DAY_COUNT', '30.00', '35', 18, '2024-11-30 16:57:54'),
(101, '002', 'EXPIRED_WASTE', '10', '0', 18, '2024-12-01 01:02:04'),
(102, '001', 'START_DAY', '21', '21', 18, '2024-12-06 20:23:51'),
(103, '001', 'END_DAY_COUNT', '21', '20', 18, '2024-12-06 20:24:09'),
(104, '001', 'ITEM_USED', '21', '20', NULL, '2024-12-06 21:39:53'),
(105, '003', 'ITEM_USED', '30', '28', NULL, '2024-12-06 21:40:14'),
(106, '001', 'ITEM_USED', '20', '15', NULL, '2024-12-06 21:58:03'),
(107, '003', 'START_DAY', '28', '28', 19, '2024-12-06 22:31:48'),
(108, '003', 'STOCK_ADDED', '28', '40', 19, '2024-12-06 22:31:56'),
(109, '002', 'START_DAY', '0', '0', 19, '2024-12-06 22:32:16'),
(110, '002', 'STOCK_ADDED', '0', '12', 19, '2024-12-06 22:32:24'),
(111, '002', 'STOCK_ADDED', '12', '15', 19, '2024-12-06 22:33:29'),
(112, '002', 'STOCK_ADDED', '15', '16', 19, '2024-12-06 22:35:37'),
(113, '002', 'STOCK_ADDED', '16', '19', 19, '2024-12-06 22:35:50'),
(114, '002', 'STOCK_ADDED', '19', '20', 19, '2024-12-06 22:38:14'),
(115, '001', 'END_DAY_COUNT', '21', '2', 19, '2024-12-06 22:40:12'),
(116, '001', 'PILFERAGE_DEDUCTION', '15', '-4', 19, '2024-12-06 22:40:13'),
(117, '002', 'PILFERAGE_DEDUCTION', '20', '10', 18, '2024-12-06 22:58:27'),
(118, '001', 'START_DAY', '-4', '4', 18, '2024-12-07 01:51:33'),
(119, '001', 'STOCK_ADDED', '4', '8', 18, '2024-12-07 01:51:40'),
(120, '001', 'ITEM_USED', '8', '6', NULL, '2024-12-07 01:54:34'),
(121, '001', 'ITEM_USED', '6', '5', 18, '2024-12-07 02:00:21'),
(122, '001', 'END_DAY_COUNT', '8', '5', 19, '2024-12-07 03:39:30'),
(123, '001', 'END_DAY_COUNT', '8', '10', 18, '2024-12-07 06:24:26'),
(124, '002', 'END_DAY_COUNT', NULL, '10', 18, '2024-12-07 06:24:32'),
(125, '003', 'END_DAY_COUNT', NULL, '10', 18, '2024-12-07 06:24:39'),
(126, '003', 'START_DAY', '40', '40', 18, '2024-12-07 06:24:42'),
(127, '003', 'END_DAY_COUNT', '40', '30', 18, '2024-12-07 06:24:50'),
(128, '003', 'PILFERAGE_DEDUCTION', '40', '30', 18, '2024-12-07 06:24:52'),
(129, '002', 'EXPIRED_WASTE', '10', '0', 18, '2025-03-13 11:08:50');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` varchar(10) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `status_id` int(11) NOT NULL,
  `unit_of_measure` varchar(50) NOT NULL DEFAULT 'pieces',
  `minimum_stock` int(11) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `unit_price`, `category_id`, `stock_quantity`, `status_id`, `unit_of_measure`, `minimum_stock`, `expiration_date`, `last_updated`, `is_deleted`) VALUES
('001', 'Cooking Oil', 30.00, 3, 5, 2, 'Kg', 10, NULL, '2024-12-06 18:00:21', 0),
('002', 'Beef Meat', 400.00, 1, 0, 3, 'Kg', 10, '2024-12-08', '2025-03-13 03:08:50', 0),
('003', 'Rice', 120.00, 4, 30, 1, 'Kg', 10, NULL, '2024-12-06 22:24:52', 0);

-- --------------------------------------------------------

--
-- Table structure for table `item_usage`
--

CREATE TABLE `item_usage` (
  `usage_id` int(11) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quantity_used` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `usage_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_usage`
--

INSERT INTO `item_usage` (`usage_id`, `item_id`, `user_id`, `quantity_used`, `total_cost`, `description`, `usage_date`) VALUES
(6, '001', 0, 1.00, 30.00, 'Adobo', '2024-12-06 21:39:53'),
(7, '003', 0, 2.00, 240.00, 'Cook', '2024-12-06 21:40:14'),
(8, '001', 0, 5.00, 150.00, 'Crispy Pata', '2024-12-06 21:58:03'),
(9, '001', 0, 2.00, 60.00, 'Adobo', '2024-12-07 01:54:34'),
(10, '001', 18, 1.00, 30.00, 'Cook', '2024-12-07 02:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `pilferage_report`
--

CREATE TABLE `pilferage_report` (
  `report_id` int(11) NOT NULL,
  `item_id` varchar(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reported_quantity` decimal(10,2) NOT NULL,
  `date_reported` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `report_status_id` int(3) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pilferage_report`
--

INSERT INTO `pilferage_report` (`report_id`, `item_id`, `user_id`, `reported_quantity`, `date_reported`, `description`, `report_status_id`, `updated_at`) VALUES
(32, '001', 19, 19.00, '2024-12-06 14:40:13', 'End day count detected missing items: 19 Kg', 1, '2024-12-06 14:40:13'),
(33, '002', 18, 10.00, '2024-12-06 14:58:27', 'End day count detected missing items: 10 Kg', 1, '2024-12-06 14:58:27'),
(34, '003', 18, 10.00, '2024-12-06 22:24:52', 'End day count detected missing items: 10 Kg', 2, '2024-12-06 22:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `problems`
--

CREATE TABLE `problems` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `approach` text DEFAULT NULL,
  `solution` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `problems`
--

INSERT INTO `problems` (`id`, `title`, `description`, `approach`, `solution`, `date_added`) VALUES
(10, 'Add', 'Adding integers', NULL, NULL, '2024-11-21 17:08:27'),
(11, 'dasda', 'asdas', NULL, NULL, '2025-01-08 10:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `report_status`
--

CREATE TABLE `report_status` (
  `report_status_id` int(3) NOT NULL,
  `report_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_status`
--

INSERT INTO `report_status` (`report_status_id`, `report_status`) VALUES
(1, 'Open'),
(2, 'Investigating'),
(3, 'Resolved');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Inventory Staff'),
(3, 'Kitchen Staff');

-- --------------------------------------------------------

--
-- Table structure for table `solutions`
--

CREATE TABLE `solutions` (
  `id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `step_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status`) VALUES
(1, 'In Stock'),
(2, 'Low Stock'),
(3, 'Out of Stock');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `access_code` varchar(15) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `firstname`, `lastname`, `middlename`, `email`, `contact_number`, `password`, `access_code`, `role_id`, `created_at`) VALUES
(18, 'deejay', 'Deejay', 'Balila', 'Esperanza', 'deejaybalila11@gmail.com', '09155679719', '$2y$10$XhoCarV/DpqdWlaPHfh1D.I6ju93DvHnQji8bma5CzBeW0Y126bhK', 'admin-12112003', 1, '2024-10-23 07:20:19'),
(19, 'dj', 'Deejay', 'Balila', 'Esperanza', 'deejaybalila5@gmail.com', '0910123213', '$2y$10$SU9GU/gVMklXhZDuvFEfXuUu3gI/VZGYeWKjTW589MV2aEYFyg0MS', 'inv-12112003', 2, '2024-10-27 17:10:37'),
(26, 'kenski', 'Kenski', 'Quiling', 'Sacasac', 'kenski@example.com', '09784652352', '$2y$10$l0YZXND9yDBEvGsfn1IL6OtyNH22NeDbvA2HDAtWcQdGayAT96xwC', NULL, 3, '2024-12-06 17:49:48'),
(30, 'asda', 'Daeasf', 'asdasd', 'asdasd', 'deejaybalila111@gmail.com', '123141', '$2y$10$RQYN8EFmjvJSkSB/4FaJ7uaxGVivF0LKBSokBLsZvLj.UnTyEkEzS', NULL, NULL, '2024-12-06 21:46:05'),
(31, 'x', 'dasd', 'asdas', 'asda', 'deeja@gmail.com', '3123123', '$2y$10$3zw1L5Ewq3dYEOMXxK/zTuR2K8tsJsw7hvDxaCCaTEZpYNoxiD35O', NULL, NULL, '2024-12-06 21:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `wastes`
--

CREATE TABLE `wastes` (
  `waste_id` int(11) NOT NULL,
  `item_id` varchar(10) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wastes`
--

INSERT INTO `wastes` (`waste_id`, `item_id`, `quantity`, `total_cost`, `user_id`, `reason`, `timestamp`) VALUES
(3, '002', 10.00, 4000.00, 18, 'Expired on 2024-12-01', '2024-12-01 01:02:04'),
(4, '002', 10.00, 4000.00, 18, 'Expired on 2024-12-08', '2025-03-13 11:08:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_types`
--
ALTER TABLE `action_types`
  ADD PRIMARY KEY (`action_type_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `inventory_logs_item_fk` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `item_usage`
--
ALTER TABLE `item_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pilferage_report`
--
ALTER TABLE `pilferage_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `report_status_id` (`report_status_id`);

--
-- Indexes for table `problems`
--
ALTER TABLE `problems`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_status`
--
ALTER TABLE `report_status`
  ADD PRIMARY KEY (`report_status_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `solutions`
--
ALTER TABLE `solutions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `problem_id` (`problem_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `wastes`
--
ALTER TABLE `wastes`
  ADD PRIMARY KEY (`waste_id`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_types`
--
ALTER TABLE `action_types`
  MODIFY `action_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `item_usage`
--
ALTER TABLE `item_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pilferage_report`
--
ALTER TABLE `pilferage_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `problems`
--
ALTER TABLE `problems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `report_status`
--
ALTER TABLE `report_status`
  MODIFY `report_status_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `solutions`
--
ALTER TABLE `solutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `wastes`
--
ALTER TABLE `wastes`
  MODIFY `waste_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_inventory`
--
ALTER TABLE `daily_inventory`
  ADD CONSTRAINT `daily_inventory_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE SET NULL;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`);

--
-- Constraints for table `item_usage`
--
ALTER TABLE `item_usage`
  ADD CONSTRAINT `item_usage_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `pilferage_report`
--
ALTER TABLE `pilferage_report`
  ADD CONSTRAINT `pilferage_report_ibfk_3` FOREIGN KEY (`report_status_id`) REFERENCES `report_status` (`report_status_id`),
  ADD CONSTRAINT `pilferage_report_item_fk` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `solutions`
--
ALTER TABLE `solutions`
  ADD CONSTRAINT `solutions_ibfk_1` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL;

--
-- Constraints for table `wastes`
--
ALTER TABLE `wastes`
  ADD CONSTRAINT `wastes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
