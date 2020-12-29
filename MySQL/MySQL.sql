-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 08, 2013 at 03:59 AM
-- Server version: 5.5.25a
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `empty`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) COLLATE utf8_romanian_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_romanian_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_romanian_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'a5ff20e027ef3106d327a99321fb0313');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `uid` int(32) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `mentions` varchar(256) CHARACTER SET latin1 NOT NULL,
  `tag` varchar(256) CHARACTER SET latin1 NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `media` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `video` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `private`
--

CREATE TABLE IF NOT EXISTS `private` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `read` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `relations`
--

CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leader` int(11) NOT NULL,
  `follower` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `idm` int(32) NOT NULL,
  `user` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `title` varchar(64) NOT NULL,
  `perpage` int(11) NOT NULL,
  `ad1` varchar(2048) NOT NULL,
  `ad2` varchar(2048) NOT NULL,
  `censor` varchar(2048) CHARACTER SET utf8 NOT NULL,
  `captcha` int(11) NOT NULL,
  `public` varchar(128) NOT NULL,
  `private` varchar(128) NOT NULL,
  `inter` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `message` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `format` varchar(256) NOT NULL,
  `mail` int(11) NOT NULL,
  `sizemsg` int(11) NOT NULL,
  `formatmsg` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`title`, `perpage`, `ad1`, `ad2`, `censor`, `captcha`, `public`, `private`, `inter`, `time`, `message`, `size`, `format`, `mail`, `sizemsg`, `formatmsg`) VALUES
('Social Network', 10, '', '', '', 0, '6LeNytoSAAAAAF0jGToTMu9FvBoVOFd-qPlEJrJO', '6LeNytoSAAAAADbYBAasIC-UTq5uKj1b_sfxqS8s', 10000, 1, 140, 1048576, 'png,jpg,gif', 1, 1048576, 'png,jpg,gif,bmp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `idu` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(128) NOT NULL,
  `bio` varchar(160) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `facebook` varchar(256) NOT NULL,
  `twitter` varchar(128) NOT NULL,
  `gplus` varchar(256) NOT NULL,
  `image` varchar(128) NOT NULL,
  `private` int(11) NOT NULL,
  `salted` varchar(256) NOT NULL,
  `background` varchar(256) NOT NULL,
  UNIQUE KEY `id` (`idu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
