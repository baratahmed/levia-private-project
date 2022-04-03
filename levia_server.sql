-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2019 at 07:45 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `levia_server`
--

-- --------------------------------------------------------

--
-- Table structure for table `ad_package`
--

CREATE TABLE `ad_package` (
  `ad_package_id` int(11) NOT NULL,
  `ad_package_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ad_package`
--

INSERT INTO `ad_package` (`ad_package_id`, `ad_package_name`) VALUES
(1, 'Sponsored Ad'),
(2, 'Offer Ad'),
(3, 'Notification Offer Ad');

-- --------------------------------------------------------

--
-- Table structure for table `ad_package_price`
--

CREATE TABLE `ad_package_price` (
  `ad_package_price_id` int(11) NOT NULL,
  `ad_package_price` decimal(6,2) NOT NULL,
  `ad_package_duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ad_package_price`
--

INSERT INTO `ad_package_price` (`ad_package_price_id`, `ad_package_price`, `ad_package_duration`) VALUES
(1, '700.00', 7),
(2, '1200.00', 15),
(3, '2000.00', 30);

-- --------------------------------------------------------

--
-- Table structure for table `ad_payment`
--

CREATE TABLE `ad_payment` (
  `ad_payment_id` int(11) NOT NULL,
  `ad_payment_amount` decimal(6,2) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `ad_payment_status` bit(1) DEFAULT b'0',
  `ad_payment_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ad_payment`
--

INSERT INTO `ad_payment` (`ad_payment_id`, `ad_payment_amount`, `payment_method_id`, `ad_payment_status`, `ad_payment_date_time`) VALUES
(1, '700.00', 1, b'1', '2018-08-07 09:13:42'),
(2, '1200.00', 1, b'1', '2018-08-23 20:34:19'),
(3, '2000.00', 2, b'1', '2018-08-14 09:52:15'),
(4, '2000.00', 2, b'1', '2018-05-14 09:52:15'),
(5, '2000.00', 1, b'1', '2018-03-14 09:32:15'),
(6, '2000.00', 2, b'1', '2018-08-14 09:42:15'),
(7, '2000.00', 1, b'1', '2018-06-14 09:52:15'),
(8, '2000.00', 1, b'1', '2018-07-14 09:42:15'),
(9, '2000.00', 2, b'1', '2018-08-14 09:52:15'),
(10, '700.00', 1, b'1', '2018-06-14 09:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `all_food`
--

CREATE TABLE `all_food` (
  `food_id` int(10) UNSIGNED NOT NULL,
  `food_category_id` int(11) NOT NULL,
  `food_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `all_food`
--

INSERT INTO `all_food` (`food_id`, `food_category_id`, `food_name`, `created_at`, `updated_at`) VALUES
(15, 62, 'THis is menu', '2018-10-11 07:41:16', '2018-10-11 07:41:16'),
(26, 63, 'Hello5', '2018-10-11 02:27:40', '2018-10-11 02:27:40'),
(27, 62, 'Hello7', '2018-10-11 02:29:04', '2018-10-11 02:29:04'),
(30, 63, 'Hello10', '2018-10-11 02:30:10', '2018-10-11 02:30:10'),
(31, 63, 'Hello', '2018-10-11 02:31:50', '2018-10-11 02:31:50'),
(32, 62, 'Zisad', '2018-10-11 02:53:03', '2018-10-11 02:53:03');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`) VALUES
(1, 'Chittagong'),
(2, 'Dhaka'),
(3, 'Khulna'),
(4, 'Rajshahi'),
(5, 'Tangail'),
(6, 'Sunamgonj'),
(7, 'Comilla'),
(8, 'Jessore'),
(9, 'Feni'),
(10, 'Noakhali'),
(11, 'Kishorgonj');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`district_id`, `district_name`) VALUES
(1, 'Chittagong'),
(2, 'Dhaka'),
(3, 'Rangamati'),
(4, 'Feni'),
(5, 'Bandarban'),
(6, 'Nilfamari'),
(7, 'Gopalgonj'),
(8, 'Noakhali'),
(9, 'Sylhet');

-- --------------------------------------------------------

--
-- Table structure for table `food`
--
-- Error reading structure for table levia_server.food: #1932 - Table 'levia_server.food' doesn't exist in engine
-- Error reading data for table levia_server.food: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `levia_server`.`food`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `food_bookmark`
--

CREATE TABLE `food_bookmark` (
  `food_bookmark_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bookmark_flag` bit(1) DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `food_bookmark`
--

INSERT INTO `food_bookmark` (`food_bookmark_id`, `rest_id`, `food_id`, `user_id`, `bookmark_flag`) VALUES
(1, 5, 2, 8, b'1'),
(2, 6, 6, 2, b'1'),
(3, 4, 5, 1, b'1'),
(4, 3, 4, 7, b'1'),
(5, 3, 4, 2, b'1'),
(6, 5, 7, 1, b'1'),
(7, 7, 6, 7, b'1'),
(8, 3, 8, 7, b'1'),
(9, 6, 5, 6, b'1');

-- --------------------------------------------------------

--
-- Table structure for table `food_category`
--

CREATE TABLE `food_category` (
  `food_category_id` int(11) NOT NULL,
  `food_category_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `food_category`
--

INSERT INTO `food_category` (`food_category_id`, `food_category_name`, `created_at`, `updated_at`) VALUES
(62, 'Sizzling Dish', '2018-10-16 08:38:56', '2018-10-11 01:41:16'),
(63, 'PItza', '2018-10-16 08:38:56', '2018-10-11 01:41:16');

-- --------------------------------------------------------

--
-- Table structure for table `food_rating`
--

CREATE TABLE `food_rating` (
  `food_rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `food_rating_value` enum('1','2','3','4','5') NOT NULL,
  `rating_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `food_rating`
--

INSERT INTO `food_rating` (`food_rating_id`, `user_id`, `rest_id`, `food_id`, `food_rating_value`, `rating_date_time`) VALUES
(1, 8, 5, 2, '2', '2018-08-02 09:20:44'),
(2, 1, 7, 6, '2', '2018-08-22 04:24:18'),
(3, 8, 4, 6, '3', '2018-08-08 13:20:00'),
(4, 7, 3, 3, '', '2018-07-11 08:38:00'),
(5, 8, 2, 5, '2', '2018-08-22 05:29:15'),
(6, 8, 6, 8, '5', '2018-07-19 08:14:36'),
(7, 8, 7, 3, '4', '2018-08-12 11:19:00'),
(8, 8, 5, 6, '3', '2018-08-03 00:25:00'),
(9, 6, 4, 6, '5', '2018-08-07 17:27:00'),
(10, 6, 2, 3, '3', '2018-06-21 07:32:00');

-- --------------------------------------------------------

--
-- Table structure for table `food_review`
--

CREATE TABLE `food_review` (
  `food_review_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `food_review`
--

INSERT INTO `food_review` (`food_review_id`, `review_id`, `rest_id`, `food_id`) VALUES
(1, 2, 4, 5),
(2, 3, 5, 6),
(3, 5, 6, 7),
(4, 4, 7, 8),
(5, 8, 2, 9),
(6, 6, 3, 3),
(7, 3, 2, 2),
(8, 5, 3, 1),
(9, 8, 6, 6),
(10, 9, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_ad_info`
--

CREATE TABLE `notification_ad_info` (
  `notification_ad_id` int(11) NOT NULL,
  `ad_package_id` int(11) NOT NULL,
  `user_notification_id` int(11) NOT NULL,
  `ad_starting_date` datetime NOT NULL,
  `ad_package_price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification_ad_info`
--

INSERT INTO `notification_ad_info` (`notification_ad_id`, `ad_package_id`, `user_notification_id`, `ad_starting_date`, `ad_package_price_id`) VALUES
(1, 3, 6, '2018-08-03 15:24:00', 1),
(2, 3, 5, '2018-08-03 20:27:00', 2),
(3, 3, 8, '2018-08-03 19:24:00', 3),
(4, 3, 2, '2018-08-03 12:14:00', 3),
(5, 3, 3, '2018-08-03 16:24:00', 1),
(6, 3, 7, '2018-08-03 15:54:00', 2),
(7, 3, 4, '2018-08-03 14:39:00', 3),
(8, 3, 7, '2018-08-03 15:24:00', 2),
(9, 3, 1, '2018-08-03 15:24:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notification_ad_payment`
--

CREATE TABLE `notification_ad_payment` (
  `user_notification_id` int(11) NOT NULL,
  `notification_ad_id` int(11) NOT NULL,
  `ad_package_id` int(11) NOT NULL,
  `ad_payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification_ad_payment`
--

INSERT INTO `notification_ad_payment` (`user_notification_id`, `notification_ad_id`, `ad_package_id`, `ad_payment_id`) VALUES
(3, 3, 3, 3),
(2, 2, 3, 2),
(1, 1, 3, 1),
(1, 2, 3, 1),
(2, 1, 3, 3),
(2, 2, 3, 1),
(3, 2, 3, 2),
(1, 3, 3, 3),
(1, 1, 3, 4),
(2, 3, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notification_type`
--

CREATE TABLE `notification_type` (
  `notification_type_id` int(11) NOT NULL,
  `notification_type_name` char(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification_type`
--

INSERT INTO `notification_type` (`notification_type_id`, `notification_type_name`) VALUES
(1, 'local'),
(2, 'global');

-- --------------------------------------------------------

--
-- Table structure for table `offer_ad_info`
--

CREATE TABLE `offer_ad_info` (
  `offer_ad_id` int(11) NOT NULL,
  `ad_package_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `ad_starting_date` datetime NOT NULL,
  `ad_package_price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offer_ad_info`
--

INSERT INTO `offer_ad_info` (`offer_ad_id`, `ad_package_id`, `offer_id`, `ad_starting_date`, `ad_package_price_id`) VALUES
(1, 2, 3, '2018-08-14 13:22:00', 2),
(2, 2, 4, '2018-05-04 13:22:00', 3),
(3, 2, 5, '2018-04-14 13:22:00', 1),
(4, 2, 6, '0000-00-00 00:00:00', 3),
(5, 2, 1, '2018-07-25 13:22:00', 3),
(6, 2, 5, '2018-08-14 13:22:00', 2),
(7, 2, 2, '2018-08-17 13:22:00', 1),
(8, 2, 4, '2018-08-14 13:22:00', 1),
(9, 2, 3, '2018-08-13 13:22:00', 2),
(10, 2, 6, '2018-08-11 13:22:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `offer_ad_payment`
--

CREATE TABLE `offer_ad_payment` (
  `offer_id` int(11) NOT NULL,
  `offer_ad_id` int(11) NOT NULL,
  `ad_package_id` int(11) NOT NULL,
  `ad_payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offer_ad_payment`
--

INSERT INTO `offer_ad_payment` (`offer_id`, `offer_ad_id`, `ad_package_id`, `ad_payment_id`) VALUES
(1, 3, 2, 1),
(2, 4, 2, 2),
(3, 9, 2, 3),
(2, 8, 2, 4),
(3, 1, 2, 5),
(4, 2, 2, 3),
(5, 6, 2, 4),
(6, 5, 2, 7),
(5, 4, 2, 8);

-- --------------------------------------------------------

--
-- Table structure for table `offer_info`
--

CREATE TABLE `offer_info` (
  `offer_id` int(11) NOT NULL,
  `offer_type_id` int(11) NOT NULL,
  `offer_title` varchar(100) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `offer_desc` varchar(500) DEFAULT NULL,
  `offer_starting_date` datetime NOT NULL,
  `offer_ending_date` datetime NOT NULL,
  `offer_image_url` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offer_info`
--

INSERT INTO `offer_info` (`offer_id`, `offer_type_id`, `offer_title`, `rest_id`, `food_id`, `offer_desc`, `offer_starting_date`, `offer_ending_date`, `offer_image_url`) VALUES
(1, 2, 'It\'s offer Title', 7, 9, 'Cosy, Good for children', '2018-08-01 00:00:00', '2018-08-03 00:00:00', 'asset/img/offer'),
(2, 1, '30% discount on grill chicken', 5, 5, 'It will run from 11am to 10 pm', '2018-08-09 00:00:00', '2018-08-12 18:30:00', 'asset/img/offer'),
(3, 1, '35% discount on chicken masala', 2, 6, 'It will run from 11am to 10 pm', '2018-08-09 10:00:00', '2018-08-12 18:30:00', 'asset/img/offer'),
(4, 2, 'Buy 2 get 1', 4, 3, 'It will run from 11am to 10 pm', '2018-08-09 10:00:00', '2018-08-12 18:30:00', 'asset/img/offer'),
(5, 2, '1 small pizza free with family pizza', 2, 2, 'It will run from 11am to 10 pm', '2018-08-09 10:00:00', '2018-08-12 18:30:00', 'asset/img/offer'),
(6, 1, '35% discount on chicken masala', 3, 3, 'It will run from 11am to 10 pm', '2018-08-09 10:00:00', '2018-08-12 18:30:00', 'asset/img/offer');

-- --------------------------------------------------------

--
-- Table structure for table `offer_type`
--

CREATE TABLE `offer_type` (
  `offer_type_id` int(11) NOT NULL,
  `offer_type_name` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offer_type`
--

INSERT INTO `offer_type` (`offer_type_id`, `offer_type_name`) VALUES
(1, 'Discount'),
(2, 'Bonus');

-- --------------------------------------------------------

--
-- Table structure for table `order_info`
--

CREATE TABLE `order_info` (
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quantity` decimal(10,4) NOT NULL,
  `table_no` varchar(50) DEFAULT NULL,
  `order_date_time` datetime NOT NULL,
  `order_status` bit(1) DEFAULT b'0',
  `order_flag` bit(1) DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_info`
--

INSERT INTO `order_info` (`order_id`, `food_id`, `user_id`, `quantity`, `table_no`, `order_date_time`, `order_status`, `order_flag`) VALUES
(1, 3, 8, '2.0000', 'B101', '2018-08-07 15:21:00', b'1', b'0'),
(2, 2, 9, '2.0000', '01', '2018-08-07 13:51:00', b'1', b'0'),
(3, 3, 2, '1.0000', '02', '2018-08-07 14:21:00', b'1', b'0'),
(4, 5, 1, '2.0000', '101', '2018-08-07 17:31:00', b'1', b'0'),
(5, 6, 2, '1.0000', '701', '2018-08-07 11:21:00', b'1', b'0'),
(6, 7, 8, '2.0000', '34', '2018-08-07 16:21:00', b'1', b'0'),
(7, 8, 7, '2.0000', '10', '2018-08-07 15:21:00', b'1', b'0'),
(8, 9, 6, '4.0000', '45', '2018-08-07 16:23:00', b'1', b'0'),
(9, 2, 8, '2.0000', '4h3', '2018-08-07 17:21:00', b'1', b'0'),
(10, 4, 7, '3.0000', '90', '2018-08-07 10:51:00', b'1', b'0'),
(11, 5, 1, '2.0000', '5', '2018-08-07 21:21:00', b'1', b'0'),
(12, 4, 2, '2.0000', '1', '2018-08-07 22:21:00', b'1', b'0');

-- --------------------------------------------------------

--
-- Table structure for table `order_payment`
--

CREATE TABLE `order_payment` (
  `order_payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `order_payable_amount` decimal(10,4) NOT NULL,
  `order_payment_status` bit(1) DEFAULT b'0',
  `payment_method_id` int(11) NOT NULL,
  `order_payment_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_payment`
--

INSERT INTO `order_payment` (`order_payment_id`, `order_id`, `trx_id`, `order_payable_amount`, `order_payment_status`, `payment_method_id`, `order_payment_date_time`) VALUES
(1, 1, 'ew87wefd67ew', '810.0000', b'1', 1, '2018-08-07 16:23:00'),
(2, 2, 'asdfghjkl9876543', '1050.5000', b'1', 1, '2018-07-10 11:23:00'),
(3, 3, 'jhgfdjhde45th987tf', '350.7500', b'1', 1, '2018-07-15 11:21:00'),
(4, 4, '08vc5rryhj', '400.0000', b'1', 1, '2018-08-12 10:00:00'),
(5, 5, 'p39085u4rhi47', '1200.0000', b'1', 1, '2018-07-20 16:28:00'),
(6, 3, 'oihvghfgdsw345678i098t', '1170.0000', b'0', 1, '2018-07-29 09:34:00'),
(7, 6, 'jhe77078rdgsdfn', '1500.0000', b'1', 1, '2018-06-20 12:23:00'),
(8, 4, '897634f3ghwf8y73', '600.0000', b'1', 1, '2018-08-11 13:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL,
  `payment_method_names` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `payment_method_names`) VALUES
(1, 'Cash'),
(2, 'bKash'),
(3, 'Rocket'),
(4, 'Credit Card'),
(5, 'Master Card'),
(6, 'Visa Card'),
(7, 'Nexus Pay'),
(8, 'Upay');

-- --------------------------------------------------------

--
-- Table structure for table `rest_ad_info`
--

CREATE TABLE `rest_ad_info` (
  `rest_ad_id` int(11) NOT NULL,
  `ad_package_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `ad_starting_date` datetime NOT NULL,
  `ad_package_price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_ad_info`
--

INSERT INTO `rest_ad_info` (`rest_ad_id`, `ad_package_id`, `rest_id`, `ad_starting_date`, `ad_package_price_id`) VALUES
(1, 3, 3, '2018-07-11 00:00:00', 2),
(2, 3, 2, '2018-08-16 09:00:00', 1),
(3, 1, 4, '2018-05-15 05:30:10', 1),
(4, 2, 5, '2018-08-09 09:14:46', 3),
(5, 2, 7, '2018-08-01 19:31:15', 2),
(6, 1, 5, '2018-07-26 04:23:00', 1),
(7, 2, 6, '2018-07-04 08:00:44', 3);

-- --------------------------------------------------------

--
-- Table structure for table `rest_ad_payment`
--

CREATE TABLE `rest_ad_payment` (
  `rest_id` int(11) NOT NULL,
  `rest_ad_id` int(11) NOT NULL,
  `ad_package_id` int(11) NOT NULL,
  `ad_payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_ad_payment`
--

INSERT INTO `rest_ad_payment` (`rest_id`, `rest_ad_id`, `ad_package_id`, `ad_payment_id`) VALUES
(3, 2, 1, 4),
(3, 2, 2, 8),
(6, 1, 1, 4),
(5, 1, 1, 7),
(3, 5, 3, 6),
(3, 1, 1, 8),
(2, 2, 2, 3),
(3, 4, 2, 4),
(6, 3, 2, 4),
(2, 3, 2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `rest_bookmark`
--

CREATE TABLE `rest_bookmark` (
  `rest_bookmark_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bookmark_flag` bit(1) DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_bookmark`
--

INSERT INTO `rest_bookmark` (`rest_bookmark_id`, `rest_id`, `user_id`, `bookmark_flag`) VALUES
(1, 6, 6, b'0'),
(2, 2, 2, b'0'),
(3, 3, 6, b'0'),
(4, 4, 1, b'0'),
(5, 6, 1, b'0'),
(6, 7, 1, b'0'),
(7, 3, 9, b'0'),
(8, 4, 9, b'0'),
(9, 3, 1, b'0');

-- --------------------------------------------------------

--
-- Table structure for table `rest_contact`
--

CREATE TABLE `rest_contact` (
  `rest_id` int(11) NOT NULL,
  `contact_no` char(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_contact`
--

INSERT INTO `rest_contact` (`rest_id`, `contact_no`) VALUES
(6, '01643265807'),
(6, '01876543215'),
(5, '01756742148'),
(5, '01925897536'),
(4, '01789064528'),
(7, '01586379753'),
(2, '01875368953'),
(2, '01986374280'),
(3, '01754269738');

-- --------------------------------------------------------

--
-- Table structure for table `rest_facility`
--

CREATE TABLE `rest_facility` (
  `rest_facility_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `parking` bit(1) DEFAULT b'0',
  `wifi` bit(1) DEFAULT b'0',
  `smoking_place` bit(1) DEFAULT b'0',
  `kids_corner` bit(1) DEFAULT b'0',
  `live_music` bit(1) DEFAULT b'0',
  `self_service` bit(1) DEFAULT b'0',
  `praying_area` bit(1) DEFAULT b'0',
  `game_zone` bit(1) DEFAULT b'0',
  `tv` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_facility`
--

INSERT INTO `rest_facility` (`rest_facility_id`, `rest_id`, `parking`, `wifi`, `smoking_place`, `kids_corner`, `live_music`, `self_service`, `praying_area`, `game_zone`, `tv`, `created_at`, `updated_at`) VALUES
(22, 6, b'0', b'0', b'1', b'0', b'1', b'0', b'0', b'1', 0, '2018-10-07 21:50:47', '0000-00-00 00:00:00'),
(29, 7, b'1', b'1', b'1', b'0', b'0', b'0', b'0', b'0', 0, '2018-10-07 21:50:47', '0000-00-00 00:00:00'),
(30, 3, b'1', b'1', b'1', b'1', b'1', b'1', b'0', b'0', 0, '2018-10-07 21:50:47', '0000-00-00 00:00:00'),
(31, 5, b'1', b'1', b'0', b'0', b'0', b'0', b'0', b'1', 0, '2018-10-07 21:50:47', '0000-00-00 00:00:00'),
(32, 2, b'0', b'0', b'0', b'0', b'0', b'0', b'0', b'0', 0, '2018-10-07 21:50:47', '0000-00-00 00:00:00'),
(33, 4, b'1', b'0', b'0', b'0', b'1', b'1', b'0', b'0', 0, '2018-10-07 21:50:47', '0000-00-00 00:00:00'),
(34, 1, b'1', b'1', b'1', b'0', b'1', b'1', b'1', b'0', 0, '2018-10-07 22:15:15', '2018-10-07 16:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `rest_food`
--

CREATE TABLE `rest_food` (
  `rest_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `unit_price` decimal(8,4) NOT NULL,
  `food_image_url` varchar(100) NOT NULL,
  `food_availabilty` bit(1) DEFAULT b'1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_food`
--

INSERT INTO `rest_food` (`rest_id`, `food_id`, `unit_price`, `food_image_url`, `food_availabilty`, `created_at`, `updated_at`) VALUES
(1, 15, '250.0000', 'D7JHF181011074116.png', b'1', '2018-10-11 07:41:16', '2018-10-11 07:41:16'),
(1, 16, '550.0000', 'EouQB181011074116.png', b'1', '2018-10-11 07:41:16', '2018-10-11 07:41:16'),
(1, 18, '236.0000', 'CVfW7181011081853.jpeg', b'1', '2018-10-11 02:18:53', '2018-10-11 02:18:53'),
(1, 26, '235.0000', 'Wvaqc181011082815.png', b'1', '2018-10-11 02:28:15', '2018-10-11 02:28:15'),
(1, 28, '235.0000', 'WlYc1181011082946.png', b'1', '2018-10-11 02:29:46', '2018-10-11 02:29:46'),
(1, 31, '235.0000', 'MjeeK181011083150.png', b'1', '2018-10-11 02:31:50', '2018-10-11 02:31:50'),
(1, 32, '350.0000', 'MH4fh181011085303.png', b'1', '2018-10-11 08:53:03', '2018-10-11 08:53:03');

-- --------------------------------------------------------

--
-- Table structure for table `rest_food_discount`
--

CREATE TABLE `rest_food_discount` (
  `rest_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `discount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_food_discount`
--

INSERT INTO `rest_food_discount` (`rest_id`, `food_id`, `discount`) VALUES
(7, 3, 20),
(2, 5, 20),
(3, 4, 20),
(4, 7, 20),
(5, 9, 20),
(6, 3, 20),
(3, 5, 20),
(4, 2, 20),
(6, 6, 20),
(2, 8, 20),
(7, 9, 20);

-- --------------------------------------------------------

--
-- Table structure for table `rest_info`
--

CREATE TABLE `rest_info` (
  `id` int(11) NOT NULL,
  `rest_name` varchar(100) NOT NULL,
  `rest_image_url` varchar(100) NOT NULL,
  `rest_latitude` decimal(15,10) DEFAULT NULL,
  `rest_longitude` decimal(15,10) DEFAULT NULL,
  `rest_street` varchar(100) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `district_id` int(11) NOT NULL,
  `rest_post_code` char(10) NOT NULL,
  `rest_description` text,
  `road_no` varchar(255) DEFAULT NULL,
  `police_station` varchar(250) DEFAULT NULL,
  `rest_tax_no` varchar(250) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone_no` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rest_verified` bit(1) DEFAULT b'0',
  `rest_reg_date` datetime DEFAULT NULL,
  `weekend1` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `weekend2` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_info`
--

INSERT INTO `rest_info` (`id`, `rest_name`, `rest_image_url`, `rest_latitude`, `rest_longitude`, `rest_street`, `city_id`, `district_id`, `rest_post_code`, `rest_description`, `road_no`, `police_station`, `rest_tax_no`, `email`, `phone_no`, `created_at`, `updated_at`, `rest_verified`, `rest_reg_date`, `weekend1`, `weekend2`) VALUES
(1, 'Bangla', '1_Bangla.jpeg', NULL, NULL, 'Chittagong', NULL, 1, 'sadasd', NULL, '15550', '15884848', 'sadasd', 'sadasd@gmail.com', 'sadasd', '2018-10-07 02:50:37', '2018-10-07 15:37:16', b'0', NULL, 'Friday', 'Saturday'),
(2, 'Hunger Games', 'assets/img/rest_pp/', '22.3447330000', '91.8260450000', '1027, CJKS Shopping Complex, Neval Avenue, Kajirdawri', 1, 1, '4139', 'Cosy, Casual, Good for kids', NULL, NULL, NULL, '', '', '2018-10-07 08:43:13', '2018-10-07 08:43:13', b'1', '2018-08-20 08:33:06', NULL, NULL),
(3, 'Food Fiesta', 'assets/img/rest_pp/', '22.3497330000', '91.8260750000', 'Chowdhury Tower (Ground Floor), Opposite of G.P.O, H. S. S. Road, Kotowali', 1, 1, '4000', 'Cosy, Casual, Good for kids', NULL, NULL, NULL, '', '', '2018-10-07 08:43:13', '2018-10-07 08:43:13', b'1', '2018-08-20 08:33:06', NULL, NULL),
(4, 'La Gondola', 'assets/img/rest_pp/', '23.3447330000', '94.8260450000', 'Kobi Kazi Nazrul Islam Road, Chittagong', 1, 1, '4127', 'All you can eat. Cosy, Casual', NULL, NULL, NULL, '', '', '2018-10-07 08:43:13', '2018-10-07 08:43:13', b'1', '2018-08-20 08:33:06', NULL, NULL),
(5, 'HashTag - Restaurant, Music Cafe & Lounge', 'assets/img/rest_pp/', '22.3674733000', '81.8260450000', 'Plot No.: 07, CDA Masjid Complex, Mehedibag', 1, 1, '4000', 'Cosy, Casual, Good for kids', NULL, NULL, NULL, '', '', '2018-10-07 08:43:13', '2018-10-07 08:43:13', b'1', '2018-08-20 08:33:06', NULL, NULL),
(6, 'Bell Pepper', 'assets/img/rest_pp/', '22.3447330000', '91.8260450000', '63, Ground Floor, Zinnurine Complex, 2 No. Gate, East Nasirabad, Panchlaish', 1, 1, '4209', 'Cosy, Casual, Good for kids', NULL, NULL, NULL, '', '', '2018-10-07 08:43:13', '2018-10-07 08:43:13', b'1', '2018-08-20 08:33:06', NULL, NULL),
(7, 'Cafe 71', 'assets/img/rest_pp/', '22.3447330000', '91.8260450000', 'RF Police Plaza, Buddist Temple Rd, Chittagong', 1, 1, '4000', 'Cosy, Casual, Good for kids', NULL, NULL, NULL, '', '', '2018-10-07 08:43:13', '2018-10-07 08:43:13', b'1', '2018-08-20 08:33:06', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rest_notification`
--

CREATE TABLE `rest_notification` (
  `rest_notification_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `rest_notification_date_time` datetime NOT NULL,
  `rest_notification_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_notification`
--

INSERT INTO `rest_notification` (`rest_notification_id`, `rest_id`, `notification_type_id`, `rest_notification_date_time`, `rest_notification_text`) VALUES
(1, 7, 1, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Yasir of 800 TK'),
(2, 2, 1, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Atikullah of 800 TK'),
(3, 3, 1, '2018-08-09 10:15:43', 'You have an order from Alif Hossain of 500 TK'),
(4, 4, 2, '2018-08-09 10:15:43', 'Sadik Hossain has rated your restaurant with 5 star'),
(5, 5, 1, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Yasir of 1000 TK'),
(6, 6, 1, '2018-08-09 10:15:43', 'Azmain Chowdhury has reviewed your menu Chimichanga'),
(7, 5, 1, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Yasir of 200 TK'),
(8, 3, 2, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Yasir of 800 TK'),
(9, 2, 1, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Yasir of 600 TK'),
(10, 7, 1, '2018-08-09 10:15:43', 'You have an order from Syed Mohammad Yasir of 340 TK');

-- --------------------------------------------------------

--
-- Table structure for table `rest_offered_food_group`
--

CREATE TABLE `rest_offered_food_group` (
  `rest_offer_group_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_offered_food_group`
--

INSERT INTO `rest_offered_food_group` (`rest_offer_group_id`, `food_id`) VALUES
(1, 6),
(1, 2),
(1, 1),
(2, 3),
(2, 7),
(3, 1),
(3, 3),
(3, 2),
(2, 10),
(1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `rest_offer_group`
--

CREATE TABLE `rest_offer_group` (
  `rest_offer_group_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_offer_group`
--

INSERT INTO `rest_offer_group` (`rest_offer_group_id`, `rest_id`) VALUES
(1, 2),
(2, 4),
(3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `rest_rating`
--

CREATE TABLE `rest_rating` (
  `rest_rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `rest_rating_value` enum('1','2','3','4','5') NOT NULL,
  `rating_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_rating`
--

INSERT INTO `rest_rating` (`rest_rating_id`, `user_id`, `rest_id`, `rest_rating_value`, `rating_date_time`) VALUES
(1, 2, 2, '3', '2018-08-21 12:20:00'),
(2, 2, 3, '3', '2018-08-14 08:27:00'),
(3, 6, 5, '2', '2018-08-06 11:15:00'),
(4, 6, 5, '3', '2018-08-08 13:23:00'),
(5, 6, 6, '5', '2018-08-02 14:22:00'),
(6, 1, 2, '5', '2018-07-11 09:22:00'),
(7, 2, 4, '3', '2018-07-29 11:23:00'),
(8, 9, 3, '5', '2018-07-22 00:00:00'),
(9, 2, 2, '4', '2018-07-20 10:26:00'),
(10, 8, 4, '4', '2018-07-19 15:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `rest_review`
--

CREATE TABLE `rest_review` (
  `rest_review_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_review`
--

INSERT INTO `rest_review` (`rest_review_id`, `review_id`, `rest_id`) VALUES
(1, 1, 7),
(2, 2, 7),
(3, 3, 4),
(4, 4, 3),
(5, 4, 4),
(6, 5, 3),
(7, 6, 2),
(8, 7, 6),
(9, 8, 5),
(10, 9, 5);

-- --------------------------------------------------------

--
-- Table structure for table `rest_schedule`
--

CREATE TABLE `rest_schedule` (
  `rest_id` int(11) NOT NULL,
  `day_id` int(11) NOT NULL,
  `day` char(10) NOT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rest_schedule`
--

INSERT INTO `rest_schedule` (`rest_id`, `day_id`, `day`, `opening_time`, `closing_time`, `created_at`, `updated_at`) VALUES
(1, 2, 'Monday', '09:00:00', '22:00:00', '2018-10-07 15:37:15', '2018-10-07 15:37:15'),
(1, 1, 'Sunday', '09:00:00', '22:00:00', '2018-10-07 15:37:15', '2018-10-07 15:37:15'),
(1, 5, 'Thursday', '09:00:00', '22:00:00', '2018-10-07 15:37:16', '2018-10-07 15:37:16'),
(1, 3, 'Tuesday', '12:00:00', '20:00:00', '2018-10-07 15:37:16', '2018-10-07 15:37:16'),
(1, 4, 'Wednesday', '09:00:00', '22:00:00', '2018-10-07 15:37:16', '2018-10-07 15:37:16'),
(2, 6, 'Friday', '09:00:00', '22:00:00', '2018-10-07 15:17:43', '2018-10-07 15:17:43'),
(2, 2, 'Monday', '09:00:00', '22:00:00', '2018-10-07 15:17:42', '2018-10-07 15:17:42'),
(2, 7, 'Saturday', '09:00:00', '22:00:00', '2018-10-07 15:17:43', '2018-10-07 15:17:43'),
(2, 1, 'Sunday', '09:00:00', '22:00:00', '2018-10-07 15:17:42', '2018-10-07 15:17:42'),
(2, 3, 'Tuesday', '12:00:00', '20:00:00', '2018-10-07 15:17:43', '2018-10-07 15:17:43'),
(2, 4, 'Wednesday', '09:00:00', '22:00:00', '2018-10-07 15:17:43', '2018-10-07 15:17:43'),
(3, 0, 'Friday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(3, 2, 'Monday', '09:00:00', '22:00:00', '2018-10-07 20:01:41', '0000-00-00 00:00:00'),
(3, 0, 'Saturday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(3, 0, 'Sunday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(3, 0, 'Thurday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(3, 0, 'Tuesday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(3, 0, 'Wednessd', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Friday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Monday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Saturday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Sunday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Thurday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Tuesday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(4, 0, 'Wednessd', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Friday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Monday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Saturday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Sunday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Thurday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Tuesday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(6, 0, 'Wednessd', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Friday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Monday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Saturday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Sunday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Thurday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Tuesday', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00'),
(7, 0, 'Wednessd', '09:00:00', '22:00:00', '2018-10-07 19:40:59', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `review_info`
--

CREATE TABLE `review_info` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `review_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `review_info`
--

INSERT INTO `review_info` (`review_id`, `user_id`, `review_text`, `review_date_time`) VALUES
(1, 1, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-08-07 04:17:00'),
(2, 2, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-08-22 10:36:17'),
(3, 8, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-06-25 00:00:00'),
(4, 8, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-07-28 10:45:00'),
(5, 6, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-07-27 00:00:00'),
(6, 6, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-07-15 06:33:00'),
(7, 2, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-07-23 09:36:00'),
(8, 2, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-06-13 18:33:00'),
(9, 1, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-07-19 12:18:00'),
(10, 8, 'Awesome, Outstanding. It was mind blowing. It is a testing purpose review. Don\'t need to be nervous after seeing this type of review. I know, it is quite awkward, but I need some long text to test. Al of the reviews are test data. These reviews will be removed after ending our testing phase. After that, you will get unique reviews.', '2018-08-23 04:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `review_reply`
--

CREATE TABLE `review_reply` (
  `review_reply_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rest_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `reply_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `review_reply`
--

INSERT INTO `review_reply` (`review_reply_id`, `review_id`, `user_id`, `rest_id`, `reply_text`, `reply_date_time`) VALUES
(1, 6, 8, 5, 'Thanks for your response, Sir. ', '2018-08-09 10:24:00'),
(2, 4, 2, 3, 'Thanks for your response, Sir. ', '2018-07-07 15:23:00'),
(3, 1, 9, 7, 'Thanks for your response, Sir. ', '2018-08-15 13:26:00'),
(4, 5, 2, 7, 'Thanks for your response, Sir. ', '2018-08-19 11:22:00'),
(5, 2, 7, 5, 'Thanks for your response, Sir. ', '2018-07-20 14:18:00'),
(6, 5, 9, 5, 'Thanks for your response, Sir. ', '2018-07-02 16:23:00'),
(7, 5, 8, 4, 'Thanks for your response, Sir. ', '2018-07-28 13:27:00'),
(8, 5, 6, 3, 'Thanks for your response, Sir. ', '2018-07-16 09:24:00'),
(9, 6, 6, 2, 'Thanks for your response, Sir. ', '2018-08-15 13:14:00'),
(10, 5, 1, 4, 'Thanks for your response, Sir. ', '2018-08-13 16:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'User'),
(2, 'Reaturant Manager');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status_name`) VALUES
(1, 'Active'),
(2, 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(11) NOT NULL,
  `fb_user_no` char(20) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `fb_profile_name` varchar(100) NOT NULL,
  `fb_profile_pic_url` varchar(100) NOT NULL,
  `contact_no` char(18) NOT NULL,
  `user_email` varchar(50) DEFAULT NULL,
  `user_bio` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `fb_user_no`, `role_id`, `status_id`, `fb_profile_name`, `fb_profile_pic_url`, `contact_no`, `user_email`, `user_bio`) VALUES
(1, '1000373428965165', 1, 1, 'Syed Mohammad Yasir', 'asset/fb_pp/1000373428965165.png', '01521485835', 'smy329@gmail.com', 'I am food lover'),
(2, '1004373468955165', 1, 1, 'Syed Mohammad Atikullah', 'asset/fb_pp/1004373468955165.png', '01521495835', 'smy329@gmail.com', 'I am food lover'),
(6, '1004373468973565', 1, 1, 'John De', 'asset/fb_pp/1004373468973565.png', '01521495035', 'smy329@gmail.com', 'I am food lover'),
(7, '1004373468243165', 1, 1, 'Sazzad Hossain', 'asset/fb_pp/1004373468243165.png', '01521295835', 'smy329@gmail.com', 'I am food lover'),
(8, '1004396068955165', 1, 1, 'Zishad', 'asset/fb_pp/1004396068955165.png', '01521495635', 'smy329@gmail.com', 'I am food lover'),
(9, '1091573468955165', 1, 1, 'fahim Shahriar', 'asset/fb_pp/1091573468955165.png', '01521498835', 'smy329@gmail.com', 'I am food lover');

-- --------------------------------------------------------

--
-- Table structure for table `user_notification`
--

CREATE TABLE `user_notification` (
  `user_notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type_id` int(11) NOT NULL,
  `user_notification_date_time` datetime NOT NULL,
  `user_notification_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_notification`
--

INSERT INTO `user_notification` (`user_notification_id`, `user_id`, `notification_type_id`, `user_notification_date_time`, `user_notification_text`) VALUES
(1, 7, 1, '2018-08-01 10:36:00', 'MD. Shipon poked you'),
(2, 2, 1, '2018-07-12 06:00:00', '40% discount is going on Pizza at Facefood restaurant.'),
(3, 2, 1, '2018-08-08 04:34:00', 'buy 1 get 1 offer is going on at Bell Pepper restaurant'),
(4, 6, 2, '2018-06-13 13:24:00', 'MD. Shipon poked you'),
(5, 6, 1, '2018-08-09 12:39:00', '40% discount is going on Pizza at Facefood restaurant.'),
(6, 9, 1, '2018-08-17 13:21:00', 'buy 1 get 1 offer is going on at Bell Pepper restaurant'),
(7, 6, 1, '2018-07-22 15:24:00', 'buy 1 get 1 offer is going on at Bell Pepper restaurant'),
(8, 6, 2, '2018-07-28 13:18:00', '40% discount is going on Pizza at Facefood restaurant.'),
(9, 1, 2, '2018-07-24 09:20:00', '40% discount is going on Pizza at Facefood restaurant.'),
(10, 7, 2, '2018-07-18 18:34:00', '40% discount is going on Pizza at Facefood restaurant.');

-- --------------------------------------------------------

--
-- Table structure for table `whiteboard_info`
--

CREATE TABLE `whiteboard_info` (
  `whiteboard_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_position` int(11) NOT NULL,
  `season_no` enum('1','2','3','4') NOT NULL,
  `year` char(6) NOT NULL,
  `season_rating_point` int(11) NOT NULL,
  `season_review_point` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `whiteboard_info`
--

INSERT INTO `whiteboard_info` (`whiteboard_id`, `user_id`, `user_position`, `season_no`, `year`, `season_rating_point`, `season_review_point`) VALUES
(1, 2, 5, '1', '2019', 12340, 340),
(2, 6, 10, '2', '2019', 3450, 1000),
(3, 8, 7, '4', '2018', 5470, 300),
(4, 1, 4, '4', '2018', 600, 600);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ad_package`
--
ALTER TABLE `ad_package`
  ADD PRIMARY KEY (`ad_package_id`);

--
-- Indexes for table `ad_package_price`
--
ALTER TABLE `ad_package_price`
  ADD PRIMARY KEY (`ad_package_price_id`);

--
-- Indexes for table `ad_payment`
--
ALTER TABLE `ad_payment`
  ADD PRIMARY KEY (`ad_payment_id`),
  ADD KEY `fk_ad_payment_payment_method_id` (`payment_method_id`);

--
-- Indexes for table `all_food`
--
ALTER TABLE `all_food`
  ADD PRIMARY KEY (`food_id`),
  ADD UNIQUE KEY `food_name` (`food_name`),
  ADD KEY `category_id` (`food_category_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`district_id`);

--
-- Indexes for table `food_bookmark`
--
ALTER TABLE `food_bookmark`
  ADD PRIMARY KEY (`food_bookmark_id`),
  ADD KEY `fk_food_bookmark_rest_id` (`rest_id`),
  ADD KEY `fk_food_bookmark_food_id` (`food_id`),
  ADD KEY `fk_food_bookmark_user_id` (`user_id`);

--
-- Indexes for table `food_category`
--
ALTER TABLE `food_category`
  ADD PRIMARY KEY (`food_category_id`),
  ADD UNIQUE KEY `food_category_name` (`food_category_name`);

--
-- Indexes for table `food_rating`
--
ALTER TABLE `food_rating`
  ADD PRIMARY KEY (`food_rating_id`),
  ADD KEY `fk_food_rating_user_id` (`user_id`),
  ADD KEY `fk_food_rating_rest_id` (`rest_id`),
  ADD KEY `fk_food_rating_food_id` (`food_id`);

--
-- Indexes for table `food_review`
--
ALTER TABLE `food_review`
  ADD PRIMARY KEY (`food_review_id`),
  ADD KEY `fk_food_review_review_id` (`review_id`),
  ADD KEY `fk_food_review_rest_id` (`rest_id`),
  ADD KEY `fk_food_review_food_id` (`food_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_ad_info`
--
ALTER TABLE `notification_ad_info`
  ADD PRIMARY KEY (`notification_ad_id`),
  ADD KEY `fk_notification_ad_info_ad_package_id` (`ad_package_id`),
  ADD KEY `fk_notification_ad_info_user_notification_id` (`user_notification_id`),
  ADD KEY `fk_notification_ad_info_ad_package_price_id` (`ad_package_price_id`);

--
-- Indexes for table `notification_ad_payment`
--
ALTER TABLE `notification_ad_payment`
  ADD KEY `fk_notification_ad_payment_user_notification_id` (`user_notification_id`),
  ADD KEY `fk_notification_ad_payment_notification_ad_id` (`notification_ad_id`),
  ADD KEY `fk_notification_ad_payment_ad_package_id` (`ad_package_id`),
  ADD KEY `fk_notification_ad_payment_ad_payment_id` (`ad_payment_id`);

--
-- Indexes for table `notification_type`
--
ALTER TABLE `notification_type`
  ADD PRIMARY KEY (`notification_type_id`);

--
-- Indexes for table `offer_ad_info`
--
ALTER TABLE `offer_ad_info`
  ADD PRIMARY KEY (`offer_ad_id`),
  ADD KEY `fk_offer_ad_info_ad_package_id` (`ad_package_id`),
  ADD KEY `fk_offer_ad_info_offer_id` (`offer_id`),
  ADD KEY `fk_offer_ad_info_ad_package_price_id` (`ad_package_price_id`);

--
-- Indexes for table `offer_ad_payment`
--
ALTER TABLE `offer_ad_payment`
  ADD KEY `fk_offer_ad_payment_offer_id` (`offer_id`),
  ADD KEY `fk_offer_ad_payment_offer_ad_id` (`offer_ad_id`),
  ADD KEY `fk_offer_ad_payment_ad_package_id` (`ad_package_id`),
  ADD KEY `fk_offer_ad_payment_ad_payment_id` (`ad_payment_id`);

--
-- Indexes for table `offer_info`
--
ALTER TABLE `offer_info`
  ADD PRIMARY KEY (`offer_id`),
  ADD KEY `fk_offer_info_offer_type_id` (`offer_type_id`),
  ADD KEY `fk_offer_info_rest_id` (`rest_id`),
  ADD KEY `fk_offer_info_food_id` (`food_id`);

--
-- Indexes for table `offer_type`
--
ALTER TABLE `offer_type`
  ADD PRIMARY KEY (`offer_type_id`);

--
-- Indexes for table `order_info`
--
ALTER TABLE `order_info`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_info_food_id` (`food_id`),
  ADD KEY `fk_order_info_user_id` (`user_id`);

--
-- Indexes for table `order_payment`
--
ALTER TABLE `order_payment`
  ADD PRIMARY KEY (`order_payment_id`),
  ADD KEY `fk_order_payment_order_id` (`order_id`),
  ADD KEY `fk_order_payment_payment_method_id` (`payment_method_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `rest_ad_info`
--
ALTER TABLE `rest_ad_info`
  ADD PRIMARY KEY (`rest_ad_id`),
  ADD KEY `fk_rest_ad_info_ad_package_id` (`ad_package_id`),
  ADD KEY `fk_rest_ad_info_rest_id` (`rest_id`),
  ADD KEY `fk_rest_ad_info_ad_package_price_id` (`ad_package_price_id`);

--
-- Indexes for table `rest_ad_payment`
--
ALTER TABLE `rest_ad_payment`
  ADD KEY `fk_rest_ad_payment_rest_id` (`rest_id`),
  ADD KEY `fk_rest_ad_payment_rest_ad_id` (`rest_ad_id`),
  ADD KEY `fk_rest_ad_payment_ad_package_id` (`ad_package_id`),
  ADD KEY `fk_rest_ad_payment_ad_payment_id` (`ad_payment_id`);

--
-- Indexes for table `rest_bookmark`
--
ALTER TABLE `rest_bookmark`
  ADD PRIMARY KEY (`rest_bookmark_id`),
  ADD KEY `fk_rest_bookmark_rest_id` (`rest_id`),
  ADD KEY `fk_rest_bookmark_user_id` (`user_id`);

--
-- Indexes for table `rest_contact`
--
ALTER TABLE `rest_contact`
  ADD KEY `fk_rest_contact_rest_id` (`rest_id`);

--
-- Indexes for table `rest_facility`
--
ALTER TABLE `rest_facility`
  ADD PRIMARY KEY (`rest_facility_id`),
  ADD KEY `fk_rest_facility_rest_id` (`rest_id`);

--
-- Indexes for table `rest_food`
--
ALTER TABLE `rest_food`
  ADD UNIQUE KEY `uk_rest_food_rest_id_food_id` (`rest_id`,`food_id`),
  ADD KEY `fk_rest_food_food_id` (`food_id`);

--
-- Indexes for table `rest_food_discount`
--
ALTER TABLE `rest_food_discount`
  ADD KEY `fk_rest_food_discount_rest_id` (`rest_id`),
  ADD KEY `fk_rest_food_discount_food_id` (`food_id`);

--
-- Indexes for table `rest_info`
--
ALTER TABLE `rest_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rest_info_city_id` (`city_id`),
  ADD KEY `fk_rest_info_district_id` (`district_id`);

--
-- Indexes for table `rest_notification`
--
ALTER TABLE `rest_notification`
  ADD PRIMARY KEY (`rest_notification_id`),
  ADD KEY `fk_rest_notification_rest_id` (`rest_id`),
  ADD KEY `fk_rest_notification_notification_type_id` (`notification_type_id`);

--
-- Indexes for table `rest_offered_food_group`
--
ALTER TABLE `rest_offered_food_group`
  ADD KEY `rest_offered_food_group_rest_id` (`rest_offer_group_id`),
  ADD KEY `fk_rest_offered_food_group_food_id` (`food_id`);

--
-- Indexes for table `rest_offer_group`
--
ALTER TABLE `rest_offer_group`
  ADD PRIMARY KEY (`rest_offer_group_id`),
  ADD UNIQUE KEY `uk_rest_offer_group_rest_offer_group_id_rest_id` (`rest_offer_group_id`,`rest_id`),
  ADD KEY `fk_rest_offer_group_rest_id` (`rest_id`);

--
-- Indexes for table `rest_rating`
--
ALTER TABLE `rest_rating`
  ADD PRIMARY KEY (`rest_rating_id`),
  ADD KEY `fk_rest_rating_user_id` (`user_id`),
  ADD KEY `fk_rest_rating_rest_id` (`rest_id`);

--
-- Indexes for table `rest_review`
--
ALTER TABLE `rest_review`
  ADD PRIMARY KEY (`rest_review_id`),
  ADD KEY `fk_rest_review_review_id` (`review_id`),
  ADD KEY `fk_rest_review_rest_id` (`rest_id`);

--
-- Indexes for table `rest_schedule`
--
ALTER TABLE `rest_schedule`
  ADD UNIQUE KEY `uk_rest_schedule_rest_id_day_opening_time` (`rest_id`,`day`,`opening_time`);

--
-- Indexes for table `review_info`
--
ALTER TABLE `review_info`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_review_info_user_id` (`user_id`);

--
-- Indexes for table `review_reply`
--
ALTER TABLE `review_reply`
  ADD PRIMARY KEY (`review_reply_id`),
  ADD KEY `fk_review_reply_review_id` (`review_id`),
  ADD KEY `fk_review_reply_user_id` (`user_id`),
  ADD KEY `fk_review_reply_rest_id` (`rest_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uk_user_info_contact_no` (`contact_no`),
  ADD KEY `fk_user_info_role_id` (`role_id`),
  ADD KEY `fk_user_info_status_id` (`status_id`);

--
-- Indexes for table `user_notification`
--
ALTER TABLE `user_notification`
  ADD PRIMARY KEY (`user_notification_id`),
  ADD KEY `fk_user_notification_user_id` (`user_id`),
  ADD KEY `fk_user_notification_notification_type_id` (`notification_type_id`);

--
-- Indexes for table `whiteboard_info`
--
ALTER TABLE `whiteboard_info`
  ADD PRIMARY KEY (`whiteboard_id`),
  ADD UNIQUE KEY `uk_whiteboard_info_year_season_no_user_position` (`user_position`,`season_no`,`year`),
  ADD KEY `fk_whiteboard_info_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ad_payment`
--
ALTER TABLE `ad_payment`
  MODIFY `ad_payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `all_food`
--
ALTER TABLE `all_food`
  MODIFY `food_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `food_bookmark`
--
ALTER TABLE `food_bookmark`
  MODIFY `food_bookmark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `food_category`
--
ALTER TABLE `food_category`
  MODIFY `food_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `food_rating`
--
ALTER TABLE `food_rating`
  MODIFY `food_rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `food_review`
--
ALTER TABLE `food_review`
  MODIFY `food_review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_ad_info`
--
ALTER TABLE `notification_ad_info`
  MODIFY `notification_ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `offer_ad_info`
--
ALTER TABLE `offer_ad_info`
  MODIFY `offer_ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `offer_info`
--
ALTER TABLE `offer_info`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_info`
--
ALTER TABLE `order_info`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_payment`
--
ALTER TABLE `order_payment`
  MODIFY `order_payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rest_ad_info`
--
ALTER TABLE `rest_ad_info`
  MODIFY `rest_ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rest_bookmark`
--
ALTER TABLE `rest_bookmark`
  MODIFY `rest_bookmark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rest_facility`
--
ALTER TABLE `rest_facility`
  MODIFY `rest_facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `rest_info`
--
ALTER TABLE `rest_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rest_notification`
--
ALTER TABLE `rest_notification`
  MODIFY `rest_notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rest_offer_group`
--
ALTER TABLE `rest_offer_group`
  MODIFY `rest_offer_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rest_rating`
--
ALTER TABLE `rest_rating`
  MODIFY `rest_rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rest_review`
--
ALTER TABLE `rest_review`
  MODIFY `rest_review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `review_info`
--
ALTER TABLE `review_info`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `review_reply`
--
ALTER TABLE `review_reply`
  MODIFY `review_reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_notification`
--
ALTER TABLE `user_notification`
  MODIFY `user_notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `whiteboard_info`
--
ALTER TABLE `whiteboard_info`
  MODIFY `whiteboard_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ad_payment`
--
ALTER TABLE `ad_payment`
  ADD CONSTRAINT `fk_ad_payment_payment_method_id` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON UPDATE CASCADE;

--
-- Constraints for table `all_food`
--
ALTER TABLE `all_food`
  ADD CONSTRAINT `all_food_ibfk_1` FOREIGN KEY (`food_category_id`) REFERENCES `food_category` (`food_category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `food_bookmark`
--
ALTER TABLE `food_bookmark`
  ADD CONSTRAINT `fk_food_bookmark_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_food_bookmark_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_food_bookmark_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `food_rating`
--
ALTER TABLE `food_rating`
  ADD CONSTRAINT `fk_food_rating_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_food_rating_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_food_rating_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `food_review`
--
ALTER TABLE `food_review`
  ADD CONSTRAINT `fk_food_review_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_food_review_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_food_review_review_id` FOREIGN KEY (`review_id`) REFERENCES `review_info` (`review_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notification_ad_info`
--
ALTER TABLE `notification_ad_info`
  ADD CONSTRAINT `fk_notification_ad_info_ad_package_id` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_package` (`ad_package_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_ad_info_ad_package_price_id` FOREIGN KEY (`ad_package_price_id`) REFERENCES `ad_package_price` (`ad_package_price_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_ad_info_user_notification_id` FOREIGN KEY (`user_notification_id`) REFERENCES `user_notification` (`user_notification_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notification_ad_payment`
--
ALTER TABLE `notification_ad_payment`
  ADD CONSTRAINT `fk_notification_ad_payment_ad_package_id` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_package` (`ad_package_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_ad_payment_ad_payment_id` FOREIGN KEY (`ad_payment_id`) REFERENCES `ad_payment` (`ad_payment_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_ad_payment_notification_ad_id` FOREIGN KEY (`notification_ad_id`) REFERENCES `notification_ad_info` (`notification_ad_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_ad_payment_user_notification_id` FOREIGN KEY (`user_notification_id`) REFERENCES `user_notification` (`user_notification_id`) ON UPDATE CASCADE;

--
-- Constraints for table `offer_ad_info`
--
ALTER TABLE `offer_ad_info`
  ADD CONSTRAINT `fk_offer_ad_info_ad_package_id` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_package` (`ad_package_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_ad_info_ad_package_price_id` FOREIGN KEY (`ad_package_price_id`) REFERENCES `ad_package_price` (`ad_package_price_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_ad_info_offer_id` FOREIGN KEY (`offer_id`) REFERENCES `offer_info` (`offer_id`) ON UPDATE CASCADE;

--
-- Constraints for table `offer_ad_payment`
--
ALTER TABLE `offer_ad_payment`
  ADD CONSTRAINT `fk_offer_ad_payment_ad_package_id` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_package` (`ad_package_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_ad_payment_ad_payment_id` FOREIGN KEY (`ad_payment_id`) REFERENCES `ad_payment` (`ad_payment_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_ad_payment_offer_ad_id` FOREIGN KEY (`offer_ad_id`) REFERENCES `offer_ad_info` (`offer_ad_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_ad_payment_offer_id` FOREIGN KEY (`offer_id`) REFERENCES `offer_info` (`offer_id`) ON UPDATE CASCADE;

--
-- Constraints for table `offer_info`
--
ALTER TABLE `offer_info`
  ADD CONSTRAINT `fk_offer_info_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_info_offer_type_id` FOREIGN KEY (`offer_type_id`) REFERENCES `offer_type` (`offer_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offer_info_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_info`
--
ALTER TABLE `order_info`
  ADD CONSTRAINT `fk_order_info_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_payment`
--
ALTER TABLE `order_payment`
  ADD CONSTRAINT `fk_order_payment_order_id` FOREIGN KEY (`order_id`) REFERENCES `order_info` (`order_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_payment_payment_method_id` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_ad_info`
--
ALTER TABLE `rest_ad_info`
  ADD CONSTRAINT `fk_rest_ad_info_ad_package_id` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_package` (`ad_package_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_ad_info_ad_package_price_id` FOREIGN KEY (`ad_package_price_id`) REFERENCES `ad_package_price` (`ad_package_price_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_ad_info_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_ad_payment`
--
ALTER TABLE `rest_ad_payment`
  ADD CONSTRAINT `fk_rest_ad_payment_ad_package_id` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_package` (`ad_package_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_ad_payment_ad_payment_id` FOREIGN KEY (`ad_payment_id`) REFERENCES `ad_payment` (`ad_payment_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_ad_payment_rest_ad_id` FOREIGN KEY (`rest_ad_id`) REFERENCES `rest_ad_info` (`rest_ad_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_ad_payment_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_bookmark`
--
ALTER TABLE `rest_bookmark`
  ADD CONSTRAINT `fk_rest_bookmark_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_bookmark_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_contact`
--
ALTER TABLE `rest_contact`
  ADD CONSTRAINT `fk_rest_contact_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_facility`
--
ALTER TABLE `rest_facility`
  ADD CONSTRAINT `fk_rest_facility_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_food`
--
ALTER TABLE `rest_food`
  ADD CONSTRAINT `fk_rest_food_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_food_discount`
--
ALTER TABLE `rest_food_discount`
  ADD CONSTRAINT `fk_rest_food_discount_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_food_discount_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_info`
--
ALTER TABLE `rest_info`
  ADD CONSTRAINT `fk_rest_info_city_id` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_info_district_id` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_notification`
--
ALTER TABLE `rest_notification`
  ADD CONSTRAINT `fk_rest_notification_notification_type_id` FOREIGN KEY (`notification_type_id`) REFERENCES `notification_type` (`notification_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_notification_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_offered_food_group`
--
ALTER TABLE `rest_offered_food_group`
  ADD CONSTRAINT `fk_rest_offered_food_group_food_id` FOREIGN KEY (`food_id`) REFERENCES `food` (`food_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rest_offered_food_group_rest_id` FOREIGN KEY (`rest_offer_group_id`) REFERENCES `rest_offer_group` (`rest_offer_group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_offer_group`
--
ALTER TABLE `rest_offer_group`
  ADD CONSTRAINT `fk_rest_offer_group_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_rating`
--
ALTER TABLE `rest_rating`
  ADD CONSTRAINT `fk_rest_rating_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_rating_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_review`
--
ALTER TABLE `rest_review`
  ADD CONSTRAINT `fk_rest_review_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rest_review_review_id` FOREIGN KEY (`review_id`) REFERENCES `review_info` (`review_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rest_schedule`
--
ALTER TABLE `rest_schedule`
  ADD CONSTRAINT `fk_rest_schedule_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `review_info`
--
ALTER TABLE `review_info`
  ADD CONSTRAINT `fk_review_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `review_reply`
--
ALTER TABLE `review_reply`
  ADD CONSTRAINT `fk_review_reply_rest_id` FOREIGN KEY (`rest_id`) REFERENCES `rest_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_review_reply_review_id` FOREIGN KEY (`review_id`) REFERENCES `review_info` (`review_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_review_reply_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `fk_user_info_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_info_status_id` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_notification`
--
ALTER TABLE `user_notification`
  ADD CONSTRAINT `fk_user_notification_notification_type_id` FOREIGN KEY (`notification_type_id`) REFERENCES `notification_type` (`notification_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_notification_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `whiteboard_info`
--
ALTER TABLE `whiteboard_info`
  ADD CONSTRAINT `fk_whiteboard_info_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
