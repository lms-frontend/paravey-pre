-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2015 at 09:26 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `speech_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `lang_id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_name` varchar(255) DEFAULT NULL,
  `lang_code` char(255) DEFAULT NULL,
  `lang_locale` varchar(255) DEFAULT NULL,
  `lang_sort_order` tinyint(4) DEFAULT '0',
  `lang_status` tinyint(4) DEFAULT '1',
  `lang_deleted` tinyint(4) DEFAULT '0',
  `lang_created` datetime DEFAULT NULL,
  `lang_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`lang_id`, `lang_name`, `lang_code`, `lang_locale`, `lang_sort_order`, `lang_status`, `lang_deleted`, `lang_created`, `lang_updated`) VALUES
(1, 'English', 'en', NULL, 0, 1, 0, NULL, NULL),
(2, 'Francis', 'fr', NULL, 0, 1, 0, NULL, NULL),
(3, 'Italian', 'it', NULL, 0, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_detail`
--

CREATE TABLE IF NOT EXISTS `user_detail` (
  `ud_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ur_id` int(11) DEFAULT NULL,
  `fk_language_id` int(11) DEFAULT NULL,
  `ud_first_name` varchar(255) DEFAULT NULL,
  `ud_last_name` varchar(255) DEFAULT NULL,
  `ud_email` varchar(255) DEFAULT NULL,
  `ud_password` varchar(255) DEFAULT NULL,
  `ud_profile_photo` varchar(255) DEFAULT NULL,
  `ud_device_token` text,
  `ud_status` tinyint(4) DEFAULT '1',
  `ud_deleted` tinyint(4) DEFAULT '0',
  `ud_created` datetime DEFAULT NULL,
  `ud_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`ud_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `user_detail`
--

INSERT INTO `user_detail` (`ud_id`, `fk_ur_id`, `fk_language_id`, `ud_first_name`, `ud_last_name`, `ud_email`, `ud_password`, `ud_profile_photo`, `ud_device_token`, `ud_status`, `ud_deleted`, `ud_created`, `ud_updated`) VALUES
(1, 1, NULL, 'Admin', 'User', 'admin@gmail.com', '123', NULL, NULL, 1, 0, NULL, NULL),
(2, 2, NULL, 'huzefa', 'ratlamwala', 'user1@gmail.com', '123', NULL, NULL, 1, 0, '2015-09-16 08:31:53', NULL),
(3, 2, NULL, 'rahul', 'sharma', 'user2@gmail.com', '123', NULL, NULL, 1, 0, '2015-09-16 08:34:48', NULL),
(4, 2, NULL, 'ankit', 'jain', 'user3@gmail.com', '123', NULL, NULL, 1, 0, '2015-09-16 10:49:35', NULL),
(6, 2, 1, 'amit', 'jain', 'user5@gmail.com', '123', NULL, NULL, 1, 0, '2015-09-22 08:21:50', NULL),
(9, 2, 1, 'navneet', 'roy', 'huzefa.ratlamwala@lmsin.com', '123', NULL, '', 1, 0, '2015-09-29 08:48:37', NULL),
(17, 2, 0, 'vishal', 'sharma', 'user10@gmail.com', '123', 'profile_photo_144361281617.jpg', NULL, 1, 0, '2015-09-30 13:33:36', NULL),
(18, 2, 0, 'Alok', 'Tiwari', 'alok.tiwari@gmail.com', '123456', 'profile_photo_144361442418.jpg', NULL, 1, 0, '2015-09-30 14:00:24', NULL),
(19, 2, 0, 'Test', 'test', 'test@yest.com', 'ggg', NULL, NULL, 1, 0, '2015-09-30 14:17:41', NULL),
(23, 2, 0, 'piyush', 'sharma', 'user11@gmail.com', '123', 'profile_photo_144361615623.jpg', NULL, 1, 0, '2015-09-30 14:29:16', NULL),
(24, 2, 0, 'piyush', 'sharma', 'user12@gmail.com', '123', NULL, NULL, 1, 0, '2015-09-30 14:29:39', NULL),
(25, 2, 0, 'Alok', 'Tiwari', 'alok@kumar.com', '123456', 'profile_photo_144369727125.jpg', NULL, 1, 0, '2015-10-01 13:01:11', NULL),
(26, 2, 0, 'Julius', 'Omo ', 'orogun@live.com', 'victory890', NULL, '', 1, 0, '2015-10-03 03:57:19', NULL),
(27, 2, 0, 'Aditya', 'shukla', 'adi@adi.com', '123456', 'profile_photo_144411226727.jpg', 'APA91bGgXSUyMk0i-pBjeOcX1TgOmqWjjtMJafNUgfYl3_lH8Teugh_EpLg9DxKBvGULTAxoTE8rVK1Hsh8BztbfnKSg0yiXSI9gz9JSAdN8QNdfVbVaffJUkI9Z4cGguPYW3OsYOCLz', 1, 0, '2015-10-06 08:17:47', NULL),
(28, 2, 0, 'nirmala', 'dhakad', 'nirmala.dhakad@lmsin.com', 'test123', 'profile_photo_144413053428.jpg', 'APA91bHEsiHnL58JjtxESZPUtZhtj5vvs6cEIvLq9jBc8TUq_WF695kOxKYh3XDsxaM52A-_0-_jnWs5Ir-wPht1Z1Oi5-P9KQirut1RLHiZTDPq-NVQj4Xjeo2fwr2UjQyYxbAj3fyI', 1, 0, '2015-10-06 13:22:14', NULL),
(29, 2, 0, 'LMS', 'Solutions', 'lms@lmsin.com', 'test123', 'profile_photo_144413532529.jpg', 'APA91bHB0_41_DJ7yDTzmufOkMfpidYhUTyGDI7TVQTSWrkv9cBuRV20BLh0TqB428ndWkVhiZEP49sNLe5W8GhNIw_RsOZ5IcFNz5wZJffNxUl87fiNtR-OlJd8Xfi1PP1jrusKK1UO', 1, 0, '2015-10-06 14:42:05', NULL),
(30, 2, 0, 'Kay', 'Oloke', 'kayode@oloke.com', 'kaycee000', NULL, '', 1, 0, '2015-10-06 21:07:31', NULL),
(31, 2, 0, 'Julius', 'Omoni ', 'Julius1o@yahoo.com', 'victory90-', NULL, 'APA91bFw6Nuq1hUT95pxEghk_245rFRvovEwjPGxowz3sU6Gy9nW9iDpNbBAILwamn_xSDvmosGF6Wg7eok1R8T9u1qiXjgP3nUdSrLDXDHUEOqhfswHR57ViW4_Qrer4kCT6AiaggZS', 1, 0, '2015-10-07 04:39:17', NULL),
(32, 2, 0, 'Alok', 'Tiwari', 'alok.tiwari@lmsin.com', '123456', NULL, 'APA91bGdz6_zmp9LQ-ga5uiSdcPKxwaPNBWNfQSxTWstmKvudNKbX6zHN5X4jQNXE_imRTyWU2Lzn-SqdiCo0t25vqx-9AuaF3g7dDD3krkdeM9-YZjTdWVza_OR4TcdzLD5Vz2dRkuY', 1, 0, '2015-10-07 09:14:34', NULL),
(33, 2, 0, 'William', 'Adeniran', 'willy242010@gmail.com', 'joshuachee24', NULL, 'APA91bF9fbilvaE4rVtia4HKIGPM8qNY_ltVkCDcaf_hNn-akYqw8g8TC0CXlborSbjIRl8f9aVv8aZ5kfcvw-cLplF5NDt_x3uSm14Ro_TGbMI6KRNj8f1_TL-bxlquYo0zye1P2G3E', 1, 0, '2015-10-08 00:00:40', NULL),
(34, 2, 0, 'test', 'user', 'test@lmsin.com', 'test123', NULL, 'APA91bE10ZEmBDG8bc2esgq7nlkQSWVLRxJGCg7qkQ1o5pIUNv2tJCQ4xqzK3IHTBxN8zhrE5wsQOSmmIMrjLd-IbPMcb7qdAJ5qjjHU4dwBKaDPjNo00FJ4VYmLYAycX9_tVuzbZ9-q', 1, 0, '2015-10-08 08:47:14', NULL),
(35, 2, 0, 'dhakad', 'nirmala', 'dhakad@lmsin.com', 'test123', NULL, 'APA91bGMZ0cobXYs6f1f1vrZo6oMQWHF2WIDT9tBkv2XmTNv2FwLjQeyiwNqq4_UvrceELbNMNncGnAeO3PxNzDDTLUsjtJdk8WgODe2taTXpSyyupa6TSnv6qLzKdlqt_DFJ83IO--D', 1, 0, '2015-10-08 14:00:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `ur_id` int(11) NOT NULL AUTO_INCREMENT,
  `ur_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ur_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`ur_id`, `ur_name`) VALUES
(1, 'admin'),
(2, 'user');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
