DROP TABLE IF EXISTS `author`;
CREATE TABLE `author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `author` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Adeel Ahmad', 'AA', 'Active', '2024-08-10 07:14:36', '2024-08-10 07:14:36');
INSERT INTO `author` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'High Voltage', 'HV', 'Deleted', '2024-08-10 07:14:47', '2024-08-10 07:15:01');


DROP TABLE IF EXISTS `basic_information`;
CREATE TABLE `basic_information` (
  `name` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(30) NOT NULL,
  `contact1` varchar(20) NOT NULL,
  `contact2` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `basic_information` (`name`, `address`, `city`, `contact1`, `contact2`) VALUES ('Wide Bridge', 'Urdu Bazar Lahore', 'Lahore', '12345678', '123456789');


DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `category` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'ABC Products', 'ABC;s', 'Active', '2024-08-10 07:41:46', '2024-08-10 07:45:59');
INSERT INTO `category` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'XYZ Product', 'XYZ', 'Deleted', '2024-08-10 07:41:58', '2024-08-10 07:42:05');


DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `primary_region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

INSERT INTO `city` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('1', 'Pattoki', 'PT', 'Deleted', '2024-08-11 11:40:58', '2024-08-13 08:54:57', '1');
INSERT INTO `city` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('2', 'Wazirabad', 'SKP333', 'Active', '2024-08-11 11:41:08', '2024-08-13 08:55:52', '1');
INSERT INTO `city` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('3', 'Sialkot', 'SL', 'Active', '2024-08-11 11:42:09', '2024-08-13 08:55:35', '1');
INSERT INTO `city` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('4', 'Karachi', 'KHI', 'Deleted', '2024-08-11 12:21:18', '2024-08-13 08:54:46', '1');
INSERT INTO `city` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('5', 'Hafizabad', 'HBD', 'Active', '2024-08-13 08:53:33', '2024-08-13 08:55:09', '1');
INSERT INTO `city` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('6', 'Gujrat', 'GUJ', 'Active', '2024-08-13 08:58:20', '2024-08-13 08:58:20', '1');


DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `country` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Pakistan', 'PK', 'Active', '2024-08-11 09:38:29', '2024-08-11 09:38:29');
INSERT INTO `country` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'United States of America', 'USA', 'Deleted', '2024-08-11 09:38:41', '2024-08-11 09:39:29');


DROP TABLE IF EXISTS `district`;
CREATE TABLE `district` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `primary_region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

INSERT INTO `district` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('1', 'Gujranwala', 'GJ', 'Active', '2024-08-11 11:34:45', '2024-08-14 10:59:01', '2');
INSERT INTO `district` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('2', 'Rawalpindi', 'RWP3', 'Deleted', '2024-08-11 11:34:59', '2024-08-14 10:59:06', '2');
INSERT INTO `district` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('3', 'Looor', 'LHR', 'Deleted', '2024-08-13 08:42:19', '2024-08-14 10:59:45', '2');
INSERT INTO `district` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('4', 'Rawalpindi', 'RWP', 'Active', '2024-08-14 10:58:38', '2024-08-14 10:58:38', '2');


DROP TABLE IF EXISTS `division`;
CREATE TABLE `division` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `primary_region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

INSERT INTO `division` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('1', 'West Zone', 'WZ', 'Deleted', '2024-08-11 11:21:08', '2024-08-14 10:49:30', '1');
INSERT INTO `division` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('2', 'DI Khan', 'NW', 'Active', '2024-08-11 11:21:32', '2024-08-14 11:03:42', '1');
INSERT INTO `division` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('3', 'Sargodha3', 'SR', 'Deleted', '2024-08-14 10:48:35', '2024-08-14 10:49:45', '1');


DROP TABLE IF EXISTS `editor`;
CREATE TABLE `editor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `editor` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Muhammad Akram Awan2', 'MAA', 'Active', '2024-08-10 07:26:17', '2024-08-10 07:26:41');
INSERT INTO `editor` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Adeel Ahmad', 'AA', 'Deleted', '2024-08-10 07:26:26', '2024-08-10 07:26:34');


DROP TABLE IF EXISTS `item_main_type`;
CREATE TABLE `item_main_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

INSERT INTO `item_main_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'High Voltage', 'HV', 'Deleted', '2024-08-10 06:25:54', '2024-08-10 06:27:57');
INSERT INTO `item_main_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Karim Block', 'KB', 'Active', '2024-08-10 06:28:08', '2024-08-10 06:28:08');
INSERT INTO `item_main_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('3', 'ABC Type', 'ABC', 'Active', '2024-08-11 06:33:32', '2024-08-11 06:33:32');


DROP TABLE IF EXISTS `party`;
CREATE TABLE `party` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_name` varchar(255) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL,
  `trade_type_id` int(11) DEFAULT NULL,
  `party_type_id` int(11) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `credit_limit` decimal(10,2) DEFAULT NULL,
  `credit_limit_days` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `sub_area_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `ntn` varchar(50) DEFAULT NULL,
  `phone1` varchar(20) DEFAULT NULL,
  `phone2` varchar(20) DEFAULT NULL,
  `phone3` varchar(20) DEFAULT NULL,
  `phone4` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `sale_tax` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('1', 'ABC Woood', '89', '89', 'Active', '0', '0', '89', '8.00', '98', '0', '0', '0', '0', '0', '0', '0', '89', '89', '98', '9899', '98', '98', '98', '98@gmail', 'https://web.whatsapp.com/', '98', '98', '98', '2024-08-11 15:12:16', '2024-08-11 15:12:16');
INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('2', 'Brook Dale - 2', '', '', 'Active', '1', '1', '', '0.00', '0', '1', '1', '2', '1', '2', '1', '1', '', '', '', '', '', '', '', '', '', '', '', '', '2024-08-11 15:14:42', '2024-08-16 06:57:40');
INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('3', 'ABC Woood - Brook Dale', '', '', 'Active', '1', '1', '', '0.00', '0', '1', '1', '2', '1', '3', '1', '1', '', '', '', '', '', '', '', '', '', '', '', '', '2024-08-11 15:15:30', '2024-08-11 15:15:30');
INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('4', 'Allied School', '', '', 'Inactive', '0', '0', '', '0.00', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '', '', '', '', '', 'URL', '', '', '', '2024-08-11 15:17:17', '2024-08-16 07:20:34');
INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('5', 'Allied School', 'AS', '47788', 'Inactive', '1', '1', '', '0.00', '0', '1', '1', '2', '1', '2', '3', '4', 'Chak No 122', '1234', '1111', '', '', '', '', '', '', '', '', '', '2024-08-14 16:48:11', '2024-08-16 07:20:40');
INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('6', '4', '4', '4', 'Inactive', '0', '0', '4', '4.00', '4', '0', '0', '0', '0', '0', '0', '0', '4', '4', '4', '4', '4', '4', '4', '4@ggddff', '4', '4', '4', '4', '2024-08-14 17:02:24', '2024-08-16 07:20:45');
INSERT INTO `party` (`id`, `party_name`, `short_name`, `code`, `status`, `trade_type_id`, `party_type_id`, `contact_person`, `credit_limit`, `credit_limit_days`, `country_id`, `province_id`, `division_id`, `district_id`, `city_id`, `zone_id`, `sub_area_id`, `address`, `ntn`, `phone1`, `phone2`, `phone3`, `phone4`, `fax`, `email`, `url`, `cnic`, `sale_tax`, `comments`, `created_at`, `updated_at`) VALUES ('7', 'Ten', '9', '9', 'Deleted', '1', '1', '9', '9.00', '9', '1', '1', '2', '1', '3', '1', '3', '9', '9', '9', '9', '9', '9', '9', '9@9', '9', '9', '9', '9', '2024-08-15 20:05:56', '2024-08-16 07:21:05');


DROP TABLE IF EXISTS `party_type`;
CREATE TABLE `party_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `party_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'BILAL BOOK CENTER', 'BBC', 'Active', '2024-08-11 09:20:00', '2024-08-11 09:20:00');
INSERT INTO `party_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Unique Book Dept', 'UBD2', 'Deleted', '2024-08-11 09:20:26', '2024-08-11 09:20:43');


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL,
  `item_main_type_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `study_level_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `editor_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'PKR',
  `currency_rate` decimal(10,2) NOT NULL,
  `foreign_purchase_price` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `auto_price` enum('Yes','No') NOT NULL,
  `on_web` enum('Yes','No') NOT NULL,
  `web_rate` decimal(10,2) NOT NULL,
  `web_disc` decimal(5,2) NOT NULL CHECK (`web_disc` <= 100),
  `avg_purchase_rate` decimal(10,2) NOT NULL,
  `avg_purchase_disc` decimal(5,2) NOT NULL CHECK (`avg_purchase_disc` <= 100),
  `bargain_rate` decimal(10,2) NOT NULL,
  `bargain_disc` decimal(5,2) NOT NULL CHECK (`bargain_disc` <= 100),
  `sale_rate` decimal(10,2) NOT NULL,
  `sale_disc` decimal(5,2) NOT NULL CHECK (`sale_disc` <= 100),
  `retail_rate` decimal(10,2) NOT NULL,
  `retail_disc` decimal(5,2) NOT NULL CHECK (`retail_disc` <= 100),
  `highest_rate` decimal(10,2) NOT NULL,
  `highest_disc` decimal(5,2) NOT NULL CHECK (`highest_disc` <= 100),
  `distb_rate` decimal(10,2) NOT NULL,
  `distb_disc` decimal(5,2) NOT NULL CHECK (`distb_disc` <= 100),
  `purchase_rate` decimal(10,2) NOT NULL,
  `purchase_disc` decimal(5,2) NOT NULL CHECK (`purchase_disc` <= 100),
  `branch_rate` decimal(10,2) NOT NULL,
  `branch_disc` decimal(5,2) NOT NULL CHECK (`branch_disc` <= 100),
  `exchange_rate` decimal(10,2) NOT NULL,
  `exchange_disc` decimal(5,2) NOT NULL CHECK (`exchange_disc` <= 100),
  `school_rate` decimal(10,2) NOT NULL,
  `school_disc` decimal(5,2) NOT NULL CHECK (`school_disc` <= 100),
  `edition` varchar(100) DEFAULT NULL,
  `size_unit_id` int(11) NOT NULL,
  `size_length` decimal(10,2) NOT NULL,
  `size_width` decimal(10,2) NOT NULL,
  `year_of_publication` year(4) NOT NULL,
  `manage_stock` enum('Yes','No') NOT NULL,
  `manage_tax` enum('Yes','No') NOT NULL,
  `weight_unit_id` int(11) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `min_stock` int(11) NOT NULL,
  `packing` varchar(255) DEFAULT NULL,
  `isbn` varchar(13) DEFAULT NULL,
  `series` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('1', 'High Voltage', '1593914720', 'Active', '0', '0', '0', '0', '0', '0', '0', '0', 'PKR', '0.00', '0.00', '0.00', 'No', 'No', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'No', 'No', '0', '0.00', '', '0', '', '', '', '2024-08-10 18:07:41', '2024-08-10 18:07:41');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('2', '2', '2', 'Active', '2', '1', '1', '2', '1', '1', '1', '1', 'PKR', '0.00', '0.00', '0.00', 'No', 'No', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'No', 'No', '0', '0.00', '', '0', '', '', '', '2024-08-10 20:42:43', '2024-08-10 20:42:43');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('3', '2', '2', 'Active', '0', '0', '0', '0', '0', '0', '0', '0', 'PKR', '2.00', '2.00', '2.00', 'Yes', 'Yes', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2.00', '2', '0', '2.00', '2.00', '2002', 'Yes', 'Yes', '0', '2.00', '2', '2', '2', '2', '2', '2024-08-10 21:14:10', '2024-08-10 21:14:10');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('4', 'Nive', '9', 'Active', '2', '1', '1', '2', '1', '1', '1', '1', 'PKR', '9.00', '9.00', '9.00', 'Yes', 'Yes', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9', '1', '9.00', '9.00', '2009', 'Yes', 'Yes', '1', '9.00', '9', '9', '9', '9', '9', '2024-08-10 21:23:15', '2024-08-16 06:53:01');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('5', 'Adeel Ahmad', '987', 'Inactive', '0', '0', '0', '0', '0', '0', '0', '0', 'PKR', '0.00', '0.00', '0.00', 'Yes', 'Yes', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '100.00', '0.00', '100.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'Yes', 'Yes', '0', '0.00', '', '0', '', '', '', '2024-08-10 21:37:25', '2024-08-16 06:50:52');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('6', 'This is New Product', '5688766', 'Inactive', '3', '1', '0', '0', '0', '0', '0', '0', 'PKR', '0.00', '0.00', '0.00', 'Yes', 'Yes', '0.00', '0.00', '700.00', '20.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'Yes', 'Yes', '0', '0.00', '', '0', '', '', '', '2024-08-11 06:34:24', '2024-08-16 06:56:15');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('7', 'New Product', '54354345', 'Active', '3', '0', '1', '0', '0', '0', '0', '0', 'PKR', '0.00', '0.00', '0.00', 'Yes', 'Yes', '0.00', '0.00', '6000.00', '10.00', '3000.00', '8.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'Yes', 'Yes', '0', '0.00', '', '0', '', '', '', '2024-08-11 07:39:39', '2024-08-11 07:39:39');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('8', 'Default Value', '8768', 'Deleted', '0', '0', '0', '0', '0', '0', '0', '0', 'PKR', '0.00', '0.00', '0.00', 'Yes', 'Yes', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'Yes', 'Yes', '0', '0.00', '', '0', '', '', '', '2024-08-13 07:24:44', '2024-08-16 07:18:43');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('9', 'High Voltage', '78687', 'Deleted', '0', '0', '0', '0', '0', '0', '0', '0', 'PKR', '0.00', '0.00', '0.00', 'Yes', 'Yes', '0.00', '0.00', '0.00', '77.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '0', '0.00', '0.00', '0000', 'Yes', 'Yes', '0', '0.00', '', '0', '', '', '', '2024-08-13 07:28:24', '2024-08-16 07:17:50');
INSERT INTO `products` (`id`, `name`, `code`, `status`, `item_main_type_id`, `subject_id`, `publisher_id`, `study_level_id`, `author_id`, `editor_id`, `supplier_id`, `category_id`, `currency`, `currency_rate`, `foreign_purchase_price`, `price`, `auto_price`, `on_web`, `web_rate`, `web_disc`, `avg_purchase_rate`, `avg_purchase_disc`, `bargain_rate`, `bargain_disc`, `sale_rate`, `sale_disc`, `retail_rate`, `retail_disc`, `highest_rate`, `highest_disc`, `distb_rate`, `distb_disc`, `purchase_rate`, `purchase_disc`, `branch_rate`, `branch_disc`, `exchange_rate`, `exchange_disc`, `school_rate`, `school_disc`, `edition`, `size_unit_id`, `size_length`, `size_width`, `year_of_publication`, `manage_stock`, `manage_tax`, `weight_unit_id`, `weight`, `unit_of_measure`, `min_stock`, `packing`, `isbn`, `series`, `created_at`, `updated_at`) VALUES ('10', 'Ten', '10', 'Deleted', '2', '1', '1', '2', '1', '1', '1', '1', 'PKR', '9.00', '9.00', '9.00', 'No', 'Yes', '9.00', '0.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9.00', '9', '1', '9.00', '9.00', '2009', 'Yes', 'Yes', '1', '9.00', '9', '9', '9', '9', '9', '2024-08-13 07:57:02', '2024-08-16 07:17:57');


DROP TABLE IF EXISTS `province`;
CREATE TABLE `province` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `primary_region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('1', 'Punjab', 'P', 'Active', '2024-08-11 09:46:03', '2024-08-14 10:38:31', '1');
INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('2', 'Sindhjjjjook', 'SSs', 'Deleted', '2024-08-11 09:46:11', '2024-08-14 10:38:36', '1');
INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('3', 'Gilgit Baltistan', 'GB', 'Deleted', '2024-08-11 10:24:19', '2024-08-14 10:38:47', '1');
INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('4', 'Adeel Ahmad', 'pc2', 'Deleted', '2024-08-11 11:04:55', '2024-08-14 10:38:51', '1');
INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('5', 'Balochistan', 'B', 'Deleted', '2024-08-11 11:05:38', '2024-08-14 10:39:43', '1');
INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('6', 'Adeel Ahmad', 'pc644', 'Deleted', '2024-08-11 11:05:49', '2024-08-14 10:38:58', '1');
INSERT INTO `province` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('7', 'Punjab', 'P', 'Active', '2024-08-14 10:38:14', '2024-08-14 10:38:14', '1');


DROP TABLE IF EXISTS `publisher`;
CREATE TABLE `publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `publisher` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Adeel Ahmad3', 'AA', 'Active', '2024-08-10 06:58:17', '2024-08-10 11:15:09');
INSERT INTO `publisher` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Muhammad Akram Awan', 'MAA', 'Deleted', '2024-08-10 06:58:34', '2024-08-10 06:58:38');


DROP TABLE IF EXISTS `study_level`;
CREATE TABLE `study_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `study_level` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Level 1', 'L1', 'Deleted', '2024-08-10 07:06:14', '2024-08-10 07:06:32');
INSERT INTO `study_level` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Level 3', 'L2', 'Active', '2024-08-10 07:06:28', '2024-08-10 07:19:10');


DROP TABLE IF EXISTS `sub_area`;
CREATE TABLE `sub_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `primary_region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

INSERT INTO `sub_area` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('1', 'ABC Area', 'ABC', 'Active', '2024-08-11 13:01:09', '2024-08-14 10:23:34', '2');
INSERT INTO `sub_area` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('2', 'BCD Areasss', 'BCD', 'Deleted', '2024-08-11 13:01:21', '2024-08-14 10:23:30', '2');
INSERT INTO `sub_area` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('3', 'Block H', 'MBH', 'Active', '2024-08-14 10:10:19', '2024-08-14 13:49:42', '1');
INSERT INTO `sub_area` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('4', 'Noor Pur', 'NP', 'Active', '2024-08-14 10:24:09', '2024-08-14 10:24:09', '3');


DROP TABLE IF EXISTS `subject`;
CREATE TABLE `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `subject` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Social Studies-3', 'SS', 'Active', '2024-08-10 06:45:37', '2024-08-10 07:19:54');
INSERT INTO `subject` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Pakistan Studies', 'PS', 'Deleted', '2024-08-10 06:45:55', '2024-08-10 06:47:53');


DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Muhammad Akram Awan2', 'MAA', 'Active', '2024-08-10 07:34:52', '2024-08-10 07:35:23');
INSERT INTO `supplier` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Adeel Ahmad - Updated', 'AAU', 'Deleted', '2024-08-10 07:35:04', '2024-08-10 07:35:15');


DROP TABLE IF EXISTS `todos`;
CREATE TABLE `todos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` varchar(255) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `trade_type`;
CREATE TABLE `trade_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

INSERT INTO `trade_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Ghosia Traders', 'GT', 'Active', '2024-08-11 08:55:25', '2024-08-11 08:55:25');
INSERT INTO `trade_type` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('2', 'High Voltage', 'HV2', 'Deleted', '2024-08-11 08:55:35', '2024-08-11 08:55:49');


DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `unit` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES ('1', 'High Voltage', 'UV', 'Active', '2024-08-10 18:04:25', '2024-08-10 18:04:25');


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES ('1', 'admin', 'admin@admin.com', '$2y$10$ayJhhyNW4UZ3X/mvTGCxLOKlpLq1We4tyqLq97NyuQlby7mu.7Qga', '2024-07-18 17:03:23');
INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES ('5', 'adeel', 'new_email@example.com', '$2y$10$VVewDrgUVULVL7pf6D65peUL2YU0Bh6FC/xllJ7FboZIS2s7njOXS', '2024-07-21 06:48:55');


DROP TABLE IF EXISTS `zone`;
CREATE TABLE `zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Deleted') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `primary_region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

INSERT INTO `zone` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('1', 'Model Town', 'MT', 'Active', '2024-08-11 12:45:44', '2024-08-14 09:41:14', '3');
INSERT INTO `zone` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('2', 'DHA-I', 'DHA', 'Deleted', '2024-08-11 12:46:02', '2024-08-14 09:01:55', '1');
INSERT INTO `zone` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('3', 'ABBBC', 'bbc', 'Active', '2024-08-14 09:23:17', '2024-08-14 10:04:14', '2');
INSERT INTO `zone` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`, `primary_region_id`) VALUES ('4', 'Ali Pur Chattah2', 'APC', 'Deleted', '2024-08-14 10:03:04', '2024-08-14 10:03:23', '2');


