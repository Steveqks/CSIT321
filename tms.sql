-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 25, 2024 at 02:05 AM
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
  `ClockOut` datetime NOT NULL,
  `StartBreak` datetime NOT NULL,
  `EndBreak` datetime NOT NULL,
  `TotalHours` float NOT NULL,
  PRIMARY KEY (`AttendanceID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`CompanyID`, `CompanyName`, `CompanyUEN`, `PlanID`, `Status`) VALUES
(3, 'SIMcanteen1', NULL, 2, 1),
(20, 'fightingco', NULL, 2, 0),
(21, 'cookhouse', NULL, 1, 1),
(24, 'bobsworld1', NULL, 1, 1),
(42, 'transluce2', NULL, 1, 1),
(44, 'nannycorp35', NULL, 2, 1),
(45, 'yesman34', NULL, 1, 1),
(51, 'yanka2', NULL, 2, 1),
(53, 'peenut15', NULL, 1, 1),
(54, 'nannycorp3', NULL, 1, 1),
(55, 'yesman23', NULL, 1, 1),
(56, 'deezy56', NULL, 2, 1),
(58, 'kingthai3', NULL, 2, 1),
(65, 'Company2', NULL, 2, 1),
(67, 'ffflllemail', NULL, 1, 1),
(68, 'emale', NULL, 3, 1),
(69, 'Company1', NULL, 1, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companyadmin`
--

INSERT INTO `companyadmin` (`CAdminID`, `CompanyID`, `FirstName`, `LastName`, `Email`, `Password`, `Status`) VALUES
(29, 78, '123123', '231231', '1231231', '123123', 0),
(30, 78, 'firstname123', 'last', '123', '123', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `existinguser`
--

INSERT INTO `existinguser` (`UserID`, `CompanyID`, `SpecialisationID`, `Role`, `FirstName`, `LastName`, `Gender`, `Email`, `Password`, `Status`) VALUES
(24, 78, 76, 'FT', 'fnameeu', 'lnameeu', '1', '1', '1', 1),
(25, 78, 76, 'PT', 'fnameeu2', 'lnameeu2', '1', '1', '1', 1),
(26, 78, 76, 'Manager', 'fnameeu3', 'lnameeu3', '1', '1', '1', 1);

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
  `Comments` varchar(100) NOT NULL,
  PRIMARY KEY (`LeaveID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`ProjectID`, `MainProjectID`, `MainTeamID`) VALUES
(4, 4, 14);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projectinfo`
--

INSERT INTO `projectinfo` (`MainProjectID`, `ProjectManagerID`, `CompanyID`, `ProjectName`, `StartDate`, `EndDate`) VALUES
(4, 26, 78, 'projectname', '2024-06-18', '2024-06-26');

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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialisation`
--

INSERT INTO `specialisation` (`SpecialisationID`, `SpecialisationName`, `CompanyID`) VALUES
(76, '123specialisationname', 78);

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

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TaskID`, `MainTeamID`, `MainTaskID`, `UserID`) VALUES
(16, 14, 17, 24),
(17, 14, 17, 25);

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

--
-- Dumping data for table `taskinfo`
--

INSERT INTO `taskinfo` (`MainTaskID`, `SpecialisationID`, `TaskName`, `TaskDesc`, `StartDate`, `DueDate`, `NumStaff`, `Priority`, `Status`) VALUES
(17, 76, 'taskname1', '123', '2024-06-06', '2024-06-20', 5, 1, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`TeamID`, `MainTeamID`, `UserID`) VALUES
(370, 14, 24),
(371, 14, 25);

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teaminfo`
--

INSERT INTO `teaminfo` (`MainTeamID`, `ManagerID`, `CompanyID`, `TeamName`) VALUES
(14, 26, 78, 'teamname1');

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
  `CompanyUEN` varchar(10) DEFAULT NULL,
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
(1, 'bobworlds@hotmail.com', '', 'bobsworld', NULL, 'bobby', 'lee', 2),
(2, 'michelleangelo@yahoo.com', '', 'cookhouse', NULL, 'michelle', 'angelo', 2),
(3, 'mt@yawee.com', '', 'fightingco', NULL, 'tyson', 'mike', 2),
(7, '1231231', '123123', '123123333', NULL, '123123', '231231', 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `plans` (`PlanID`);

--
-- Constraints for table `unregisteredusers`
--
ALTER TABLE `unregisteredusers`
  ADD CONSTRAINT `unregisteredusers_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `plans` (`PlanID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
