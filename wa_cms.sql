-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2015 at 07:07 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wa_cms`
--
CREATE DATABASE IF NOT EXISTS `wa_cms` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_520_ci;
USE `wa_cms`;

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
CREATE TABLE IF NOT EXISTS `author` (
  `id` int(3) NOT NULL DEFAULT '0',
  `screenname` varchar(20) NOT NULL,
  `role` enum('author','editor','admin') DEFAULT 'author',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE IF NOT EXISTS `document` (
  `guid` int(4) NOT NULL AUTO_INCREMENT,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `public` tinyint(1) NOT NULL,
  `recent_update` timestamp NULL DEFAULT NULL,
  `author_id` int(3) NOT NULL,
  `editor_id` int(3) DEFAULT NULL,
  PRIMARY KEY (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `version`;
CREATE TABLE IF NOT EXISTS `version` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `document_guid` int(4) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `body` varchar(1000) NOT NULL,
  `author_id` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
