-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 06, 2024 at 03:43 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tms`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `AttendanceID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `ClockIn` datetime NOT NULL,
  `ClockOut` datetime DEFAULT NULL,
  `StartBreak` datetime DEFAULT NULL,
  `EndBreak` datetime DEFAULT NULL,
  `TotalHours` float DEFAULT NULL,
  `NumOfOverTimeHours` float DEFAULT NULL,
  PRIMARY KEY (`AttendanceID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`AttendanceID`, `UserID`, `ClockIn`, `ClockOut`, `StartBreak`, `EndBreak`, `TotalHours`, `NumOfOverTimeHours`) VALUES
(1, 30, '2024-07-15 11:46:18', '2024-07-15 11:51:04', '2024-07-15 11:47:26', '2024-07-15 11:48:19', 0.0647222, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

DROP TABLE IF EXISTS `availability`;
CREATE TABLE IF NOT EXISTS `availability` (
  `AvailabilityID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `WeekStartDate` date NOT NULL,
  `DayOfWeek` varchar(10) NOT NULL,
  `IsAvailable` tinyint(1) DEFAULT NULL,
  `Status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`AvailabilityID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`AvailabilityID`, `UserID`, `WeekStartDate`, `DayOfWeek`, `IsAvailable`, `Status`) VALUES
(1, 31, '2024-08-12', 'Monday', 1, NULL),
(2, 31, '2024-08-12', 'Tuesday', 0, NULL),
(3, 31, '2024-08-12', 'Wednesday', 1, NULL),
(4, 31, '2024-08-12', 'Thursday', 1, NULL),
(5, 31, '2024-08-12', 'Friday', 0, NULL),
(6, 31, '2024-08-12', 'Saturday', 0, NULL),
(7, 31, '2024-08-12', 'Sunday', 0, NULL),
(8, 31, '2024-08-19', 'Monday', 0, NULL),
(9, 31, '2024-08-19', 'Tuesday', 1, NULL),
(10, 31, '2024-08-19', 'Wednesday', 1, NULL),
(11, 31, '2024-08-19', 'Thursday', 1, NULL),
(12, 31, '2024-08-19', 'Friday', 0, NULL),
(13, 31, '2024-08-19', 'Saturday', 0, NULL),
(14, 31, '2024-08-19', 'Sunday', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
CREATE TABLE IF NOT EXISTS `calendar` (
  `CalendarID` int NOT NULL AUTO_INCREMENT,
  `CompanyID` int NOT NULL,
  `DateName` varchar(32) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`CalendarID`),
  KEY `calendar_ibfk_1` (`CompanyID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `calendar`
--

INSERT INTO `calendar` (`CalendarID`, `CompanyID`, `DateName`, `Date`) VALUES
(8, 42, 'christmas day', '2024-12-25'),
(9, 42, 'CO birthday', '2024-07-10'),
(11, 42, 'valentines day', '2024-02-14');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `CompanyID` int NOT NULL AUTO_INCREMENT,
  `CompanyName` varchar(16) NOT NULL,
  `CompanyUEN` varchar(10) DEFAULT NULL,
  `PlanID` int NOT NULL,
  `Status` tinyint(1) NOT NULL,
  PRIMARY KEY (`CompanyID`),
  KEY `PlanID` (`PlanID`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`CompanyID`, `CompanyName`, `CompanyUEN`, `PlanID`, `Status`) VALUES
(3, 'SIMcanteen1', '1234567890', 2, 1),
(20, 'fightingco', '1234567890', 2, 0),
(21, 'cookhouse', '1234567890', 1, 1),
(24, 'bobsworld1', '1234567890', 1, 1),
(42, 'transluce2', '1234567890', 1, 1),
(44, 'nannycorp35', '1234567890', 2, 1),
(45, 'yesman34', '1234567890', 1, 1),
(51, 'yanka2', '1234567890', 2, 1),
(53, 'peenut15', '1234567890', 1, 1),
(54, 'nannycorp3', '1234567890', 1, 1),
(55, 'yesman23', '1234567890', 1, 1),
(56, 'deezy56', '1234567890', 2, 1),
(58, 'kingthai3', '1234567890', 2, 1),
(65, 'Company2', '1234567890', 2, 1),
(67, 'ffflllemail', '1234567890', 1, 1),
(68, 'emale', '1234567890', 3, 1),
(69, 'Company1', '1234567890', 1, 1),
(82, '123123333', '1233312', 3, 1),
(83, 'Prem Company', '12345678A', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `companyadmin`
--

DROP TABLE IF EXISTS `companyadmin`;
CREATE TABLE IF NOT EXISTS `companyadmin` (
  `CAdminID` int NOT NULL AUTO_INCREMENT,
  `CompanyID` int NOT NULL,
  `FirstName` varchar(16) NOT NULL,
  `LastName` varchar(16) NOT NULL,
  `Email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Password` varchar(16) NOT NULL,
  `Status` tinyint(1) NOT NULL,
  PRIMARY KEY (`CAdminID`),
  KEY `CompanyID` (`CompanyID`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companyadmin`
--

INSERT INTO `companyadmin` (`CAdminID`, `CompanyID`, `FirstName`, `LastName`, `Email`, `Password`, `Status`) VALUES
(32, 3, 'abc', 'canteenman', 'foodguy@mail.com', '123', 1),
(35, 82, '123123', '231231', 'email@ca', '123', 1),
(36, 3, 'canteen', 'man 68', 'canteen@blkB', '123', 1),
(37, 83, 'Prem', 'P', 'premp@email.com', '123', 0);

-- --------------------------------------------------------

--
-- Table structure for table `existinguser`
--

DROP TABLE IF EXISTS `existinguser`;
CREATE TABLE IF NOT EXISTS `existinguser` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `CompanyID` int NOT NULL,
  `SpecialisationID` int NOT NULL,
  `Role` varchar(8) NOT NULL,
  `FirstName` varchar(16) NOT NULL,
  `LastName` varchar(16) NOT NULL,
  `Gender` varchar(5) NOT NULL,
  `Email` varchar(32) NOT NULL,
  `Password` varchar(16) NOT NULL,
  `Status` tinyint(1) NOT NULL,
  PRIMARY KEY (`UserID`),
  KEY `CompanyID` (`CompanyID`),
  KEY `SpecialisationID` (`SpecialisationID`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `existinguser`
--

INSERT INTO `existinguser` (`UserID`, `CompanyID`, `SpecialisationID`, `Role`, `FirstName`, `LastName`, `Gender`, `Email`, `Password`, `Status`) VALUES
(27, 82, 77, 'Manager', 'manager', '1', 'T', 'manager1@email.com', '123', 1),
(28, 82, 77, 'FT', 'FT', '1', '1', 'FT1@email.com', '123', 1),
(29, 82, 78, 'FT', 'FT', '2', 'M', 'FT2@email.com', '123', 1),
(30, 82, 78, 'PT', 'PT', '1', 'M', 'PT1@email.com', '123', 1),
(31, 82, 79, 'PT', 'PT', '2', 'A', 'PT2@email.com', '123', 1),
(32, 82, 78, 'Manager', 'Manager', '2', 'F', 'manager2@email.com', '123', 1);

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

DROP TABLE IF EXISTS `features`;
CREATE TABLE IF NOT EXISTS `features` (
  `FeatureID` int NOT NULL AUTO_INCREMENT,
  `Name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Image` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`FeatureID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`FeatureID`, `Name`, `Description`, `Icon`, `Image`) VALUES
(1, 'Feature 1', 'Description 1', 'Images/tasks.png', 'Images/Placeholder.jpeg'),
(2, 'Feature 2', 'Description 2', 'Images/attendance.png', 'Images/Placeholder.jpeg'),
(3, 'Feature 3', 'Description 3', 'Images/project-management.png', 'Images/Placeholder.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
CREATE TABLE IF NOT EXISTS `leaves` (
  `LeaveID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `LeaveType` varchar(8) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `HalfDay` tinyint(1) NOT NULL,
  `Status` int DEFAULT NULL,
  `Comments` varchar(100) NOT NULL,
  PRIMARY KEY (`LeaveID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`LeaveID`, `UserID`, `LeaveType`, `StartDate`, `EndDate`, `HalfDay`, `Status`, `Comments`) VALUES
(1, 28, 'Personal', '2024-07-04', '2024-07-04', 1, 1, ''),
(2, 29, 'Vacation', '2024-07-11', '2024-07-13', 0, 0, ''),
(5, 29, 'Vacation', '2024-08-05', '2024-08-06', 0, 1, ''),
(6, 29, 'Vacation', '2024-08-06', '2024-08-08', 0, 1, ''),
(7, 29, 'Vacation', '2024-08-06', '2024-08-08', 0, 1, ''),
(8, 29, 'Vacation', '2024-08-06', '2024-08-08', 0, 0, ''),
(9, 29, 'Vacation', '2024-08-06', '2024-08-08', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `newsfeed`
--

DROP TABLE IF EXISTS `newsfeed`;
CREATE TABLE IF NOT EXISTS `newsfeed` (
  `NewsFeedID` int NOT NULL AUTO_INCREMENT,
  `ManagerID` int NOT NULL,
  `NewsTitle` varchar(32) DEFAULT NULL,
  `NewsDesc` varchar(500) DEFAULT NULL,
  `DatePosted` date NOT NULL,
  PRIMARY KEY (`NewsFeedID`),
  KEY `ManagerID` (`ManagerID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
CREATE TABLE IF NOT EXISTS `plans` (
  `PlanID` int NOT NULL AUTO_INCREMENT,
  `PlanName` varchar(16) NOT NULL,
  `Price` double NOT NULL,
  `UserAccess` int NOT NULL,
  `CustomerSupport` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`PlanID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`PlanID`, `PlanName`, `Price`, `UserAccess`, `CustomerSupport`) VALUES
(1, 'Tier 1', 9.99, 50, 'Email Support'),
(2, 'Tier 2', 29.99, 100, 'Email and Phone Support'),
(3, 'Tier 3', 59.99, 200, '24/7 Priority Support');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `ProjectID` int NOT NULL AUTO_INCREMENT,
  `MainProjectID` int NOT NULL,
  `MainGroupID` int NOT NULL,
  PRIMARY KEY (`ProjectID`),
  KEY `MainProjectID` (`MainProjectID`) USING BTREE,
  KEY `MainGroupID` (`MainGroupID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`ProjectID`, `MainProjectID`, `MainGroupID`) VALUES
(22, 11, 19);

-- --------------------------------------------------------

--
-- Table structure for table `projectinfo`
--

DROP TABLE IF EXISTS `projectinfo`;
CREATE TABLE IF NOT EXISTS `projectinfo` (
  `MainProjectID` int NOT NULL AUTO_INCREMENT,
  `ProjectManagerID` int NOT NULL,
  `CompanyID` int NOT NULL,
  `ProjectName` varchar(32) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  PRIMARY KEY (`MainProjectID`),
  KEY `CompanyID` (`CompanyID`),
  KEY `ProjectManagerID` (`ProjectManagerID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projectinfo`
--

INSERT INTO `projectinfo` (`MainProjectID`, `ProjectManagerID`, `CompanyID`, `ProjectName`, `StartDate`, `EndDate`) VALUES
(11, 27, 82, 'Project 1', '2024-08-01', '2024-08-31');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `ReviewTitle` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Rating` int NOT NULL,
  `Comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DatePosted` date NOT NULL,
  PRIMARY KEY (`ReviewID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `UserID`, `ReviewTitle`, `Rating`, `Comments`, `DatePosted`) VALUES
(8, 28, 'Good Website', 5, 'very good', '2024-07-23'),
(10, 31, 'had fun using website', 5, 'need user manual', '2024-07-24'),
(11, 29, 'BOOOO', 1, 'Shit', '2024-07-24'),
(12, 27, 'Very bad!', 1, 'Would not recommend!', '2024-08-06');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `ScheduleID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `WorkDate` date NOT NULL,
  `StartWork` datetime NOT NULL,
  `EndWork` datetime NOT NULL,
  PRIMARY KEY (`ScheduleID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`ScheduleID`, `UserID`, `WorkDate`, `StartWork`, `EndWork`) VALUES
(1, 30, '2024-07-15', '2024-07-15 08:00:00', '2024-07-15 21:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `specialisation`
--

DROP TABLE IF EXISTS `specialisation`;
CREATE TABLE IF NOT EXISTS `specialisation` (
  `SpecialisationID` int NOT NULL AUTO_INCREMENT,
  `SpecialisationName` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `CompanyID` int NOT NULL,
  PRIMARY KEY (`SpecialisationID`),
  KEY `specialisation_ibfk_1` (`CompanyID`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialisation`
--

INSERT INTO `specialisation` (`SpecialisationID`, `SpecialisationName`, `CompanyID`) VALUES
(77, 'Specialisation1', 82),
(78, 'Specialisation2', 82),
(79, 'Specialisation3', 82),
(80, 'Manager', 83);

-- --------------------------------------------------------

--
-- Table structure for table `specialisationgroup`
--

DROP TABLE IF EXISTS `specialisationgroup`;
CREATE TABLE IF NOT EXISTS `specialisationgroup` (
  `GroupID` int NOT NULL AUTO_INCREMENT,
  `MainGroupID` int NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`GroupID`),
  KEY `MainGroupID` (`MainGroupID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=389 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialisationgroup`
--

INSERT INTO `specialisationgroup` (`GroupID`, `MainGroupID`, `UserID`) VALUES
(387, 19, 28),
(388, 19, 29);

-- --------------------------------------------------------

--
-- Table structure for table `specialisationgroupinfo`
--

DROP TABLE IF EXISTS `specialisationgroupinfo`;
CREATE TABLE IF NOT EXISTS `specialisationgroupinfo` (
  `MainGroupID` int NOT NULL AUTO_INCREMENT,
  `SpecialisationID` int NOT NULL,
  `CompanyID` int NOT NULL,
  `GroupName` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`MainGroupID`),
  KEY `CompanyID` (`CompanyID`),
  KEY `SpecialisationID` (`SpecialisationID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialisationgroupinfo`
--

INSERT INTO `specialisationgroupinfo` (`MainGroupID`, `SpecialisationID`, `CompanyID`, `GroupName`) VALUES
(19, 77, 82, 'Team Specialisation 1');

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

DROP TABLE IF EXISTS `superadmin`;
CREATE TABLE IF NOT EXISTS `superadmin` (
  `SAdminID` int NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(16) NOT NULL,
  `LastName` varchar(16) NOT NULL,
  `Email` varchar(32) NOT NULL,
  `Password` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`SAdminID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`SAdminID`, `FirstName`, `LastName`, `Email`, `Password`) VALUES
(1, 'Superb', 'Administrator', 'blank13@paper.com', '666'),
(2, 'super', 'admin', 'email@sa', '123');

-- --------------------------------------------------------

--
-- Table structure for table `swap_requests`
--

DROP TABLE IF EXISTS `swap_requests`;
CREATE TABLE IF NOT EXISTS `swap_requests` (
  `SwapRequestID` int NOT NULL AUTO_INCREMENT,
  `RequestorScheduleID` int NOT NULL,
  `RequestedScheduleID` int NOT NULL,
  `RequestorUserID` int NOT NULL,
  `RequestedUserID` int NOT NULL,
  `Status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `RequestDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SwapRequestID`),
  KEY `RequestorScheduleID` (`RequestorScheduleID`),
  KEY `RequestedScheduleID` (`RequestedScheduleID`),
  KEY `RequestorUserID` (`RequestorUserID`),
  KEY `RequestedUserID` (`RequestedUserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `TaskID` int NOT NULL AUTO_INCREMENT,
  `MainGroupID` int NOT NULL,
  `MainTaskID` int NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`TaskID`),
  KEY `UserID` (`UserID`),
  KEY `MainTaskID` (`MainTaskID`),
  KEY `MainGroupID` (`MainGroupID`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TaskID`, `MainGroupID`, `MainTaskID`, `UserID`) VALUES
(28, 19, 22, 28);

-- --------------------------------------------------------

--
-- Table structure for table `taskinfo`
--

DROP TABLE IF EXISTS `taskinfo`;
CREATE TABLE IF NOT EXISTS `taskinfo` (
  `MainTaskID` int NOT NULL AUTO_INCREMENT,
  `MainProjectID` int NOT NULL,
  `TaskName` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `TaskDesc` varchar(100) NOT NULL,
  `StartDate` date NOT NULL,
  `DueDate` date NOT NULL,
  `NumStaff` int NOT NULL,
  `Priority` tinyint(1) NOT NULL,
  `Status` tinyint(1) NOT NULL,
  PRIMARY KEY (`MainTaskID`),
  KEY `MainProjectID` (`MainProjectID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `taskinfo`
--

INSERT INTO `taskinfo` (`MainTaskID`, `MainProjectID`, `TaskName`, `TaskDesc`, `StartDate`, `DueDate`, `NumStaff`, `Priority`, `Status`) VALUES
(22, 11, 'task 1', 'hello', '2024-08-13', '2024-08-14', 1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `unregisteredusers`
--

DROP TABLE IF EXISTS `unregisteredusers`;
CREATE TABLE IF NOT EXISTS `unregisteredusers` (
  `ApplicationID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(32) NOT NULL,
  `Password` varchar(16) NOT NULL,
  `CompanyName` varchar(16) NOT NULL,
  `CompanyUEN` varchar(10) NOT NULL,
  `FirstName` varchar(16) NOT NULL,
  `LastName` varchar(16) NOT NULL,
  `ApplicationDate` date NOT NULL,
  `PlanID` int NOT NULL,
  PRIMARY KEY (`ApplicationID`),
  KEY `PlanID` (`PlanID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `unregisteredusers`
--

INSERT INTO `unregisteredusers` (`ApplicationID`, `Email`, `Password`, `CompanyName`, `CompanyUEN`, `FirstName`, `LastName`, `ApplicationDate`, `PlanID`) VALUES
(1, 'bobworlds@hotmail.com', '', 'bobsworld', '', 'bobby', 'lee', '2012-01-01', 2),
(2, 'michelleangelo@yahoo.com', '', 'cookhouse', '', 'michelle', 'angelo', '2014-02-02', 2),
(3, 'mt@yawee.com', '', 'fightingco', '', 'tyson', 'mike', '2023-05-01', 2),
(7, '1231231', '123123', '123123333', '1233312', '123123', '231231', '2024-06-13', 3),
(8, 'premp@email.com', '123', 'Prem Company', '12345678A', 'Prem', 'P', '2019-10-20', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `availability_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `calendar`
--
ALTER TABLE `calendar`
  ADD CONSTRAINT `calendar_constraint1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `plans` (`PlanID`);

--
-- Constraints for table `companyadmin`
--
ALTER TABLE `companyadmin`
  ADD CONSTRAINT `companyadmin_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `existinguser`
--
ALTER TABLE `existinguser`
  ADD CONSTRAINT `existinguser_ibfk_1` FOREIGN KEY (`SpecialisationID`) REFERENCES `specialisation` (`SpecialisationID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `existinguser_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `newsfeed`
--
ALTER TABLE `newsfeed`
  ADD CONSTRAINT `newsfeed_ibfk_1` FOREIGN KEY (`ManagerID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`MainGroupID`) REFERENCES `specialisationgroupinfo` (`MainGroupID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`MainProjectID`) REFERENCES `projectinfo` (`MainProjectID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `projectinfo`
--
ALTER TABLE `projectinfo`
  ADD CONSTRAINT `projectinfo_ibfk_1` FOREIGN KEY (`ProjectManagerID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `projectinfo_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `specialisation`
--
ALTER TABLE `specialisation`
  ADD CONSTRAINT `specialisation_ibfk_1` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `specialisationgroup`
--
ALTER TABLE `specialisationgroup`
  ADD CONSTRAINT `specialisationgroup_ibfk_1` FOREIGN KEY (`MainGroupID`) REFERENCES `specialisationgroupinfo` (`MainGroupID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `specialisationgroup_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `specialisationgroupinfo`
--
ALTER TABLE `specialisationgroupinfo`
  ADD CONSTRAINT `specialisationgroupinfo_ibfk_1` FOREIGN KEY (`SpecialisationID`) REFERENCES `specialisation` (`SpecialisationID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `specialisationgroupinfo_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD CONSTRAINT `constraint_swap_requests` FOREIGN KEY (`RequestorUserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `task_ibfk_2` FOREIGN KEY (`MainTaskID`) REFERENCES `taskinfo` (`MainTaskID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `task_ibfk_3` FOREIGN KEY (`MainGroupID`) REFERENCES `specialisationgroupinfo` (`MainGroupID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `taskinfo`
--
ALTER TABLE `taskinfo`
  ADD CONSTRAINT `taskinfo_ibfk_1` FOREIGN KEY (`MainProjectID`) REFERENCES `projectinfo` (`MainProjectID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `unregisteredusers`
--
ALTER TABLE `unregisteredusers`
  ADD CONSTRAINT `unregisteredusers_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `plans` (`PlanID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
