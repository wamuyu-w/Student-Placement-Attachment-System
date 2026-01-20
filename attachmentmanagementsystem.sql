-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 12:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment`
--

INSERT INTO `assessment` (`AssessmentID`, `AttachmentID`, `AssessmentDate`, `AssessmentType`, `Marks`, `Remarks`) VALUES
(1, 1, '2025-05-15', 'Mid-Term', 68.50, 'Shows good understanding'),
(2, 2, '2025-06-25', 'Final', 82.00, 'Excellent overall performance');

-- --------------------------------------------------------

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
  `AttachmentStatus` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachment`
--

INSERT INTO `attachment` (`AttachmentID`, `StudentID`, `HostOrgID`, `StartDate`, `EndDate`, `ClearanceStatus`, `AttachmentStatus`) VALUES
(1, 1, 1, '2025-03-01', '2025-06-30', 'Cleared', 'Ongoing'),
(2, 2, 2, '2025-03-01', '2025-06-30', 'Cleared', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `attachmentapplication`
--

CREATE TABLE `attachmentapplication` (
  `ApplicationID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `ApplicationDate` date DEFAULT NULL,
  `ApplicationStatus` varchar(20) DEFAULT NULL,
  `RejectionReason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachmentapplication`
--

INSERT INTO `attachmentapplication` (`ApplicationID`, `StudentID`, `ApplicationDate`, `ApplicationStatus`, `RejectionReason`) VALUES
(1, 1, '2025-01-12', 'Approved', NULL),
(2, 2, '2025-01-15', 'Approved', NULL),
(3, 3, '2025-01-18', 'Rejected', 'Student not eligible'),
(4, 4, '2025-01-20', 'Pending', NULL);

-- --------------------------------------------------------

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
-- Dumping data for table `attachmentopportunity`
--

INSERT INTO `attachmentopportunity` (`OpportunityID`, `HostOrgID`, `Description`, `EligibilityCriteria`, `ApplicationStartDate`, `ApplicationEndDate`, `Status`) VALUES
(1, 1, 'Software Development Intern', 'Computer Science or IT students', '2025-01-01', '2025-02-15', 'Open'),
(2, 2, 'ICT Support Intern', 'IT or BIS students', '2025-01-10', '2025-02-20', 'Closed'),
(3, 3, 'Health Information Systems Intern', 'IT students with database skills', '2025-01-05', '2025-02-10', 'Open'),
(4, 1, 'Cyber Risk Interns', 'BS IT or Computer Science Students. Those with a certification have an added advantage', '2026-01-05', '2026-01-21', 'Open');

-- --------------------------------------------------------

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

--
-- Dumping data for table `finalreport`
--

INSERT INTO `finalreport` (`ReportID`, `AttachmentID`, `SubmissionDate`, `ReportFile`, `Status`) VALUES
(1, 2, '2025-06-28', 'mary_achieng_final_report.pdf', 'Approved');

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
-- Dumping data for table `hostorganization`
--

INSERT INTO `hostorganization` (`HostOrgID`, `UserID`, `OrganizationName`, `ContactPerson`, `Email`, `PhoneNumber`, `PhysicalAddress`) VALUES
(1, 9, 'TechNova Solutions', 'Alice Mwangi', 'alice@technova.com', '0712345678', 'Nairobi, Westlands'),
(2, 10, 'GreenFields Agro Ltd', 'Peter Otieno', 'peter@greenfields.co.ke', '0723456789', 'Eldoret, Industrial Area'),
(3, 11, 'MediCare Hospital', 'Dr. Sarah Kim', 'sarah@medicare.org', '0734567890', 'Kisumu, CBD'),
(4, 12, 'CyberAce Africa Limited', 'Kate Akungo', 'info@cyberaceafrica.org', '07283748532', 'Marsabit Plaza, Ngong Road\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `jobapplication`
--

CREATE TABLE `jobapplication` (
  `OpportunityID` int(11) NOT NULL,
  `HostOrgID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `ApplicationDate` date NOT NULL,
  `Status` varchar(30) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobapplication`
--

INSERT INTO `jobapplication` (`OpportunityID`, `HostOrgID`, `StudentID`, `ApplicationDate`, `Status`) VALUES
(1, 1, 4, '2026-01-06', 'Pending');

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
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`LecturerID`, `UserID`, `StaffNumber`, `Name`, `Department`, `Faculty`, `Role`) VALUES
(1, 5, 'STAFF-001', 'Dr. James Karanja', 'Computer Science', 'Science & Technology', 'Supervisor'),
(2, 6, 'STAFF-003', 'Ms. Rose Njeri', 'Information Systems', 'Science & Technology', 'Supervisor'),
(3, 7, 'STAFF-019', 'Mr. Paul Otieno', 'Business IT', 'Business', 'Supervisor'),
(4, 8, 'STAFF-020', 'Dr. Chris Nandasaba', 'Computer Science', 'Science', 'Admin');

-- --------------------------------------------------------

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

INSERT INTO `logbook` (`LogbookID`, `AttachmentID`, `IssueDate`, `Status`) VALUES
(1, 1, '2025-03-05', 'Active'),
(2, 2, '2025-03-05', 'Submitted');

-- --------------------------------------------------------

--
-- Table structure for table `logbookentry`
--

CREATE TABLE `logbookentry` (
  `EntryID` int(11) NOT NULL,
  `LogbookID` int(11) DEFAULT NULL,
  `EntryDate` date DEFAULT NULL,
  `Activities` text DEFAULT NULL,
  `HostSupervisorComments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbookentry`
--

INSERT INTO `logbookentry` (`EntryID`, `LogbookID`, `EntryDate`, `Activities`, `HostSupervisorComments`) VALUES
(1, 1, '2025-03-10', 'Developed login module using PHP and MySQL', 'Good progress'),
(2, 1, '2025-03-17', 'Worked on REST API integration', 'Satisfactory'),
(3, 2, '2025-03-12', 'Installed network equipment and provided user support', 'Excellent performance');

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

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentID`, `UserID`, `FirstName`, `LastName`, `Course`, `Faculty`, `YearOfStudy`, `PhoneNumber`, `Email`, `EligibilityStatus`) VALUES
(1, 1, 'John', 'Kamau', 'Computer Science', 'Science & Technology', 3, '0700111222', 'john.kamau@student.edu', 'Eligible'),
(2, 2, 'Mary', 'Achieng', 'Information Technology', 'Science & Technology', 3, '0700333444', 'mary.achieng@student.edu', 'Eligible'),
(3, 3, 'Brian', 'Mutiso', 'Business Information Systems', 'Business', 2, '0700555666', 'brian.mutiso@student.edu', 'Not Eligible'),
(4, 4, 'Linda', 'Wanjiku', 'Software Engineering', 'Engineering', 4, '0700777888', 'linda.wanjiku@student.edu', 'Eligible'),
(5, 13, 'Wamuyu', 'Wachira', 'Computer Science', 'Computer and Information Science', 4, '0701573708', 'michellewachira25@gmail.com', 'Pending');

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

INSERT INTO `supervision` (`SupervisionID`, `LecturerID`, `AttachmentID`) VALUES
(1, 1, 1),
(2, 2, 2);

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
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `Role`, `Status`) VALUES
(1, '1049088', '$2y$10$3Cwxx619o9s8hEBhEdJS8eacNXvS7aPDWSfRtTHtJLxX9Gf4JsCwW', 'Student', 'Active'),
(2, '1019089', '$2y$10$rl8DksRGgHL.a68HzJHGmeRKVDJM410aj8Hk9MNDkq5AHFvRvjAjC', 'Student', 'Active'),
(3, '1039090', 'password123', 'Student', 'Inactive'),
(4, '1069091', '$2y$10$ykuSOwXxICqm8hnB5mNV5.j4DSjWZIvbIK9ejGmHkYFrZ3oMFVYgm', 'Student', 'Active'),
(5, 'L001', '$2y$10$mubIdHR1yxqE.I8wf70PqOwPdafMWT.MKKISQvH8H62LuFz0TBVo6', 'Lecturer', 'Active'),
(6, 'L003', '$2y$10$aWDW.tAQg6XcM9cGHNT3PeCeI2znI0/uGOr/qrSV3gC.l6slzGqWG', 'Lecturer', 'Active'),
(7, 'L019', '$2y$10$VGyjHW8jd/4ZuLg.E5q0UeXRIwiu30GCv16Btw7FbNT9l/ruvLZJu', 'Lecturer', 'Active'),
(8, 'A001', '$2y$10$k5SpDxTee4cB3BJPlkXH7./X.dC5.OUijCAxrFfm7OF4dQRGsQKV6', 'Admin', 'Active'),
(9, 'H001', '$2y$10$2hiyKXZAsdln8or1Th659OpPy7XG9hgrecnl.DwUVr/hNW3pT.jiq', 'Host Organization', 'Active'),
(10, 'H017', 'password123', 'Host Organization', 'Inactive'),
(11, 'H190', '$2y$10$nXcPHcecuhTaG2TT9AkDM.9A0JXDSmjsRedPQ0UvD6uD3C./8eq5O', 'Host Organization', 'Active'),
(12, 'H203', '$2y$10$hH0xWSFUtFkUYtT3pf0qiOayHXPL8DKhd1ey7V0XpJdtdtf4wUuMC', 'Host Organization', 'Active'),
(13, '1090899', '$2y$10$MrkvRaie.Di2JgktBA6VBOY8Js1f9zSF533Hbee7LMFRkQxtBQk5G', 'Student', 'Active');

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
  ADD KEY `StudentID` (`StudentID`);

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
  MODIFY `AttachmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attachmentapplication`
--
ALTER TABLE `attachmentapplication`
  MODIFY `ApplicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `attachmentopportunity`
--
ALTER TABLE `attachmentopportunity`
  MODIFY `OpportunityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `finalreport`
--
ALTER TABLE `finalreport`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hostorganization`
--
ALTER TABLE `hostorganization`
  MODIFY `HostOrgID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `LecturerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `logbook`
--
ALTER TABLE `logbook`
  MODIFY `LogbookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `logbookentry`
--
ALTER TABLE `logbookentry`
  MODIFY `EntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supervision`
--
ALTER TABLE `supervision`
  MODIFY `SupervisionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  ADD CONSTRAINT `attachmentapplication_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`);

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
