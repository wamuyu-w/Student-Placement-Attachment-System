-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 20, 2026 at 10:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attachmentmanagementsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

CREATE TABLE `assessment` (
  `AssessmentID` int(11) NOT NULL,
  `AttachmentID` int(11) DEFAULT NULL,
  `AssessmentDate` date DEFAULT NULL,
  `AssessmentType` varchar(20) DEFAULT NULL,
  `Marks` decimal(5,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CriteriaScores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`CriteriaScores`)),
  `LecturerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment`
--

--
-- Table structure for table `attachment`
--

CREATE TABLE `attachment` (
  `AttachmentID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `HostOrgID` int(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `ClearanceStatus` varchar(20) DEFAULT NULL,
  `AttachmentStatus` varchar(20) DEFAULT NULL,
  `AssessmentCode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachment`
--

--
-- Table structure for table `attachmentapplication`
--

CREATE TABLE `attachmentapplication` (
  `ApplicationID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `ApplicationDate` date DEFAULT NULL,
  `ApplicationStatus` varchar(20) DEFAULT NULL,
  `IntendedHostOrg` varchar(255) DEFAULT NULL,
  `RejectionReason` text DEFAULT NULL,
  `HostOrgID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--

--
-- Table structure for table `attachmentopportunity`
--

CREATE TABLE `attachmentopportunity` (
  `OpportunityID` int(11) NOT NULL,
  `HostOrgID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `EligibilityCriteria` text DEFAULT NULL,
  `ApplicationStartDate` date DEFAULT NULL,
  `ApplicationEndDate` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--

--
-- Table structure for table `finalreport`
--

CREATE TABLE `finalreport` (
  `ReportID` int(11) NOT NULL,
  `AttachmentID` int(11) DEFAULT NULL,
  `SubmissionDate` date DEFAULT NULL,
  `ReportFile` varchar(255) DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostorganization`
--

CREATE TABLE `hostorganization` (
  `HostOrgID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `OrganizationName` varchar(150) NOT NULL,
  `ContactPerson` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `PhysicalAddress` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--

--
-- Table structure for table `jobapplication`
--

CREATE TABLE `jobapplication` (
  `OpportunityID` int(11) NOT NULL,
  `HostOrgID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `ApplicationDate` date NOT NULL,
  `Status` varchar(30) DEFAULT 'Pending',
  `ResumePath` varchar(255) DEFAULT NULL,
  `ResumeLink` varchar(255) DEFAULT NULL,
  `Motivation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `LecturerID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `StaffNumber` varchar(30) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Faculty` varchar(100) DEFAULT NULL,
  `Role` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `logbook`
--

CREATE TABLE `logbook` (
  `LogbookID` int(11) NOT NULL,
  `AttachmentID` int(11) DEFAULT NULL,
  `IssueDate` date DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbook`
--

--
-- Table structure for table `logbookentry`
--

CREATE TABLE `logbookentry` (
  `EntryID` int(11) NOT NULL,
  `LogbookID` int(11) DEFAULT NULL,
  `EntryDate` date DEFAULT NULL,
  `Activities` text DEFAULT NULL,
  `HostSupervisorComments` text DEFAULT NULL,
  `AcademicSupervisorComments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `StudentID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Course` varchar(100) DEFAULT NULL,
  `Faculty` varchar(100) DEFAULT NULL,
  `YearOfStudy` int(11) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `EligibilityStatus` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `supervision`
--

CREATE TABLE `supervision` (
  `SupervisionID` int(11) NOT NULL,
  `LecturerID` int(11) DEFAULT NULL,
  `AttachmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervision`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` varchar(30) NOT NULL,
  `Status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`AssessmentID`),
  ADD KEY `AttachmentID` (`AttachmentID`);

--
-- Indexes for table `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`AttachmentID`),
  ADD UNIQUE KEY `StudentID` (`StudentID`),
  ADD KEY `HostOrgID` (`HostOrgID`);

--
-- Indexes for table `attachmentapplication`
--
ALTER TABLE `attachmentapplication`
  ADD PRIMARY KEY (`ApplicationID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `HostOrgID` (`HostOrgID`);

--
-- Indexes for table `attachmentopportunity`
--
ALTER TABLE `attachmentopportunity`
  ADD PRIMARY KEY (`OpportunityID`),
  ADD KEY `HostOrgID` (`HostOrgID`);

--
-- Indexes for table `finalreport`
--
ALTER TABLE `finalreport`
  ADD PRIMARY KEY (`ReportID`),
  ADD UNIQUE KEY `AttachmentID` (`AttachmentID`);

--
-- Indexes for table `hostorganization`
--
ALTER TABLE `hostorganization`
  ADD PRIMARY KEY (`HostOrgID`),
  ADD UNIQUE KEY `UserID` (`UserID`);

--
-- Indexes for table `jobapplication`
--
ALTER TABLE `jobapplication`
  ADD PRIMARY KEY (`OpportunityID`,`StudentID`),
  ADD KEY `HostOrgID` (`HostOrgID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`LecturerID`),
  ADD UNIQUE KEY `UserID` (`UserID`),
  ADD UNIQUE KEY `StaffNumber` (`StaffNumber`);

--
-- Indexes for table `logbook`
--
ALTER TABLE `logbook`
  ADD PRIMARY KEY (`LogbookID`),
  ADD UNIQUE KEY `AttachmentID` (`AttachmentID`);

--
-- Indexes for table `logbookentry`
--
ALTER TABLE `logbookentry`
  ADD PRIMARY KEY (`EntryID`),
  ADD KEY `LogbookID` (`LogbookID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `UserID` (`UserID`);

--
-- Indexes for table `supervision`
--
ALTER TABLE `supervision`
  ADD PRIMARY KEY (`SupervisionID`),
  ADD KEY `LecturerID` (`LecturerID`),
  ADD KEY `AttachmentID` (`AttachmentID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessment`
--
ALTER TABLE `assessment`
  MODIFY `AssessmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attachment`
--
ALTER TABLE `attachment`
  MODIFY `AttachmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attachmentapplication`
--
ALTER TABLE `attachmentapplication`
  MODIFY `ApplicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attachmentopportunity`
--
ALTER TABLE `attachmentopportunity`
  MODIFY `OpportunityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `finalreport`
--
ALTER TABLE `finalreport`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostorganization`
--
ALTER TABLE `hostorganization`
  MODIFY `HostOrgID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `LecturerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `logbook`
--
ALTER TABLE `logbook`
  MODIFY `LogbookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logbookentry`
--
ALTER TABLE `logbookentry`
  MODIFY `EntryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `supervision`
--
ALTER TABLE `supervision`
  MODIFY `SupervisionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment`
--
ALTER TABLE `assessment`
  ADD CONSTRAINT `assessment_ibfk_1` FOREIGN KEY (`AttachmentID`) REFERENCES `attachment` (`AttachmentID`);

--
-- Constraints for table `attachment`
--
ALTER TABLE `attachment`
  ADD CONSTRAINT `attachment_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`),
  ADD CONSTRAINT `attachment_ibfk_2` FOREIGN KEY (`HostOrgID`) REFERENCES `hostorganization` (`HostOrgID`);

--
-- Constraints for table `attachmentapplication`
--
ALTER TABLE `attachmentapplication`
  ADD CONSTRAINT `attachmentapplication_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`),
  ADD CONSTRAINT `attachmentapplication_ibfk_2` FOREIGN KEY (`HostOrgID`) REFERENCES `hostorganization` (`HostOrgID`);

--
-- Constraints for table `attachmentopportunity`
--
ALTER TABLE `attachmentopportunity`
  ADD CONSTRAINT `attachmentopportunity_ibfk_1` FOREIGN KEY (`HostOrgID`) REFERENCES `hostorganization` (`HostOrgID`);

--
-- Constraints for table `finalreport`
--
ALTER TABLE `finalreport`
  ADD CONSTRAINT `finalreport_ibfk_1` FOREIGN KEY (`AttachmentID`) REFERENCES `attachment` (`AttachmentID`);

--
-- Constraints for table `hostorganization`
--
ALTER TABLE `hostorganization`
  ADD CONSTRAINT `hostorganization_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `jobapplication`
--
ALTER TABLE `jobapplication`
  ADD CONSTRAINT `jobapplication_ibfk_1` FOREIGN KEY (`OpportunityID`) REFERENCES `attachmentopportunity` (`OpportunityID`),
  ADD CONSTRAINT `jobapplication_ibfk_2` FOREIGN KEY (`HostOrgID`) REFERENCES `hostorganization` (`HostOrgID`),
  ADD CONSTRAINT `jobapplication_ibfk_3` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`);

--
-- Constraints for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD CONSTRAINT `lecturer_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `logbook`
--
ALTER TABLE `logbook`
  ADD CONSTRAINT `logbook_ibfk_1` FOREIGN KEY (`AttachmentID`) REFERENCES `attachment` (`AttachmentID`);

--
-- Constraints for table `logbookentry`
--
ALTER TABLE `logbookentry`
  ADD CONSTRAINT `logbookentry_ibfk_1` FOREIGN KEY (`LogbookID`) REFERENCES `logbook` (`LogbookID`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `supervision`
--
ALTER TABLE `supervision`
  ADD CONSTRAINT `supervision_ibfk_1` FOREIGN KEY (`LecturerID`) REFERENCES `lecturer` (`LecturerID`),
  ADD CONSTRAINT `supervision_ibfk_2` FOREIGN KEY (`AttachmentID`) REFERENCES `attachment` (`AttachmentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
