-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 20, 2020 at 09:56 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jbrown88`
--

-- --------------------------------------------------------

--
-- Table structure for table `webdev_appointments`
--

CREATE TABLE `webdev_appointments` (
  `id` int(11) NOT NULL,
  `coach_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` varchar(5) NOT NULL DEFAULT current_timestamp(),
  `duration` varchar(10) NOT NULL,
  `details` varchar(100) NOT NULL,
  `confirmed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_appointments`
--

INSERT INTO `webdev_appointments` (`id`, `coach_id`, `user_id`, `group_id`, `date`, `time`, `duration`, `details`, `confirmed`) VALUES
(168, 2, NULL, 2, '2020-04-18', '11:00', '90 Minutes', 'sfsef', 1),
(170, 2, NULL, 2, '2020-04-18', '10:30', '30 Minutes', 'addadad', 1),
(171, 2, NULL, 2, '2020-04-18', '10:30', '30 Minutes', 'ff', 1),
(184, 2, 101, NULL, '2020-04-17', '10:30', '30 Minutes', 'fffff', 1),
(185, 2, 139, NULL, '2020-04-17', '10:30', '30 Minutes', 'aaaaaa', 1),
(191, 2, 103, NULL, '2020-04-18', '10:30', '30 Minutes', 'trying to make once i changed stuff', 1),
(192, 2, 103, NULL, '2020-04-18', '10:30', '30 Minutes', 'trying to make since i changed stuff', 1),
(193, 2, NULL, 2, '2020-04-17', '11:30', '30 Minutes', 'new group booking with new code', 1),
(194, 2, 103, NULL, '2020-04-18', '10:00', '30 Minutes', 'trying to create with inbox stuff', 1),
(195, 2, 103, NULL, '2020-04-18', '10:00', '30 Minutes', 'trying to create with inbox stuff', 1),
(203, 2, NULL, 2, '2020-04-17', '11:00', '30 Minutes', 'inbox group', 1),
(209, 2, 103, NULL, '2020-04-16', '11:00', '30 Minutes', 'test', 1),
(211, 2, 103, NULL, '2020-04-16', '11:00', '30 Minutes', 'aaad', 1),
(218, 2, 103, NULL, '2020-04-24', '11:00', '30 Minutes', 'bbb', 1),
(220, 2, 103, NULL, '2020-04-27', '11:00', '30 Minutes', 'test', 1),
(221, 2, 103, NULL, '2020-04-27', '11:00', '30 Minutes', 'test', 1),
(232, 2, 103, NULL, '2020-04-23', '19:00', '30 Minutes', 'hhh', 1),
(233, 2, 103, NULL, '2020-04-25', '20:00', '90 Minutes', 'test', 1),
(234, 2, 103, NULL, '2020-04-25', '20:00', '90 Minutes', 'test', 1),
(235, 2, 103, NULL, '2020-04-25', '19:30', '30 Minutes', 'test', 1),
(237, 2, 103, NULL, '2020-04-27', '20:00', '90 Minutes', 'I want to train', 1),
(239, 2, 103, NULL, '2020-04-26', '20:00', '30 Minutes', 'I want to train', 1);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_appointments_logs`
--

CREATE TABLE `webdev_appointments_logs` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `user_comments` varchar(100) NOT NULL,
  `user_rating` int(11) NOT NULL,
  `coach_comments` varchar(100) DEFAULT NULL,
  `coach_rating` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_appointments_logs`
--

INSERT INTO `webdev_appointments_logs` (`id`, `appointment_id`, `user_comments`, `user_rating`, `coach_comments`, `coach_rating`) VALUES
(25, 195, 'test', 5, 'aa', 5),
(26, 209, 'Thought the routine went well, proud of what I achieved.', 4, NULL, 0),
(28, 194, 'Thought the routine went well, proud of what I achieved.', 4, 'well done!', 5),
(29, 191, 'make to delete', 3, 'bit of slacking today, double work next time', 2);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_coach`
--

CREATE TABLE `webdev_coach` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `area` varchar(75) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `img_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_coach`
--

INSERT INTO `webdev_coach` (`id`, `name`, `area`, `user_id`, `image`, `img_description`) VALUES
(1, 'Jessica Kim', 'Life Coach or Fitness', 1, 'jessica.jpg', 'Jessica, only child. From Illinois Chicago.'),
(2, 'Kevin Kim', 'Personal Trainer', 2, 'kevin.jpg', 'Kevin, 32, fitness coach.'),
(3, 'Moon-Gwang Gook', 'Nutritionist', 3, 'moon_gwang.jpg', 'Moon-Gwang, 62, health coach with 35 years industry experience.');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_groups`
--

CREATE TABLE `webdev_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `description` varchar(100) NOT NULL,
  `coach` int(11) NOT NULL,
  `member_one` int(11) NOT NULL,
  `member_two` int(11) NOT NULL,
  `member_three` int(11) DEFAULT NULL,
  `member_four` int(11) DEFAULT NULL,
  `first_session` varchar(100) DEFAULT NULL,
  `second_session` varchar(100) DEFAULT NULL,
  `third_session` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_groups`
--

INSERT INTO `webdev_groups` (`id`, `group_name`, `description`, `coach`, `member_one`, `member_two`, `member_three`, `member_four`, `first_session`, `second_session`, `third_session`) VALUES
(1, 'Cycle Club', 'a', 2, 144, 0, 101, 103, 'c', 'b', 'a'),
(2, 'Cardio Club', '', 2, 0, 0, 126, 127, '', '', ''),
(23, 'sanitisedGroupName', 'sanitisedGroupName', 2, 144, 103, 133, 129, 'sanitisedGroupName', 'sanitisedGroupName', 'sanitisedGroupName');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_images`
--

CREATE TABLE `webdev_images` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_images`
--

INSERT INTO `webdev_images` (`id`, `description`, `path`, `user_id`) VALUES
(34, 'yummy', 'food_1.jpg', 103),
(37, 'home cooked', 'food_5.png', 103),
(39, '25th birthday ', 'food_6.png', 103),
(57, 'me', 'jeff.jpg', 144),
(59, '', 'spock.jpg', 144),
(61, '', 'resident sleeper.jpg', 144),
(64, '', 'Capture.PNG', 144),
(66, '', 'wp2096954.jpg', 144),
(67, '', 'wp2096954.jpg', 144),
(70, 'sal', 'sal.png', 103),
(98, 'Picture of Jessica Kim', 'jessica.jpg', 99),
(99, 'Picture of Kevin', 'kevin.jpg', 99),
(100, 'Picture of MoonGwang', 'moon_gwang.jpg', 99);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_inbox`
--

CREATE TABLE `webdev_inbox` (
  `id` int(11) NOT NULL,
  `recipient` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `subject` varchar(65) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `coach` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `hide` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_inbox`
--

INSERT INTO `webdev_inbox` (`id`, `recipient`, `sender`, `subject`, `message`, `attachment`, `coach`, `user`, `group_id`, `hide`) VALUES
(318, 103, 2, 'trying with trim', 'trimmed', NULL, 2, 103, NULL, 1),
(319, 2, 103, 'trying with trim', 'also trimmed', NULL, 2, 103, NULL, 1),
(320, 103, 2, 'trimmed to everyone with attachment', 'test test trim', 'shep.png', 2, 103, NULL, 1),
(321, 101, 2, 'trimmed to everyone with attachment', 'test test trim', 'shep.png', 2, 101, NULL, 0),
(322, 129, 2, 'trimmed to everyone with attachment', 'test test trim', 'shep.png', 2, 129, NULL, 0),
(325, 133, 2, 'trimmed to everyone with attachment', 'test test trim', 'shep.png', 2, 133, NULL, 0),
(327, 139, 2, 'trimmed to everyone with attachment', 'test test trim', 'shep.png', 2, 139, NULL, 0),
(328, 144, 2, 'trimmed to everyone with attachment', 'test test trim', 'shep.png', 2, 144, NULL, 0),
(329, 2, 103, '??', 'now its green', NULL, 2, 103, NULL, 1),
(332, 103, 2, '??', 'asf', NULL, 2, 103, NULL, 1),
(333, 103, 2, 'etsting group', 'test group', NULL, 2, 103, 2, 1),
(334, 2, 103, 'etsting group', 'replying to group thing', NULL, 2, 103, NULL, 1),
(335, 103, 101, 'test group', 'test group', NULL, 2, 103, 2, 1),
(339, 2, 103, 'TEST', 'ALSO TEST', NULL, 2, 2, 2, 1),
(343, 2, 103, 'test group', 'echo $conn->error;', NULL, 2, 2, 2, 1),
(345, 126, 103, 'test group', 'echo $conn->error;', NULL, 2, 126, 2, 0),
(346, 127, 103, 'test group', 'echo $conn->error;', NULL, 2, 127, 2, 0),
(360, 2, 103, 'test group', 'nice, sal!', 'Mitzinewleaf.png', 2, 2, 2, 1),
(362, 126, 103, 'test group', 'nice, sal!', 'Mitzinewleaf.png', 2, 126, 2, 0),
(363, 127, 103, 'test group', 'nice, sal!', 'Mitzinewleaf.png', 2, 127, 2, 0),
(364, 2, 103, 'etsting group', 'test', NULL, 2, 2, 2, 1),
(366, 126, 103, 'etsting group', 'test', NULL, 2, 126, 2, 0),
(367, 127, 103, 'etsting group', 'test', NULL, 2, 127, 2, 0),
(368, 103, 2, 'etsting group', 'trying to reply to group!', NULL, 2, 103, 2, 1),
(370, 126, 2, 'etsting group', 'trying to reply to group!', NULL, 2, 126, 2, 0),
(371, 127, 2, 'etsting group', 'trying to reply to group!', NULL, 2, 127, 2, 0),
(372, 103, 2, 'etsting group', 'trying to reply', NULL, 2, 103, 2, 1),
(374, 126, 2, 'etsting group', 'trying to reply', NULL, 2, 126, 2, 0),
(375, 127, 2, 'etsting group', 'trying to reply', NULL, 2, 127, 2, 0),
(376, 103, 2, 'test group', 'test test 123', NULL, 2, 103, 2, 1),
(378, 126, 2, 'test group', 'test test 123', NULL, 2, 126, 2, 0),
(379, 127, 2, 'test group', 'test test 123', NULL, 2, 127, 2, 0),
(380, 103, 2, 'etsting group', 'test123', NULL, 2, 103, 2, 1),
(382, 126, 2, 'etsting group', 'test123', NULL, 2, 126, 2, 0),
(383, 127, 2, 'etsting group', 'test123', NULL, 2, 127, 2, 0),
(384, 2, 103, 'etsting group', 'ok cool', 'pthink.png', 2, 2, 2, 1),
(386, 126, 103, 'etsting group', 'ok cool', 'pthink.png', 2, 126, 2, 0),
(387, 127, 103, 'etsting group', 'ok cool', 'pthink.png', 2, 127, 2, 0),
(388, 103, 2, 'etsting group', 'test', NULL, 2, 103, 2, 1),
(390, 126, 2, 'etsting group', 'test', NULL, 2, 126, 2, 0),
(391, 127, 2, 'etsting group', 'test', NULL, 2, 127, 2, 0),
(392, 103, 2, 'etsting group', 'trying to reply', NULL, 2, 103, 2, 1),
(394, 126, 2, 'etsting group', 'trying to reply', NULL, 2, 126, 2, 0),
(395, 127, 2, 'etsting group', 'trying to reply', NULL, 2, 127, 2, 0),
(396, 103, 2, 'etsting group', 'kk', NULL, 2, 103, 2, 1),
(398, 126, 2, 'etsting group', 'kk', NULL, 2, 126, 2, 0),
(399, 127, 2, 'etsting group', 'kk', NULL, 2, 127, 2, 0),
(400, 103, 2, 'test group', 'ok ok', NULL, 2, 103, 2, 1),
(402, 126, 2, 'test group', 'ok ok', NULL, 2, 126, 2, 0),
(403, 127, 2, 'test group', 'ok ok', NULL, 2, 127, 2, 0),
(404, 103, 2, 'test group', 'kk', NULL, 2, 103, 2, 1),
(406, 126, 2, 'test group', 'kk', NULL, 2, 126, 2, 0),
(407, 127, 2, 'test group', 'kk', NULL, 2, 127, 2, 0),
(408, 103, 2, '', '', NULL, 2, 103, 2, 1),
(410, 126, 2, '', '', NULL, 2, 126, 2, 0),
(411, 127, 2, '', '', NULL, 2, 127, 2, 0),
(412, 103, 2, 'trying to msg group as coach nw code', 'trying to msg group as coach nw code', NULL, 2, 103, 2, 1),
(414, 126, 2, 'trying to msg group as coach nw code', 'trying to msg group as coach nw code', NULL, 2, 126, 2, 0),
(415, 127, 2, 'trying to msg group as coach nw code', 'trying to msg group as coach nw code', NULL, 2, 127, 2, 0),
(416, 103, 2, 'msgAttachment', 'msgAttachment', NULL, 2, 103, 2, 1),
(418, 126, 2, 'msgAttachment', 'msgAttachment', NULL, 2, 126, 2, 0),
(419, 127, 2, 'msgAttachment', 'msgAttachment', NULL, 2, 127, 2, 0),
(420, 103, 2, 'sike pls', 'sike pls', 'sike.png', 2, 103, 2, 1),
(422, 126, 2, 'sike pls', 'sike pls', 'sike.png', 2, 126, 2, 0),
(423, 127, 2, 'sike pls', 'sike pls', 'sike.png', 2, 127, 2, 0),
(424, 2, 103, 'TRYING TO MSG AS NORMAL USER', 'TRYING TO MSG AS NORMAL USER', NULL, 2, 2, 1, 1),
(426, 126, 103, 'TRYING TO MSG AS NORMAL USER', 'TRYING TO MSG AS NORMAL USER', NULL, 2, 126, 1, 0),
(427, 127, 103, 'TRYING TO MSG AS NORMAL USER', 'TRYING TO MSG AS NORMAL USER', NULL, 2, 127, 1, 0),
(428, 2, 103, 'TRYING TO MSG AS NORMAL USER', 'TRYING TO MSG AS NORMAL USER', 'HappyToaster (1).png', 2, 2, 1, 1),
(430, 126, 103, 'TRYING TO MSG AS NORMAL USER', 'TRYING TO MSG AS NORMAL USER', 'HappyToaster (1).png', 2, 126, 1, 0),
(431, 127, 103, 'TRYING TO MSG AS NORMAL USER', 'TRYING TO MSG AS NORMAL USER', 'HappyToaster (1).png', 2, 127, 1, 0),
(432, 2, 103, 'hi', 'hi guys', NULL, 2, 2, 2, 1),
(434, 126, 103, 'hi', 'hi guys', NULL, 2, 126, 2, 0),
(435, 127, 103, 'hi', 'hi guys', NULL, 2, 127, 2, 0),
(438, 103, 2, 'Appointment - 2020-04-18', 'Dear Jordan Brown, your request for an appointment for trying to make once i changed stuff on 2020-04-18\n    at 10:30 has been confirmed. I will seen you then!', NULL, 2, 103, NULL, 1),
(443, 133, 2, 'Creation of new group - testing inbox', 'Dear client, I have added you to the group testing inbox.\nPleas keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 133, NULL, 0),
(444, 103, 2, 'Creation of new group - testing inbox', 'Dear client, I have added you to the group testing inbox.\nPleas keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 103, NULL, 1),
(445, 144, 2, 'Creation of new group - testing inbox', 'Dear client, I have added you to the group testing inbox.\nPleas keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 144, NULL, 0),
(447, 103, 2, 'Appointment - 2020-04-18 at 10:00', 'Dear Jordan Brown, I have createdan appointment for  on \n                at 10:00. The details of this session are: trying to create with inbox stuff. I will seen you then!', NULL, 2, 103, NULL, 1),
(449, 103, 2, 'New session for Cardio Club - 2020-04-17', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-17\n                at 11:00. The details for this session are: inbox group. I hope to see you there.', NULL, 2, 103, 2, 1),
(451, 126, 2, 'New session for Cardio Club - 2020-04-17', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-17\n                at 11:00. The details for this session are: inbox group. I hope to see you there.', NULL, 2, 126, 2, 0),
(452, 127, 2, 'New session for Cardio Club - 2020-04-17', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-17\n                at 11:00. The details for this session are: inbox group. I hope to see you there.', NULL, 2, 127, 2, 0),
(453, 103, 2, 'Cancelled session for 2020-04-12', 'Dear client, the session scheculed for 2020-04-12\n        at 10:30 has been cancelled. Please feel free to submit additional appointment requests.', NULL, 2, 103, NULL, 1),
(454, 103, 2, 'Cancelled session for 2020-04-17', 'Dear client, the session scheculed for 2020-04-17\n        at 19:00 has been cancelled. Please feel free to submit additional appointment requests.  \n Kind regards, Ki-Woo Kim', NULL, 2, 103, NULL, 1),
(455, 103, 2, 'Cancelled session for 2020-04-17', 'Dear client, the session scheculed for 2020-04-17\n        at 19:00 has been cancelled. Please feel free to submit additional appointment requests.  \n Kind regards, Ki-Woo Kim', NULL, 2, 103, NULL, 1),
(456, 103, 2, 'Cancelled session for 2020-04-17', 'Dear client, the session scheculed for 2020-04-17\n        at 11:00 has been cancelled. Please feel free to submit additional appointment requests.  \n Kind regards, Ki-Woo Kim', NULL, 2, 103, NULL, 1),
(457, 103, 2, 'Cancelled session for 2020-04-17', 'Dear client, the session scheculed for 2020-04-17\n        at 09:30 has been cancelled. Please feel free to submit additional appointment requests.  \n Kind regards, Ki-Woo Kim', NULL, 2, 103, NULL, 1),
(459, 2, 103, 'Cancelled session for 2020-04-18', 'Dear coach, I have cancelled my appointment on 2020-04-18 at 12:01. \n\n        Apologies for any inconvenience.', NULL, 2, 103, NULL, 1),
(460, 103, 2, 'Appointment - 2020-04-21 at 10:30', 'Dear Jordan Brown, your request for an appointment for I want to train  on 2020-04-21\n    at 10:30 has been confirmed. I will seen you then!', NULL, 2, 103, NULL, 1),
(467, 103, 2, 'Appointment - 2020-04-22 at 10:30', 'Dear Jordan Brown, your request for an appointment for test on 2020-04-22\n    at 10:30 has been confirmed. I will seen you then!', NULL, 2, 103, NULL, 1),
(469, 103, 2, 'Appointment - 2020-04-16 at 11:00', 'Dear Jordan Brown, your request for an appointment for intense workout on 2020-04-16\n    at 11:00 has been confirmed. I will seen you then!', NULL, 2, 103, NULL, 1),
(470, 103, 2, 'Appointment - 2020-04-16 at 11:00', 'Dear Jordan Brown, your request for an appointment for test on 2020-04-16\n    at 11:00 has been confirmed. I will seen you then!', NULL, 2, 103, NULL, 1),
(471, 103, 2, 'Appointment - 2020-04-16 at 11:00', 'Dear Jordan Brown, your request for an appointment for aaad on 2020-04-16\n    at 11:00 has been confirmed. I will seen you then!', NULL, 2, 103, NULL, 1),
(472, 2, 103, 'Cancelled session for 2020-04-14', 'Dear coach, I have cancelled my appointment on 2020-04-14 at 10:00. \n\n        Apologies for any inconvenience.', NULL, 2, 103, NULL, 1),
(473, 2, 103, 'a', 'testing new stuff', 'messagegroup.txt', 2, 103, NULL, 1),
(474, 2, 103, 'test', 'a', 'Jordan_Brown_CV.docx', 2, 103, NULL, 1),
(475, 133, 2, 'Creation of new group - a', 'Dear client, I have added you to the group a.\nPleas keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 133, NULL, 0),
(476, 144, 2, 'Creation of new group - a', 'Dear client, I have added you to the group a.\nPleas keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 144, NULL, 0),
(478, 101, 2, 'Creation of new group - a', 'Dear client, I have added you to the group a.\nPleas keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 101, NULL, 0),
(480, 144, 2, 'Cancelled session for 2020-04-16', 'Dear client, the session scheculed for 2020-04-16\n        at 14:24 has been cancelled. Please feel free to submit additional appointment requests.  \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 144, NULL, 0),
(481, 144, 2, 'Cancelled session for 2020-04-17', 'Dear client, the session scheculed for 2020-04-17\n        at 18:00 has been cancelled. Please feel free to submit additional appointment requests.  \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 144, NULL, 0),
(482, 129, 2, 'Welcome to Gymafi', 'Welcome - your account has been approved for Gymafi and \n  I have accepted you as a client. Please set up your initial details by visiting your profile. \n  Hope to see you soon!', 'beginners_help.pdf', 2, 129, NULL, 0),
(488, 2, 103, 'Cancelled session for 2020-04-21', 'I have cancelled my appointment on 2020-04-21 at 10:30. \n\n        Apologies for any inconvenience.', NULL, 2, 103, NULL, 1),
(489, 2, 103, 'a', 'test', 'Jordan_Brown_CV.docx', 2, 103, NULL, 1),
(490, 2, 103, 'a', 'b', 'Jordan_Brown_CV.docx', 2, 103, NULL, 1),
(491, 103, 2, 'a', 'a', 'Jordan_Brown_CV.docx', 2, 103, NULL, 1),
(492, 101, 2, 'a', 'a', 'Jordan_Brown_CV.docx', 2, 101, NULL, 0),
(493, 129, 2, 'a', 'a', 'Jordan_Brown_CV.docx', 2, 129, NULL, 0),
(494, 133, 2, 'a', 'a', 'Jordan_Brown_CV.docx', 2, 133, NULL, 0),
(496, 139, 2, 'a', 'a', 'Jordan_Brown_CV.docx', 2, 139, NULL, 0),
(498, 144, 2, 'a', 'a', 'Jordan_Brown_CV.docx', 2, 144, NULL, 0),
(500, 2, 103, 'aa', 'a', 'Jordan_Brown_CV.docx', 2, 103, NULL, 1),
(501, 2, 103, 'a', 'a', 'sal1.png', 2, 103, NULL, 1),
(502, 2, 103, 'a', 'abbbb', 'sal1.png', 2, 103, NULL, 1),
(503, 2, 103, 'cc', 'cc', 'Jordan_Brown_CV1.docx', 2, 103, NULL, 1),
(504, 2, 103, 'abb', 'abb', 'Jordan_Brown_CV2.docx', 2, 103, NULL, 1),
(505, 103, 2, 'abb', 'ccc', 'Jordan_Brown_CV3.docx', 2, 103, NULL, 1),
(506, 2, 103, 't\'e\'s\'t', 't\'e\'s\'t', NULL, 2, 103, NULL, 1),
(507, 103, 2, 'Appointment - 2020-04-24 at 11:00', 'Dear Jordan Brown, your request for an appointment for bbb on 2020-04-24\n    at 11:00 has been confirmed. I will see you then!', NULL, 2, 103, NULL, 0),
(508, 103, 2, 'Appointment - 2020-04-25 at 10:30', 'Dear Jordan Brown, your request for an appointment for test on 2020-04-25\n    at 10:30 has been confirmed. I will see you then!', NULL, 2, 103, NULL, 0),
(509, 103, 2, 'Cancelled session for 2020-04-16', 'Dear client, the session scheculed for 2020-04-16\n        at 11:00 has been cancelled. Please feel free to submit additional appointment requests.  \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 103, NULL, 1),
(518, 103, 2, 'Cancelled session for groupName - deleteDate', 'Dear member of groupName, the session scheculed for $groupName on deleteDate\r\n       at deleteTime has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n\r\n       Kind regards, nameOfCoach.', NULL, 2, 103, 2, 1),
(532, 0, 2, 'Cancelled session for Cardio Club - 2020-04-15', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-15\n       at 19:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 0, 2, 0),
(533, 0, 2, 'Cancelled session for Cardio Club - 2020-04-15', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-15\n       at 19:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 0, 2, 0),
(534, 126, 2, 'Cancelled session for Cardio Club - 2020-04-15', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-15\n       at 19:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 126, 2, 0),
(535, 127, 2, 'Cancelled session for Cardio Club - 2020-04-15', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-15\n       at 19:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 127, 2, 0),
(536, 144, 2, 'Cancelled session for Cycle Club - 2020-04-19', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-19\n       at 11:55 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 144, 1, 0),
(538, 101, 2, 'Cancelled session for Cycle Club - 2020-04-19', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-19\n       at 11:55 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 101, 1, 0),
(539, 103, 2, 'Cancelled session for Cycle Club - 2020-04-19', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-19\n       at 11:55 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Ki-Woo Kim.', NULL, 2, 103, 1, 0),
(540, 103, 2, 't\'e\'s\'t', 't;;\'\'\'\'test', NULL, 2, 103, NULL, 1),
(541, 2, 103, 'tes\'\'t \'\'', 'tes\'\'t \'\'tes\'\'t \'\'tes\'\'t \'\'', NULL, 2, 2, 1, 1),
(542, 144, 103, 'tes\'\'t \'\'', 'tes\'\'t \'\'tes\'\'t \'\'tes\'\'t \'\'', NULL, 2, 144, 1, 0),
(544, 101, 103, 'tes\'\'t \'\'', 'tes\'\'t \'\'tes\'\'t \'\'tes\'\'t \'\'', NULL, 2, 101, 1, 0),
(545, 144, 2, 'tes\'\'t \'\'', 'test\'\'\'', NULL, 2, 144, 1, 0),
(547, 101, 2, 'tes\'\'t \'\'', 'test\'\'\'', NULL, 2, 101, 1, 0),
(548, 103, 2, 'tes\'\'t \'\'', 'test\'\'\'', NULL, 2, 103, 1, 0),
(553, 2, 103, 'message to group', 'a', NULL, 2, 2, 1, 1),
(554, 144, 103, 'message to group', 'a', NULL, 2, 144, 1, 0),
(556, 101, 103, 'message to group', 'a', NULL, 2, 101, 1, 0),
(557, 144, 2, 'message to group', 'test', NULL, 2, 144, 1, 0),
(559, 101, 2, 'message to group', 'test', NULL, 2, 101, 1, 0),
(560, 103, 2, 'message to group', 'test', NULL, 2, 103, 1, 1),
(561, 2, 103, 'aa', 'a', 'bye1.png', 2, 103, NULL, 1),
(562, 2, 103, 'a', 'a', NULL, 2, 103, NULL, 1),
(563, 2, 103, 'aa', 'aa', NULL, 2, 103, NULL, 1),
(564, 2, 103, 'aa', 'aa', NULL, 2, 2, 1, 1),
(565, 144, 103, 'aa', 'aa', NULL, 2, 144, 1, 0),
(567, 101, 103, 'aa', 'aa', NULL, 2, 101, 1, 0),
(568, 2, 103, 'message to group', 'aa', NULL, 2, 103, NULL, 1),
(569, 2, 103, 'message to group', 't', NULL, 2, 103, NULL, 1),
(570, 103, 2, 'message to group', 'if (strlen($messageSubject) > 65) {\r\n        $messageError = \"Subject too long, must be below 65 characters.\";\r\n      } else if', NULL, 2, 103, NULL, 1),
(572, 103, 2, 'Appointment - 2020-04-27 at 11:00', 'Dear Jordan Brown, I have created an appointment for  on \n                at 11:00. The details of this session are: test. I will seen you then!', NULL, 2, 103, NULL, 1),
(573, 103, 2, 'Appointment - 2020-04-23 at 11:00', 'Dear Jordan Brown, I have created an appointment for aa on 2020-04-23\n                at 11:00. The details of this session are: aa. I will seen you then!', NULL, 2, 103, NULL, 1),
(574, 144, 2, 'New session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, there will be a new session for Cycle Club on 2020-04-24\n                at 20:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 144, 1, 0),
(576, 101, 2, 'New session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, there will be a new session for Cycle Club on 2020-04-24\n                at 20:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 101, 1, 0),
(577, 103, 2, 'New session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, there will be a new session for Cycle Club on 2020-04-24\n                at 20:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 103, 1, 1),
(578, 0, 2, 'New session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-21\n                at 11:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 0, 2, 0),
(579, 0, 2, 'New session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-21\n                at 11:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 0, 2, 0),
(580, 126, 2, 'New session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-21\n                at 11:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 126, 2, 0),
(581, 127, 2, 'New session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, there will be a new session for Cardio Club on 2020-04-21\n                at 11:00. The details for this session are: aa. I hope to see you there.', NULL, 2, 127, 2, 0),
(584, 144, 2, 'Cancelled session for Cycle Club - 2020-04-16', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-16\n       at 10:30 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n\n       Kind regards, Kevin Kim.', NULL, 2, 144, 1, 0),
(586, 101, 2, 'Cancelled session for Cycle Club - 2020-04-16', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-16\n       at 10:30 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 101, 1, 0),
(587, 103, 2, 'Cancelled session for Cycle Club - 2020-04-16', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-16\n       at 10:30 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 103, 1, 1),
(589, 103, 2, 'Appointment - 2020-04-23 at 10:00', 'Dear Jordan Brown, your request for an appointment for I want to train on 2020-04-23\n    at 10:00 has been confirmed. I will see you then!', NULL, 2, 103, NULL, 1),
(590, 2, 103, 'Cancelled session for 2020-04-22', 'I have cancelled my appointment on 2020-04-22 at 10:30. \n\n        Apologies for any inconvenience.', NULL, 2, 103, NULL, 1),
(591, 103, 2, 'Cancelled session for 2020-04-25', 'Dear client, the session scheculed for 2020-04-25\n        at 10:30 has been cancelled. Please feel free to submit additional appointment requests.  \n\n       Kind regards, Kevin Kim.', NULL, 2, 103, NULL, 1),
(592, 2, 103, 'testing attachment', 'test', 'nav thing.txt', 2, 103, NULL, 1),
(593, 103, 2, 'Cancelled session for 2020-04-22', 'test', NULL, 2, 103, NULL, 1),
(594, 2, 103, 'Cancelled session for 2020-04-25', 'test', NULL, 2, 103, NULL, 0),
(595, 2, 103, 'Cancelled session for 2020-04-22', 'test', NULL, 2, 103, NULL, 0),
(596, 2, 103, 'hi', 'test', NULL, 2, 2, 1, 1),
(597, 144, 103, 'hi', 'test', NULL, 2, 144, 1, 0),
(599, 101, 103, 'hi', 'test', NULL, 2, 101, 1, 0),
(600, 144, 2, 'hi', 'ok', NULL, 2, 144, 1, 0),
(602, 101, 2, 'hi', 'ok', NULL, 2, 101, 1, 0),
(603, 103, 2, 'hi', 'ok', NULL, 2, 103, 1, 1),
(604, 2, 103, 'hi', 'hi', NULL, 2, 2, 1, 0),
(605, 144, 103, 'hi', 'hi', NULL, 2, 144, 1, 0),
(607, 101, 103, 'hi', 'hi', NULL, 2, 101, 1, 0),
(608, 2, 103, 'Appointment - 2020-04-23 at 10:00', 'a', NULL, 2, 103, NULL, 0),
(609, 2, 103, 'Appointment - 2020-04-23 at 11:00', 'testing even though name dosn\'t show', NULL, 2, 103, NULL, 0),
(610, 2, 103, 'Cancelled session for Cycle Club - 2020-04-16', 'test', NULL, 2, 2, 1, 0),
(611, 144, 103, 'Cancelled session for Cycle Club - 2020-04-16', 'test', NULL, 2, 144, 1, 0),
(613, 101, 103, 'Cancelled session for Cycle Club - 2020-04-16', 'test', NULL, 2, 101, 1, 0),
(614, 2, 103, 'Appointment - 2020-04-27 at 11:00', 'test', NULL, 2, 103, NULL, 0),
(615, 2, 103, 'message to group', 'yrdy', NULL, 2, 103, NULL, 0),
(616, 2, 103, 'Cancelled session for 2020-04-16', 'hello, sorry to hear that. see you soon!', NULL, 2, 103, NULL, 0),
(617, 0, 2, 'hello group!', 'hi', NULL, 2, 0, 2, 0),
(618, 0, 2, 'hello group!', 'hi', NULL, 2, 0, 2, 0),
(619, 126, 2, 'hello group!', 'hi', NULL, 2, 126, 2, 0),
(620, 127, 2, 'hello group!', 'hi', NULL, 2, 127, 2, 0),
(621, 0, 2, 'hi', 'hi', NULL, 2, 0, 2, 0),
(622, 0, 2, 'hi', 'hi', NULL, 2, 0, 2, 0),
(623, 126, 2, 'hi', 'hi', NULL, 2, 126, 2, 0),
(624, 127, 2, 'hi', 'hi', NULL, 2, 127, 2, 0),
(625, 0, 2, 'test', 'test', NULL, 2, 0, 2, 0),
(626, 0, 2, 'test', 'test', NULL, 2, 0, 2, 0),
(627, 126, 2, 'test', 'test', NULL, 2, 126, 2, 0),
(628, 127, 2, 'test', 'test', NULL, 2, 127, 2, 0),
(629, 0, 2, 'aaa', 'aaa', NULL, 2, 0, 2, 0),
(630, 0, 2, 'aaa', 'aaa', NULL, 2, 0, 2, 0),
(631, 126, 2, 'aaa', 'aaa', NULL, 2, 126, 2, 0),
(632, 127, 2, 'aaa', 'aaa', NULL, 2, 127, 2, 0),
(633, 144, 2, 'test', 'test', NULL, 2, 144, 1, 0),
(635, 101, 2, 'test', 'test', NULL, 2, 101, 1, 0),
(636, 103, 2, 'test', 'test', NULL, 2, 103, 1, 0),
(637, 144, 2, 'hi cycle club', 'hello!', NULL, 2, 144, 1, 0),
(639, 101, 2, 'hi cycle club', 'hello!', NULL, 2, 101, 1, 0),
(640, 103, 2, 'hi cycle club', 'hello!', NULL, 2, 103, 1, 1),
(641, 2, 103, 'hi cycle club', 'hello there!', NULL, 2, 2, 1, 0),
(642, 144, 103, 'hi cycle club', 'hello there!', NULL, 2, 144, 1, 0),
(644, 101, 103, 'hi cycle club', 'hello there!', NULL, 2, 101, 1, 0),
(645, 103, 2, 'test all client', 'test', NULL, 2, 103, NULL, 0),
(646, 101, 2, 'test all client', 'test', NULL, 2, 101, NULL, 0),
(647, 129, 2, 'test all client', 'test', NULL, 2, 129, NULL, 0),
(648, 133, 2, 'test all client', 'test', NULL, 2, 133, NULL, 0),
(650, 139, 2, 'test all client', 'test', NULL, 2, 139, NULL, 0),
(651, 144, 2, 'test all client', 'test', NULL, 2, 144, NULL, 0),
(652, 144, 2, 'test', 'aa', NULL, 2, 144, NULL, 0),
(653, 139, 2, 'test', 'aa', NULL, 2, 139, NULL, 0),
(654, 133, 2, 'test', 'aa', NULL, 2, 133, NULL, 0),
(655, 101, 2, 'test', 'aa', NULL, 2, 101, NULL, 0),
(656, 103, 2, 'testing attachment', 'a', 'fail1.png', 2, 103, NULL, 0),
(657, 101, 2, 'testing attachment', 'a', 'fail2.png', 2, 101, NULL, 0),
(658, 129, 2, 'testing attachment', 'a', 'fail2.png', 2, 129, NULL, 0),
(659, 133, 2, 'testing attachment', 'a', 'fail2.png', 2, 133, NULL, 0),
(661, 139, 2, 'testing attachment', 'a', 'fail2.png', 2, 139, NULL, 0),
(662, 144, 2, 'testing attachment', 'a', 'fail2.png', 2, 144, NULL, 0),
(663, 103, 2, 'a', 'test', NULL, 2, 103, NULL, 0),
(664, 101, 2, 'a', 'test', NULL, 2, 101, NULL, 0),
(665, 129, 2, 'a', 'test', NULL, 2, 129, NULL, 0),
(666, 133, 2, 'a', 'test', NULL, 2, 133, NULL, 0),
(668, 139, 2, 'a', 'test', NULL, 2, 139, NULL, 0),
(669, 144, 2, 'a', 'test', NULL, 2, 144, NULL, 0),
(670, 103, 2, 't', 't', NULL, 2, 103, NULL, 0),
(671, 101, 2, 't', 't', NULL, 2, 101, NULL, 0),
(672, 129, 2, 't', 't', NULL, 2, 129, NULL, 0),
(673, 133, 2, 't', 't', NULL, 2, 133, NULL, 0),
(675, 139, 2, 't', 't', NULL, 2, 139, NULL, 0),
(676, 144, 2, 't', 't', NULL, 2, 144, NULL, 0),
(677, 103, 2, 't', 't', NULL, 2, 103, NULL, 0),
(678, 101, 2, 't', 't', NULL, 2, 101, NULL, 0),
(679, 129, 2, 't', 't', NULL, 2, 129, NULL, 0),
(680, 133, 2, 't', 't', NULL, 2, 133, NULL, 0),
(682, 139, 2, 't', 't', NULL, 2, 139, NULL, 0),
(683, 144, 2, 't', 't', NULL, 2, 144, NULL, 0),
(684, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(685, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(686, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(687, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(689, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(690, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(691, 103, 2, 't', 's', NULL, 2, 103, NULL, 0),
(692, 101, 2, 't', 's', NULL, 2, 101, NULL, 0),
(693, 129, 2, 't', 's', NULL, 2, 129, NULL, 0),
(694, 133, 2, 't', 's', NULL, 2, 133, NULL, 0),
(696, 139, 2, 't', 's', NULL, 2, 139, NULL, 0),
(697, 144, 2, 't', 's', NULL, 2, 144, NULL, 0),
(698, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(699, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(700, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(701, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(703, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(704, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(705, 103, 2, 'test', 'test', NULL, 2, 103, NULL, 0),
(706, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(707, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(708, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(709, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(711, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(712, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(713, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(714, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(715, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(716, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(718, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(719, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(720, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(721, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(722, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(723, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(725, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(726, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(727, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(728, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(729, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(730, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(732, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(733, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(734, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(735, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(736, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(737, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(739, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(740, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(741, 103, 2, 'test', 'a', NULL, 2, 103, NULL, 0),
(742, 101, 2, 'test', 'a', NULL, 2, 101, NULL, 0),
(743, 129, 2, 'test', 'a', NULL, 2, 129, NULL, 0),
(744, 133, 2, 'test', 'a', NULL, 2, 133, NULL, 0),
(746, 139, 2, 'test', 'a', NULL, 2, 139, NULL, 0),
(747, 144, 2, 'test', 'a', NULL, 2, 144, NULL, 0),
(748, 103, 2, 'aa', 'aa', NULL, 2, 103, NULL, 0),
(749, 101, 2, 'aa', 'aa', NULL, 2, 101, NULL, 0),
(750, 129, 2, 'aa', 'aa', NULL, 2, 129, NULL, 0),
(751, 133, 2, 'aa', 'aa', NULL, 2, 133, NULL, 0),
(753, 139, 2, 'aa', 'aa', NULL, 2, 139, NULL, 0),
(754, 144, 2, 'aa', 'aa', NULL, 2, 144, NULL, 0),
(755, 103, 2, 'all with attach', 'aa', 'sal.png', 2, 103, NULL, 1),
(756, 101, 2, 'all with attach', 'aa', 'sal2.png', 2, 101, NULL, 0),
(757, 129, 2, 'all with attach', 'aa', 'sal2.png', 2, 129, NULL, 0),
(758, 133, 2, 'all with attach', 'aa', 'sal2.png', 2, 133, NULL, 0),
(760, 139, 2, 'all with attach', 'aa', 'sal2.png', 2, 139, NULL, 0),
(761, 144, 2, 'all with attach', 'aa', 'sal2.png', 2, 144, NULL, 0),
(762, 103, 2, 'a', 'test', NULL, 2, 103, NULL, 0),
(763, 101, 2, 'a', 'test', NULL, 2, 101, NULL, 0),
(764, 129, 2, 'a', 'test', NULL, 2, 129, NULL, 0),
(765, 133, 2, 'a', 'test', NULL, 2, 133, NULL, 0),
(767, 139, 2, 'a', 'test', NULL, 2, 139, NULL, 0),
(768, 144, 2, 'a', 'test', NULL, 2, 144, NULL, 0),
(769, 144, 2, 'testing multiple', 'aa', NULL, 2, 144, NULL, 0),
(771, 101, 2, 'testing multiple', 'aa', NULL, 2, 101, NULL, 0),
(772, 103, 2, 'testing all', 'testing all', NULL, 2, 103, NULL, 1),
(773, 101, 2, 'testing all', 'testing all', NULL, 2, 101, NULL, 0),
(774, 129, 2, 'testing all', 'testing all', NULL, 2, 129, NULL, 0),
(775, 133, 2, 'testing all', 'testing all', NULL, 2, 133, NULL, 0),
(777, 139, 2, 'testing all', 'testing all', NULL, 2, 139, NULL, 0),
(778, 144, 2, 'testing all', 'testing all', NULL, 2, 144, NULL, 0),
(779, 103, 2, 'testing all attach', 'testing all attach', 'bye2.png', 2, 103, NULL, 1),
(780, 101, 2, 'testing all attach', 'testing all attach', 'bye3.png', 2, 101, NULL, 0),
(781, 129, 2, 'testing all attach', 'testing all attach', 'bye3.png', 2, 129, NULL, 0),
(782, 133, 2, 'testing all attach', 'testing all attach', 'bye3.png', 2, 133, NULL, 0),
(784, 139, 2, 'testing all attach', 'testing all attach', 'bye3.png', 2, 139, NULL, 0),
(785, 144, 2, 'testing all attach', 'testing all attach', 'bye3.png', 2, 144, NULL, 0),
(786, 144, 2, 'hi cycle', 'a', NULL, 2, 144, 1, 0),
(788, 101, 2, 'hi cycle', 'a', NULL, 2, 101, 1, 0),
(789, 103, 2, 'hi cycle', 'a', NULL, 2, 103, 1, 1),
(790, 2, 103, 'test', 'test1', NULL, 2, 103, NULL, 0),
(791, 2, 103, 'hi cycle', 'hi', NULL, 2, 2, 1, 0),
(792, 144, 103, 'hi cycle', 'hi', NULL, 2, 144, 1, 0),
(794, 101, 103, 'hi cycle', 'hi', NULL, 2, 101, 1, 0),
(795, 2, 103, 'testing all attach', 'ok', NULL, 2, 103, NULL, 0),
(796, 2, 103, 'testing all', 'test attach', 'messagegroup1.txt', 2, 103, NULL, 0),
(797, 2, 103, 'a', 'msg group', NULL, 2, 2, 1, 0),
(798, 144, 103, 'a', 'msg group', NULL, 2, 144, 1, 0),
(800, 101, 103, 'a', 'msg group', NULL, 2, 101, 1, 0),
(801, 2, 103, 'hello group!', 'attavh', NULL, 2, 2, 1, 0),
(802, 144, 103, 'hello group!', 'attavh', NULL, 2, 144, 1, 0),
(804, 101, 103, 'hello group!', 'attavh', NULL, 2, 101, 1, 0),
(805, 2, 103, 'msg group with attach', 'msg group with attach', 'fail2.png', 2, 2, 1, 0),
(806, 144, 103, 'msg group with attach', 'msg group with attach', 'fail2.png', 2, 144, 1, 0),
(808, 101, 103, 'msg group with attach', 'msg group with attach', 'fail2.png', 2, 101, 1, 0),
(809, 2, 103, 'msg group without attach', 'msg group with attach', NULL, 2, 2, 1, 0),
(810, 144, 103, 'msg group without attach', 'msg group with attach', NULL, 2, 144, 1, 0),
(812, 101, 103, 'msg group without attach', 'msg group with attach', NULL, 2, 101, 1, 0),
(813, 2, 103, 'aa', 'a', NULL, 2, 2, 1, 0),
(814, 144, 103, 'aa', 'a', NULL, 2, 144, 1, 0),
(816, 101, 103, 'aa', 'a', NULL, 2, 101, 1, 0),
(817, 2, 103, 'hi cycle', 'reply', NULL, 2, 103, NULL, 0),
(818, 2, 103, 'hi cycle', 'reply /w attach', 'bye3.png', 2, 103, NULL, 0),
(819, 103, 2, 'REJECTION of Appointment - 2020-04-23 at 19:30', 'Dear Jordan Brown, your request for an appointment for aaaaaaaaaaa on 2020-04-23\n    at 19:30 has been rejected. Feel free to try another date/time', NULL, 2, 103, NULL, 1),
(820, 103, 2, 'Appointment - 2020-04-23 at 19:00', 'Dear Jordan Brown, your request for an appointment for hhh on 2020-04-23\n    at 19:00 has been confirmed. I will see you then!', NULL, 2, 103, NULL, 1),
(821, 103, 2, 'Appointment - 2020-04-25 at 20:00', 'Dear Jordan Brown, I have created an appointment for test on 2020-04-25\n                at 20:00. The details of this session are: test. I will seen you then!', NULL, 2, 103, NULL, 1),
(822, 0, 2, 'Cancelled session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-21\n       at 11:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n\n       Kind regards, Kevin Kim.', NULL, 2, 0, 2, 0),
(823, 0, 2, 'Cancelled session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-21\n       at 11:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 0, 2, 0),
(824, 126, 2, 'Cancelled session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-21\n       at 11:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 126, 2, 0),
(825, 127, 2, 'Cancelled session for Cardio Club - 2020-04-21', 'Dear member of Cardio Club, the session scheculed for Cardio Club on 2020-04-21\n       at 11:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 127, 2, 0),
(826, 2, 103, 'Cancelled session for 2020-04-23', 'I have cancelled my appointment on 2020-04-23 at 11:00. \n\n        Apologies for any inconvenience.', NULL, 2, 103, NULL, 0),
(827, 103, 2, 'Cancelled session for 2020-04-23', 'Dear client, the session scheculed for 2020-04-23\n        at 11:00 has been cancelled. Please feel free to submit additional appointment requests.  \n\n       Kind regards, Kevin Kim.', NULL, 2, 103, NULL, 1),
(829, 144, 2, 'Creation of new group - group to delete', 'Dear client, I have added you to the group .\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 144, NULL, 0),
(830, 101, 2, 'Creation of new group - group to delete', 'Dear client, I have added you to the group .\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 101, NULL, 0),
(831, 103, 2, 'Creation of new group - group to delete', 'Dear client, I have added you to the group .\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 103, NULL, 1),
(832, 144, 2, 'Creation of new group - sanitisedGroupName', 'Dear client, I have added you to the group sanitisedGroupName.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 144, NULL, 0),
(833, 103, 2, 'Creation of new group - sanitisedGroupName', 'Dear client, I have added you to the group sanitisedGroupName.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 103, NULL, 1),
(834, 133, 2, 'Creation of new group - sanitisedGroupName', 'Dear client, I have added you to the group sanitisedGroupName.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 133, NULL, 0),
(835, 129, 2, 'Creation of new group - sanitisedGroupName', 'Dear client, I have added you to the group sanitisedGroupName.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 129, NULL, 0),
(836, 2, 103, 'Creation of new group - sanitisedGroupName', 'ok', NULL, 2, 103, NULL, 0),
(837, 2, 103, 'Creation of new group - group to delete', 'ok', NULL, 2, 103, NULL, 0),
(838, 2, 103, 'REJECTION of Appointment - 2020-04-23 at 19:30', 'sad to hear!', NULL, 2, 103, NULL, 0),
(839, 2, 103, 'all with attach', 'ok', NULL, 2, 103, NULL, 0),
(840, 144, 2, 'Cancelled session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-24\n       at 20:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at.  \n\n       Kind regards, Kevin Kim.', NULL, 2, 144, 1, 0),
(841, 0, 2, 'Cancelled session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-24\n       at 20:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 0, 1, 0),
(842, 101, 2, 'Cancelled session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-24\n       at 20:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 101, 1, 0),
(843, 103, 2, 'Cancelled session for Cycle Club - 2020-04-24', 'Dear member of Cycle Club, the session scheculed for Cycle Club on 2020-04-24\n       at 20:00 has been cancelled. Keep an eye on your inbox/dashboard for further upcoming events of which I hope to see you at. \n\n       Kind regards, Kevin Kim.', NULL, 2, 103, 1, 0),
(844, 103, 2, 'Appointment - 2020-04-25 at 19:30', 'Dear Jordan Brown, your request for an appointment for test on 2020-04-25\n    at 19:30 has been confirmed. I will see you then!', NULL, 2, 103, NULL, 0),
(845, 103, 2, 'REJECTION of Appointment - 2020-04-26 at 19:00', 'Dear Jordan Brown, your request for an appointment for I want to train on 2020-04-26\n    at 19:00 has been rejected. Feel free to try another date/time. Regards.', NULL, 2, 103, NULL, 0),
(846, 103, 2, 'CONFIRMATION of Appointment - 2020-04-27 at 20:00', 'Dear Jordan Brown, your request for an appointment for I want to train on 2020-04-27\n    at 20:00 has been confirmed. I will see you then!', NULL, 2, 103, NULL, 1),
(847, 103, 2, 'Appointment - 2020-04-26 at 20:00', 'Dear Jordan Brown, I have created an appointment for I want to train on 2020-04-26\n                at 20:00. The details of this session are: I want to train. I will seen you then!', NULL, 2, 103, NULL, 1),
(848, 2, 103, 'Cancelled session for 2020-04-23', 'I have cancelled my appointment on 2020-04-23 at 10:00. \n\n        Apologies for any inconvenience.', NULL, 2, 103, NULL, 0),
(849, 103, 2, 'Cancelled session for 2020-04-26', 'Dear client, the session scheculed for 2020-04-26\n        at 20:00 has been cancelled. Please feel free to submit additional appointment requests.  \n\n       Kind regards, Kevin Kim.', NULL, 2, 103, NULL, 1),
(850, 2, 103, 'testing message', 'a', NULL, 2, 103, NULL, 0),
(851, 2, 103, 'testing attachment 2mb', 'ok', 'sal2.png', 2, 103, NULL, 0),
(852, 2, 103, 'tihi', 'msg to group', NULL, 2, 2, 1, 0),
(853, 144, 103, 'tihi', 'msg to group', NULL, 2, 144, 1, 0),
(854, 0, 103, 'tihi', 'msg to group', NULL, 2, 0, 1, 0),
(855, 101, 103, 'tihi', 'msg to group', NULL, 2, 101, 1, 0),
(856, 2, 103, 'grp msg attach', 'grp msg attach', 'fail3.png', 2, 2, 1, 0),
(857, 144, 103, 'grp msg attach', 'grp msg attach', 'fail3.png', 2, 144, 1, 0),
(858, 0, 103, 'grp msg attach', 'grp msg attach', 'fail3.png', 2, 0, 1, 0),
(859, 101, 103, 'grp msg attach', 'grp msg attach', 'fail3.png', 2, 101, 1, 0),
(860, 2, 103, 'Cancelled session for 2020-04-26', 'ok reply', NULL, 2, 103, NULL, 0),
(861, 2, 103, 'Appointment - 2020-04-26 at 20:00', 'sad', NULL, 2, 103, NULL, 0),
(862, 103, 2, 'msg all clients', 'msg all clients', NULL, 2, 103, NULL, 0),
(863, 101, 2, 'msg all clients', 'msg all clients', NULL, 2, 101, NULL, 0),
(864, 129, 2, 'msg all clients', 'msg all clients', NULL, 2, 129, NULL, 0),
(865, 133, 2, 'msg all clients', 'msg all clients', NULL, 2, 133, NULL, 0),
(866, 139, 2, 'msg all clients', 'msg all clients', NULL, 2, 139, NULL, 0),
(867, 144, 2, 'msg all clients', 'msg all clients', NULL, 2, 144, NULL, 0),
(868, 144, 2, 'aa', 'to group', NULL, 2, 144, 1, 0),
(869, 0, 2, 'aa', 'to group', NULL, 2, 0, 1, 0),
(870, 101, 2, 'aa', 'to group', NULL, 2, 101, 1, 0),
(871, 103, 2, 'aa', 'to group', NULL, 2, 103, 1, 0),
(872, 144, 2, 'grp with attach', 'a', 'bye4.png', 2, 144, 1, 0),
(873, 0, 2, 'grp with attach', 'a', 'bye4.png', 2, 0, 1, 0),
(874, 101, 2, 'grp with attach', 'a', 'bye4.png', 2, 101, 1, 0),
(875, 103, 2, 'grp with attach', 'a', 'bye4.png', 2, 103, 1, 0),
(876, 103, 2, 'all with attach', 'q', 'messagegroup2.txt', 2, 103, NULL, 0),
(877, 101, 2, 'all with attach', 'q', 'messagegroup3.txt', 2, 101, NULL, 0),
(878, 129, 2, 'all with attach', 'q', 'messagegroup3.txt', 2, 129, NULL, 0),
(879, 133, 2, 'all with attach', 'q', 'messagegroup3.txt', 2, 133, NULL, 0),
(880, 139, 2, 'all with attach', 'q', 'messagegroup3.txt', 2, 139, NULL, 0),
(881, 144, 2, 'all with attach', 'q', 'messagegroup3.txt', 2, 144, NULL, 0),
(882, 103, 2, 'Creation of new group - creating a group to delete', 'Dear client, I have added you to the group creating a group to delete.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 103, NULL, 0),
(883, 101, 2, 'Creation of new group - creating a group to delete', 'Dear client, I have added you to the group creating a group to delete.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 101, NULL, 0),
(884, 144, 2, 'Creation of new group - creating a group to delete', 'Dear client, I have added you to the group creating a group to delete.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 144, NULL, 0),
(885, 133, 2, 'Creation of new group - creating a group to delete', 'Dear client, I have added you to the group creating a group to delete.\n        Please keep an eye on your dashboard for upcoming events. You can find out specifics of the upcoming sessiosn in your calendar!', NULL, 2, 133, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_pages`
--

CREATE TABLE `webdev_pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `content_size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_pages`
--

INSERT INTO `webdev_pages` (`id`, `page_name`, `content_size`) VALUES
(1, 'Home', 5),
(2, 'About Us', 1),
(3, 'Coaches', 3),
(4, 'Contact', 1),
(6, 'Sign Up', 1),
(7, 'Login', 1),
(8, 'Forgot Password', 1);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_page_content`
--

CREATE TABLE `webdev_page_content` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `content_2` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_page_content`
--

INSERT INTO `webdev_page_content` (`id`, `page_id`, `description`, `title`, `content`, `content_2`) VALUES
(4, 2, 'The text shown on the About Us Page', 'Our Mission', 'Gymafi was founded in 2016 as an attempt to provide a tailored fitness package to a wide array of clients. Whether you want to build muscle, lose weight or focus on nutrition - we can help you. We have a dedicated and experienced team who can help you achieve your goals, no matter of your starting point.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ultricies mi nec elit pretium porta. Ut pellentesque mollis magna et molestie. In elementum nulla vel augue tempor non ultrices mauris semper. Vestibulum nulla augue, volutpat at bibendum id, interdu\'\'m ut ante.'),
(5, 1, 'The text that goes over the image on the home page.', 'UNLOCK YOUR POTENTIAL', 'Are you ready to Gymafi your life? Get in touch today.', NULL),
(6, 1, 'The text within the \'weight loss\' column on the home page.', 'Weight Loss', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque risus mi, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum felis venenatis efficitur. Aenean ac eleifend lacus, in mollis lectus. Donec sodales, arcu et sollicitudin porttitor, tortor urna tempor ligula, id porttitor mi magna a neque. Donec dui urna, vehicula et sem eget, facilisis sodales sem.', NULL),
(7, 1, 'The text within the \'nutrition\' column on the home page.', 'Nutrition', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque risus mi, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum felis venenatis efficitur. Aenean ac eleifend lacus, in mollis lectus. Donec sodales, arcu et sollicitudin porttitor, tortor urna tempor ligula, id porttitor mi magna a neque. Donec dui urna, vehicula et sem eget, facilisis sodales sem.', NULL),
(8, 1, 'The text within the \'Well-being \' column on the home page.', 'Well-Being', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque risus mi, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum felis venenatis efficitur. Aenean ac eleifend lacus, in mollis lectus. Donec sodales, arcu et sollicitudin porttitor, tortor urna tempor ligula, id porttitor mi magna a neque. Donec dui urna, vehicula et sem eget, facilisis sodales sem.', NULL),
(9, 1, 'The text within the \'Body Building\' column on the home page.', 'Body Building', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque risus mi, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum felis venenatis efficitur. Aenean ac eleifend lacus, in mollis lectus. Donec sodales, arcu et sollicitudin porttitor, tortor urna tempor ligula, id porttitor mi magna a neque. Donec dui urna, vehicula et sem eget, facilisis sodales sem.', NULL),
(10, 3, 'Coach information for Jessica Kim', 'Jessica Kim', 'Jessica has lived it all. From Art Therapist, to graphic editor to semi-professional actress and singer, there\'s nothing she hasn\'t tried. The one thing in common throughout her extensive and varied career has been her love and interest in fitness. Nothing gets Jessica happier than seeing her clients improve. She studied Kinesiology and Psychology at Illinois State University and has been offering lessons since graduation. Jessica specialises in Core Stability, Circuit Training and is a budding Yoga enthusiast but it open to trying whatever it takes for you. We\'re positive there\'s nothing she can\'t turn her hand to.', 'Jessica\'s advice to prospective clients would be: \"Don\'t be scared or worried. Everyone has to start somewhere. When I began training, I was worried about people staring at me in the gym and thinking I had no idea what I\'m doing. I\'m here to alleviate that worry from you. I will work with you to create a bespoke fitness package for your needs - whether you wish to tone up, lose weight or generally just gain muscle. No matter what destination you desire, let\'s get there together.'),
(11, 3, 'Coach information for Ki-Woo Kim', 'Kevin Kim', 'Ki-woo grew up in Seoul, South Korea and was a regional Taekwondo champion. After retiring from his adventures in Taekwondo, he has since devoted his career to helping others transform their bodies and get in shape.\r\nKi-woo\'s primary focus is in muscle building and weight loss. If you\'re looking to gain muscles or shed excess weigh, Ki-woo would love to hear from you - no matter what your starting point is. \'\r\n', 'To any possible clients, Ki-woo would say: \"No matter what your starting position is, I can help you improve. Whether you want to lose weight, build muscle or generally just get fitter we can build a routine that will work for you. I hope to hear from you soon.\"'),
(12, 3, 'Coach information for Moon-Gwang Gook', 'Moon-Gwang Gook', 'Once the housekeeper and nanny of a wealthy Korean family, Moon-Gwang transitioned into a full time health coach after wanting a new opportunity to challenge her. She has extensive experience in meal planning and preparation, ensuring that you get all of your daily nutrients within a suitable caloric manner. Scientific studies have shown that generally weight loss is around 75% diet and 25% exercise. Moon-Gwang can ensure that you don\'t compromise your body transformation journey by providing nutritional advice and home-cooked meal planning so tasty that you won\'t even know you\'re dieting! You can see a selection of some meals previous clients have loved in our Gallery.', 'In her own words, she says: \"Most people fail at losing weight due to not sticking to a diet. I can help you get great nutritionally balanced meals in a calorie controlled way. All of my plans are for home-cooked, easy-to-make delicious meals that make it easy to stay on track. Please get in contact today.\"'),
(13, 4, 'Information displayed on the Contact Us page', 'Contact Us', 'Please contact us with any enquiries you have. We aim to get back to you within 48 hours.', 'Alternatively, you can contact us on 02892###### between the hours of 09:00 - 17:00 (Monday - Friday).'),
(14, 6, 'Information shown on the Sign Up page.', 'Sign Up', 'Please enter details you wish to sign up with.\r\n', 'You will receive a confirmation email upon successful registration.\r\n'),
(15, 7, 'Information shown on the Login page.', 'Login', 'Please login using your username or email address and password.', 'If you have forgotten your password, please click the \'Forgot Password\' button.'),
(16, 8, 'Information shown on the Forgot Password page.', 'Reset Password\'\'', 'Please enter your username and/or email address.', 'A new, temporary, password will be created and emailed to you.');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_roles`
--

CREATE TABLE `webdev_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_roles`
--

INSERT INTO `webdev_roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Coach'),
(3, 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_testimonials`
--

CREATE TABLE `webdev_testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coach_id` int(11) NOT NULL,
  `testimonial` text NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_testimonials`
--

INSERT INTO `webdev_testimonials` (`id`, `user_id`, `coach_id`, `testimonial`, `title`) VALUES
(1, 100, 3, 'This is some test dummy data for testing stuff. ', 'Great stuff!'),
(2, 0, 1, 'This is some test dummy data for testing stuff. ', 'Fantastic energy!'),
(5, 101, 1, 'This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature.', 'Happier and healthier!'),
(6, 143, 3, 'This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature.', 'Testing this out'),
(8, 159, 1, 'This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some st data to test out the testimonial feature.', 'Amazing!'),
(9, 127, 2, 'This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. This is some test data to test out the testimonial feature. ', 'Gymafi helped me so much!'),
(10, 142, 2, 'wow really cool', 'amazing'),
(11, 102, 2, 'test test this is a test', 'alert'),
(12, 103, 2, 'This is some test data to test out the testimonial feature.', 'Very happy with my results!'),
(13, 136, 3, 'Great advice from a lovely woman! Would recommend to all!', 'Fantastic Service!'),
(15, 139, 2, 'Nullam gravida purus diam, et dictum felis venenatis efficitur. Aenean ac eleifend lacus, in mollis lectus. Donec sodales, arcu et sollicitudin porttitor, tortor urna tempor ligula, id porttitor mi magna a neque. Donec dui urna, vehicula et sem eget, facilisis sodales sem.', 'Amazing!'),
(16, 158, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque risus mi, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. ', 'Perfect for what I needed!'),
(25, 126, 3, 'Wow, so good! Helped me lose a bunch of weight through mostly diet! I never knew eating so good could help you lose weight!', 'Lose weight with delicious food!'),
(26, 133, 3, 'Healthy, happy and no longer hungry! Moon Gwang\'s nutritional advice is amazing.', 'Great.'),
(27, 138, 3, 'Test review to populate the testimonial feature for this coach.', 'Test.'),
(28, 129, 3, 'Additional testimonial dummy data', 'Additional testimonial dummy data.'),
(30, 157, 2, 'Additional dummy data to populate this coach\'s testimonials to ensure that no errors appear.', 'Ki-Woo is amazing!'),
(31, 147, 3, 'Additional dummy data to populate this coach\'s testimonials to ensure that no errors appear.', 'Over the Moon!'),
(32, 103, 2, 'This is some test data to test out the testimonial feature.', 'Very happy with my results!'),
(33, 103, 2, 'This is some test data to test out the testimonial feature.', 'Very happy with my results!'),
(34, 103, 2, 'This is some test data to test out the testimonial feature.', 'Very happy with my results!');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_training_meals`
--

CREATE TABLE `webdev_training_meals` (
  `id` int(11) NOT NULL,
  `meal_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_training_meals`
--

INSERT INTO `webdev_training_meals` (`id`, `meal_type`) VALUES
(1, 'Keto (Low Carb, High Fat and Protein)'),
(2, 'High Protein, Medium Carbs and Low Fat'),
(3, 'High Protein, Low Carbs and Fat'),
(4, 'Carnivore'),
(5, 'default');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_training_plans`
--

CREATE TABLE `webdev_training_plans` (
  `id` int(11) NOT NULL,
  `plan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_training_plans`
--

INSERT INTO `webdev_training_plans` (`id`, `plan`) VALUES
(5, 'Back'),
(6, 'Legs'),
(7, 'Cardio'),
(8, 'Arms'),
(9, 'Chest'),
(10, 'Rest day'),
(11, 'default');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_training_regime`
--

CREATE TABLE `webdev_training_regime` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `diet_plan` int(11) NOT NULL,
  `monday` int(11) NOT NULL,
  `tuesday` int(11) NOT NULL,
  `wednesday` int(11) NOT NULL,
  `thursday` int(11) NOT NULL,
  `friday` int(11) NOT NULL,
  `saturday` int(11) NOT NULL,
  `sunday` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_training_regime`
--

INSERT INTO `webdev_training_regime` (`id`, `user_id`, `diet_plan`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`) VALUES
(1, 103, 1, 5, 5, 6, 8, 8, 9, 8),
(2, 133, 5, 9, 10, 9, 10, 10, 7, 5),
(4, 139, 5, 11, 11, 11, 11, 11, 11, 11),
(11, 128, 5, 11, 11, 11, 11, 11, 11, 11),
(12, 135, 5, 11, 11, 11, 11, 11, 11, 11),
(13, 136, 5, 11, 11, 11, 11, 11, 11, 11),
(14, 138, 5, 11, 11, 11, 11, 11, 11, 11),
(16, 144, 4, 6, 6, 6, 6, 6, 6, 6),
(31, 129, 5, 11, 11, 11, 11, 11, 11, 11);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_users`
--

CREATE TABLE `webdev_users` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(55) NOT NULL,
  `role` int(11) NOT NULL,
  `date_of_birth` date NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `reset_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_users`
--

INSERT INTO `webdev_users` (`id`, `username`, `password`, `email`, `role`, `date_of_birth`, `approved`, `reset_code`) VALUES
(0, 'None', ' ', '  ', 3, '2000-04-03', 1, NULL),
(1, 'jkim', '½<”–µJØÃ[¶ÕÓ°½CCËo´çž~6„NÂÊÔ', 'jkim@email.com', 2, '1970-01-01', 1, NULL),
(2, 'kwoo92', 'ìŽ%è,“XÑI 2Y–^Z', 'kwoo92@email.com', 2, '0000-00-00', 1, NULL),
(3, 'mgwang62', 'ã#Vœæ6	nÒ/[é`<&I2œ«¶V×ªoúq²y', 'mgwang62@email.com', 2, '1962-04-01', 1, NULL),
(99, 'admin', 'Ï@¢n€Þ¦]ußèíc', 'admin@gymafi.com', 1, '1995-01-10', 1, NULL),
(100, 'jbrown88', '‡r_Ç’ùÄ9ÐÉ{Û4øsg±új6÷ZÉuóg~›Õ±', 'aa', 3, '0000-00-00', 1, NULL),
(101, 'Jeff', '<:ºŸP%q øâ\"OT&š>Ñ:eçŒ‚_‰ZQã4Âð', 'dkihbd@email.com', 3, '1987-04-12', 1, NULL),
(102, 'hello', '5o»S«¬l×è¡XÉ8?', 'test@yahoo.com', 3, '0000-00-00', 1, NULL),
(103, 'jbrown123', 'p`!åX×>¬\'ñîÒ†', 'jbrown88@qub.ac.uk', 3, '1991-02-01', 1, 'ƒ·•`IP‰q]ËB?'),
(126, 'oswhiwosh', '	S/|³ƒ}v`Cr¦fÝÁ', 'opdwjhpod@gmail.com', 3, '1990-01-01', 1, NULL),
(127, 'aaaa', 'øA\Z<ÓXõ\0CF0ÿÿ/', 'aaa@b.com', 3, '1991-01-01', 1, NULL),
(128, 'aaa', 'û{Õ—-6Ð›j@hgEæÑ', 'aaa@gmail.com', 3, '1990-01-01', 1, NULL),
(129, 'sals', 'Hï˜<b~ùix-ŒøÞ«ˆ', 'sal@s.com', 3, '1991-01-01', 1, NULL),
(133, 'teeeeest', 'øA\Z<ÓXõ\0CF0ÿÿ/', 'p@tm.aom', 3, '1992-09-02', 1, NULL),
(135, 'rggrgrgr', 'øA\Z<ÓXõ\0CF0ÿÿ/', 'rggrgrgr@gmail.com', 3, '2003-01-01', 1, NULL),
(136, 'fikjsbsifb', 'øA\Z<ÓXõ\0CF0ÿÿ/', 'fikjsbsifb@gmail.com', 3, '2003-01-01', 1, NULL),
(138, 'vofdindfon', 'øA\Z<ÓXõ\0CF0ÿÿ/', 'vofdindfon@gmail.com', 3, '2003-04-09', 1, NULL),
(139, 'jbrown1234', 'øA\Z<ÓXõ\0CF0ÿÿ/', 'jaa@gmail.com', 3, '1991-01-01', 1, NULL),
(142, 'aa', 'ÇnJØF:±PÃxßÉÚº–', 'a@gmail.com', 3, '0019-12-19', 1, NULL),
(143, 'sf', 'õm°ººŒJ\\wÒÙzË¹Ó', 'wrwr@email.com', 3, '1992-01-01', 1, NULL),
(144, 'byemom', 'wiÂªêç‰ØC\r˜¢\\‹;ë*×I_I™V<hñ', 'iubfgds@gmail.com', 3, '1945-04-30', 1, NULL),
(147, 'test1234', 'èªeJL\'½ÿÝâÈ€Í', 'test1234@email.com', 3, '1991-01-01', 1, NULL),
(152, 'testError', '«\rßa¹áÏ!M”ÄþBž', 'testError@email.com', 3, '1991-01-01', 1, NULL),
(157, 'aaaaa', '«\rßa¹áÏ!M”ÄþBž', 'fonf@gmail.com', 3, '1991-01-01', 0, NULL),
(158, '2342cf', '«\rßa¹áÏ!M”ÄþBž', 'dfffs@gm', 3, '1901-10-10', 0, NULL),
(159, 'testing12356t', '«\rßa¹áÏ!M”ÄþBž', 'email@email.com', 3, '1990-01-01', 0, NULL),
(162, 'sanitisedPassword', '«\rßa¹áÏ!M”ÄþBž', 'sanitisedPassword@email.com', 3, '1991-01-01', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `webdev_user_details`
--

CREATE TABLE `webdev_user_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(35) NOT NULL,
  `address` varchar(100) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `city` varchar(25) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `picture` varchar(300) DEFAULT NULL,
  `coach` int(11) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_user_details`
--

INSERT INTO `webdev_user_details` (`id`, `user_id`, `name`, `address`, `postcode`, `city`, `phone_number`, `picture`, `coach`, `username`) VALUES
(1, 103, 'Jordan Brown', '712 Happy Hill', 'BT71 0A', 'Belfast', '028718271911', 'sal.png', 2, 'jbrown123'),
(2, 101, 'Jeff Bridges', 'Jeff\'s Address', 'BT76 0AP', 'Belfast', '008021890281', '', 2, ''),
(21, 129, 'sal', 'sal', 'sal', 'sal', '282', NULL, 2, 'sals'),
(25, 133, 'ibib ', 'ofdsjnfodsj', 'ggg', 'gffff', '932872397', NULL, 2, 'teeeeest'),
(26, 128, 'aaaa', '716 Happy Hill', 'BT670SE', 'moira', '07725846761', NULL, 1, 'aaa'),
(27, 135, 'rggrgrgr', 'rggrgrgr', 'rggrgrgr', 'rggrgrgr', '328963298', NULL, 1, 'rggrgrgr'),
(28, 136, 'fikjsbsifb', 'fikjsbsifb', 'fikjsbsifb', 'fikjsbsifb', '40874308', NULL, 1, 'fikjsbsifb'),
(30, 138, 'pass', 'vofdindfon', 'vofdindfon', 'vofdindfon', '3498743', NULL, 1, 'vofdindfon'),
(31, 139, 'jordan', 'odeifje', 'fdoij', 'odfeijhf', '32876238', NULL, 2, 'jbrown1234'),
(34, NULL, 'a', 'a', 'a', 'a', '439734', NULL, 1, 'aa'),
(35, NULL, 'fsfss', 'sfsd', 'sdfsd', 'sfdf', '430982340', NULL, 1, 'sf'),
(36, 144, 'Fitzy', 'Starship Enterprise', '', 'Not Moira', '12345', NULL, 2, 'byemom'),
(39, NULL, 'test', 'test1234', 'a', 'a', '1222', NULL, 1, 'test1234'),
(44, NULL, 'testError', 'testError', 'testError', 'testError', '3189763', NULL, 1, 'testError'),
(49, NULL, 'pa', 'pwjk', 'pj', 'pj', '3209', NULL, 1, 'aaaaa'),
(50, NULL, 'sef', 'iksdefuh', 'wd', 'fewdiun', '432987', NULL, 1, '2342cf'),
(51, NULL, 'jordan', 'aa', 'aa', 'aa', '42534534', NULL, 1, 'testing12356t'),
(53, NULL, 'sanitisedPassword', 'o', 'o', 'o', '01', NULL, 1, 'sanitisedPassword'),
(54, NULL, 'jordan brown', '716 Happy Hill', 'BT71 0AP', 'Belfast', '02891728179', NULL, 2, 'testingdemo123');

-- --------------------------------------------------------

--
-- Table structure for table `webdev_user_stats`
--

CREATE TABLE `webdev_user_stats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `height` decimal(4,1) NOT NULL,
  `starting_weight` decimal(4,1) NOT NULL,
  `weight_current` decimal(4,1) NOT NULL,
  `weight_goal` decimal(4,1) NOT NULL,
  `BMI_current` decimal(4,1) NOT NULL,
  `BMI_goal` decimal(4,1) NOT NULL,
  `body_fat_current` decimal(4,1) NOT NULL,
  `body_fat_goal` decimal(4,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `webdev_user_stats`
--

INSERT INTO `webdev_user_stats` (`id`, `user_id`, `height`, `starting_weight`, `weight_current`, `weight_goal`, `BMI_current`, `BMI_goal`, `body_fat_current`, `body_fat_goal`) VALUES
(1, 103, '181.5', '80.0', '77.0', '65.0', '23.0', '21.0', '13.0', '12.0'),
(2, 101, '175.0', '86.0', '82.0', '75.0', '27.0', '23.0', '18.0', '16.0'),
(6, 133, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0'),
(10, 139, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0'),
(15, 128, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0'),
(16, 135, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0'),
(17, 136, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0'),
(18, 138, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0'),
(20, 144, '116.0', '88.0', '200.0', '450.0', '76.0', '16.0', '1.0', '102.0'),
(31, 129, '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0', '0.0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `webdev_appointments`
--
ALTER TABLE `webdev_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_coach_appt` (`coach_id`),
  ADD KEY `fk_grp_appt` (`group_id`),
  ADD KEY `fk_single_appt` (`user_id`);

--
-- Indexes for table `webdev_appointments_logs`
--
ALTER TABLE `webdev_appointments_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_appt_id` (`appointment_id`);

--
-- Indexes for table `webdev_coach`
--
ALTER TABLE `webdev_coach`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_coach_user` (`user_id`);

--
-- Indexes for table `webdev_groups`
--
ALTER TABLE `webdev_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_coach_groups` (`coach`),
  ADD KEY `fk_member_one` (`member_one`),
  ADD KEY `fk_member_two` (`member_two`),
  ADD KEY `fk_member_three` (`member_three`),
  ADD KEY `fk_member_four` (`member_four`);

--
-- Indexes for table `webdev_images`
--
ALTER TABLE `webdev_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `webdev_inbox`
--
ALTER TABLE `webdev_inbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_coach_inbox` (`coach`),
  ADD KEY `fk_user_inbox` (`user`),
  ADD KEY `fk_group_inbox_id` (`group_id`),
  ADD KEY `fk_user_user_inbox` (`recipient`),
  ADD KEY `fk_sender` (`sender`);

--
-- Indexes for table `webdev_pages`
--
ALTER TABLE `webdev_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webdev_page_content`
--
ALTER TABLE `webdev_page_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_page_id` (`page_id`);

--
-- Indexes for table `webdev_roles`
--
ALTER TABLE `webdev_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webdev_testimonials`
--
ALTER TABLE `webdev_testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_coach_review` (`coach_id`),
  ADD KEY `FK_user_review` (`user_id`);

--
-- Indexes for table `webdev_training_meals`
--
ALTER TABLE `webdev_training_meals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webdev_training_plans`
--
ALTER TABLE `webdev_training_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webdev_training_regime`
--
ALTER TABLE `webdev_training_regime`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_training_plans_d1` (`monday`),
  ADD KEY `fk_training_plans_d2` (`tuesday`),
  ADD KEY `fk_training_plans_d3` (`wednesday`),
  ADD KEY `fk_training_plans_d4` (`thursday`),
  ADD KEY `fk_training_plans_diet` (`diet_plan`),
  ADD KEY `fk_training_plans_d5` (`friday`),
  ADD KEY `fk_training_plans_d7` (`sunday`),
  ADD KEY `fk_training_plans_d6` (`saturday`);

--
-- Indexes for table `webdev_users`
--
ALTER TABLE `webdev_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `FK_role` (`role`);

--
-- Indexes for table `webdev_user_details`
--
ALTER TABLE `webdev_user_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_details_user_id` (`user_id`),
  ADD KEY `fk_coach_a` (`coach`);

--
-- Indexes for table `webdev_user_stats`
--
ALTER TABLE `webdev_user_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `webdev_appointments`
--
ALTER TABLE `webdev_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `webdev_appointments_logs`
--
ALTER TABLE `webdev_appointments_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `webdev_coach`
--
ALTER TABLE `webdev_coach`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `webdev_groups`
--
ALTER TABLE `webdev_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `webdev_images`
--
ALTER TABLE `webdev_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `webdev_inbox`
--
ALTER TABLE `webdev_inbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=892;

--
-- AUTO_INCREMENT for table `webdev_pages`
--
ALTER TABLE `webdev_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `webdev_page_content`
--
ALTER TABLE `webdev_page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `webdev_roles`
--
ALTER TABLE `webdev_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `webdev_testimonials`
--
ALTER TABLE `webdev_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `webdev_training_meals`
--
ALTER TABLE `webdev_training_meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `webdev_training_plans`
--
ALTER TABLE `webdev_training_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `webdev_training_regime`
--
ALTER TABLE `webdev_training_regime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `webdev_users`
--
ALTER TABLE `webdev_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `webdev_user_details`
--
ALTER TABLE `webdev_user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `webdev_user_stats`
--
ALTER TABLE `webdev_user_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `webdev_appointments`
--
ALTER TABLE `webdev_appointments`
  ADD CONSTRAINT `fk_coach_appt` FOREIGN KEY (`coach_id`) REFERENCES `webdev_coach` (`id`),
  ADD CONSTRAINT `fk_grp_appt` FOREIGN KEY (`group_id`) REFERENCES `webdev_groups` (`id`),
  ADD CONSTRAINT `fk_single_appt` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_appointments_logs`
--
ALTER TABLE `webdev_appointments_logs`
  ADD CONSTRAINT `fk_appt_id` FOREIGN KEY (`appointment_id`) REFERENCES `webdev_appointments` (`id`);

--
-- Constraints for table `webdev_coach`
--
ALTER TABLE `webdev_coach`
  ADD CONSTRAINT `fk_coach_user` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_groups`
--
ALTER TABLE `webdev_groups`
  ADD CONSTRAINT `fk_coach_groups` FOREIGN KEY (`coach`) REFERENCES `webdev_coach` (`id`),
  ADD CONSTRAINT `fk_member_four` FOREIGN KEY (`member_four`) REFERENCES `webdev_users` (`id`),
  ADD CONSTRAINT `fk_member_one` FOREIGN KEY (`member_one`) REFERENCES `webdev_users` (`id`),
  ADD CONSTRAINT `fk_member_three` FOREIGN KEY (`member_three`) REFERENCES `webdev_users` (`id`),
  ADD CONSTRAINT `fk_member_two` FOREIGN KEY (`member_two`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_images`
--
ALTER TABLE `webdev_images`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_inbox`
--
ALTER TABLE `webdev_inbox`
  ADD CONSTRAINT `fk_coach_inbox` FOREIGN KEY (`coach`) REFERENCES `webdev_coach` (`id`),
  ADD CONSTRAINT `fk_group_inbox_id` FOREIGN KEY (`group_id`) REFERENCES `webdev_groups` (`id`),
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender`) REFERENCES `webdev_users` (`id`),
  ADD CONSTRAINT `fk_user_inbox` FOREIGN KEY (`user`) REFERENCES `webdev_users` (`id`),
  ADD CONSTRAINT `fk_user_user_inbox` FOREIGN KEY (`recipient`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_page_content`
--
ALTER TABLE `webdev_page_content`
  ADD CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `webdev_pages` (`id`);

--
-- Constraints for table `webdev_testimonials`
--
ALTER TABLE `webdev_testimonials`
  ADD CONSTRAINT `FK_coach_review` FOREIGN KEY (`coach_id`) REFERENCES `webdev_coach` (`id`),
  ADD CONSTRAINT `FK_user_review` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_training_regime`
--
ALTER TABLE `webdev_training_regime`
  ADD CONSTRAINT `fk_training_plans_d1` FOREIGN KEY (`monday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_d2` FOREIGN KEY (`tuesday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_d3` FOREIGN KEY (`wednesday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_d4` FOREIGN KEY (`thursday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_d5` FOREIGN KEY (`friday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_d6` FOREIGN KEY (`saturday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_d7` FOREIGN KEY (`sunday`) REFERENCES `webdev_training_plans` (`id`),
  ADD CONSTRAINT `fk_training_plans_diet` FOREIGN KEY (`diet_plan`) REFERENCES `webdev_training_meals` (`id`),
  ADD CONSTRAINT `fk_user_train` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_users`
--
ALTER TABLE `webdev_users`
  ADD CONSTRAINT `FK_role` FOREIGN KEY (`role`) REFERENCES `webdev_roles` (`id`);

--
-- Constraints for table `webdev_user_details`
--
ALTER TABLE `webdev_user_details`
  ADD CONSTRAINT `fk_coach_a` FOREIGN KEY (`coach`) REFERENCES `webdev_coach` (`id`),
  ADD CONSTRAINT `fk_user_details_user_id` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);

--
-- Constraints for table `webdev_user_stats`
--
ALTER TABLE `webdev_user_stats`
  ADD CONSTRAINT `fk_user_stats` FOREIGN KEY (`user_id`) REFERENCES `webdev_users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
