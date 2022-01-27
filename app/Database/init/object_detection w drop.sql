-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2022 at 12:21 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `object_detection`
--
CREATE DATABASE IF NOT EXISTS `object_detection` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `object_detection`;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
    `id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `price`) VALUES
(0, 'Muendungsabschluss', '99.00'),
(1, 'Regenhaube', '129.99'),
(2, 'Dachdurchfuehrung_gerade', '399.99'),
(3, 'Dachdurchfuehrung_geneigt', '459.99'),
(4, 'Wandhalterung', '29.99'),
(5, 'Wanddurchfuehrung', '429.99'),
(6, 'Reinigungsoeffnung', '89.99'),
(7, 'Bodenmontage', '239.99'),
(8, 'Abschluss_Konsole', '219.99');

-- --------------------------------------------------------

--
-- Table structure for table `hitboxes`
--

DROP TABLE IF EXISTS `hitboxes`;
CREATE TABLE IF NOT EXISTS `hitboxes` (
    `box_id` int(11) NOT NULL AUTO_INCREMENT,
    `id` int(11) NOT NULL,
    `class` int(11) NOT NULL DEFAULT 0,
    `x` float NOT NULL DEFAULT 0,
    `y` float NOT NULL DEFAULT 0,
    `w` float NOT NULL DEFAULT 0,
    `h` float NOT NULL DEFAULT 0,
    PRIMARY KEY (`box_id`),
    KEY `id` (`id`),
    KEY `class` (`class`)
    ) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE IF NOT EXISTS `pictures` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    `path` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hitboxes`
--
ALTER TABLE `hitboxes`
    ADD CONSTRAINT `cl_fk` FOREIGN KEY (`class`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hb_fk` FOREIGN KEY (`id`) REFERENCES `pictures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*
GRANT USAGE ON *.* TO `ci`@`%`;

GRANT SELECT, INSERT, UPDATE, DELETE ON `object_detection`.* TO `ci`@`%`;

GRANT USAGE ON *.* TO `python`@`%`;

GRANT SELECT, INSERT, UPDATE, DELETE ON `object_detection`.* TO `python`@`%`;
*/

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 */
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
