-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 12, 2024 at 07:58 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

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
  PRIMARY KEY (`AttendanceID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

DROP TABLE IF EXISTS `availability`;
CREATE TABLE IF NOT EXISTS `availability` (
  `UserID` int NOT NULL,
  `WeekStartDate` date NOT NULL,
  `DayOfWeek` varchar(10) NOT NULL,
  `IsAvailable` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`UserID`,`WeekStartDate`,`DayOfWeek`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(82, '123123333', '1233312', 3, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companyadmin`
--

INSERT INTO `companyadmin` (`CAdminID`, `CompanyID`, `FirstName`, `LastName`, `Email`, `Password`, `Status`) VALUES
(32, 3, 'abc', 'canteenman', 'foodguy@mail.com', '123', 0),
(35, 82, '123123', '231231', 'email.ca', '123', 0),
(36, 3, 'canteen', 'man 68', 'canteen@blkB', '123', 0);

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
(30, 82, 78, 'PT', 'PT', '1', 'M', 'PT1@email.com', '31', 1),
(31, 82, 79, 'PT', 'PT', '2', 'A', 'PT2@email.com', '123', 1),
(32, 82, 78, 'Manager', 'Manager', '2', 'F', 'manager2@email.com', '123', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`LeaveID`, `UserID`, `LeaveType`, `StartDate`, `EndDate`, `HalfDay`, `Status`, `Comments`) VALUES
(1, 28, 'Personal', '2024-07-04', '2024-07-04', 1, 1, ''),
(2, 29, 'Vacation', '2024-07-11', '2024-07-13', 0, 0, '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
CREATE TABLE IF NOT EXISTS `plans` (
  `PlanID` int NOT NULL AUTO_INCREMENT,
  `PlanName` varchar(16) NOT NULL,
  `PlanDesc` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`PlanID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`PlanID`, `PlanName`, `PlanDesc`) VALUES
(1, 'Basic', 'this is a basic plan'),
(2, 'Premium', 'this is a premium plan'),
(3, 'Super', 'This is a Super Plan');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `ProjectID` int NOT NULL AUTO_INCREMENT,
  `MainProjectID` int NOT NULL,
  `MainTeamID` int NOT NULL,
  PRIMARY KEY (`ProjectID`),
  KEY `TeamID` (`MainTeamID`),
  KEY `project_ibfk_2` (`MainProjectID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projectinfo`
--

INSERT INTO `projectinfo` (`MainProjectID`, `ProjectManagerID`, `CompanyID`, `ProjectName`, `StartDate`, `EndDate`) VALUES
(6, 27, 82, 'Resident Evil 1', '2024-06-28', '2024-09-01');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialisation`
--

INSERT INTO `specialisation` (`SpecialisationID`, `SpecialisationName`, `CompanyID`) VALUES
(77, 'Specialisation1', 82),
(78, 'Specialisation2', 82),
(79, 'Specialisation3', 82);

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
(2, 'super', 'admin', 'email.sa', '123');

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
  `MainTeamID` int NOT NULL,
  `MainTaskID` int NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`TaskID`),
  KEY `UserID` (`UserID`),
  KEY `task_ibfk_2` (`MainTaskID`),
  KEY `task_ibfk_3` (`MainTeamID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taskinfo`
--

DROP TABLE IF EXISTS `taskinfo`;
CREATE TABLE IF NOT EXISTS `taskinfo` (
  `MainTaskID` int NOT NULL AUTO_INCREMENT,
  `SpecialisationID` int NOT NULL,
  `TaskName` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `TaskDesc` varchar(100) NOT NULL,
  `StartDate` date NOT NULL,
  `DueDate` date NOT NULL,
  `NumStaff` int NOT NULL,
  `Priority` tinyint(1) NOT NULL,
  `Status` tinyint(1) NOT NULL,
  PRIMARY KEY (`MainTaskID`),
  KEY `SpecialisationID` (`SpecialisationID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `TeamID` int NOT NULL AUTO_INCREMENT,
  `MainTeamID` int NOT NULL,
  `UserID` int NOT NULL,
  PRIMARY KEY (`TeamID`),
  KEY `MainTeamID` (`MainTeamID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=381 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`TeamID`, `MainTeamID`, `UserID`) VALUES
(372, 15, 28),
(373, 15, 31),
(375, 16, 30),
(376, 16, 28),
(378, 17, 30),
(379, 17, 28),
(380, 18, 29);

-- --------------------------------------------------------

--
-- Table structure for table `teaminfo`
--

DROP TABLE IF EXISTS `teaminfo`;
CREATE TABLE IF NOT EXISTS `teaminfo` (
  `MainTeamID` int NOT NULL AUTO_INCREMENT,
  `ManagerID` int NOT NULL,
  `CompanyID` int NOT NULL,
  `TeamName` varchar(32) NOT NULL,
  PRIMARY KEY (`MainTeamID`),
  KEY `ManagerID` (`ManagerID`),
  KEY `CompanyID` (`CompanyID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teaminfo`
--

INSERT INTO `teaminfo` (`MainTeamID`, `ManagerID`, `CompanyID`, `TeamName`) VALUES
(15, 27, 82, 'Team A'),
(16, 27, 82, 'Team B'),
(17, 27, 82, 'Team C'),
(18, 32, 82, 'Team D');

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
  `PlanID` int NOT NULL,
  PRIMARY KEY (`ApplicationID`),
  KEY `PlanID` (`PlanID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `unregisteredusers`
--

INSERT INTO `unregisteredusers` (`ApplicationID`, `Email`, `Password`, `CompanyName`, `CompanyUEN`, `FirstName`, `LastName`, `PlanID`) VALUES
(1, 'bobworlds@hotmail.com', '', 'bobsworld', '', 'bobby', 'lee', 2),
(2, 'michelleangelo@yahoo.com', '', 'cookhouse', '', 'michelle', 'angelo', 2),
(3, 'mt@yawee.com', '', 'fightingco', '', 'tyson', 'mike', 2),
(7, '1231231', '123123', '123123333', '1233312', '123123', '231231', 3);

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
  ADD CONSTRAINT `avaliability_constraint1` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

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
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`MainTeamID`) REFERENCES `team` (`MainTeamID`) ON DELETE CASCADE ON UPDATE RESTRICT,
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
  ADD CONSTRAINT `task_ibfk_3` FOREIGN KEY (`MainTeamID`) REFERENCES `teaminfo` (`MainTeamID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `taskinfo`
--
ALTER TABLE `taskinfo`
  ADD CONSTRAINT `taskinfo_ibfk_1` FOREIGN KEY (`SpecialisationID`) REFERENCES `specialisation` (`SpecialisationID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`MainTeamID`) REFERENCES `teaminfo` (`MainTeamID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `team_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `teaminfo`
--
ALTER TABLE `teaminfo`
  ADD CONSTRAINT `teaminfo_ibfk_1` FOREIGN KEY (`ManagerID`) REFERENCES `existinguser` (`UserID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `teaminfo_ibfk_2` FOREIGN KEY (`CompanyID`) REFERENCES `company` (`CompanyID`) ON DELETE CASCADE;

--
-- Constraints for table `unregisteredusers`
--
ALTER TABLE `unregisteredusers`
  ADD CONSTRAINT `unregisteredusers_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `plans` (`PlanID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
