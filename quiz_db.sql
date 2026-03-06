 -- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2024 at 10:42 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `solitaireinfo_quiz_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', ' 123123'),
(2, 'sahilsandhu', '321321');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `status`) VALUES
(1, ' Computer graphics', 1),
(2, ' Artificial intelligence', 1),
(3, 'Mechanical Engineering', 1),
(4, 'Networking', 1),
(5, 'Software Engineering', 1),
(6, 'Cybersecurity', 1),
(7, 'Data Science', 1),
(8, 'Cloud computing', 1),
(9, 'Networks and security', 1);

-- --------------------------------------------------------

--
-- Table structure for table `create_session`
--

CREATE TABLE `create_session` (
  `id` int(11) NOT NULL,
  `stored` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `current_session`
--

CREATE TABLE `current_session` (
  `id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `current_session`
--

INSERT INTO `current_session` (`id`, `session_name`, `status`, `created_at`, `updated_at`) VALUES
(4, 'jan-2025', 1, '2024-12-20 07:21:47', '2024-12-24 04:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `new_courses`
--

CREATE TABLE `new_courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `published` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_courses`
--

INSERT INTO `new_courses` (`id`, `course_name`, `published`) VALUES
(4, 'Bachelor of Arts (BA)', 1),
(5, 'Bachelor of Science (BSc)', 1),
(6, 'Bachelor of Commerce (BCom)', 1),
(7, 'Bachelor of Business Administration (BBA)', 1),
(8, 'Bachelor of Business Administration - Bachelor of Law (BBA-LLB)', 1),
(9, 'Bachelor of Computer Applications (BCA)', 1),
(10, 'Bachelor of Hotel Management (BHM)', 1),
(11, 'Bachelor of Design (BDes)', 1),
(12, 'Bachelor of Education (BEd)', 1),
(13, 'Bachelor of Pharmacy (BPharm)', 1),
(14, 'Master of Arts (MA)', 1),
(15, 'Master of Science (MSc)', 1),
(16, 'Master of Commerce (MCom)', 1),
(17, 'Master of Business Administration (MBA)', 1),
(18, 'Master of Computer Applications (MCA)', 1),
(19, 'Master of Business Administration - Master of Law (MBA-LLM)', 1),
(20, 'Master of Finance and Control (MFC)', 1),
(21, 'Master of Design (MDes)', 1),
(22, 'Master of Education (MEd)', 1),
(23, 'Master of Social Work (MSW)', 1),
(24, 'Master of Journalism and Mass Communication (MJMC)', 1),
(25, 'Master of Technology (MTech)', 1),
(26, 'Master of Engineering (ME)', 1),
(27, 'Master of Science in Nursing (MSc Nursing)', 1),
(28, 'Master of Dental Surgery (MDS)', 1),
(29, 'Master of Technology (MTech) in Computer Science', 1),
(30, 'Master of Science (MSc) in Computer Science', 1),
(31, 'Master of Information Technology (MIT)', 1),
(32, 'Master of Computer Science and Engineering (MSc in CS or MTech in CS&E)', 1),
(33, 'Master of Artificial Intelligence (AI)', 1),
(34, 'Master of Data Science', 1),
(35, 'Master of Cybersecurity', 1),
(36, 'Master of Cloud Computing', 1),
(37, 'Master of Software Engineering (MSE)', 1),
(38, 'Master of Business Administration (MBA) in Information Technology', 1),
(39, 'Master of Digital Marketing', 1),
(40, 'Master of Networking and Internet Technology', 1),
(41, 'Master of Web Development', 1);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` char(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `section` varchar(50) DEFAULT 'Aptitude'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `created_at`, `section`) VALUES
(21, 2, 'A company is reviewing two strategies for launching a new product. Strategy A involves targeting a specific market niche, while Strategy B focuses on broader market penetration. Which factor would most likely justify choosing Strategy B over Strategy A?', 'The company has limited marketing resources', 'The product appeals to a wide demographic.', 'The niche market offers a higher profit margin.', 'Strategy B is less time-consuming to implement', 'B', '2024-11-23 20:21:54', 'Aptitude'),
(22, 2, 'Of the following two statements, both of which cannot be true, but both can also be false. Which are these two\r\nstatements? I. All machines make noise\r\nII. Some machines are noisy\r\nIII. No machine makes noise IV. Some machines are not noisy', 'I & II', 'I & III', 'III & IV', 'II & IV', 'B', '2024-11-23 20:22:37', 'Aptitude'),
(23, 2, 'During a debate, a speaker claims, \"Because this policy has always worked in the past, it will certainly succeed now.\" What is the flaw in this reasoning?', 'It overlooks alternative policies', 'It assumes past success guarantees future results.', 'It ignores the need for current data.', 'It relies too heavily on expert opinions.', 'B', '2024-11-23 20:23:00', 'Aptitude'),
(24, 2, 'Choose the figure which is different from the rest.', '2', '3', '1', '4', 'B', '2024-11-23 20:23:30', 'Aptitude'),
(25, 2, 'Ornithologist : Bird :: Archealogist : ?', 'Islands', 'Archealogy', 'Mediators', 'Aquatic', 'B', '2024-11-23 20:24:16', 'Aptitude'),
(26, 2, 'In a family there are husband wife, two sons and two daughters. All the ladies were invited to a dinner. Both sons\r\nwent out to play. Husband did not return from office. Who was at home?', 'Only wife was at home', ' All ladies were at home', 'No body was at home', 'Only sons were at home', 'A', '2024-11-23 20:24:59', 'Aptitude'),
(27, 2, 'What was the day of the week on 28th May, 2006?', 'Thursday', 'Friday', 'Sunday', 'Saturday', 'A', '2024-11-23 20:25:33', 'Aptitude'),
(28, 2, 'If A is the son of Q, Q and Y are sisters, Z is the mother of Y, P is the son of Z, then which of the following\r\nstatements is correct?', 'P and Y are sisters', 'A and P are cousins', 'P is the maternal uncle of A', 'None', 'A', '2024-11-23 20:26:17', 'Aptitude'),
(29, 2, 'There are five books A, B, C, D and E placed on a table. If A is placed below E, C is placed above D, B is placed\r\nbelow A and D is placed above E, then which of the following books touches the surface of the table?', 'C', 'A', 'B', 'E', 'A', '2024-11-23 20:26:48', 'Aptitude'),
(30, 2, ' If 7 spiders make 7 webs in 7 days, then 1 spider will make 1 web in how many days?', '1', '7/2', '7', '49', 'A', '2024-11-23 20:27:18', 'Aptitude'),
(31, 2, 'A city is considering a ban on single-use plastics to reduce pollution. Critics argue that this would harm local businesses, while supporters highlight environmental benefits. What is the best way to evaluate the proposed ban?', 'Focus only on short-term economic impacts', 'Ignore the critics\' concerns as biased.', 'Implement the ban immediately without assessment', 'Conduct a comprehensive study on both economic and environmental effects', 'D', '2024-11-23 20:27:45', 'Aptitude'),
(32, 2, 'If TRANSFER is coded as RTNAFSRE, then ELEPHANT would be coded as', 'LEPEHATN', 'LEEPAHTN', 'LEPEAHNT', 'LEPEAHTN', 'D', '2024-11-23 20:28:24', 'Aptitude'),
(33, 2, ' In a certain code, PAINTER is written NCGPRGP, then REASON would be written as', 'PCYQMN', 'PGYQMN', 'PGYUPM', 'PGYUMP', 'D', '2024-11-23 20:29:01', 'Aptitude'),
(34, 2, 'Find the odd one out', 'Raid', 'Attack', 'Ambush', 'Defence', 'D', '2024-11-23 20:29:37', 'Aptitude'),
(35, 2, 'Find the odd one out', 'Arc', 'Radius', 'Diameter', 'Diagonal', 'D', '2024-11-23 20:30:17', 'Aptitude'),
(36, 2, ' truthfulness: court : : cleanliness : _________ ', 'restaurant', 'bath', 'virtue', 'pig', 'C', '2024-11-23 20:30:56', 'Aptitude'),
(37, 2, 'Pituitary : Brain : : Thymus : ?', 'Chest', 'Spinal Cord', 'Throat', 'Larynx', 'C', '2024-11-23 20:31:42', 'Aptitude'),
(38, 2, 'If a quarter kg of potato costs 60 paise, how many paise will 200 gm cost?', '48 paise', '54 paise ', '56 paise', '72 paise', 'C', '2024-11-23 20:32:29', 'Aptitude'),
(39, 2, 'clock is started at noon. By 10 minutes past 5, the hour hand has turned through:', '155°', '145°', '150°', '160°', 'C', '2024-11-23 20:33:07', 'Aptitude'),
(40, 2, 'At a game of billiards, A can give B 15 points in 60 and A can give C to 20 points in 60. How many points can B\r\ngive C in a game of 90?', '10 points', '30 points', '20 points', '12 points', 'C', '2024-11-23 20:33:54', 'Aptitude'),
(41, 1, 'Find the H.C.F, if the numbers are in the ratio of 4 : 5 : 6 and their L.C.M. is 2400.', '35', '20', '40', '65', 'B', '2024-11-23 20:35:41', 'Aptitude'),
(42, 1, 'Find the speed of the boat in still water, if a boat covers a certain distance upstream in 2 hours, while it comes\r\nback in 1`1/2` hours. If the speed of the stream be 3 kmph', '12km/h ', '21km/h ', '18km/h ', '24km/h ', 'B', '2024-11-23 20:36:28', 'Aptitude'),
(43, 1, 'If 2994 ÷ 14.5 = 172, then 29.94 ÷ 1.45 = ?', '17.2', '17', '21.4', '7.1', 'B', '2024-11-23 20:37:13', 'Aptitude'),
(44, 1, 'The sum of the two digits of a number is 10. If the number is subtracted from the number obtained by reversing\r\nits digits, the result is 54. Find the number?', '28', '34', '12', '13', 'B', '2024-11-23 20:37:38', 'Aptitude'),
(45, 1, 'A alone can do a piece of work in 6 days and B alone in 8 days. A and B undertook to do it for Rs. 3200. With\r\nthe help of C, they completed the work in 3 days. How much is to be paid to C?', '375', '600', '400', '800', 'B', '2024-11-23 20:38:07', 'Aptitude'),
(46, 1, 'Ramesh ranks 13th in the class of 33 students. There are 5 students below Suresh rankwise. How many students\r\nare there between Ramesh and Suresh ?', '12', '15', '14', '16', 'A', '2024-11-23 20:38:32', 'Aptitude'),
(47, 1, 'A train 125 m long passes a man, running at 5 km/ hr in the same direction in which the train is going, in 10\r\nseconds. The speed of the train is:', '45 km/hr', '55 km/hr', '54 km/hr', '50 km/hr', 'A', '2024-11-23 20:39:13', 'Aptitude'),
(48, 1, 'Today is Monday. After 61 days, it will be?', 'Sunday', 'Monday', 'Saturday', 'Friday', 'A', '2024-11-23 20:39:48', 'Aptitude'),
(49, 1, 'The angle of elevation of a ladder leaning against a wall is 60° and the foot of the ladder is 4.6 m away from the\r\nwall. The length of the ladder is:', '2.3m', '4.6 m', '9.2 m', '7.8 m', 'A', '2024-11-23 20:40:22', 'Aptitude'),
(50, 1, '12 is related to 36 in the same way 17 is related to ?', '32', '65', '51', '76', 'A', '2024-11-23 20:40:52', 'Aptitude'),
(51, 1, 'The cost price of 20 articles is the same as the selling price of x articles. If the profit is 25%, then the value\r\nof x is:', '9', '29', '32', '16', 'D', '2024-11-23 20:41:19', 'Aptitude'),
(52, 1, 'A fruit seller had some apples. He sells 40% apples and still has 420 apples. Originally, he had:', '588 apples', ' 600 apples', ' 672 apples', '700 apples', 'D', '2024-11-23 20:42:03', 'Aptitude'),
(53, 1, 'The average age of husband, wife and their child 3 years ago was 27 years and that of wife and the child 5 years\r\nago was 20 years. The present age of the husband is:', '35 years', '40 years', '50 years', 'None of these', 'D', '2024-11-23 20:42:42', 'Aptitude'),
(54, 1, 'A is two years older than B who is twice as old as C.If the total of the ages of A, B and C be 27then how old is B?', '7', '8', '9', '10', 'D', '2024-11-23 20:43:14', 'Aptitude'),
(55, 1, 'A grocer has a sale of Rs. 6435, Rs. 6927, Rs. 6855, Rs. 7230 and Rs. 6562 for 5 consecutive months. How\r\nmuch sale must he have in the sixth month so that he gets an average sale of Rs. 6500?', 'Rs 6001 ', 'Rs 6991', 'Rs 5991', 'RS 4991', 'D', '2024-11-23 20:43:54', 'Aptitude'),
(56, 1, 'A boat takes 90 minutes less to travel 36 miles downstream than to travel the same distance upstream. If the\r\nspeed of the boat in still water is 10 mph, the speed of the stream is:', '2 mph', '2.5 mph', '3 mph', '4 mph', 'C', '2024-11-23 20:44:31', 'Aptitude'),
(57, 1, 'Two numbers are respectively 20% and 50% more than a third number. The ratio of the two numbers is:', ' 4:5', ' 3:5', ' 2:5', '6:7', 'C', '2024-11-23 20:45:19', 'Aptitude'),
(58, 1, ' fill the question mark?', '2', '1', '3', '4', 'C', '2024-11-23 20:45:44', 'Aptitude'),
(59, 1, 'Find the odd man out….6, 9, 15, 21, 24, 28, 30.', '28', '21', '24', '30', 'C', '2024-11-23 20:46:06', 'Aptitude'),
(60, 1, 'In how many ways can the letters of the word \'LEADER\' be arranged?', '360', '72', '144', '720', 'C', '2024-11-23 20:46:32', 'Aptitude'),
(61, 4, 'I prefer to stay in background', 'Very inaccurate', ' Moderately inaccurate', 'very accurate', 'Moderately accurate', 'B', '2024-11-23 20:48:14', 'Aptitude'),
(62, 4, 'I keep going until everything is perfected .', 'Very inaccurate', 'Moderately accurate', 'Very accurate', 'Moderately inaccurate', 'B', '2024-11-23 20:49:02', 'Aptitude'),
(63, 4, 'I find it easy to get along with people .', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'B', '2024-11-23 20:49:58', 'Aptitude'),
(64, 4, 'I hate it when people contradict me .', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'B', '2024-11-23 20:50:43', 'Aptitude'),
(65, 4, 'I find my opinions of people change often .', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'B', '2024-11-23 20:51:24', 'Aptitude'),
(66, 4, 'I believe that people generally untrustworthy .\r\n', 'Very inaccurate', 'Moderately inaccurate', 'Moderately accurate', 'Very accurate', 'A', '2024-11-23 20:52:12', 'Aptitude'),
(67, 4, 'I donot get lost in my thoughts .', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'A', '2024-11-23 20:52:43', 'Aptitude'),
(68, 4, 'I see myself as reserved ,quiet .', 'Moderately inaccurate', 'Moderately accurate', 'very inaccurate', 'Very accurate', 'A', '2024-11-23 20:53:23', 'Aptitude'),
(69, 4, 'I see myself sympathetic and warm  ', 'Moderately inaccurate', 'Moderately accurate', 'Very accurate', 'Very inaccurate', 'A', '2024-11-23 20:54:35', 'Aptitude'),
(70, 4, 'I see myself as Anxious ,easily upset .', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'A', '2024-11-23 20:55:01', 'Aptitude'),
(71, 4, 'I see myself dependable .', 'Very inaccurate', 'Moderately accurate', 'Very accurate', 'Moderately inaccurate', 'D', '2024-11-23 20:56:03', 'Aptitude'),
(72, 4, 'When do you feel your best ?', 'In the morning', ' in afternoon and evening', 'Late at night', 'Anytime', 'D', '2024-11-23 20:56:41', 'Aptitude'),
(73, 4, 'Which color do you like the most ?', 'Red or orange', 'Black', 'Green', 'White', 'D', '2024-11-23 20:57:23', 'Aptitude'),
(74, 4, 'You often dream that you are ', 'Searching for something', 'Falling', 'Fighting or struggling', ' Flying and floating', 'D', '2024-11-23 20:58:08', 'Aptitude'),
(75, 4, ' You are working very hard ,concentrating hard and you are interrupted.\r\n', 'Take the break', 'vary between all these cases', 'calm', 'feel extremely irritated', 'D', '2024-11-23 20:58:57', 'Aptitude'),
(76, 4, 'I see myself open to new experiences .', 'Very accurate', 'Moderately accurate', 'Moderately inaccurate', 'Very inaccurate', 'C', '2024-11-23 20:59:40', 'Aptitude'),
(77, 4, ' I see myself calm and emotionaly stable .\r\n', 'Somewhat accurate', 'Moderately accurate', 'moderately inaccurate', 'Very accurate', 'C', '2024-11-23 21:00:30', 'Aptitude'),
(78, 4, 'I see myself as critical ,quaerrelsome', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'C', '2024-11-23 21:01:08', 'Aptitude'),
(79, 4, 'Do you have a place for everything and keep everything at place .', 'Very accurate', 'Moderately accurate', 'Moderately inaccurate', 'Very inaccurate', 'C', '2024-11-23 21:01:48', 'Aptitude'),
(80, 4, 'I see myself conventional ,uncreative .', 'Very inaccurate', 'Moderately accurate', 'Moderately inaccurate', 'Very accurate', 'C', '2024-11-23 21:02:22', 'Aptitude'),
(94, 0, 'what is the variable?', 'a', 'd', 'd', 'd', 'A', '2024-12-24 05:06:47', 'Aptitude');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_limit` int(11) DEFAULT 300,
  `status` enum('published','unpublished') DEFAULT 'unpublished',
  `published` tinyint(1) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `group_id`, `created_at`, `time_limit`, `status`, `published`, `category_id`) VALUES
(1, 'GENERAL APTITUDE TEST', 'W', 1, '2024-11-23 20:34:42', 1200, 'published', 0, NULL),
(2, 'CRITICAL REASONING TEST', '', 1, '2024-11-23 20:19:41', 1200, 'published', 0, NULL),
(4, 'Psychometric Test', '', 2, '2024-11-23 20:47:04', 900, 'published', 0, NULL),
(30, 'Technical Written', 'Technical  test for', 3, '2024-12-24 04:39:53', 300, 'unpublished', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_responses`
--

CREATE TABLE `quiz_responses` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `submission_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `question_id` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_responses`
--

INSERT INTO `quiz_responses` (`id`, `student_id`, `quiz_id`, `result`, `submission_time`, `question_id`, `answer`) VALUES
(168, 165, 2, 1, '2024-12-19 12:07:08', 0, ''),
(169, 165, 4, 0, '2024-12-19 12:07:09', 0, ''),
(170, 165, 13, 1, '2024-12-19 12:07:11', 0, ''),
(171, 166, 2, 5, '2024-12-19 12:14:00', 0, ''),
(172, 166, 4, 3, '2024-12-19 12:14:20', 0, ''),
(173, 166, 13, 0, '2024-12-19 12:14:22', 0, ''),
(174, 167, 2, 7, '2024-12-19 12:40:23', 0, ''),
(175, 167, 4, 2, '2024-12-19 12:40:36', 0, ''),
(176, 167, 13, 0, '2024-12-19 12:40:38', 0, ''),
(177, 168, 4, 0, '2024-12-19 12:43:48', 0, ''),
(178, 168, 2, 0, '2024-12-19 12:43:53', 0, ''),
(179, 248, 2, 0, '2024-12-20 08:57:30', 0, ''),
(180, 248, 4, 0, '2024-12-20 08:57:31', 0, ''),
(181, 248, 13, 0, '2024-12-20 08:57:31', 0, ''),
(182, 253, 2, 2, '2024-12-20 09:15:42', 0, ''),
(183, 253, 4, 2, '2024-12-20 09:15:49', 0, ''),
(184, 253, 13, 0, '2024-12-20 09:15:51', 0, ''),
(185, 254, 2, 1, '2024-12-20 09:17:25', 0, ''),
(186, 254, 4, 0, '2024-12-20 09:17:28', 0, ''),
(187, 254, 13, 0, '2024-12-20 09:17:30', 0, ''),
(188, 3, 2, 1, '2024-12-20 11:10:13', 0, ''),
(189, 3, 4, 0, '2024-12-20 11:10:14', 0, ''),
(190, 3, 13, 0, '2024-12-20 11:10:14', 0, ''),
(191, 4, 2, 1, '2024-12-20 11:19:35', 0, ''),
(192, 4, 4, 0, '2024-12-20 11:19:37', 0, ''),
(193, 4, 13, 0, '2024-12-20 11:19:41', 0, ''),
(194, 5, 2, 3, '2024-12-20 11:40:56', 0, ''),
(195, 5, 4, 1, '2024-12-20 11:41:06', 0, ''),
(196, 5, 13, 0, '2024-12-20 11:41:06', 0, ''),
(197, 6, 2, 0, '2024-12-20 12:29:18', 0, ''),
(198, 6, 4, 0, '2024-12-20 12:29:21', 0, ''),
(199, 6, 13, 0, '2024-12-20 12:29:22', 0, ''),
(200, 7, 2, 1, '2024-12-20 12:49:29', 0, ''),
(201, 7, 4, 1, '2024-12-20 12:49:46', 0, ''),
(202, 7, 13, 0, '2024-12-20 12:49:47', 0, ''),
(203, 8, 2, 0, '2024-12-21 07:07:16', 0, ''),
(204, 8, 4, 0, '2024-12-21 07:07:18', 0, ''),
(205, 8, 13, 0, '2024-12-21 07:07:18', 0, ''),
(206, 12, 2, 0, '2024-12-21 07:40:28', 0, ''),
(207, 12, 4, 0, '2024-12-21 07:40:32', 0, ''),
(208, 12, 13, 0, '2024-12-21 07:40:52', 0, ''),
(209, 13, 2, 0, '2024-12-21 07:44:32', 0, ''),
(210, 13, 4, 0, '2024-12-21 07:44:33', 0, ''),
(211, 13, 13, 0, '2024-12-21 07:44:37', 0, ''),
(212, 18, 2, 0, '2024-12-21 09:06:28', 0, ''),
(213, 20, 2, 1, '2024-12-21 09:15:34', 0, ''),
(214, 20, 4, 0, '2024-12-21 10:04:19', 0, ''),
(215, 20, 13, 0, '2024-12-21 10:04:20', 0, ''),
(216, 21, 2, 0, '2024-12-21 10:05:26', 0, ''),
(217, 21, 4, 0, '2024-12-21 10:05:27', 0, ''),
(218, 21, 13, 0, '2024-12-21 10:05:29', 0, ''),
(219, 22, 2, 0, '2024-12-21 10:24:37', 0, ''),
(220, 22, 4, 0, '2024-12-21 10:25:37', 0, ''),
(221, 22, 13, 0, '2024-12-21 10:26:38', 0, ''),
(222, 23, 1, 0, '2024-12-21 10:40:19', 0, ''),
(223, 23, 2, 0, '2024-12-21 10:41:20', 0, ''),
(224, 23, 4, 0, '2024-12-21 10:42:20', 0, ''),
(225, 24, 1, 0, '2024-12-21 11:34:09', 0, ''),
(226, 24, 4, 0, '2024-12-21 11:48:43', 0, ''),
(227, 25, 1, 1, '2024-12-21 12:22:21', 0, ''),
(228, 25, 2, 0, '2024-12-21 12:23:21', 0, ''),
(229, 25, 4, 0, '2024-12-21 12:24:22', 0, ''),
(230, 27, 1, 0, '2024-12-23 05:41:40', 0, ''),
(231, 27, 2, 0, '2024-12-23 05:41:41', 0, ''),
(232, 27, 4, 0, '2024-12-23 05:44:45', 0, ''),
(233, 28, 1, 0, '2024-12-23 05:48:19', 0, ''),
(234, 28, 2, 0, '2024-12-23 05:48:20', 0, ''),
(235, 28, 4, 0, '2024-12-23 05:49:21', 0, ''),
(236, 29, 1, 0, '2024-12-23 05:55:19', 0, ''),
(237, 29, 2, 0, '2024-12-23 05:55:21', 0, ''),
(238, 30, 1, 0, '2024-12-23 06:02:04', 0, ''),
(239, 30, 2, 0, '2024-12-23 06:02:07', 0, ''),
(240, 31, 1, 0, '2024-12-23 06:28:54', 0, ''),
(241, 31, 2, 0, '2024-12-23 06:28:55', 0, ''),
(242, 31, 4, 0, '2024-12-23 06:52:04', 0, ''),
(243, 32, 2, 0, '2024-12-23 06:56:05', 0, ''),
(244, 32, 1, 0, '2024-12-23 06:58:40', 0, ''),
(245, 33, 1, 0, '2024-12-23 06:59:14', 0, ''),
(246, 33, 2, 0, '2024-12-23 06:59:15', 0, ''),
(247, 33, 4, 0, '2024-12-23 07:00:41', 0, ''),
(248, 34, 1, 0, '2024-12-23 07:08:12', 0, ''),
(249, 34, 2, 0, '2024-12-23 07:08:13', 0, ''),
(250, 35, 1, 0, '2024-12-23 07:11:43', 0, ''),
(251, 35, 2, 0, '2024-12-23 07:11:44', 0, ''),
(252, 36, 1, 0, '2024-12-23 07:15:20', 0, ''),
(253, 36, 2, 0, '2024-12-23 07:15:23', 0, ''),
(254, 37, 1, 2, '2024-12-23 07:18:59', 0, ''),
(255, 37, 2, 0, '2024-12-23 07:19:00', 0, ''),
(256, 38, 1, 0, '2024-12-23 07:25:07', 0, ''),
(257, 38, 2, 0, '2024-12-23 07:25:08', 0, ''),
(258, 39, 1, 0, '2024-12-23 08:24:49', 0, ''),
(259, 39, 2, 0, '2024-12-23 08:24:50', 0, ''),
(260, 39, 1, 0, '2024-12-23 08:36:36', 0, ''),
(261, 39, 2, 0, '2024-12-23 08:36:37', 0, ''),
(262, 39, 1, 0, '2024-12-23 08:36:43', 0, ''),
(263, 39, 2, 0, '2024-12-23 08:36:43', 0, ''),
(264, 39, 1, 0, '2024-12-23 08:36:48', 0, ''),
(265, 40, 1, 0, '2024-12-23 08:38:14', 0, ''),
(266, 40, 2, 0, '2024-12-23 08:38:15', 0, ''),
(267, 41, 1, 0, '2024-12-23 08:40:51', 0, ''),
(268, 41, 2, 0, '2024-12-23 08:40:52', 0, ''),
(269, 42, 1, 0, '2024-12-23 08:42:53', 0, ''),
(270, 42, 2, 0, '2024-12-23 08:42:53', 0, ''),
(271, 42, 4, 0, '2024-12-23 08:44:02', 0, ''),
(272, 43, 1, 0, '2024-12-23 08:46:48', 0, ''),
(273, 43, 2, 0, '2024-12-23 08:46:49', 0, ''),
(274, 44, 2, 0, '2024-12-23 08:58:12', 0, ''),
(275, 44, 4, 0, '2024-12-23 09:01:09', 0, ''),
(276, 45, 1, 0, '2024-12-23 09:03:51', 0, ''),
(277, 45, 2, 0, '2024-12-23 09:03:53', 0, ''),
(278, 45, 4, 0, '2024-12-23 09:08:30', 0, ''),
(279, 46, 1, 0, '2024-12-23 09:10:16', 0, ''),
(280, 46, 2, 0, '2024-12-23 09:10:17', 0, ''),
(281, 47, 2, 0, '2024-12-23 09:14:16', 0, ''),
(282, 48, 2, 0, '2024-12-23 09:14:38', 0, ''),
(283, 48, 1, 0, '2024-12-23 09:15:08', 0, ''),
(284, 49, 1, 0, '2024-12-23 09:15:25', 0, ''),
(285, 49, 2, 0, '2024-12-23 09:15:26', 0, ''),
(286, 50, 1, 0, '2024-12-23 09:23:05', 0, ''),
(287, 50, 2, 0, '2024-12-23 09:23:06', 0, ''),
(288, 51, 1, 0, '2024-12-23 09:26:20', 0, ''),
(289, 51, 2, 0, '2024-12-23 09:26:21', 0, ''),
(290, 52, 1, 0, '2024-12-23 09:34:42', 0, ''),
(291, 52, 2, 1, '2024-12-23 09:35:17', 0, ''),
(292, 53, 1, 0, '2024-12-23 09:35:44', 0, ''),
(293, 53, 2, 0, '2024-12-23 09:35:46', 0, ''),
(294, 54, 1, 0, '2024-12-23 09:43:24', 0, ''),
(295, 54, 2, 0, '2024-12-23 09:43:24', 0, ''),
(296, 55, 1, 0, '2024-12-23 09:45:18', 0, ''),
(297, 55, 2, 0, '2024-12-23 09:45:19', 0, ''),
(298, 56, 1, 1, '2024-12-23 09:47:28', 0, ''),
(299, 56, 2, 0, '2024-12-23 09:47:29', 0, ''),
(300, 57, 1, 0, '2024-12-23 09:50:28', 0, ''),
(301, 57, 2, 0, '2024-12-23 09:50:31', 0, ''),
(302, 53, 4, 0, '2024-12-23 09:55:28', 0, ''),
(303, 57, 4, 0, '2024-12-23 10:04:53', 0, ''),
(304, 58, 1, 0, '2024-12-23 11:06:04', 0, ''),
(305, 59, 1, 5, '2024-12-23 11:12:28', 0, ''),
(306, 59, 2, 0, '2024-12-23 11:12:31', 0, ''),
(307, 61, 1, 0, '2024-12-23 11:55:36', 0, ''),
(308, 61, 2, NULL, '2024-12-23 12:00:26', 39, 'option_c'),
(309, 61, 4, 0, '2024-12-23 12:01:14', 0, ''),
(310, 62, 1, 1, '2024-12-23 12:02:45', 0, ''),
(311, 62, 2, 1, '2024-12-23 12:02:49', 0, ''),
(312, 63, 1, 9, '2024-12-23 12:10:08', 0, ''),
(313, 63, 2, 1, '2024-12-23 12:10:18', 0, ''),
(314, 64, 1, 0, '2024-12-23 12:18:17', 0, ''),
(315, 60, 1, 0, '2024-12-23 12:30:13', 0, ''),
(316, 64, 2, 0, '2024-12-23 12:37:58', 0, ''),
(317, 64, 2, 0, '2024-12-23 12:40:21', 0, ''),
(318, 66, 1, 4, '2024-12-24 04:35:34', 0, ''),
(319, 66, 2, 3, '2024-12-24 04:36:16', 0, ''),
(320, 67, 30, 0, '2024-12-24 05:08:58', 0, ''),
(321, 68, 1, 0, '2024-12-24 07:12:16', 0, ''),
(322, 68, 2, 0, '2024-12-24 07:12:17', 0, ''),
(323, 68, 30, 0, '2024-12-24 07:12:25', 0, ''),
(324, 68, 32, 0, '2024-12-24 07:12:26', 0, ''),
(325, 70, 1, 0, '2024-12-24 08:04:28', 0, ''),
(326, 70, 2, 0, '2024-12-24 08:04:29', 0, ''),
(327, 71, 1, 0, '2024-12-24 08:06:06', 0, ''),
(328, 71, 2, 0, '2024-12-24 08:06:07', 0, ''),
(329, 72, 1, 1, '2024-12-24 08:14:33', 0, ''),
(330, 72, 2, 0, '2024-12-24 08:14:34', 0, ''),
(331, 72, 4, 0, '2024-12-24 10:06:55', 0, ''),
(332, 73, 4, 0, '2024-12-24 10:26:28', 0, ''),
(333, 74, 4, 0, '2024-12-24 10:58:54', 0, ''),
(334, 75, 1, 0, '2024-12-24 11:01:35', 0, ''),
(335, 75, 2, 0, '2024-12-24 11:01:36', 0, ''),
(336, 75, 4, 0, '2024-12-24 11:01:54', 0, ''),
(337, 76, 1, 0, '2024-12-24 11:02:57', 0, ''),
(338, 76, 2, 0, '2024-12-24 11:02:58', 0, ''),
(339, 77, 4, 0, '2024-12-24 11:05:33', 0, ''),
(340, 78, 1, 1, '2024-12-24 11:21:43', 0, ''),
(341, 78, 2, 0, '2024-12-24 11:21:45', 0, ''),
(342, 79, 4, 0, '2024-12-24 11:27:58', 0, ''),
(343, 80, 4, 1, '2024-12-24 11:30:06', 0, ''),
(344, 81, 4, 0, '2024-12-24 11:35:09', 0, ''),
(345, 82, 16, 0, '2024-12-24 11:39:34', 0, ''),
(346, 82, 16, 0, '2024-12-24 11:39:35', 0, ''),
(347, 82, 16, 0, '2024-12-24 11:39:35', 0, ''),
(348, 82, 18, 0, '2024-12-24 11:39:42', 0, ''),
(349, 82, 18, 0, '2024-12-24 11:39:42', 0, ''),
(350, 82, 16, 0, '2024-12-24 11:40:58', 0, ''),
(351, 82, 16, 0, '2024-12-24 11:40:58', 0, ''),
(352, 82, 16, 0, '2024-12-24 11:40:58', 0, ''),
(353, 82, 16, 0, '2024-12-24 11:40:58', 0, ''),
(354, 82, 16, 0, '2024-12-24 11:40:58', 0, ''),
(355, 82, 16, 0, '2024-12-24 11:41:27', 0, ''),
(356, 82, 4, 0, '2024-12-24 11:42:09', 0, ''),
(357, 82, 16, 0, '2024-12-24 11:42:19', 0, ''),
(358, 82, 16, 0, '2024-12-24 11:42:20', 0, ''),
(359, 82, 16, 0, '2024-12-24 11:44:39', 0, ''),
(360, 82, 16, 0, '2024-12-24 11:44:40', 0, ''),
(361, 82, 16, 0, '2024-12-24 11:45:12', 0, ''),
(362, 82, 16, 0, '2024-12-24 11:45:21', 0, ''),
(363, 82, 16, 0, '2024-12-24 11:45:22', 0, ''),
(364, 82, 16, 0, '2024-12-24 11:45:22', 0, ''),
(365, 86, 1, 0, '2024-12-25 05:43:21', 0, ''),
(366, 86, 2, 0, '2024-12-25 05:43:21', 0, ''),
(367, 86, 4, 0, '2024-12-25 05:43:22', 0, ''),
(368, 86, 30, 0, '2024-12-25 05:43:23', 0, ''),
(369, 87, 1, 0, '2024-12-25 05:49:04', 0, ''),
(370, 87, 2, 0, '2024-12-25 05:49:05', 0, ''),
(371, 87, 4, 0, '2024-12-25 05:49:06', 0, ''),
(372, 87, 30, 0, '2024-12-25 05:49:08', 0, ''),
(373, 88, 2, 0, '2024-12-25 05:52:22', 0, ''),
(374, 88, 1, 0, '2024-12-25 05:52:27', 0, ''),
(375, 89, 1, 0, '2024-12-25 05:56:20', 0, ''),
(376, 89, 2, 0, '2024-12-25 05:56:21', 0, ''),
(377, 90, 1, 0, '2024-12-25 06:06:40', 0, ''),
(378, 90, 2, 0, '2024-12-25 06:06:41', 0, ''),
(379, 91, 1, 0, '2024-12-25 06:13:13', 0, ''),
(380, 91, 2, 0, '2024-12-25 06:13:14', 0, ''),
(381, 92, 1, 1, '2024-12-25 06:20:23', 0, ''),
(382, 92, 2, 1, '2024-12-25 06:20:33', 0, ''),
(383, 93, 1, 1, '2024-12-25 06:24:56', 0, ''),
(384, 93, 2, 1, '2024-12-25 06:25:02', 0, ''),
(385, 94, 1, 0, '2024-12-25 06:32:44', 0, ''),
(386, 94, 2, 1, '2024-12-25 06:32:47', 0, ''),
(387, 95, 1, 0, '2024-12-25 06:35:44', 0, ''),
(388, 95, 2, 0, '2024-12-25 06:35:46', 0, ''),
(389, 96, 1, 1, '2024-12-25 06:37:05', 0, ''),
(390, 96, 2, 0, '2024-12-25 06:37:07', 0, ''),
(391, 97, 1, 0, '2024-12-25 06:42:33', 0, ''),
(392, 97, 2, 0, '2024-12-25 06:42:34', 0, ''),
(393, 98, 1, 1, '2024-12-25 06:45:50', 0, ''),
(394, 98, 2, 0, '2024-12-25 06:45:51', 0, ''),
(395, 98, 4, 1, '2024-12-25 06:47:55', 0, ''),
(396, 99, 1, 3, '2024-12-25 06:52:07', 0, ''),
(397, 99, 2, 0, '2024-12-25 06:52:15', 0, ''),
(398, 100, 1, 3, '2024-12-25 07:08:27', 0, ''),
(399, 100, 2, 1, '2024-12-25 07:08:43', 0, ''),
(400, 100, 4, 0, '2024-12-25 07:09:02', 0, ''),
(401, 101, 4, 0, '2024-12-25 08:28:27', 0, ''),
(402, 101, 1, 0, '2024-12-25 08:28:31', 0, ''),
(403, 101, 2, 0, '2024-12-25 08:28:32', 0, ''),
(404, 102, 1, 1, '2024-12-25 08:29:17', 0, ''),
(405, 102, 2, 1, '2024-12-25 08:31:32', 0, ''),
(406, 103, 1, 0, '2024-12-25 08:31:57', 0, ''),
(407, 103, 2, 0, '2024-12-25 08:32:10', 0, ''),
(408, 103, 4, 1, '2024-12-25 08:34:30', 0, ''),
(409, 106, 1, 3, '2024-12-26 04:37:57', 0, ''),
(410, 106, 2, 0, '2024-12-26 04:38:00', 0, ''),
(411, 106, 4, 2, '2024-12-26 04:38:24', 0, ''),
(412, 107, 4, 0, '2024-12-26 04:40:48', 0, ''),
(413, 108, 4, 0, '2024-12-26 04:47:02', 0, ''),
(414, 108, 4, 0, '2024-12-26 04:47:06', 0, ''),
(415, 108, 4, 0, '2024-12-26 04:47:32', 0, ''),
(416, 109, 4, 0, '2024-12-26 04:49:48', 0, ''),
(417, 110, 4, 0, '2024-12-26 04:52:34', 0, ''),
(418, 108, 1, 0, '2024-12-26 04:53:18', 0, ''),
(419, 108, 2, 0, '2024-12-26 04:53:42', 0, ''),
(420, 111, 1, 0, '2024-12-26 04:54:02', 0, ''),
(421, 111, 2, 0, '2024-12-26 04:54:27', 0, ''),
(422, 112, 1, 0, '2024-12-26 04:55:11', 0, ''),
(423, 112, 2, 0, '2024-12-26 04:55:12', 0, ''),
(424, 112, 4, 0, '2024-12-26 05:02:29', 0, ''),
(425, 113, 4, 0, '2024-12-26 05:10:40', 0, ''),
(426, 114, 4, 0, '2024-12-26 05:12:07', 0, ''),
(427, 116, 4, 0, '2024-12-26 05:50:19', 0, ''),
(428, 121, 1, 2, '2024-12-26 11:41:16', 0, ''),
(429, 121, 2, 1, '2024-12-26 11:41:19', 0, ''),
(430, 121, 4, 1, '2024-12-26 11:41:41', 0, ''),
(431, 122, 1, 0, '2024-12-27 04:31:56', 0, ''),
(432, 123, 1, 0, '2024-12-27 04:33:44', 0, ''),
(433, 123, 2, 0, '2024-12-27 04:33:46', 0, ''),
(434, 125, 1, 0, '2024-12-27 05:26:12', 0, ''),
(435, 125, 2, 0, '2024-12-27 05:26:12', 0, ''),
(436, 127, 1, 0, '2024-12-27 05:36:53', 0, ''),
(437, 127, 2, 0, '2024-12-27 05:36:55', 0, ''),
(438, 129, 1, 0, '2024-12-27 05:52:11', 0, ''),
(439, 129, 2, 0, '2024-12-27 05:52:12', 0, ''),
(440, 130, 1, 0, '2024-12-27 05:55:08', 0, ''),
(441, 130, 2, 0, '2024-12-27 05:55:10', 0, ''),
(442, 131, 1, 0, '2024-12-27 05:56:13', 0, ''),
(443, 131, 2, 0, '2024-12-27 05:56:14', 0, ''),
(444, 132, 1, 2, '2024-12-27 11:04:51', 0, ''),
(445, 132, 2, 1, '2024-12-27 11:04:55', 0, ''),
(446, 132, 4, 0, '2024-12-27 11:05:11', 0, ''),
(447, 133, 1, 7, '2024-12-27 11:08:44', 0, ''),
(448, 133, 2, 1, '2024-12-27 11:09:02', 0, ''),
(449, 133, 4, 1, '2024-12-27 11:09:18', 0, ''),
(450, 134, 1, 6, '2024-12-27 11:13:32', 0, ''),
(451, 134, 2, 1, '2024-12-27 11:13:34', 0, ''),
(452, 135, 1, 3, '2024-12-27 11:25:13', 0, ''),
(453, 135, 2, 2, '2024-12-27 11:25:18', 0, ''),
(454, 136, 1, 4, '2024-12-27 11:48:00', 0, ''),
(455, 136, 2, 2, '2024-12-27 11:48:04', 0, ''),
(456, 137, 1, 0, '2024-12-27 12:11:34', 0, ''),
(457, 137, 2, 0, '2024-12-27 12:11:35', 0, ''),
(458, 138, 1, 0, '2024-12-27 12:14:00', 0, ''),
(459, 138, 2, 1, '2024-12-27 12:14:02', 0, ''),
(460, 138, 4, 0, '2024-12-27 12:14:13', 0, ''),
(461, 139, 1, 0, '2024-12-27 12:18:06', 0, ''),
(462, 139, 2, 0, '2024-12-27 12:18:07', 0, ''),
(463, 140, 1, 0, '2024-12-27 12:22:23', 0, ''),
(464, 140, 2, 0, '2024-12-27 12:22:24', 0, ''),
(465, 141, 1, 0, '2024-12-27 12:32:15', 0, ''),
(466, 141, 2, 0, '2024-12-27 12:32:16', 0, ''),
(467, 142, 1, 0, '2024-12-28 04:42:45', 0, ''),
(468, 142, 2, 0, '2024-12-28 04:42:46', 0, ''),
(469, 142, 4, 0, '2024-12-28 04:43:21', 0, ''),
(470, 143, 1, 2, '2024-12-28 05:24:01', 0, ''),
(471, 143, 2, 1, '2024-12-28 05:24:03', 0, ''),
(472, 143, 4, 0, '2024-12-28 05:32:33', 0, ''),
(473, 144, 1, 9, '2024-12-28 05:34:59', 0, ''),
(474, 144, 2, 4, '2024-12-28 05:35:30', 0, ''),
(475, 144, 4, 0, '2024-12-28 05:41:21', 0, ''),
(476, 145, 1, 0, '2024-12-28 06:11:15', 0, ''),
(477, 145, 2, 0, '2024-12-28 06:11:16', 0, ''),
(478, 145, 4, 2, '2024-12-28 06:11:35', 0, ''),
(479, 146, 1, 0, '2024-12-28 06:14:01', 0, ''),
(480, 146, 2, 0, '2024-12-28 06:14:02', 0, ''),
(481, 146, 4, 0, '2024-12-28 06:14:19', 0, ''),
(482, 147, 1, 3, '2024-12-28 06:18:52', 0, ''),
(483, 147, 2, 1, '2024-12-28 06:18:57', 0, ''),
(484, 147, 4, 0, '2024-12-28 06:19:23', 0, ''),
(485, 148, 1, 3, '2024-12-28 06:23:23', 0, ''),
(486, 148, 2, 0, '2024-12-28 06:23:28', 0, ''),
(487, 149, 1, 2, '2024-12-28 10:15:18', 0, ''),
(488, 149, 2, 1, '2024-12-28 10:15:48', 0, ''),
(489, 150, 1, 0, '2024-12-28 12:48:08', 0, ''),
(490, 150, 2, 0, '2024-12-28 12:48:14', 0, ''),
(491, 150, 4, 0, '2024-12-28 12:51:25', 0, ''),
(492, 151, 1, 6, '2024-12-31 04:53:12', 0, ''),
(493, 151, 2, 0, '2024-12-31 04:53:16', 0, ''),
(494, 152, 1, 5, '2024-12-31 04:56:32', 0, ''),
(495, 152, 2, 4, '2024-12-31 04:57:06', 0, ''),
(496, 152, 4, 1, '2024-12-31 05:03:51', 0, ''),
(497, 153, 1, 2, '2024-12-31 05:38:33', 0, ''),
(498, 153, 2, 0, '2024-12-31 05:38:40', 0, ''),
(499, 153, 4, 3, '2024-12-31 05:40:14', 0, ''),
(500, 154, 1, 1, '2024-12-31 06:00:10', 0, ''),
(501, 154, 2, 2, '2024-12-31 06:00:26', 0, ''),
(502, 155, 1, 0, '2024-12-31 06:31:36', 0, ''),
(503, 155, 2, 0, '2024-12-31 06:31:36', 0, ''),
(504, 156, 1, 0, '2024-12-31 06:31:53', 0, ''),
(505, 156, 2, 0, '2024-12-31 06:31:54', 0, ''),
(506, 157, 1, 0, '2024-12-31 06:51:36', 0, ''),
(507, 157, 2, 0, '2024-12-31 06:51:36', 0, ''),
(508, 158, 1, 0, '2024-12-31 06:51:52', 0, ''),
(509, 158, 2, 0, '2024-12-31 06:51:53', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_test_participation`
--

CREATE TABLE `quiz_test_participation` (
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `college` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(15) NOT NULL,
  `session` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `selected` tinyint(1) DEFAULT 0,
  `test_completed` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `college`, `course`, `semester`, `branch`, `contact`, `email`, `whatsapp`, `session`, `created_at`, `selected`, `test_completed`, `is_published`) VALUES
(157, 'sahilsandhu', 'Chandigarh College Of Engineering and Technology', 'Master of Arts (MA)', '3rd', '3', '7807697370', 'sahilsandhuphp@gmail.com', '7807697370', 'jan-2025', '2024-12-31 06:51:29', 1, 0, 1),
(158, 'JogvinSSSder Singh', 'University Institute Of Engineering and Technology, Panjab University', 'Master of Commerce (MCom)', '4th', '3', '1231231231', 'jogvinderasds@gmail.com', '1231231231', 'jan-2025', '2024-12-31 06:51:51', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tech_questions`
--

CREATE TABLE `tech_questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_A` varchar(255) NOT NULL,
  `option_B` varchar(255) NOT NULL,
  `option_C` varchar(255) NOT NULL,
  `option_D` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL,
  `quiz_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tech_questions`
--

INSERT INTO `tech_questions` (`id`, `question`, `option_A`, `option_B`, `option_C`, `option_D`, `correct_option`, `quiz_id`) VALUES
(1, 'perror ( ) function used to ?', 'Work same as printf()', 'prints the error message specified by the compiler', 'prints the garbage value assigned by the compiler', 'None of the above', 'B', 'C&C++_Placement_Test'),
(2, 'Which is the right way to declare constant in C?', 'int constant var =10;', 'int const var = 10;', 'const int var = 10;', 'B & C Both', 'B', 'C&C++_Placement_Test'),
(3, 'Which one of the following is not a linear data structure?', 'Array', 'Binary Tree', 'Queue', 'Stack', 'B', 'C&C++_Placement_Test'),
(4, 'The _______ memory allocation function modifies the previous allocated space.', 'calloc()', 'realloc()', 'malloc()', 'free()', 'B', 'C&C++_Placement_Test'),
(5, 'What will be the output of the following statement? int a=10; printf(\"%d &i\",a,10);', 'Error', '10 10', '10', 'none of these', 'B', 'C&C++_Placement_Test'),
(6, 'What will be the output of the following statements? void main() { int a = 5, b = 2, c = 10, i = a>b; printf(\"hello\"); main(); }', '1', '2', 'Infinite no of times', 'none of these', 'C', 'C&C++_Placement_Test'),
(7, 'Which of the following is the correct output for the program given below? #include <stdio.h> int main() { static int a[] = {0, 1, 2, 3, 4}; static int *p[] = {a+2, a, a+4, a+3, a+1}; int **ptr; ptr = p; **++ptr; printf(\"%d %d\", **ptr, ptr - p); return 0; }', '2 1', '1 2', '0 1', '1 0', 'C', 'C&C++_Placement_Test'),
(8, 'What will be the address of the arr[2][3] if arr is a 2-D long array of 4 rows and 5 columns and starting address of the array is 2000?', '2048', '2056', '2052', '2042', 'C', 'C&C++_Placement_Test'),
(9, 'What is the output of the following code? main() { int i= 0; switch ( i ) { case 0 : i++; case 1 : i++; case 2 : ++i; } printf(\"%d\", i++); }', '1', '4', '3', '5', 'C', 'C&C++_Placement_Test'),
(10, 'Which of the following is not a file opening mode—', 'ios::ate', 'ios::nocreate', 'ios::noreplace', 'ios::truncate', 'C', 'C&C++_Placement_Test'),
(11, 'Which is the correct form of default constructor for following class? #include <iostream> using namespace std; class sample{ private:int x,y; };', 'public: void sample(){}', 'public: void sample(){ x=0; y=0;}', 'public: void sample(int a,int b){ x=a; y=b;}', 'Both 1 and 2', 'D', 'C&C++_Placement_Test'),
(12, 'What will be the output of following program? #include <iostream> using namespace std; class sample { private: int x,y; public: void sample(int a,int b) { x=a; y=b; } }; int main() { sample s; return 0; }', 'Run time error', 'Warning', 'No Error', 'Compile time error', 'D', 'C&C++_Placement_Test'),
(13, 'Syntax for Pure Virtual Function is__________', 'virtual void show()==0', 'void virtual show()=0', 'virtual void show()=0', 'void virtual show()==0', 'D', 'C&C++_Placement_Test'),
(14, 'this pointer', 'implicitly points to an object', 'can be explicitly used in a class.', 'can be used to return an object.', 'All of the above.', 'D', 'C&C++_Placement_Test'),
(15, 'Which of the following are member dereferencing operators in CPP? 1. * 2. :: 3. ->* 4. ::* 5. ->', 'Only 1, 3, 4', 'Only 1 and 5', 'Only 3 and 4', 'Only 3,4,5', 'D', 'C&C++_Placement_Test'),
(16, 'In case of operator overloading, operator function must be__ 1. Static member functions 2. Non- static member functions 3. Friend Functions', 'Only 2 , 3', 'Only 2', 'Only 1, 3', 'All 1 , 2, 3', 'A', 'C&C++_Placement_Test'),
(17, 'The null character will take space of', '8byte', '2 byte', '1 byte', '0byte', 'A', 'C&C++_Placement_Test'),
(18, 'What will happen if the below program is executed? #include <stdio.h> int main() { int main = 3; printf(\"%d\", main); return 0; }', 'It will run without any error and prints 3', 'It will cause a compile-time error', 'It will cause a run-time error', 'It will experience infinite looping', 'A', 'C&C++_Placement_Test'),
(21, 'Choose the correct HTML element for the largest heading:', '      &lt;heading &gt;\n', '     &lt;h1&gt;\n', ' &lt;head&gt;\n', '     &lt;h6&gt;\n', 'B', 'P'),
(22, 'What is the correct HTML for creating a hyperlink?', '<a url=\"http://www.slinfy.com\">Slinfy.com</a>', '      &lt; a href=\"http://www.slinfy.com\">Slinfy&gt;\n', '      &lt;a name=\"http://www.slinfy.com\">Slinfy.com  &gt;\n', '<a>http://www.slinfy.com</a>', 'B', 'P'),
(23, 'Block elements are normally displayed without starting a new line.', 'True', 'False', '', '', 'B', 'P'),
(24, 'Which HTML tag is used to define an internal style sheet?', '      &lt;css &gt;\n', '      &lt;style &gt;\n', '      &lt;link &gt;\n', '<script>', 'B', 'P'),
(25, 'How do you create a function in JavaScript?', 'Function = myfunction()', 'Function myfunction()', 'Function:myfunction()', 'none of above', 'B', 'P'),
(26, 'Where is the correct place to insert a JavaScript?', 'The <head> section', 'The <body> section', 'Both the <head> section and the <body> section are correct', '', 'C', 'P'),
(27, 'How do you round the number 7.25, to the nearest integer?', 'Round(7.25)', 'Rnd(7.25)', 'Math.round(7.25)', 'Math.rnd(7.25)', 'C', 'P'),
(28, 'What is the correct jQuery code to set the background color of all p elements to red?', '$(\"p\").layout(\"background-color\",\"red\");', '$(\"p\").css(\"background-color\",\"red\");', '$(\"p\").style(\"background-color\",\"red\");', '$(\"p\").manipulate(\"background-color\",\"red\");', 'B', 'P'),
(29, 'The jQuery animate() method can be used to animate ANY CSS property?', 'Yes', 'All properties except the shorthand properties', 'Only properties containing numeric values', '', 'A', 'P'),
(30, 'Which of the looping statements is/are supported by PHP?', 'i) for loop i', 'i) while loop', 'ii) do-while loop iii) foreach loop', '', 'C', 'P'),
(31, 'Which of the following php statement/statements will store 111 in variable num?', 'i) int $num = 111;', 'ii) int mum = 111;', 'iii) $num = 111;', 'iv) 111 = $num;', 'C', 'P'),
(32, 'Which statement will output $x on the screen?', 'echo “$x”;', 'echo “$$x”;', 'echo “$x; ', 'echo “/$x”;', 'B', 'P'),
(33, 'PHP recognizes constructors by the name.', 'classname()', '_construct()', 'function _construct()', 'function__construct()', 'D', 'P'),
(34, 'The practice of creating objects based on predefined classes is often referred to as.', 'class creation', 'object creation', 'object instantiation', 'class instantiation', 'C', 'P'),
(36, 'If $a = 12 what will be returned when ($a == 12) ? 5 : 1 is executed?', '5', '1', 'Error', '12', 'A', 'P'),
(37, '<?php $i = 0; $j = 1; $k = 2; print !(($i + $k) < ($j - $k)); ?>', '1', 'true', 'false', '0', 'C', 'P'),
(38, 'Which of the following function creates an array?', 'array()', 'array_change_key_case()', 'array_chunk()', 'array_count_values()', 'A', 'P'),
(39, 'Which function will return true if a variable is an array or false if it is not?', 'is_array()', 'this_array()', 'do_array()', 'in_array()', 'A', 'P'),
(40, 'How many ways can a session data be stored?', '4', '3', '5', '6', 'B', 'P'),
(84, 'How many ways can a session data be stored?', '4', '3', '5', '6', 'B', 'P'),
(101, 'x = 40, y = 35, z = 20, w = 10; If these are the values assigned, comment on the output of these two statements. Statement 1: print x * y / z – w Statement 2: print x * y / (z – w)', 'The output will change by 80', 'Change by 160', 'Change by 50', 'Will remain the same', 'B', 'Java_Quiz'),
(102, 'Which class does not override the equals() and hashCode() methods, inheriting them directly from class Object?', 'java.lang.String', 'java.lang.StringBuffer', 'java.lang.Double', 'java.lang.Character', 'B', 'Java_Quiz'),
(103, 'Which interface provides the capability to store objects using a key-value pair?', 'Java.util.Set', 'Java.util.Map', 'Java.util.List', 'Java.util.Collection', 'B', 'Java_Quiz'),
(104, 'What is the numerical range of char?', '0 to 32767', '0 to 65535', '-256 to 255', '-32768 to 32767', 'B', 'Java_Quiz'),
(105, 'Which of the following are Java reserved words?', '1 and 2', '2 and 3', '2 and 4', '3 and 4', 'D', 'Java_Quiz'),
(106, 'Which three are methods of the Object class?', '1, 2, 6', '1, 2, 4', '1, 2, 6', '2, 3, 4', 'C', 'Java_Quiz'),
(107, 'What will be the output of the program? int i = 4, j = 2; i <<= j; System.out.println(i);', '2', '8', '4', '16', 'B', 'Java_Quiz'),
(108, 'What will be the output of the program? int x = 11 & 9 , y = x ^ 3; System.out.println( y | 12 );', '0', '7', '14', '8', 'B', 'Java_Quiz'),
(109, 'Which statement is true? public void loop() { int x= 0; //Line 1 while ( 1 ) /* Line 6 */ { System.out.print(\"x plus one is \" + (x + 1)); /* Line 8 */ } }', 'syntax errors on lines 1 and 6.', 'syntax error on line 1.', 'a syntax error on line 6.', 'syntax errors on lines 1, 6, and 8', 'C', 'Java_Quiz'),
(110, 'Which is a reserved word in the Java programming language?', 'method', 'Reference', 'native', 'Run', 'C', 'Java_Quiz'),
(111, 'Which three are valid declarations of a char? 1.char c1 = 064770; 2. char c3 = 0xbeef; 3. char c4 = \\u0022; 4.char c5 = \'\\iface\'; 5.char c6 = \'\\uface\';', '1,2,4', '2,3,5', '2,5', '1,2,5', 'D', 'Java_Quiz'),
(112, 'Which is a valid declaration of a String?', 'String s4 = (String) \'ufeed\';', 'String s2 = \'null\';', 'String s3 = (String) \'abc\';', 'String s1 = null;', 'D', 'Java_Quiz'),
(113, 'What is the prototype of the default constructor? public class Test { }', 'Test()', 'Test(void)', 'public Test(void)', 'public Test()', 'D', 'Java_Quiz'),
(114, 'You want a class to have access to members of another class in the same package. Which is the most restrictive access that accomplishes this objective?', 'protected', 'private', 'public', 'Default access', 'D', 'Java_Quiz'),
(115, 'Which of the following class level (nonlocal) variable declarations will not compile?', 'protected int a;', 'transient int b = 3;', 'volatile int d;', 'private synchronized int e;', 'D', 'Java_Quiz'),
(116, 'What is the value of \"d\" after this line of code has been executed? double d = Math.round( 2.5 + Math.random() );', '3', '2', '4', '2.5', 'A', 'Java_Quiz'),
(117, 'To prevent any method from overriding, we declare the method as,', 'final', 'static', 'abstract', 'Const', 'A', 'Java_Quiz'),
(118, 'Which of the following variable declarations would NOT compile in a Java program?', 'int 1_var;', 'int VAR;', 'int var1;', 'int var_1;', 'A', 'Java_Quiz'),
(119, 'What will be the output of the program? class BitShift { public static void main(String [] args) { int x = 0x80000000; System.out.print(x + \" and \"); x = x >>> 31; System.out.println(x); } }', '-2147483648 and 1', '0x80000 and 0x00001', '-214748 and -1', '1 and -2147483', 'A', 'Java_Quiz'),
(120, 'Which of the following line of code is suitable to start a thread? class X implements Runnable { public static void main(String args[]) { /* Missing code? */ } public void run() {} }', 'X run = new X(); Thread t = new Thread(run); t.start();', 'Thread t = new Thread(X); t.start();', 'Thread t = new Thread(X);', 'Thread t = new Thread(); x.run();', 'A', 'Java_Quiz'),
(121, 'Which code used by android is not open source?', 'A) Video Driver', 'B) WiFi Driver', 'C) Device Driver', 'D) Bluetooth Driver', 'C', 'A'),
(122, 'Android web browser is based on?', 'A) Chrome', 'B) Open-source Webkit', 'C) Safari', 'D) Firefox', 'B', 'A'),
(123, 'Which of the following does not belong to transitions?', 'A) ViewFlipper', 'B) ViewSlider', 'C) ViewSwitcher', 'D) ViewAnimator', 'B', 'A'),
(124, 'Which Broadcast in Android includes information about battery state level?', 'A) Android.intent.action.BATTERY_LOW', 'B) Android.intent.action.BATTERY_CHANGED', 'C) Android.intent.action.BATTERY_OKAY', 'D) Android.intent.action.CALL_BUTTON', 'B', 'A'),
(125, 'Action Bar can be associated to?', 'A) Only Fragments', 'B) Only Activities', 'C) Both A and B', 'D) None', 'C', 'A'),
(126, 'Which of the following is/are appropriate for saving the state of an Android application?', 'A) onFreeze()', 'B) onStop()', 'C) onPause()', 'D) onDestroy()', 'B', 'A'),
(127, 'Which of the following fields of the Message class should be used to store custom message codes about the Message?', 'A) tag', 'B) arg1', 'C) what', 'D) userData', 'C', 'A'),
(128, 'Which of the following can be used to bind data from an SQL database to a ListView in an Android application?', 'A) SimpleCursor', 'B) SimpleAdapter', 'C) SimpleCursorAdapter', 'D) SQLiteCursor', 'C', 'A'),
(129, 'Which of the following can you use to add items to the screen menu?', 'A) onCreateOptionsMenu', 'B) onCreate', 'C) both A & B', 'D) Activity.onPrepareOptionsMenu', 'A', 'A'),
(130, 'Broadcast receivers are Android’s implementation of a system-wide publish/subscribe mechanism, or more precisely, what design pattern?', 'A) Command', 'B) Mediator', 'C) Observer', 'D) Facade', 'C', 'A'),
(131, 'When did Google purchase Android?', 'A) 2008', 'B) 2010', 'C) 2007', 'D) 2005', 'C', 'A'),
(132, 'Which of the following is the most “resource hungry” part of dealing with Activities on Android?', 'A) Closing an app', 'B) Suspending an app', 'C) Restoring the most recent app', 'D) Opening a new app', 'D', 'A'),
(133, 'Android is licensed under which open source licensing license?', 'A) Gnu’s GPL', 'B) OSS', 'C) Sourceforge', 'D) Apache/MIT', 'D', 'A'),
(134, 'Status data will be exposed to the rest of the Android system via:', 'A) Intents', 'B) Altering permissions', 'C) Network receivers', 'D) A content provider', 'D', 'A'),
(135, 'Intents are:', 'A) messages that are sent among major building blocks', 'B) trigger activities and services to start or stop', 'C) are asynchronous', 'D) all of those', 'D', 'A'),
(136, 'What does the .apk extension stand for?', 'A) Application Package', 'B) Application Program Kit', 'C) Android Proprietary Kit', 'D) Android Package', 'A', 'A'),
(137, 'How does Google check for malicious software in the Android Market?', 'A) Users report malicious software to Google', 'B) Every new app is scanned by a virus scanner', 'C) Google employees verify each new app', 'D) Separate company monitors Android Market', 'B', 'A'),
(138, 'What was the first phone released that ran the Android OS?', 'A) T-Mobile G1', 'B) Google gPhone', 'C) Motorola Droid', 'D) HTC Hero', 'A', 'A'),
(139, 'Which of the following is NOT a state in the lifecycle of a service?', 'A) paused', 'B) started', 'C) running', 'D) Destroyed', 'C', 'A'),
(140, 'What relationship is appropriate for Fruit and Papaya?', 'A) inheritance', 'B) composition', 'C) association', 'D) All of the above', 'D', 'D'),
(161, 'Which Of The Following Represents A Distinctly Identifiable Entity In The Real World?', 'A class', 'An object', 'A method', 'A data field', 'B', 'Python'),
(162, 'Which Of the following Represents A Template, Blueprint, Or Contract That Defines Objects of the Same type?', 'An Object', 'A Class', 'A method', 'A data field', 'B', 'Python'),
(163, 'Which Of The Following Keywords Mark The Beginning Of The Class Definition?', 'def', 'class', 'return', 'All of the above.', 'B', 'Python'),
(164, 'Which Of The Following Is Required To Create A New Instance Of The Class?', 'A class', 'constructor', 'A value-returning method', 'A None method', 'B', 'Python'),
(165, 'Which Of The Following Statements Is Most Accurate For The Declaration X = Circle()?', 'x contains a reference to a Circle object', 'x contains an object of the Circle type', 'x contains an int value', 'You can assign an int value to x', 'B', 'Python'),
(166, 'Which Of The Following Statements Are Correct?', 'A reference variable is an object.', 'A reference variable refers to an object.', 'An object may contain other objects.', 'All of the Above.', 'C', 'Python'),
(167, 'Which Of The Following Can Be Used To Invoke The __init__ Method In B From A. Where A Is A Subclass of B?', 'super().__init__()', 'super().__init__(self)', 'B.__init__(self)', 'B.__init__()', 'C', 'Python'),
(168, 'What Relationship Correctly Fits For University And Professor?', 'association', 'constructor', 'composition', 'inheritance', 'C', 'Python'),
(169, 'What is the output of print list if list = [\'abcd\', 786 , 2.23, \'john\', 70.2 ]?', 'Error', 'list', '[\'abcd\', 786 , 2.23, \'john\', 70.2 ]', 'None of the above', 'C', 'Python'),
(170, 'What is the output for − \'solitare infosys\' [100:200]?', '\'Tutorials Point\'', ' ', 'Index Error', 'Syntax error', 'C', 'Python'),
(171, 'What is the output of the following? print(\"xyyzxyzxzxyy\".count(\'yy\'))', 'None', '0', 'error', '2', 'D', 'Python'),
(172, 'How can you generate random numbers in Python?', 'random.choice()', 'random.randint()', 'random.random()', 'all of above', 'D', 'Python'),
(173, 'What is the type of each element in sys.argv?', 'set', 'list', 'tuple', 'string', 'D', 'Python'),
(174, 'What is the length of sys.argv?', 'number of arguments', 'number of arguments + 1', 'number of arguments – 1', 'none of the mentioned', 'D', 'Python'),
(175, 'What is the output when following statement is executed ? >>>\"a\"+\"bc\"', 'abc', 'bc', 'bca', 'bc', 'D', 'Python'),
(176, 'What is the output when following statement is executed ? >>>\"abcd\"[2:]', 'cd', 'ab', 'A', 'dc', 'A', 'Python'),
(177, 'The output of executing string.ascii_letters can also be achieved by:', 'string.ascii_lowercase+string.ascii_uppercase', 'string.ascii_lowercase_string.digits', 'string.letters', 'string.lowercase_string.uppercase', 'A', 'Python'),
(178, 'Which of the following statements create a dictionary?', 'd = {}', 'd = {\"john\":40, \"peter\":45}', 'd = {40:\"john\", 45:\"peter\"}', 'All of the mentioned', 'A', 'Python'),
(180, 'What is the output of the code shown below? l=list(\'HELLO\') p=l[0], l[-1], l[1:3] \'a={0}, b={1}, c={2}\'.format(*p)', 'a=H, b=O, c=[\'E\', \'L\']', 'a=\'H\', b=\'O\', c=(E, L)', 'Error', 'Junk value', 'A', 'Python'),
(181, 'Which command displays RIP routing updates?', 'show ip route', 'debug ip rip', 'show protocols', 'debug ip route', 'B', 'Networking'),
(182, 'If you want to have more than one Telnet session open at the same time, what keystroke combination would you use?', 'Tab+Spacebar', 'Ctrl+Shift+6, then X', 'Ctrl+Shift+X, then 6', 'Ctrl+X, then 6', 'B', 'Networking'),
(183, 'What layer of the OSI model would you assume the problem is in if you type show interface serial 1 and receive the following message? \"Serial1 is down, line protocol is down.\"', 'Data Link layer', 'Physical layer', 'Network layer', 'None', 'B', 'Networking'),
(184, 'You are troubleshooting a connectivity problem in your corporate network and want to isolate the problem. You suspect that a router on the route to an unreachable network is at fault. What IOS user exec command should you issue?', 'ping', 'trace', 'show ip route', 'show interface', 'B', 'Networking'),
(185, 'The configuration register setting of 0x2102 provides what function to a router?', 'Tells the router to boot into ROM monitor mode', 'Tells the router to look in NVRAM for the boot sequence', 'Provides password recovery', 'Boots the IOS from a TFTP server', 'B', 'Networking'),
(186, 'What is the frequency range of the IEEE 802.11a standard?', '2.4Gbps', '5Gbps', '5GHz', '2.4GHz', 'C', 'Networking'),
(187, 'How many non-overlapping channels are available with 802.11h?', '23', '12', '3', '40', 'C', 'Networking'),
(188, 'When a DNS server accepts and uses incorrect information from a host that has no authority giving that information, then it is called', 'DNS lookup', 'DNS hijacking', 'DNS spoofing', 'None of the mentioned', 'C', 'Networking'),
(189, 'Which one of the following computer network is built on the top of another network?', 'Prior network', 'Chief network', 'Overlay network', 'Prime network', 'C', 'Networking'),
(190, 'ATM and frame relay are', 'Virtual circuit networks', 'Datagram networks', 'Both (a) and (b)', 'None of the mentioned', 'C', 'Networking'),
(191, 'DHCP client and servers on the same subnet communicate via', 'UDP unicast', 'TCP broadcast', 'TCP unicast', 'UDP broadcast', 'D', 'Networking'),
(192, 'You want to improve network performance by increasing the bandwidth available to hosts and limit the size of the broadcast domains. Which of the following options will achieve this goal?', 'Managed hubs', 'Bridges', 'Switches', 'Switches configured with VLANs', 'D', 'Networking'),
(193, 'A firewall needs to be __________ so that it can grow with the network it protects', 'Robust', 'Scalable', 'Fast', 'Expansive', 'D', 'Networking'),
(194, 'Which of the following is not a transition strategy?', 'Dual stack', 'Tunneling', 'Header translation', 'Conversion', 'D', 'Networking'),
(195, 'Port number used by Network Time Protocol (NTP) with UDP is', '161', '12', '162', '123', 'D', 'Networking'),
(196, 'Which statement describes a spanning-tree network that has converged?', 'All switch and bridge ports are in either the forwarding or blocking state.', 'All switch and bridge ports are assigned as either root or designated ports.', 'All switch and bridge ports are in the forwarding state.', 'All switch and bridge ports are either blocking or looping.', 'A', 'Networking'),
(197, 'Identify the incorrect statement', 'FTP sends its control information in-band', 'FTP uses two parallel TCP connections', 'FTP stands for File Transfer Protocol', 'FTP sends exactly one file over the data connection', 'A', 'Networking'),
(198, 'If 5 files are transferred from server A to client B in the same session. The number of TCP connection between A and B is', '6', '10', '2', '5', 'A', 'Networking'),
(199, 'What command is used to remove files?', 'rm', 'dm', 'delete', 'erase', 'A', 'Networking'),
(200, 'Using the illustration from the previous question, what would be the IP address of S0 if you were using the first subnet? The network ID is 192.168.10.0/28 and you need to use the last available IP address in the range. Again, the zero subnet should not be considered valid for this question.', '192.168.10.30', '192.168.10.62', '192.168.10.24', '192.168.10.127', 'A', 'Networking');

-- --------------------------------------------------------

--
-- Table structure for table `tech_quiz`
--

CREATE TABLE `tech_quiz` (
  `id` int(11) NOT NULL,
  `quiz_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tech_quiz`
--

INSERT INTO `tech_quiz` (`id`, `quiz_name`) VALUES
(8, 'C & C++ TEST');

-- --------------------------------------------------------

--
-- Table structure for table `tech_results`
--

CREATE TABLE `tech_results` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `quiz_id` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `submission_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tech_results`
--

INSERT INTO `tech_results` (`id`, `user_name`, `quiz_id`, `score`, `total_questions`, `submission_time`) VALUES
(1, 'Anonymous', 'C&C++_Placement_Test', 0, 18, '2024-12-25 10:28:36'),
(2, 'Anonymous', 'C&C++_Placement_Test', 0, 18, '2024-12-25 10:30:04'),
(3, 'User', 'C&C++_Placement_Test', 0, 18, '2024-12-25 12:11:52'),
(4, 'User', 'Java_Quiz', 0, 20, '2024-12-25 12:17:59'),
(5, 'User', 'C&C++_Placement_Test', 0, 18, '2024-12-25 12:18:12'),
(6, 'User', 'C&C++_Placement_Test', 0, 18, '2024-12-25 12:19:05'),
(7, 'User', 'C&C++_Placement_Test', 0, 18, '2024-12-25 12:20:30'),
(8, 'User', 'C&C++_Placement_Test', 0, 18, '2024-12-25 12:25:48'),
(9, 'User', 'P', 0, 20, '2024-12-26 05:41:48'),
(10, 'User', 'P', 0, 20, '2024-12-26 05:45:07'),
(11, 'User', 'P', 0, 20, '2024-12-26 06:00:35'),
(12, 'User', 'P', 0, 20, '2024-12-26 06:35:02'),
(13, 'User', 'P', 0, 20, '2024-12-28 05:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `temporary_links`
--

CREATE TABLE `temporary_links` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_result`
--

CREATE TABLE `test_result` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `college_name` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `quiz_title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `result` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `updated-courses`
--

CREATE TABLE `updated-courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `updated_college`
--

CREATE TABLE `updated_college` (
  `id` int(11) NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `published` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `updated_college`
--

INSERT INTO `updated_college` (`id`, `college_name`, `published`) VALUES
(19, 'Chandigarh Group Of Colleges', 1),
(23, 'Chandigarh University', 1),
(24, 'Panjab University', 1),
(25, 'University Institute Of Engineering and Technology, Panjab University', 1),
(26, 'Punjab Engineering College', 1),
(27, 'Chandigarh College Of Engineering and Technology', 1),
(28, 'MCM DAV College for Women', 1),
(29, ' S.D. College, Chandigarh', 1),
(30, 'S.D. College, Chandigarh', 0),
(31, 'Guru Gobind Singh College, Chandigarh', 0),
(32, 'Chandigarh Group of Colleges (CGC), Landran', 1),
(33, 'Indira Gandhi Institute of Engineering and Technology (IGIET)', 1),
(34, 'Punjab College of Engineering and Technology (PCET), Mohali', 1),
(35, 'Rayat Bahra University, Mohali', 1),
(36, 'Chandigarh University (CU), Mohali', 1),
(37, 'College of Engineering, Chandigarh', 1),
(38, 'Baba Mastnath University, Chandigarh', 1),
(39, 'Institute of Engineering and Technology (IET), Chandigarh', 1),
(40, 'Labh Singh College of Engineering and Technology, Mohali', 1),
(41, 'Apex College of Engineering and Technology, Mohali', 1),
(42, ' Satyam College of Engineering and Technology, Mohali', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `create_session`
--
ALTER TABLE `create_session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `current_session`
--
ALTER TABLE `current_session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_courses`
--
ALTER TABLE `new_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_test_participation`
--
ALTER TABLE `quiz_test_participation`
  ADD PRIMARY KEY (`quiz_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tech_questions`
--
ALTER TABLE `tech_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tech_quiz`
--
ALTER TABLE `tech_quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tech_results`
--
ALTER TABLE `tech_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temporary_links`
--
ALTER TABLE `temporary_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `test_result`
--
ALTER TABLE `test_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updated-courses`
--
ALTER TABLE `updated-courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updated_college`
--
ALTER TABLE `updated_college`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `create_session`
--
ALTER TABLE `create_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `current_session`
--
ALTER TABLE `current_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `new_courses`
--
ALTER TABLE `new_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=510;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `tech_questions`
--
ALTER TABLE `tech_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;

--
-- AUTO_INCREMENT for table `tech_quiz`
--
ALTER TABLE `tech_quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tech_results`
--
ALTER TABLE `tech_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `temporary_links`
--
ALTER TABLE `temporary_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_result`
--
ALTER TABLE `test_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `updated-courses`
--
ALTER TABLE `updated-courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `updated_college`
--
ALTER TABLE `updated_college`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `quiz_results_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);

--
-- Constraints for table `quiz_test_participation`
--
ALTER TABLE `quiz_test_participation`
  ADD CONSTRAINT `quiz_test_participation_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `quiz_test_participation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `temporary_links`
--
ALTER TABLE `temporary_links`
  ADD CONSTRAINT `temporary_links_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
