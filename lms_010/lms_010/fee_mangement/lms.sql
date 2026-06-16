-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2026 at 09:08 AM
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
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `city_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `name`, `city_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Model Town', 1, 'Active', '2026-04-07 17:11:10', '2026-04-07 17:11:10');

-- --------------------------------------------------------

--
-- Table structure for table `basic_information`
--

CREATE TABLE `basic_information` (
  `name` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(30) NOT NULL,
  `country` varchar(30) NOT NULL,
  `contact1` varchar(20) NOT NULL,
  `contact2` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `basic_information`
--

INSERT INTO `basic_information` (`name`, `address`, `city`, `country`, `contact1`, `contact2`) VALUES
('MindCraft', 'Urdu Bazar Lahore', 'Lahore', '', '12345678', '123456789');

-- --------------------------------------------------------

--
-- Table structure for table `blood_group`
--

CREATE TABLE `blood_group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_group`
--

INSERT INTO `blood_group` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'A+', 'Active', '2026-04-07 11:28:22', '2026-04-07 11:28:22');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `province_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `province_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Lahore', 1, 'Active', '2026-04-07 17:00:56', '2026-04-07 17:01:25');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Play Group (PG)', 'Active', '2026-04-07 08:30:09', '2026-04-07 08:30:38');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Pakistan', 'Active', '2026-04-07 16:45:18', '2026-04-07 16:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `fee_packages`
--

CREATE TABLE `fee_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_packages`
--

INSERT INTO `fee_packages` (`id`, `name`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Two Child', 600.00, 'Active', '2026-04-07 13:44:06', '2026-04-07 13:44:06'),
(2, 'Three Child', 500.00, 'Active', '2026-04-07 13:44:20', '2026-04-07 13:44:31');

-- --------------------------------------------------------

--
-- Table structure for table `fee_heads`
--

CREATE TABLE `fee_heads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

CREATE TABLE `fee_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_structures`
--

CREATE TABLE `fee_structures` (
  `id` int(11) NOT NULL,
  `fee_head_id` int(11) NOT NULL,
  `fee_type_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `fee_head_id` (`fee_head_id`),
  KEY `fee_type_id` (`fee_type_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `fee_structures_ibfk_1` FOREIGN KEY (`fee_head_id`) REFERENCES `fee_heads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_structures_ibfk_2` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_structures_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

CREATE TABLE `gender` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Male', 'Active', '2026-04-07 10:58:52', '2026-04-07 10:59:10'),
(2, 'Female', 'Active', '2026-04-07 10:59:19', '2026-04-07 10:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Morning', 'Active', '2026-04-07 10:05:07', '2026-04-07 10:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `mobile_operators`
--

CREATE TABLE `mobile_operators` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobile_operators`
--

INSERT INTO `mobile_operators` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Jazz', 'Active', '2026-04-07 13:49:40', '2026-04-07 13:49:40'),
(2, 'Zong', 'Active', '2026-04-07 13:49:40', '2026-04-07 13:49:40'),
(3, 'Telenor', 'Inactive', '2026-04-07 13:49:40', '2026-04-11 06:59:15'),
(4, 'Ufone', 'Active', '2026-04-07 13:49:40', '2026-04-07 13:49:40'),
(5, 'Warid', 'Active', '2026-04-07 13:49:40', '2026-04-07 13:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `name`, `country_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Punjab', 1, 'Active', '2026-04-07 16:53:46', '2026-04-07 16:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `religion`
--

CREATE TABLE `religion` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `religion`
--

INSERT INTO `religion` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Islam', 'Active', '2026-04-07 10:44:51', '2026-04-07 10:44:51'),
(2, 'Christianity', 'Active', '2026-04-07 10:45:11', '2026-04-07 10:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `created_at`) VALUES
(3, 'Admin', '2024-11-05 09:38:27'),
(4, 'School', '2024-11-12 13:06:14'),
(5, 'Shop', '2024-11-26 06:27:22'),
(6, 'Distributor', '2024-11-28 05:28:21');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `can_view` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`permission_id`, `role_id`, `module_name`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(2390, 3, 'Parties', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2391, 3, 'Products', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2392, 3, 'Sale', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2393, 3, 'Sale Return', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2394, 3, 'Sale Order', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2395, 3, 'Purchase', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2396, 3, 'Purchase Return', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2397, 3, 'Purchase Order', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2398, 3, 'Accounts', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2399, 3, 'Transactions', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2400, 3, 'Purchase Reports', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2401, 3, 'Sale Reports', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2402, 3, 'Stock Reports', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2403, 3, 'Account Reports', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2404, 3, 'Services', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2405, 3, 'Recyclebin', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2406, 3, 'User Management', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2407, 3, 'Settings', 1, 1, 1, 1, '2024-11-21 10:44:00'),
(2520, 4, 'Parties', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2521, 4, 'Products', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2522, 4, 'Sale', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2523, 4, 'Sale Return', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2524, 4, 'Sale Order', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2525, 4, 'Purchase', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2526, 4, 'Purchase Return', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2527, 4, 'Purchase Order', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2528, 4, 'Accounts', 1, 0, 0, 0, '2025-01-14 05:15:23'),
(2529, 4, 'Transactions', 1, 0, 0, 0, '2025-01-14 05:15:23'),
(2530, 4, 'Purchase Reports', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2531, 4, 'Sale Reports', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2532, 4, 'Stock Reports', 1, 1, 0, 0, '2025-01-14 05:15:23'),
(2533, 4, 'Account Reports', 1, 0, 0, 0, '2025-01-14 05:15:23'),
(2534, 4, 'Services', 1, 0, 0, 0, '2025-01-14 05:15:23'),
(2535, 6, 'Parties', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2536, 6, 'Products', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2537, 6, 'Sale', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2538, 6, 'Sale Return', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2539, 6, 'Sale Order', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2540, 6, 'Purchase', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2541, 6, 'Purchase Return', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2542, 6, 'Purchase Order', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2543, 6, 'Accounts', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2544, 6, 'Transactions', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2545, 6, 'Purchase Reports', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2546, 6, 'Sale Reports', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2547, 6, 'Stock Reports', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2548, 6, 'Account Reports', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2549, 6, 'Services', 1, 1, 0, 0, '2025-01-14 05:48:06'),
(2554, 5, 'Sale', 1, 1, 0, 0, '2025-01-17 04:44:24'),
(2555, 5, 'Sale Return', 1, 1, 0, 0, '2025-01-17 04:44:24'),
(2556, 5, 'Sale Order', 1, 1, 0, 0, '2025-01-17 04:44:24'),
(2557, 5, 'Transactions', 0, 0, 0, 0, '2025-01-17 04:44:24');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(2, 'B', 'Active', '2026-04-07 09:57:24', '2026-04-07 09:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE `statistics` (
  `id` int(11) NOT NULL,
  `total_today_purchase` int(11) DEFAULT NULL,
  `total_today_sale` int(11) DEFAULT NULL,
  `total_supplier_balance` int(11) DEFAULT NULL,
  `total_customer_balance` int(11) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statistics`
--

INSERT INTO `statistics` (`id`, `total_today_purchase`, `total_today_sale`, `total_supplier_balance`, `total_customer_balance`, `last_updated`) VALUES
(1, 529, 11597, 50, 10, '2025-01-27 12:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `guardian_id` int(11) DEFAULT NULL COMMENT 'Reference to student_guardians table',
  `admission_date` date NOT NULL,
  `admission_number` varchar(50) DEFAULT NULL,
  `student_name` varchar(100) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `roll_number` varchar(50) DEFAULT NULL,
  `student_category_id` int(11) NOT NULL,
  `religion_id` int(11) NOT NULL,
  `gender_id` int(11) NOT NULL,
  `blood_group_id` int(11) DEFAULT NULL,
  `admission_effective_date` date NOT NULL,
  `family_number` varchar(50) DEFAULT NULL,
  `hobbies` text DEFAULT NULL,
  `place_of_birth` text DEFAULT NULL,
  `fee_package_id` int(11) DEFAULT NULL,
  `student_picture` varchar(255) DEFAULT NULL,
  `student_search1` varchar(50) DEFAULT NULL,
  `student_search2` varchar(50) DEFAULT NULL,
  `student_search3` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `class_id`, `section_id`, `group_id`, `guardian_id`, `admission_date`, `admission_number`, `student_name`, `father_name`, `date_of_birth`, `roll_number`, `student_category_id`, `religion_id`, `gender_id`, `blood_group_id`, `admission_effective_date`, `family_number`, `hobbies`, `place_of_birth`, `fee_package_id`, `student_picture`, `student_search1`, `student_search2`, `student_search3`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 1, 4, '2026-04-09', NULL, 'Adeel Ahmadd', 'Rasheed Ahmad', '2026-04-09', NULL, 1, 2, 2, NULL, '2026-04-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-10 00:18:44', NULL),
(3, 1, 2, 1, 4, '2026-04-09', NULL, 'Adi Itx', 'Rasheed Ahmad', '2026-04-09', NULL, 1, 2, 2, NULL, '2026-04-09', NULL, NULL, NULL, NULL, '1775762968_Untitled.png', NULL, NULL, NULL, 'Active', '2026-04-10 00:29:28', NULL),
(4, 1, 2, 1, 4, '2026-04-09', NULL, 'Adeel Ahmad', 'Rasheed Ahmad', '2026-04-09', NULL, 1, 2, 2, NULL, '2026-04-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-10 00:32:18', NULL),
(5, 1, 2, 1, 4, '2026-04-09', NULL, 'Adeel Ahmad', 'Rasheed Ahmad', '2026-04-09', NULL, 1, 2, 2, NULL, '2026-04-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-10 00:35:16', NULL),
(6, 1, 2, 1, 4, '2026-04-09', NULL, 'Adeel Ahmad', 'Rasheed Ahmad', '2026-04-09', NULL, 1, 2, 2, NULL, '2026-04-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-10 00:38:42', NULL),
(7, 1, 2, 1, NULL, '2026-04-09', NULL, 'Adeel Ahmad', 'Rasheed Ahmad', '2026-04-09', NULL, 1, 2, 2, NULL, '2026-04-09', NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, 'Active', '2026-04-10 00:46:23', NULL),
(8, 1, 2, 1, 4, '2026-04-10', NULL, 'Areeb Gull', 'Rasheed Ahmad', '2026-04-10', NULL, 1, 1, 2, NULL, '2026-04-10', NULL, NULL, NULL, NULL, '1775799024_UMR_9918.JPG', NULL, NULL, NULL, 'Active', '2026-04-10 10:30:24', NULL),
(9, 1, 2, 1, NULL, '2026-04-10', NULL, 'Adeel', 'Ahmad', '2026-04-10', NULL, 1, 2, 2, NULL, '2026-04-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-10 10:38:25', NULL),
(10, 1, 2, 1, 5, '0000-00-00', '1', 'Adeel Ahmad', 'Rashid AHmad', '2026-04-10', '', 1, 1, 1, NULL, '0000-00-00', '', '', '', 2, '', '', '', '', 'Active', '2026-04-10 11:41:33', '2026-04-10 12:16:04'),
(11, 1, 2, 1, 7, '2026-04-11', NULL, 'Adeel Ahmad', 'Your Father', '2026-04-11', NULL, 1, 2, 2, NULL, '2026-04-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-11 07:41:46', NULL),
(12, 1, 2, 1, 4, '2026-04-11', NULL, 'Adeel Ahmad', 'Rasheed Ahmad', '2026-04-11', NULL, 1, 2, 2, 1, '2026-04-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Active', '2026-04-11 08:04:30', NULL),
(13, 1, 2, 1, 7, '0000-00-00', '12', 'Aseel Bakar', 'Your Father', '2026-04-11', '12', 1, 1, 2, NULL, '0000-00-00', '12', 'No one', 'Sialkot', 2, '1775879601_7399.jpeg', '33100-6892368-5', '33100-6892368-5', '33100-6892368-5', 'Active', '2026-04-11 08:06:55', '2026-04-11 09:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `student_category`
--

CREATE TABLE `student_category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_category`
--

INSERT INTO `student_category` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'OBC', 'Active', '2026-04-07 10:26:34', '2026-04-07 10:26:34');

-- --------------------------------------------------------

--
-- Table structure for table `student_guardians`
--

CREATE TABLE `student_guardians` (
  `id` int(11) NOT NULL,
  `present_address` text NOT NULL,
  `present_city_id` int(11) NOT NULL,
  `present_area_id` int(11) DEFAULT NULL,
  `present_country` varchar(100) DEFAULT NULL,
  `present_province` varchar(100) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `permanent_city_id` int(11) DEFAULT NULL,
  `permanent_area_id` int(11) DEFAULT NULL,
  `permanent_country` varchar(100) DEFAULT NULL,
  `permanent_province` varchar(100) DEFAULT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_cnic` varchar(20) DEFAULT NULL,
  `father_mobile` varchar(15) DEFAULT NULL,
  `father_mobile_operator` int(11) DEFAULT NULL,
  `father_sms` tinyint(1) DEFAULT 0,
  `father_whatsapp` tinyint(1) DEFAULT 0,
  `father_whatsapp_number` varchar(15) DEFAULT NULL,
  `father_profession` varchar(255) DEFAULT NULL,
  `father_education` varchar(255) DEFAULT NULL,
  `father_email` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) NOT NULL,
  `mother_cnic` varchar(20) DEFAULT NULL,
  `mother_mobile` varchar(15) DEFAULT NULL,
  `mother_mobile_operator` int(11) DEFAULT NULL,
  `mother_sms` tinyint(1) DEFAULT 0,
  `mother_whatsapp` tinyint(1) DEFAULT 0,
  `mother_whatsapp_number` varchar(15) DEFAULT NULL,
  `mother_profession` varchar(255) DEFAULT NULL,
  `mother_education` varchar(255) DEFAULT NULL,
  `mother_email` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) NOT NULL,
  `guardian_cnic` varchar(20) DEFAULT NULL,
  `guardian_mobile` varchar(15) DEFAULT NULL,
  `guardian_mobile_operator` int(11) DEFAULT NULL,
  `guardian_sms` tinyint(1) DEFAULT 0,
  `guardian_whatsapp` tinyint(1) DEFAULT 0,
  `guardian_whatsapp_number` varchar(15) DEFAULT NULL,
  `guardian_profession` varchar(255) DEFAULT NULL,
  `guardian_education` varchar(255) DEFAULT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_guardians`
--

INSERT INTO `student_guardians` (`id`, `present_address`, `present_city_id`, `present_area_id`, `present_country`, `present_province`, `permanent_address`, `permanent_city_id`, `permanent_area_id`, `permanent_country`, `permanent_province`, `father_name`, `father_cnic`, `father_mobile`, `father_mobile_operator`, `father_sms`, `father_whatsapp`, `father_whatsapp_number`, `father_profession`, `father_education`, `father_email`, `mother_name`, `mother_cnic`, `mother_mobile`, `mother_mobile_operator`, `mother_sms`, `mother_whatsapp`, `mother_whatsapp_number`, `mother_profession`, `mother_education`, `mother_email`, `guardian_name`, `guardian_cnic`, `guardian_mobile`, `guardian_mobile_operator`, `guardian_sms`, `guardian_whatsapp`, `guardian_whatsapp_number`, `guardian_profession`, `guardian_education`, `guardian_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'P-6 St No-02, Muslim Town Ali Block', 1, NULL, 'Pakistan', 'Punjab', 'P-6 St No-02, Muslim Town Ali Block', 1, NULL, 'Pakistan', 'Punjab', 'P-6 St No-02, Muslim Town Ali Block', '', '', NULL, 0, 0, '', '', '', '', 'P-6 St No-02, Muslim Town Ali Block', '', '', NULL, 0, 0, '', '', '', '', 'P-6 St No-02, Muslim Town Ali Block', '', '', NULL, 0, 0, '', '', '', '', 'Active', '2026-04-08 07:05:51', NULL),
(2, 'P-6 St No-02, Muslim Town Ali Block', 1, 1, 'Pakistan', 'Punjab', 'P-6 St No-02, Muslim Town Ali Block', 1, 1, 'Pakistan', 'Punjab', 'Father Name', '', '', NULL, 0, 0, '', '', '', '', 'Father Name', '', '', NULL, 0, 0, '', '', '', '', '', '', '', NULL, 0, 0, '', '', '', '', 'Active', '2026-04-08 07:16:07', NULL),
(3, 'Student Address', 1, 1, 'Pakistan', 'Punjab', 'Student Address', 1, 1, 'Pakistan', 'Punjab', 'Father Name', '', '', NULL, 0, 0, '', '', '', '', 'Father Name', '', '', NULL, 0, 0, '', '', '', '', '', '', '', NULL, 0, 0, '', '', '', '', 'Active', '2026-04-08 08:00:54', NULL),
(4, 'P-6 St No-02, Muslim Town Ali Block', 1, 1, 'Pakistan', 'Punjab', 'P-6 St No-02, Muslim Town Ali Block', 1, 1, 'Pakistan', 'Punjab', 'Rasheed Ahmad', '33100-1101100-9', '923456678789', 3, 1, 1, '923000000000', 'Govt Employee', 'Matrics', 'rashid.custom13@gmail.com', 'Balqees Akhtar', '33100-1234567-8', '923008989898', 4, 1, 1, '923090909090', 'House WIfe', 'MA', 'motheremail@gmail.com', 'Adeel Ahmad', '33100-9909909-0', '923440909090', 2, 1, 1, '923009089898', 'FreeLanceer', 'MBA', 'aeeel073@gmail.com', 'Active', '2026-04-08 09:03:48', '2026-04-08 10:59:55'),
(5, 'P6, Street No-2, Muslim Town Ali Block', 1, 1, 'Pakistan', 'Punjab', 'P6, Street No-2, Muslim Town Ali Block', 1, 1, 'Pakistan', 'Punjab', 'Rashid AHmad', '33100-1008976-5', '923007292289', 5, 1, 1, '923007292289', 'Excise and Textation', 'Intermediate and Secondray Education', 'arshid.custom3@gmail.com', 'Balqwws Ahktar', '33106-5657798-7', '923008765445', 3, 1, 0, '923008765445', 'House Wife', 'MSc', 'mother@gmail.com', 'Adeel Ahmad', '33109-0997655-6', '923007565346', 1, 1, 0, '923111441456', 'Father of ALl Business', 'MBA', 'adeel946@gmail.com', 'Active', '2026-04-08 10:03:14', '2026-04-08 14:07:03'),
(6, 'P-6 St No-02, Muslim Town Ali Block\r\nFSD', 1, NULL, 'Pakistan', 'Punjab', 'P-6 St No-02, Muslim Town Ali Block\r\nFSD', 1, NULL, 'Pakistan', 'Punjab', 'Rasheed Ahmad', '', '', NULL, 0, 0, '', '', '', '', 'Rasheed Ahmad', '', '', NULL, 0, 0, '', '', '', '', 'Rasheed Ahmad', '', '', NULL, 0, 0, '', '', '', '', 'Active', '2026-04-08 10:10:33', NULL),
(7, 'P-6 St No-02, Muslim Town Ali Block', 1, NULL, 'Pakistan', 'Punjab', 'P-6 St No-02, Muslim Town Ali Block', 1, NULL, 'Pakistan', 'Punjab', 'Your Father', '33678-7879898-9', '', NULL, 0, 0, '', '', '', '', 'P-6 St No-02, Muslim Town Ali Block', '', '', NULL, 0, 0, '', '', '', '', 'P-6 St No-02, Muslim Town Ali Block', '', '', NULL, 0, 0, '', '', '', '', 'Inactive', '2026-04-08 10:11:07', '2026-04-08 13:57:18'),
(9, 'Adeel Ahmad 2', 1, 1, 'Pakistan', 'Punjab', 'Adeel Ahmad', 1, 1, 'Pakistan', 'Punjab', 'Rasheed AHmad', '32310-0905670-9', '923009935676', 4, 0, 1, '923009935676', 'Vegeratable', 'Farmer', 'rashidcustomer@gmail.com', 'Rasheed AHmad', '32310-0905670-9', '923009935676', 1, 1, 0, '923009935676', 'Stitching', 'MMA', 'rashidcustomer@gmail.com', 'Rasheed AHmad', '32310-0905670-9', '923009935676', 3, 1, 0, '923009935676', 'Self', 'MMNA', 'rashidcustomer@gmail.com', 'Active', '2026-04-09 16:35:46', '2026-04-09 16:54:29'),
(10, 'Present Address', 1, 1, 'Pakistan', 'Punjab', 'Present Address', 1, 1, 'Pakistan', 'Punjab', 'Rasheed Ahmad', '', '', NULL, 0, 0, '', '', '', '', 'Rasheed Ahmad', '66666-6666666-6', '', NULL, 0, 0, '', '', '', '', '', '', '', NULL, 0, 0, '', '', '', '', 'Active', '2026-04-10 09:37:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `institution_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `role_id` int(11) NOT NULL,
  `allow_additional_discount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `institution_name`, `logo`, `username`, `email`, `password`, `created_at`, `status`, `role_id`, `allow_additional_discount`) VALUES
(1, 'Admin', '69d471c094dee_1775530432.jpeg', 'admin', 'admin@admin.com', '$2y$10$cXIl5FFigJ.F8NIiBzvbCeiqUgcfCKbtM0d8KUc/nFfviGJ4bJvyW', '2024-07-18 12:03:23', 'Active', 3, 0),
(15, 'Tayyab School ', '69ca0fe74fb50_1774850023.jpg', 'tayyab', 'tayyab123@gamail.com', '$2y$10$yxvwVADe32KFFSqDk5phE.T1iUNsOHEovzTa2nF/wcxppTp9kNNVG', '2025-02-19 07:00:33', 'Active', 4, 0),
(16, 'Milat School System', '69ca0e1856d5e_1774849560.jpg', 'Milatschool', 'milatschool@gmail.com', '$2y$10$gvonNUHwuK8Xyi9LD.30o.kWKXWjXzpkYaN9la6pL7elGYN8devq.', '2025-12-19 04:48:08', 'Active', 4, 0),
(17, 'Ali Kunz-Ul-Iman School', '69ca0e599b55e_1774849625.jpeg', 'alikunzuleman', 'samiyaa445@gmail.com', '$2y$10$5IJF59z.3LjQLQO86FSGzuZoiFMXiNBEvqcZa1v5WqE28yoWxUBpW', '2025-12-23 12:10:31', 'Active', 4, 0),
(18, 'Brookfield International School', NULL, 'brookfield', 'brookfieldschool@gmail.com', '$2y$10$TFRQMW.NyS3i5HlVDje2mejHh/Lciw5xfNicYwdJx0Rez/.fuvp/S', '2025-12-27 07:56:24', 'Active', 4, 0),
(19, 'Pakistan Foundation School', NULL, 'p.fSchool', 'pakistanfoundation@gmail.com', '$2y$10$5UKkgaghQ9eNPqh5aJGlUe339kTvnmlZz4c0/JPJeMDjj6Qf8FYpC', '2026-01-02 08:35:43', 'Active', 4, 0),
(20, 'Cambridge School System', NULL, 'cambridgeschool', 'cambridgeschool@gmail.com', '$2y$10$5xOA3F8HrbE/OxVJvcQI..xEwkEj6u2Dfh2X63h6XsWAULNONnkP2', '2026-01-10 09:19:29', 'Active', 4, 0),
(21, 'photon School & n colleges', NULL, 'photonschool', 'photonschool@gmail.com', '$2y$10$qqmbCAffiWxcR0aufP0Y3uSz4I3r8FFviYSNpwLJ.QXbBWbZVQyf6', '2026-01-28 05:03:05', 'Active', 4, 0),
(22, 'Makhdoom Kids Camous', NULL, 'makhdoomkids', 'makhdoomkidscampus@gmail.com', '$2y$10$C5Wu99bf7F2M/jYb88jAnuUfY1XLhfUuYb6fc/vczfg3ufyLxVoQ.', '2026-02-02 07:02:42', 'Active', 4, 0),
(23, 'Professional school & Acdemia', NULL, 'professionalschool', 'professionalschool@gmail.com', '$2y$10$6ovHtz.uv3qhYyQ5dDdSOeHf6K4QSkzVkJYGRaCDOpT23UcRDcl2C', '2026-02-02 10:00:00', 'Active', 4, 0),
(24, 'Ilmora Education system', NULL, 'ilmora', 'ilmoraeducation@gmail.com', '$2y$10$eARdR7zz2V0kDPYmqCbwWuriq3HqbGa0IsvStIjiFqwYRGlsHp7JW', '2026-02-12 04:57:53', 'Active', 4, 0),
(25, 'Star Group Of Schools', NULL, 'starschool', 'starschool@gmail.com', '$2y$10$29TN.RRDyNc.9vDzohWGWeVvuL7yiz.HPVOWOKwRTHSsy5pCgO7Iu', '2026-02-14 06:42:27', 'Active', 4, 0),
(26, 'Wide Bridge Publication', NULL, 'marketer', 'widebridge@gmail.com', '$2y$10$YV2gs3Gb.pcv5xw8GKuap.QzyxIEluGLROf/FfSsvV73zu.Zx8Txm', '2026-02-18 11:51:01', 'Active', 4, 0),
(27, 'Allied Montessori school ', NULL, 'Alliednoorpur', 'alliednoorpur@gmail.com', '$2y$10$fzrMzzIORsgEEh56jT7Q.uBzNZzEWEJ8DNwUgUqSbe8yPZ8A44lXC', '2026-02-19 15:33:01', 'Active', 4, 0),
(30, 'Airborne School System', NULL, 'airborneschool', 'airborne@gmail.com', '$2y$10$Qm9VvQWMCumbI.UaLHbWq.hsm2VaesGwQ8TMXQkB7Jm.3I8expZTy', '2026-02-19 15:48:18', 'Active', 4, 0),
(31, 'Hujra Lyceum School ', NULL, 'hujralyceum ', 'hujralyceum@gmail.com', '$2y$10$3tdvTRr2YKM6hWmukn61pOqrNq1vbBAxET07xgyFvMHcLOt7iTgj.', '2026-02-21 09:16:37', 'Active', 4, 0),
(32, 'Apple Group Of School', NULL, 'appleschool', 'applegroupofschool@gmail.com', '$2y$10$icDPM7hDfAbQzeegwkywMOLnWck/tZFp5f/VLVAX4YyOCZemIxihG', '2026-02-24 05:05:51', 'Active', 4, 0),
(33, 'Hashmat Memorial School', NULL, 'hashmatschool', 'hashmatschool@gmail.com', '$2y$10$ATjtomX/E8n7C62kkanmnuFO10kM8qt1HYude6uljK2OtEt4K/C6y', '2026-02-24 07:21:01', 'Active', 4, 0),
(34, 'Brain Builders School', NULL, 'brainbuilder', 'brainbuilder@gmail.com', '$2y$10$4U6Lm.gZrsjZWMRDuHqGnuw326NvOzNuZTL7JVE.CzKxaL.YcrFpW', '2026-02-24 07:28:45', 'Active', 4, 0),
(35, 'Iqra Education Complex', NULL, 'iqraschool', 'iqraeducationcomplex@gmail.com', '$2y$10$VL.MHSaRpisdVtw75d48yuGOY4GilJx3qNZawdYtC7fZGpH93oyzq', '2026-02-24 07:30:28', 'Active', 4, 0),
(36, 'Progressive Aims School', NULL, 'aimsschool', 'aimsschool@gmail.com', '$2y$10$Bnx147W0ve3c75AXcGxyeejPonxk42tr8gzBt8HeHl0woKKPCq1Vy', '2026-02-24 10:31:26', 'Active', 4, 0),
(37, 'The School Of Excellence', NULL, 'excellence', 'excellenceschool@gmail.com', '$2y$10$HHnv8HleWe6P1BxUAl.Xjun40HBM8FSZRaBXwjiTM5mwjAlYKZc32', '2026-02-26 07:38:38', 'Active', 4, 0),
(38, 'Becon Grammar School', NULL, 'becongrammar', 'becongrammer@gmail.com', '$2y$10$TT9SLFqyLvV7nR281lOiv.QHbeqPmPiElOw9pf6gVGrQoYGhpmcxe', '2026-02-26 10:03:44', 'Active', 4, 0),
(39, 'Day Star School', NULL, 'daystar', 'daystarschool@gmail.com', '$2y$10$SsH9jn5NDbOVLWAbdJQxX.mAfkHANIGKOsWiCry.hYv.wbGBU0IM.', '2026-02-27 06:09:23', 'Active', 4, 0),
(40, 'Star School, College & Academy', NULL, 'starschoolcollege', 'starschool&college@gmail.com', '$2y$10$8vD.gXs7eDHguxO0aUkdROE2PGoRPD2x8vetUif9gV2f82c7xWAam', '2026-02-27 07:51:07', 'Active', 4, 0),
(41, 'The Aspire School', NULL, 'theaspire', 'aspireschool@gmail.com', '$2y$10$eC5JO8d3snQO550LeP/nKeK9TE2qAoWZZM/NOD1WhjWQgQsUElQ5y', '2026-03-02 10:17:25', 'Active', 4, 0),
(42, 'Defence Educational School System', NULL, 'defenceschool', 'defenceschool@gmail.com', '$2y$10$uVtaID9csRvGnY8YTiq5/OTeP5xdgi3QFLVsp3d4R0H.IsTSau9eS', '2026-03-10 08:18:02', 'Active', 4, 0),
(43, 'The Eden Rose School', NULL, 'edenrose', 'edenroseschool@gmail.com', '$2y$10$3UXQZSbdf7pf/BaBShaX8Ogv3yN1QRu7y6uDJMXJL9q6DmtQYemay', '2026-03-12 07:48:02', 'Active', 4, 0),
(44, 'Alliedford School', NULL, 'alliedford', 'alliedford@gmail.com', '$2y$10$tXfRvl/7VSkkcDuXWlndwumdWaTkx4KSWa2F.Fl6ZDG2gtmv4/.3e', '2026-03-16 04:34:15', 'Active', 4, 0),
(45, 'Meesaq School system', NULL, 'meesaq', 'meesaqschool@gmail.com', '$2y$10$OhMEWcgw6aFoR5kQTH/qg.UbQxXS/o1QsBqtSD4bfM569XQvIsbje', '2026-03-25 11:49:14', 'Active', 4, 0),
(46, 'White Rose Public School', NULL, 'adminabc', 'adeel073abc@gmail.com', '$2y$10$9dguyo3jIuWgpt14GnmpwOeFwP6LG0W10AOCWI0yH7s76vM1D3ZLi', '2026-03-30 05:56:30', 'Active', 4, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_area_per_city` (`name`,`city_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `blood_group`
--
ALTER TABLE `blood_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_city_per_province` (`name`,`province_id`),
  ADD KEY `province_id` (`province_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_country_name` (`name`);

--
-- Indexes for table `fee_packages`
--
ALTER TABLE `fee_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobile_operators`
--
ALTER TABLE `mobile_operators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_province_per_country` (`name`,`country_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `religion`
--
ALTER TABLE `religion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_class_id` (`class_id`),
  ADD KEY `idx_section_id` (`section_id`),
  ADD KEY `idx_guardian_id` (`guardian_id`),
  ADD KEY `idx_student_name` (`student_name`),
  ADD KEY `idx_admission_number` (`admission_number`),
  ADD KEY `idx_roll_number` (`roll_number`);

--
-- Indexes for table `student_category`
--
ALTER TABLE `student_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_guardians`
--
ALTER TABLE `student_guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_present_city` (`present_city_id`),
  ADD KEY `idx_present_area` (`present_area_id`),
  ADD KEY `idx_permanent_city` (`permanent_city_id`),
  ADD KEY `idx_permanent_area` (`permanent_area_id`),
  ADD KEY `idx_father_mobile` (`father_mobile`),
  ADD KEY `idx_mother_mobile` (`mother_mobile`),
  ADD KEY `idx_guardian_mobile` (`guardian_mobile`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blood_group`
--
ALTER TABLE `blood_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_packages`
--
ALTER TABLE `fee_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fee_heads`
--
ALTER TABLE `fee_heads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `fee_types`
--
ALTER TABLE `fee_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `fee_structures`
--
ALTER TABLE `fee_structures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `gender`
--
ALTER TABLE `gender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mobile_operators`
--
ALTER TABLE `mobile_operators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `religion`
--
ALTER TABLE `religion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2558;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `statistics`
--
ALTER TABLE `statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_category`
--
ALTER TABLE `student_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_guardians`
--
ALTER TABLE `student_guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `areas_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provinces`
--
ALTER TABLE `provinces`
  ADD CONSTRAINT `provinces_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_students_guardian` FOREIGN KEY (`guardian_id`) REFERENCES `student_guardians` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_students_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
