-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 10:33 AM
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
CREATE DATABASE IF NOT EXISTS `attachmentmanagementsystem` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `attachmentmanagementsystem`;

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

DROP TABLE IF EXISTS `assessment`;
CREATE TABLE `assessment` (
  `AssessmentID` int(11) NOT NULL,
  `AttachmentID` int(11) DEFAULT NULL,
  `AssessmentDate` date DEFAULT NULL,
  `AssessmentType` varchar(20) DEFAULT NULL,
  `Marks` decimal(5,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CriteriaScores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`CriteriaScores`)),
  `LecturerID` int(11) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Completed',
  `SupervisionComments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment`
--

INSERT INTO `assessment` (`AssessmentID`, `AttachmentID`, `AssessmentDate`, `AssessmentType`, `Marks`, `Remarks`, `CriteriaScores`, `LecturerID`, `Status`, `SupervisionComments`) VALUES
(1, 1, '2026-06-08', 'First Assessment', 87.00, 'John shows exceptional adaptability and team integration. His logbook entries are well-organized and his coding practices are solid.', '[8,9,8,9,10,8,9,9,8,9]', 2, 'Completed', 'Conducted dynamic video review session and verified logs. Good coding skills.'),
(2, 2, '2026-06-08', 'First Assessment', 89.00, 'Mercy has a strong grasp of vulnerability scanning concepts. She was very proactive in manual pentesting tasks.', '[9,8,9,9,10,9,9,8,9,9]', 3, 'Completed', 'Evaluated VM sandbox environment configurations. The student is alert and highly focused.'),
(3, 3, '2026-06-08', 'First Assessment', 86.00, 'David shows great analytical skills. He successfully deployed the Streamlit dashboard and optimized SQL queries.', '[8,8,9,9,9,8,9,9,8,9]', 4, 'Completed', 'Inspected backend migrations and dashboard output structure. Outstanding analytical depth.'),
(4, 4, '2026-06-08', 'First Assessment', 91.00, 'Grace did an outstanding job with React navigation configurations and component building.', '[9,9,9,10,9,9,9,9,10,9]', 5, 'Completed', 'Reviewed GitHub repository logs and CSS visual alignments. Form validations look complete.'),
(5, 5, '2026-06-08', 'First Assessment', 88.00, 'Patrick is highly proficient with Cisco CLI commands. He terminates cables neatly and plans subnets with precision.', '[9,8,9,9,9,8,9,9,9,9]', 6, 'Completed', 'Inspected network patch panels terminations. Good understanding of DHCP configurations.'),
(6, 6, '2026-06-08', 'First Assessment', 85.00, 'Evans adapted quickly to our AWS environments. His Terraform templates were clean and reusable.', '[8,9,8,9,9,8,9,8,9,8]', 7, 'Completed', 'Verified S3 policy boundaries configurations. Demonstrates nice progress.'),
(7, 7, '2026-06-08', 'First Assessment', 84.00, 'Faith handles queued hardware diagnostics tickets with great diligence and user empathy.', '[8,8,8,9,9,8,9,8,8,9]', 8, 'Completed', 'Reviewed system diagnostics tickets logs. Punctual and active in client interactions.'),
(8, 8, '2026-06-08', 'First Assessment', 85.00, 'Benson has good development workflow concepts and communicates well with the team.', '[8,8,9,9,9,8,8,9,8,9]', 9, 'Completed', 'Evaluated local API deployments and code documentation summaries.'),
(9, 9, '2026-06-08', 'First Assessment', 90.00, 'Joseph shows strong diagnostic skills in analyzing network packets and tracking open port compliance.', '[9,9,9,9,10,9,9,8,9,9]', 10, 'Completed', 'Verified Wireshark packet logs analysis capabilities. Exceptional analytical skill.'),
(10, 10, '2026-06-08', 'First Assessment', 87.00, 'Sarah has strong scripting capabilities in Pandas. She delivered correlation reports on schedule.', '[8,9,8,9,10,8,9,9,8,9]', 11, 'Completed', 'Inspected Jupyter notebooks and outliers boxplots diagrams.'),
(11, 11, '2026-06-08', 'First Assessment', 86.00, 'Peter has quick adaptation of state hooks and styling methods. He built beautiful shimmer skeletons.', '[8,8,9,9,9,8,9,9,8,9]', 12, 'Completed', 'Reviewed DOM event listener logic files. Excellent presentation.'),
(12, 12, '2026-06-08', 'First Assessment', 89.00, 'Alice shows great skill in subnet planning and static IP pool management.', '[9,8,9,9,10,9,9,8,9,9]', 13, 'Completed', 'Verified VLAN trunk links configurations on staging switch.'),
(13, 13, '2026-06-08', 'First Assessment', 88.00, 'Charles handles container configuration files perfectly. He completed tasks rapidly.', '[9,8,9,9,9,8,9,9,9,9]', 14, 'Completed', 'Inspected Dockerfile layers build config logic. Safe deployment loop.'),
(14, 14, '2026-06-08', 'First Assessment', 83.00, 'Amina resolved Windows domain controller mapping tickets very methodically.', '[8,8,8,9,9,8,8,8,8,9]', 15, 'Completed', 'Reviewed Active Directory user unlocks ticket queues logs.'),
(15, 15, '2026-06-08', 'First Assessment', 90.00, 'Brian has strong testing logic and is quick to adopt MVC architectures.', '[9,9,9,9,10,9,9,8,9,9]', 16, 'Completed', 'Inspected controller unit tests and routing bindings. Outstanding progress.'),
(16, 16, '2026-06-08', 'First Assessment', 87.00, 'Beatrice is disciplined and pays excellent attention to database security configuration guidelines.', '[8,9,8,9,10,8,9,9,8,9]', 17, 'Completed', 'Reviewed database user privilege auditing logs. Very high diligence.'),
(17, 17, '2026-06-08', 'First Assessment', 86.00, 'Emmanuel has deep understanding of regression metrics and mathematical modeling.', '[8,8,9,9,9,8,9,9,8,9]', 18, 'Completed', 'Inspected GridSearchCV model metrics reports. Informative charts.'),
(18, 18, '2026-06-08', 'First Assessment', 89.00, 'Gloria has excellent visual component layout designs and handled user signups forms efficiently.', '[9,8,9,9,10,9,9,8,9,9]', 19, 'Completed', 'Reviewed React props flow and multi-step wizard logic.'),
(19, 19, '2026-06-08', 'First Assessment', 88.00, 'James completed server static configurations and OSPF path validations successfully.', '[9,8,9,9,9,8,9,9,9,9]', 20, 'Completed', 'Verified OSPF area 0 convergence tables on laboratory setup.'),
(20, 20, '2026-06-08', 'First Assessment', 85.00, 'Cynthia showed strong analytical capabilities during EKS clustering deployments.', '[8,9,8,9,9,8,9,8,9,8]', 21, 'Completed', 'Reviewed Kubernetes deployment manifests files. Safe deployments configuration.'),
(21, 18, '2026-06-15', 'Final Assessment', 69.00, 'Good progress', '[\"9\",\"8\",\"5\",\"6\",\"8\",\"9\",\"1\",\"8\",\"7\",\"8\"]', 13, 'Completed', ''),
(74, 1, '2026-06-14', 'Final Assessment', 74.00, 'Student shows good progress but needs to enhance his understanding on tasks given', '[\"9\",\"9\",\"9\",\"9\",\"9\",\"7\",\"6\",\"5\",\"3\",\"8\"]', 14, 'Completed', NULL),
(76, 2, '2026-06-15', 'Final Assessment', NULL, NULL, NULL, 12, 'Scheduled', NULL),
(78, 3, '2026-06-15', 'Final Assessment', 81.00, 'Good progress', '[\"9\",\"9\",\"9\",\"9\",\"9\",\"9\",\"7\",\"7\",\"6\",\"7\"]', 17, 'Completed', NULL),
(80, 4, '2026-06-15', 'Final Assessment', NULL, NULL, NULL, 19, 'Scheduled', NULL),
(82, 5, '2026-06-15', 'Final Assessment', NULL, NULL, NULL, 2, 'Scheduled', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attachment`
--

DROP TABLE IF EXISTS `attachment`;
CREATE TABLE `attachment` (
  `AttachmentID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `HostOrgID` int(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `ClearanceStatus` varchar(20) DEFAULT NULL,
  `AttachmentStatus` varchar(20) DEFAULT NULL,
  `AssessmentCode` varchar(50) DEFAULT NULL,
  `ClearedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachment`
--

INSERT INTO `attachment` (`AttachmentID`, `StudentID`, `HostOrgID`, `StartDate`, `EndDate`, `ClearanceStatus`, `AttachmentStatus`, `AssessmentCode`, `ClearedAt`) VALUES
(1, 1, 1, '2026-05-04', '2026-07-31', 'Cleared', 'Completed', '3JOSMD', '2026-04-15 07:00:00'),
(2, 2, 2, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1002', '2026-04-15 07:00:00'),
(3, 3, 3, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'XI2W5H', '2026-04-15 07:00:00'),
(4, 4, 4, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1004', '2026-04-15 07:00:00'),
(5, 5, 5, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1005', '2026-04-15 07:00:00'),
(6, 6, 6, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1006', '2026-04-15 07:00:00'),
(7, 7, 7, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1007', '2026-04-15 07:00:00'),
(8, 8, 8, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1008', '2026-04-15 07:00:00'),
(9, 9, 9, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1009', '2026-04-15 07:00:00'),
(10, 10, 10, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1010', '2026-04-15 07:00:00'),
(11, 11, 11, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1011', '2026-04-15 07:00:00'),
(12, 12, 12, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1012', '2026-04-15 07:00:00'),
(13, 13, 13, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1013', '2026-04-15 07:00:00'),
(14, 14, 14, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1014', '2026-04-15 07:00:00'),
(15, 15, 15, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1015', '2026-04-15 07:00:00'),
(16, 16, 1, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'LUZENI', '2026-04-15 07:00:00'),
(17, 17, 2, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1017', '2026-04-15 07:00:00'),
(18, 18, 16, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'MWUYLP', '2026-04-15 07:00:00'),
(19, 19, 17, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1019', '2026-04-15 07:00:00'),
(20, 20, 18, '2026-05-04', '2026-07-31', 'Cleared', 'Ongoing', 'ASS-1020', '2026-04-15 07:00:00'),
(21, 31, 19, '2026-06-14', '2026-07-29', 'Cleared', 'Completed', NULL, '2026-06-14 06:04:59'),
(55, 23, 19, '2026-06-14', '2026-07-29', 'Cleared', 'Ongoing', NULL, '2026-06-14 12:58:55'),
(56, 21, 20, '2026-06-15', '2026-07-30', 'Cleared', 'Ongoing', NULL, '2026-06-15 02:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `attachmentapplication`
--

DROP TABLE IF EXISTS `attachmentapplication`;
CREATE TABLE `attachmentapplication` (
  `ApplicationID` int(11) NOT NULL,
  `StudentID` int(11) DEFAULT NULL,
  `ApplicationDate` date DEFAULT NULL,
  `ApplicationStatus` varchar(20) DEFAULT NULL,
  `IntendedHostOrg` varchar(255) DEFAULT NULL,
  `RejectionReason` text DEFAULT NULL,
  `HostOrgID` int(11) DEFAULT NULL,
  `FinancialClearanceStatus` varchar(20) DEFAULT 'Pending',
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachmentapplication`
--

INSERT INTO `attachmentapplication` (`ApplicationID`, `StudentID`, `ApplicationDate`, `ApplicationStatus`, `IntendedHostOrg`, `RejectionReason`, `HostOrgID`, `FinancialClearanceStatus`, `StartDate`, `EndDate`) VALUES
(1, 1, '2026-04-10', 'Approved', 'Safaricom PLC', NULL, 1, 'Cleared', '2026-05-04', '2026-07-31'),
(2, 2, '2026-04-10', 'Approved', 'Equity Bank Kenya', NULL, 2, 'Cleared', '2026-05-04', '2026-07-31'),
(3, 3, '2026-04-10', 'Approved', 'KCB Bank Group', NULL, 3, 'Cleared', '2026-05-04', '2026-07-31'),
(4, 4, '2026-04-10', 'Approved', 'Cellulant Kenya', NULL, 4, 'Cleared', '2026-05-04', '2026-07-31'),
(5, 5, '2026-04-10', 'Approved', 'Andela Kenya', NULL, 5, 'Cleared', '2026-05-04', '2026-07-31'),
(6, 6, '2026-04-10', 'Approved', 'Jamii Telecommunications', NULL, 6, 'Cleared', '2026-05-04', '2026-07-31'),
(7, 7, '2026-04-10', 'Approved', 'MTN Business Kenya', NULL, 7, 'Cleared', '2026-05-04', '2026-07-31'),
(8, 8, '2026-04-10', 'Approved', 'Computech Limited', NULL, 8, 'Cleared', '2026-05-04', '2026-07-31'),
(9, 9, '2026-04-10', 'Approved', 'Copy Cat Kenya', NULL, 9, 'Cleared', '2026-05-04', '2026-07-31'),
(10, 10, '2026-04-10', 'Approved', 'Techno Brain Kenya', NULL, 10, 'Cleared', '2026-05-04', '2026-07-31'),
(11, 11, '2026-04-10', 'Approved', 'iHub Nairobi', NULL, 11, 'Cleared', '2026-05-04', '2026-07-31'),
(12, 12, '2026-04-10', 'Approved', 'Microsoft ADC', NULL, 12, 'Cleared', '2026-05-04', '2026-07-31'),
(13, 13, '2026-04-10', 'Approved', 'Google Kenya', NULL, 13, 'Cleared', '2026-05-04', '2026-07-31'),
(14, 14, '2026-04-10', 'Approved', 'Oracle Kenya', NULL, 14, 'Cleared', '2026-05-04', '2026-07-31'),
(15, 15, '2026-04-10', 'Approved', 'Cisco Systems Kenya', NULL, 15, 'Cleared', '2026-05-04', '2026-07-31'),
(16, 16, '2026-04-10', 'Approved', 'Safaricom PLC', NULL, 1, 'Cleared', '2026-05-04', '2026-07-31'),
(17, 17, '2026-04-10', 'Approved', 'Equity Bank Kenya', NULL, 2, 'Cleared', '2026-05-04', '2026-07-31'),
(18, 18, '2026-04-12', 'Approved', 'Airtel Kenya', NULL, 16, 'Cleared', '2026-05-04', '2026-07-31'),
(19, 19, '2026-04-12', 'Approved', 'Craft Silicon', NULL, 17, 'Cleared', '2026-05-04', '2026-07-31'),
(20, 20, '2026-04-12', 'Approved', 'Little Cab', NULL, 18, 'Cleared', '2026-05-04', '2026-07-31'),
(21, 21, '2026-04-12', 'Rejected', 'Safaricom PLC', 'Rejected due to lack of financial clearance.', 1, 'Not Cleared', '2026-05-04', '2026-07-31'),
(22, 22, '2026-04-12', 'Rejected', 'Equity Bank Kenya', 'Rejected due to lack of financial clearance.', 2, 'Not Cleared', '2026-05-04', '2026-07-31'),
(23, 23, '2026-04-12', 'Rejected', 'KCB Bank Group', 'Rejected due to lack of financial clearance.', 3, 'Not Cleared', '2026-05-04', '2026-07-31'),
(24, 31, '2026-06-14', 'Approved', 'Salaam Technologies', NULL, 19, 'Cleared', '2026-06-14', '2026-07-29'),
(25, 23, '2026-06-14', 'Approved', 'Salaam Technologies', NULL, 19, 'Cleared', '2026-06-14', '2026-07-29'),
(26, 21, '2026-06-15', 'Approved', 'Catholic University of Eastern Africa', NULL, 20, 'Cleared', '2026-06-15', '2026-07-30');

-- --------------------------------------------------------

--
-- Table structure for table `attachmentopportunity`
--

DROP TABLE IF EXISTS `attachmentopportunity`;
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
(1, 1, 'Software Engineering Intern (Android/iOS/Web)', 'Year 3/4 Student, Java/Kotlin/React skills', '2026-05-01', '2026-06-14', 'Active'),
(2, 2, 'Cyber Security Analyst Intern', 'Knowledge of network security protocols and pentesting VM setups', '2026-05-01', '2026-05-25', 'Closed'),
(3, 3, 'Database Administrator Intern', 'Strong MySQL, SQL Server, query optimization skills', '2026-05-01', '2026-05-25', 'Closed'),
(4, 4, 'Fintech API Integration Developer Intern', 'Understanding of REST APIs, JSON, PHP/Node.js', '2026-05-01', '2026-05-25', 'Closed'),
(5, 5, 'Junior Fullstack React/Node.js Developer Intern', 'Proficiency in JS, Git workflow, React framework', '2026-05-01', '2026-05-25', 'Closed'),
(6, 6, 'Network Security & Routing Intern', 'CCNA coursework completed, understanding of switches and routing', '2026-05-15', '2026-06-15', 'Active'),
(7, 7, 'Cloud Solutions Architect (AWS/Azure) Intern', 'Familiarity with cloud concepts, EC2, S3, IAM user roles', '2026-05-15', '2026-06-15', 'Active'),
(8, 8, 'IT Support & System Administration Intern', 'OS installations (Windows/Linux), hardware diagnostic capabilities', '2026-05-15', '2026-06-15', 'Active'),
(9, 9, 'Hardware & Network Engineering Intern', 'Cat6 crimping, router configurations, local subnet layouts', '2026-05-15', '2026-06-15', 'Active'),
(10, 10, 'Enterprise Software QA Intern', 'Knowledge of automated testing (Selenium/Jest), test case writing', '2026-05-15', '2026-06-15', 'Active'),
(11, 11, 'Tech Innovation & Product Management Intern', 'Strong communication, understanding of Agile methodology', '2026-06-01', '2026-06-30', 'Active'),
(12, 12, 'Software Engineer Intern (Cloud & Distributed Systems)', 'Strong algorithms, C#/C++/Java, understanding of scale', '2026-06-01', '2026-06-30', 'Active'),
(13, 13, 'Data Scientist & ML Intern', 'Python, Pandas, NumPy, introductory ML algorithms, linear algebra', '2026-06-01', '2026-06-30', 'Active'),
(14, 14, 'Database Management Systems Intern', 'Oracle SQL, PL/SQL, database recovery procedures knowledge', '2026-06-01', '2026-06-30', 'Active'),
(15, 15, 'Network Infrastructure Specialist Intern', 'VLAN configurations, routing protocol troubleshooting, Cisco IOS', '2026-06-01', '2026-06-30', 'Active'),
(16, 19, 'SOC Intern', 'Craft well-structured documentation, notebooks, and technical content for both technical and non-technical audiences:\r\nhttps://substack.com/redirect/db458efe-a427-4f91-b…', '2026-06-14', '2026-06-30', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `finalreport`
--

DROP TABLE IF EXISTS `finalreport`;
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
(1, 1, '2026-06-14', 'report_1_1781436863.pdf', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `hostorganization`
--

DROP TABLE IF EXISTS `hostorganization`;
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
(1, 200, 'Safaricom PLC', 'Brenda Wambua', 'safaricom@host.cuea.edu', '+254722000000', 'Safaricom House, Waiyaki Way, Nairobi'),
(2, 201, 'Equity Bank Kenya', 'James Mwangi', 'equity@host.cuea.edu', '+254763000000', 'Equity Centre, Upperhill, Nairobi'),
(3, 202, 'KCB Bank Group', 'Paul Russo', 'kcb@host.cuea.edu', '+254711012000', 'Kencom House, City Square, Nairobi'),
(4, 203, 'Cellulant Kenya', 'Ken Njoroge', 'cellulant@host.cuea.edu', '+254202799000', 'Cavendish Block, Belgravia Office Park, Nairobi'),
(5, 204, 'Andela Kenya', 'Agnes Muthoni', 'andela@host.cuea.edu', '+254700000000', 'Andela Offices, Plaza Place, Nairobi'),
(6, 205, 'Jamii Telecommunications', 'Joshua Chepkwony', 'jtl@host.cuea.edu', '+254740000100', 'Jamii Towers, Nairobi'),
(7, 206, 'MTN Business Kenya', 'Rose Kinyua', 'mtn@host.cuea.edu', '+254203600000', 'MTN House, Galana Road, Nairobi'),
(8, 207, 'Computech Limited', 'Sandip Patel', 'computech@host.cuea.edu', '+254203995000', 'Computech House, Nairobi'),
(9, 208, 'Copy Cat Kenya', 'Nazir Noordin', 'copycat@host.cuea.edu', '+254203970000', 'Copy Cat Building, Nairobi'),
(10, 209, 'Techno Brain Kenya', 'Manoj Shanker', 'technobrain@host.cuea.edu', '+254203740924', 'Techno Brain Suite, Nairobi'),
(11, 210, 'iHub Nairobi', 'Njuguna Kirubi', 'ihub@host.cuea.edu', '+254707234567', 'Senteu Plaza, Nairobi'),
(12, 211, 'Microsoft ADC', 'Catherine Muraga', 'microsoft@host.cuea.edu', '+254205240000', 'Dunhill Towers, Nairobi'),
(13, 212, 'Google Kenya', 'Agnes Gathaiya', 'google@host.cuea.edu', '+254203601000', 'Purshottam Place, Westlands, Nairobi'),
(14, 213, 'Oracle Kenya', 'David Biamah', 'oracle@host.cuea.edu', '+254202763000', 'Delta Corner, Nairobi'),
(15, 214, 'Cisco Systems Kenya', 'Shehab Ghalib', 'cisco@host.cuea.edu', '+254203676000', 'Landmark Plaza, Nairobi'),
(16, 215, 'Airtel Kenya', 'Ashish Malhotra', 'airtel@host.cuea.edu', '+254733100000', 'Airtel Plaza, Mombasa Road, Nairobi'),
(17, 216, 'Craft Silicon', 'Kamal Budhabhatti', 'craftsilicon@host.cuea.edu', '+254204225000', 'Craft Silicon Campus, Nairobi'),
(18, 217, 'Little Cab', 'Alex Mwaura', 'littlecab@host.cuea.edu', '+254709302302', 'Little HQ, Nairobi'),
(19, 219, 'Salaam Technologies', 'Michelle Wachira', 'michellewachira25@gmail.com', '+254701573708', NULL),
(20, 311, 'Catholic University of Eastern Africa', 'Michelle Wachira', 'michellewachira25@gmail.com', '+254701573708', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobapplication`
--

DROP TABLE IF EXISTS `jobapplication`;
CREATE TABLE `jobapplication` (
  `OpportunityID` int(11) NOT NULL,
  `HostOrgID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `ApplicationDate` date NOT NULL,
  `Status` varchar(30) DEFAULT 'Pending',
  `ResumePath` varchar(255) DEFAULT NULL,
  `ResumeLink` varchar(255) DEFAULT NULL,
  `Motivation` text DEFAULT NULL,
  `RejectionReason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobapplication`
--

INSERT INTO `jobapplication` (`OpportunityID`, `HostOrgID`, `StudentID`, `ApplicationDate`, `Status`, `ResumePath`, `ResumeLink`, `Motivation`, `RejectionReason`) VALUES
(1, 1, 1, '2026-05-10', 'Approved', '/uploads/resumes/std1.pdf', 'https://linkedin.com/in/johnkamau', 'I am very passionate about mobile application development and hoping to learn Safaricoms industry scaling standards.', NULL),
(1, 1, 21, '2026-06-15', 'Pending', 'uploads/resumes/dummy.pdf', NULL, 'Test motivation', NULL),
(2, 2, 2, '2026-05-12', 'Approved', '/uploads/resumes/std2.pdf', 'https://linkedin.com/in/mercywanjiku', 'Interested in securing enterprise banking apps. I want to build skills in penetration testing and cyber defense.', NULL),
(3, 3, 3, '2026-05-14', 'Approved', '/uploads/resumes/std3.pdf', 'https://linkedin.com/in/davidomondi', 'Database management is my key interest, and KCB is the perfect place to learn enterprise database administration.', NULL),
(4, 4, 4, '2026-05-15', 'Approved', '/uploads/resumes/std4.pdf', 'https://linkedin.com/in/gracemutua', 'Excited about Fintech APIs and integrations. Cellulant stands out as an ecosystem pioneer in Africa.', NULL),
(5, 5, 5, '2026-05-16', 'Approved', '/uploads/resumes/std5.pdf', 'https://linkedin.com/in/patrickmwangi', 'My long term goal is full stack web development and I want to adopt the strict Agile workflows used by Andela teams.', NULL),
(6, 6, 6, '2026-05-18', 'Pending', '/uploads/resumes/std6.pdf', 'https://linkedin.com/in/evanskiprop', 'I want to build my career in ISP configurations and understand fibers network structure.', NULL),
(7, 7, 7, '2026-05-20', 'Pending', '/uploads/resumes/std7.pdf', 'https://linkedin.com/in/faithchepngetich', 'Cloud infrastructure is the future, and MTN Business provides excellent opportunities to manage hybrid deployments.', NULL),
(8, 8, 8, '2026-05-21', 'Pending', '/uploads/resumes/std8.pdf', 'https://linkedin.com/in/bensononyango', 'I have strong troubleshooting skills and want to master windows/linux server active directory structures.', NULL),
(9, 9, 1, '2026-05-22', 'Pending', '/uploads/resumes/std1.pdf', 'https://linkedin.com/in/johnkamau', 'Hoping to get exposure in routing hardware configuration and switch patch panel planning.', NULL),
(10, 10, 2, '2026-05-23', 'Pending', '/uploads/resumes/std2.pdf', 'https://linkedin.com/in/mercywanjiku', 'I have structured programming skills and want to learn automated testing and security audits in QA workflows.', NULL),
(11, 11, 3, '2026-06-02', 'Pending', '/uploads/resumes/std3.pdf', 'https://linkedin.com/in/davidomondi', 'I want to experience the vibrant startup incubation environment at iHub and learn modern product cycles.', NULL),
(12, 12, 4, '2026-06-03', 'Rejected', '/uploads/resumes/std4.pdf', 'https://linkedin.com/in/gracemutua', 'Fascinated by distributed systems and cloud scale computing. I want to build clean backend architectures.', NULL),
(12, 12, 21, '2026-06-15', 'Pending', 'uploads/resumes/resume_6a2f65912cd45.pdf', NULL, 'lorem ipsum lorem ipsum', NULL),
(13, 13, 5, '2026-06-04', 'Pending', '/uploads/resumes/std5.pdf', 'https://linkedin.com/in/patrickmwangi', 'Data analysis and ML models are my areas of focus. This role will help me master dynamic visualization.', NULL),
(14, 14, 6, '2026-06-05', 'Pending', '/uploads/resumes/std6.pdf', 'https://linkedin.com/in/evanskiprop', 'I want to master database backup and shadow copy restoration techniques on Oracle DBMS platforms.', NULL),
(15, 15, 7, '2026-06-06', 'Pending', '/uploads/resumes/std7.pdf', 'https://linkedin.com/in/faithchepngetich', 'Interested in VLAN configuration and routing path troubleshooting at Cisco Systems.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

DROP TABLE IF EXISTS `lecturer`;
CREATE TABLE `lecturer` (
  `LecturerID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `StaffNumber` varchar(30) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Faculty` varchar(100) DEFAULT NULL,
  `Role` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`LecturerID`, `UserID`, `StaffNumber`, `Name`, `Department`, `Faculty`, `Role`, `Email`) VALUES
(1, 1, 'A001', 'Prof. John Muthama', 'Computer and Information Science', 'Faculty of Science', 'Admin', 'john.muthama@staff.cuea.edu'),
(2, 10, 'STAFF-101', 'Dr. Andrew Ndegwa', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'andrew.ndegwa@staff.cuea.edu'),
(3, 11, 'STAFF-102', 'Prof. Rosemary Wanja', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'rosemary.wanja@staff.cuea.edu'),
(4, 12, 'STAFF-103', 'Dr. Josephat Oburu', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'josephat.oburu@staff.cuea.edu'),
(5, 13, 'STAFF-104', 'Ms. Tabitha Nyaboke', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'tabitha.nyaboke@staff.cuea.edu'),
(6, 14, 'STAFF-105', 'Mr. Silas Kipkorir', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'silas.kipkorir@staff.cuea.edu'),
(7, 15, 'STAFF-106', 'Dr. Agnes Mutisya', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'agnes.mutisya@staff.cuea.edu'),
(8, 16, 'STAFF-107', 'Prof. Fredrick Ochieng', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'fredrick.ochieng@staff.cuea.edu'),
(9, 17, 'STAFF-108', 'Ms. Beatrice Kemunto', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'beatrice.kemunto@staff.cuea.edu'),
(10, 18, 'STAFF-109', 'Dr. Paul Kiprop', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'paul.kiprop@staff.cuea.edu'),
(11, 19, 'STAFF-110', 'Mr. David Mwangi', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'david.mwangi@staff.cuea.edu'),
(12, 20, 'STAFF-111', 'Dr. Lucy Wambui', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'lucy.wambui@staff.cuea.edu'),
(13, 21, 'STAFF-112', 'Prof. Charles Otieno', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'charles.otieno@staff.cuea.edu'),
(14, 22, 'STAFF-113', 'Mr. Evans Kosgei', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'evans.kosgei@staff.cuea.edu'),
(15, 23, 'STAFF-114', 'Ms. Grace Mutua', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'grace.mutua@staff.cuea.edu'),
(16, 24, 'STAFF-115', 'Dr. Patrick Omwamba', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'patrick.omwamba@staff.cuea.edu'),
(17, 25, 'STAFF-116', 'Prof. Amina Hassan', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'amina.hassan@staff.cuea.edu'),
(18, 26, 'STAFF-117', 'Mr. Benson Onyango', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'benson.onyango@staff.cuea.edu'),
(19, 27, 'STAFF-118', 'Dr. Faith Chepngetich', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'faith.chepngetich@staff.cuea.edu'),
(20, 28, 'STAFF-119', 'Ms. Sarah Nafula', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'sarah.nafula@staff.cuea.edu'),
(21, 29, 'STAFF-120', 'Dr. Brian Mwenda', 'Computer and Information Science', 'Faculty of Science', 'Supervisor', 'brian.mwenda@staff.cuea.edu'),
(30, 248, 'STAFF-121', 'Prof. Peter Njoroge', 'Mathematics', 'Science', 'Supervisor', NULL),
(31, 249, 'STAFF-122', 'Dr. Grace Moraa', 'Actuarial Science', 'Science', 'Supervisor', NULL),
(32, 250, 'STAFF-123', 'Prof. John Masinde', 'Computer and Information Science', 'Science', 'Supervisor', NULL),
(33, 251, 'STAFF-124', 'Dr. Faith Cherono', 'Natural Sciences', 'Science', 'Supervisor', NULL),
(34, 252, 'STAFF-125', 'Prof. George Odhiambo', 'Mathematics', 'Science', 'Supervisor', NULL),
(35, 253, 'STAFF-126', 'Dr. Thomas Ndege', 'Actuarial Science', 'Science', 'Supervisor', NULL),
(36, 254, 'STAFF-127', 'Prof. Agnes Kilonzo', 'Computer and Information Science', 'Science', 'Supervisor', NULL),
(37, 255, 'STAFF-128', 'Dr. Simon Kariuki', 'Natural Sciences', 'Science', 'Supervisor', NULL),
(38, 256, 'STAFF-129', 'Prof. Lucy Gacheru', 'Mathematics', 'Science', 'Supervisor', NULL),
(39, 257, 'STAFF-130', 'Dr. Mark Wasike', 'Actuarial Science', 'Science', 'Supervisor', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `logbook`
--

DROP TABLE IF EXISTS `logbook`;
CREATE TABLE `logbook` (
  `LogbookID` int(11) NOT NULL,
  `AttachmentID` int(11) NOT NULL,
  `WeekNumber` int(11) NOT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Activities` text DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  `EntryDate` datetime DEFAULT current_timestamp(),
  `SubmittedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `AcademicSupervisorComments` text DEFAULT NULL,
  `HostSupervisorComments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbook`
--

INSERT INTO `logbook` (`LogbookID`, `AttachmentID`, `WeekNumber`, `StartDate`, `EndDate`, `Activities`, `Status`, `EntryDate`, `SubmittedAt`, `AcademicSupervisorComments`, `HostSupervisorComments`) VALUES
(2, 8, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"IT onboarding and setting up development environment (Git, VS Code, Docker).\",\"comment\":\"Successfully installed all dependencies.\"},\"tuesday\":{\"task\":\"Overview of the codebase and reading technical documentation.\",\"comment\":\"Got an understanding of the application architecture.\"},\"wednesday\":{\"task\":\"Cloning repository and setting up database connection locally.\",\"comment\":\"Faced port conflicts with MySQL, resolved with mentor support.\"},\"thursday\":{\"task\":\"Resolving a minor UI layout bug on the landing page.\",\"comment\":\"Pushed my first CSS changes.\"},\"friday\":{\"task\":\"Submitting first pull request and participating in weekly team review.\",\"comment\":\"PR was approved and merged!\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good initial progress. Focus on clean coding habits.', 'The student is cooperative and has integrated well with the team.'),
(3, 15, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"IT onboarding and setting up development environment (Git, VS Code, Docker).\",\"comment\":\"Successfully installed all dependencies.\"},\"tuesday\":{\"task\":\"Overview of the codebase and reading technical documentation.\",\"comment\":\"Got an understanding of the application architecture.\"},\"wednesday\":{\"task\":\"Cloning repository and setting up database connection locally.\",\"comment\":\"Faced port conflicts with MySQL, resolved with mentor support.\"},\"thursday\":{\"task\":\"Resolving a minor UI layout bug on the landing page.\",\"comment\":\"Pushed my first CSS changes.\"},\"friday\":{\"task\":\"Submitting first pull request and participating in weekly team review.\",\"comment\":\"PR was approved and merged!\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Clear documentation in the logbook. Good work.', 'Brian showed great enthusiasm during onboarding.'),
(5, 8, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Creating reusable card components in React.\",\"comment\":\"Followed clean CSS structures.\"},\"tuesday\":{\"task\":\"Integrating styled-components and maintaining CSS uniformity.\",\"comment\":\"Ensured consistent layouts.\"},\"wednesday\":{\"task\":\"Fetching API data using Axios and handling loading/error states.\",\"comment\":\"Understood promise chaining.\"},\"thursday\":{\"task\":\"Setting up client-side form validation for user registration.\",\"comment\":\"Applied regex validations.\"},\"friday\":{\"task\":\"Conducting cross-browser testing and fixing alignment issues.\",\"comment\":\"Verified behavior on Safari and Chrome.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Nice implementation details. Keep learning React hooks.', 'Benson did a great job implementing the form validations.'),
(6, 15, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Creating reusable card components in React.\",\"comment\":\"Followed clean CSS structures.\"},\"tuesday\":{\"task\":\"Integrating styled-components and maintaining CSS uniformity.\",\"comment\":\"Ensured consistent layouts.\"},\"wednesday\":{\"task\":\"Fetching API data using Axios and handling loading/error states.\",\"comment\":\"Understood promise chaining.\"},\"thursday\":{\"task\":\"Setting up client-side form validation for user registration.\",\"comment\":\"Applied regex validations.\"},\"friday\":{\"task\":\"Conducting cross-browser testing and fixing alignment issues.\",\"comment\":\"Verified behavior on Safari and Chrome.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent work on the styling system.', 'The student works well under minimal supervision.'),
(8, 8, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Creating RESTful API endpoints for user profiles.\",\"comment\":\"Developed GET and PUT routes.\"},\"tuesday\":{\"task\":\"Writing SQL queries and designing table structures.\",\"comment\":\"Optimized user relationship indexing.\"},\"wednesday\":{\"task\":\"Implementing JWT authentication middleware.\",\"comment\":\"Secured API access routes.\"},\"thursday\":{\"task\":\"Unit testing API endpoints using Postman.\",\"comment\":\"Discovered and resolved authentication edge cases.\"},\"friday\":{\"task\":\"Debugging API performance and optimizing query join statements.\",\"comment\":\"Reduced load times by 20%.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Very clear reporting of backend progress.', 'The student shows strong database design capabilities.'),
(9, 15, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Creating RESTful API endpoints for user profiles.\",\"comment\":\"Developed GET and PUT routes.\"},\"tuesday\":{\"task\":\"Writing SQL queries and designing table structures.\",\"comment\":\"Optimized user relationship indexing.\"},\"wednesday\":{\"task\":\"Implementing JWT authentication middleware.\",\"comment\":\"Secured API access routes.\"},\"thursday\":{\"task\":\"Unit testing API endpoints using Postman.\",\"comment\":\"Discovered and resolved authentication edge cases.\"},\"friday\":{\"task\":\"Debugging API performance and optimizing query join statements.\",\"comment\":\"Reduced load times by 20%.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Glad to see you exploring backend security.', 'Excellent comprehension of token validation systems.'),
(11, 8, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Integrating Redis caching for frequently accessed database rows.\",\"comment\":\"Cached config tables.\"},\"tuesday\":{\"task\":\"Writing integration tests for authentication flows.\",\"comment\":\"Covered success and lock scenarios.\"},\"wednesday\":{\"task\":\"Fixing SQL injection vulnerabilities by using prepared statements.\",\"comment\":\"Refactored search endpoints.\"},\"thursday\":{\"task\":\"Running load tests on the profile API.\",\"comment\":\"Tested up to 500 concurrent requests.\"},\"friday\":{\"task\":\"Refactoring controller code to adhere to MVC clean architecture.\",\"comment\":\"Kept controllers slim and models logic-dense.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good alignment of concepts. Security refactoring is critical.', 'The caching integration has improved our dev branch response rate.'),
(12, 15, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Integrating Redis caching for frequently accessed database rows.\",\"comment\":\"Cached config tables.\"},\"tuesday\":{\"task\":\"Writing integration tests for authentication flows.\",\"comment\":\"Covered success and lock scenarios.\"},\"wednesday\":{\"task\":\"Fixing SQL injection vulnerabilities by using prepared statements.\",\"comment\":\"Refactored search endpoints.\"},\"thursday\":{\"task\":\"Running load tests on the profile API.\",\"comment\":\"Tested up to 500 concurrent requests.\"},\"friday\":{\"task\":\"Refactoring controller code to adhere to MVC clean architecture.\",\"comment\":\"Kept controllers slim and models logic-dense.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Important topics. Good execution on prepared queries.', 'Brian showed outstanding testing logic during load assessments.'),
(14, 8, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Writing a Dockerfile to package the application.\",\"comment\":\"Optimized container layer cache.\"},\"tuesday\":{\"task\":\"Setting up a GitHub Actions workflow for automated testing.\",\"comment\":\"Integrated linting tests.\"},\"wednesday\":{\"task\":\"Deploying the containerized application to AWS ECS.\",\"comment\":\"Configured task definitions.\"},\"thursday\":{\"task\":\"Configuring environment variables and security groups.\",\"comment\":\"Secured DB access from outside.\"},\"friday\":{\"task\":\"Monitoring logs using CloudWatch and troubleshooting container launch issues.\",\"comment\":\"Fixed memory allocation limits.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good practical understanding of cloud deployment loops.', 'Very pleased with the Docker orchestration workflow contributions.'),
(15, 15, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Writing a Dockerfile to package the application.\",\"comment\":\"Optimized container layer cache.\"},\"tuesday\":{\"task\":\"Setting up a GitHub Actions workflow for automated testing.\",\"comment\":\"Integrated linting tests.\"},\"wednesday\":{\"task\":\"Deploying the containerized application to AWS ECS.\",\"comment\":\"Configured task definitions.\"},\"thursday\":{\"task\":\"Configuring environment variables and security groups.\",\"comment\":\"Secured DB access from outside.\"},\"friday\":{\"task\":\"Monitoring logs using CloudWatch and troubleshooting container launch issues.\",\"comment\":\"Fixed memory allocation limits.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Very complete and relevant tasks. Cloud setups are essential.', 'Brian has proven highly analytical and adaptive to our cloud tooling.'),
(17, 8, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Code review and refactoring messy legacy classes.\",\"comment\":\"Improved variable namings.\"},\"tuesday\":{\"task\":\"Implementing password hashing migration script.\",\"comment\":\"Safe migration to bcrypt.\"},\"wednesday\":{\"task\":\"Testing edge cases in registration inputs.\",\"comment\":\"Found index out of range bug.\"},\"thursday\":{\"task\":\"Documenting API endpoints using Swagger.\",\"comment\":\"Added parameter descriptions.\"},\"friday\":{\"task\":\"Documenting internal developer guide.\",\"comment\":\"Written in markdown.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(18, 15, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Code review and refactoring messy legacy classes.\",\"comment\":\"Improved variable namings.\"},\"tuesday\":{\"task\":\"Implementing password hashing migration script.\",\"comment\":\"Safe migration to bcrypt.\"},\"wednesday\":{\"task\":\"Testing edge cases in registration inputs.\",\"comment\":\"Found index out of range bug.\"},\"thursday\":{\"task\":\"Documenting API endpoints using Swagger.\",\"comment\":\"Added parameter descriptions.\"},\"friday\":{\"task\":\"Documenting internal developer guide.\",\"comment\":\"Written in markdown.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(20, 9, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Security onboarding and reviewing company IT policies.\",\"comment\":\"Learned device policy regulations.\"},\"tuesday\":{\"task\":\"Setting up security tools (Wireshark, Kali Linux, Nmap) in a VM.\",\"comment\":\"Verified package hashes.\"},\"wednesday\":{\"task\":\"Reading through OWASP Top 10 vulnerabilities documentation.\",\"comment\":\"Studied XSS in detail.\"},\"thursday\":{\"task\":\"Shadowing a senior engineer reviewing server logs.\",\"comment\":\"Understood log correlation patterns.\"},\"friday\":{\"task\":\"Attending a seminar on social engineering and phishing awareness.\",\"comment\":\"Learned about corporate phishing indicators.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good documentation. Keep exploring network logs.', 'Joseph shows high interest in malware behavior logs.'),
(21, 16, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Security onboarding and reviewing company IT policies.\",\"comment\":\"Learned device policy regulations.\"},\"tuesday\":{\"task\":\"Setting up security tools (Wireshark, Kali Linux, Nmap) in a VM.\",\"comment\":\"Verified package hashes.\"},\"wednesday\":{\"task\":\"Reading through OWASP Top 10 vulnerabilities documentation.\",\"comment\":\"Studied XSS in detail.\"},\"thursday\":{\"task\":\"Shadowing a senior engineer reviewing server logs.\",\"comment\":\"Understood log correlation patterns.\"},\"friday\":{\"task\":\"Attending a seminar on social engineering and phishing awareness.\",\"comment\":\"Learned about corporate phishing indicators.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Clear descriptions. Keep it up.', 'Beatrice was punctual and active during our security policies briefings.'),
(23, 9, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Running Nessus scans on development servers.\",\"comment\":\"Identified missing SSL patches.\"},\"tuesday\":{\"task\":\"Analyzing Nessus scan reports for critical patches.\",\"comment\":\"Created action plan for OS upgrades.\"},\"wednesday\":{\"task\":\"Scanning internal subnets using Nmap for open ports.\",\"comment\":\"Found unauthorized HTTP port open.\"},\"thursday\":{\"task\":\"Testing web forms for SQL Injection and XSS vulnerabilities manually.\",\"comment\":\"Proved vulnerability on review forms.\"},\"friday\":{\"task\":\"Writing a basic report on the findings and recommending fixes.\",\"comment\":\"Report submitted to lead dev.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Keep analyzing open ports and recommend firewalls.', 'Joseph has delivered an exceptionally structured scan report.'),
(24, 16, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Running Nessus scans on development servers.\",\"comment\":\"Identified missing SSL patches.\"},\"tuesday\":{\"task\":\"Analyzing Nessus scan reports for critical patches.\",\"comment\":\"Created action plan for OS upgrades.\"},\"wednesday\":{\"task\":\"Scanning internal subnets using Nmap for open ports.\",\"comment\":\"Found unauthorized HTTP port open.\"},\"thursday\":{\"task\":\"Testing web forms for SQL Injection and XSS vulnerabilities manually.\",\"comment\":\"Proved vulnerability on review forms.\"},\"friday\":{\"task\":\"Writing a basic report on the findings and recommending fixes.\",\"comment\":\"Report submitted to lead dev.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good overview of the scan findings.', 'We patched 2 ports based directly on Beatrice\'s feedback.'),
(26, 9, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Monitoring network traffic using Wireshark.\",\"comment\":\"Inspected TCP handshakes.\"},\"tuesday\":{\"task\":\"Analyzing packet captures for unencrypted password transmissions.\",\"comment\":\"Highlighted HTTP basic auth vulnerabilities.\"},\"wednesday\":{\"task\":\"Setting up Snort IDS rules on a local test gateway.\",\"comment\":\"Added rule for scanning alerts.\"},\"thursday\":{\"task\":\"Monitoring security logs for brute-force login attempts.\",\"comment\":\"Identified 3 failed login bursts.\"},\"friday\":{\"task\":\"Investigating a suspicious IP address and writing a block rule.\",\"comment\":\"Updated firewall rules successfully.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Keep building security IDS rules.', 'Joseph shows high interest in malware behavior logs.'),
(27, 16, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Monitoring network traffic using Wireshark.\",\"comment\":\"Inspected TCP handshakes.\"},\"tuesday\":{\"task\":\"Analyzing packet captures for unencrypted password transmissions.\",\"comment\":\"Highlighted HTTP basic auth vulnerabilities.\"},\"wednesday\":{\"task\":\"Setting up Snort IDS rules on a local test gateway.\",\"comment\":\"Added rule for scanning alerts.\"},\"thursday\":{\"task\":\"Monitoring security logs for brute-force login attempts.\",\"comment\":\"Identified 3 failed login bursts.\"},\"friday\":{\"task\":\"Investigating a suspicious IP address and writing a block rule.\",\"comment\":\"Updated firewall rules successfully.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Keep up the packet routing inspection.', 'Beatrice showed quick critical thinking in resolving gateway blocks.'),
(29, 9, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Auditing user access permissions across active databases.\",\"comment\":\"Created mapping of current admin accounts.\"},\"tuesday\":{\"task\":\"Identifying inactive accounts with elevated privileges.\",\"comment\":\"Flagged 4 accounts for deletion.\"},\"wednesday\":{\"task\":\"Reviewing password strength policies and implementation.\",\"comment\":\"Proposed character limit rules.\"},\"thursday\":{\"task\":\"Testing the application\'s CSRF token protection mechanism.\",\"comment\":\"Verified token generation validity.\"},\"friday\":{\"task\":\"Submitting recommendations to align with ISO 27001 standards.\",\"comment\":\"Delivered compliance roadmap.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good practical coverage of authorization schemes.', 'Joseph\'s security audit was thorough and exposed unused accounts.'),
(30, 16, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Auditing user access permissions across active databases.\",\"comment\":\"Created mapping of current admin accounts.\"},\"tuesday\":{\"task\":\"Identifying inactive accounts with elevated privileges.\",\"comment\":\"Flagged 4 accounts for deletion.\"},\"wednesday\":{\"task\":\"Reviewing password strength policies and implementation.\",\"comment\":\"Proposed character limit rules.\"},\"thursday\":{\"task\":\"Testing the application\'s CSRF token protection mechanism.\",\"comment\":\"Verified token generation validity.\"},\"friday\":{\"task\":\"Submitting recommendations to align with ISO 27001 standards.\",\"comment\":\"Delivered compliance roadmap.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent work on compliance alignment.', 'The audit helped our team deprecate obsolete database credentials.'),
(32, 9, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Simulating a DDoS attack in a controlled sandbox.\",\"comment\":\"Used Apache Bench tool.\"},\"tuesday\":{\"task\":\"Configuring rate limiting rules on Nginx web server.\",\"comment\":\"Limited logins to 10 requests per minute.\"},\"wednesday\":{\"task\":\"Setting up Fail2ban to block IPs showing malicious behavior.\",\"comment\":\"Integrated with SSH login logs.\"},\"thursday\":{\"task\":\"Testing data recovery procedures from recent system backups.\",\"comment\":\"Successfully restored backup test schema.\"},\"friday\":{\"task\":\"Participating in an incident response dry-run meeting.\",\"comment\":\"Shared rate limit configurations with dev team.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Strong work. Incident dry runs are very educational.', 'Joseph is working very hard on blocking script-based attacks.'),
(33, 16, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Simulating a DDoS attack in a controlled sandbox.\",\"comment\":\"Used Apache Bench tool.\"},\"tuesday\":{\"task\":\"Configuring rate limiting rules on Nginx web server.\",\"comment\":\"Limited logins to 10 requests per minute.\"},\"wednesday\":{\"task\":\"Setting up Fail2ban to block IPs showing malicious behavior.\",\"comment\":\"Integrated with SSH login logs.\"},\"thursday\":{\"task\":\"Testing data recovery procedures from recent system backups.\",\"comment\":\"Successfully restored backup test schema.\"},\"friday\":{\"task\":\"Participating in an incident response dry-run meeting.\",\"comment\":\"Shared rate limit configurations with dev team.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Very complete and relevant tasks. Cloud setups are essential.', 'Beatrice shows consistent execution and security discipline.'),
(35, 9, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Performing authorized pentesting on a dummy application.\",\"comment\":\"Tested session cookies settings.\"},\"tuesday\":{\"task\":\"Exploiting an outdated SSH version to gain user access.\",\"comment\":\"Tested in isolated sandbox.\"},\"wednesday\":{\"task\":\"Conducting privilege escalation testing using local exploits.\",\"comment\":\"Successfully proved root vulnerability.\"},\"thursday\":{\"task\":\"Reporting the exact steps to reproduce the exploit.\",\"comment\":\"Delivered detailed write-up.\"},\"friday\":{\"task\":\"Verifying the patches implemented by the dev team.\",\"comment\":\"Verified SSH version is upgraded.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(36, 16, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Performing authorized pentesting on a dummy application.\",\"comment\":\"Tested session cookies settings.\"},\"tuesday\":{\"task\":\"Exploiting an outdated SSH version to gain user access.\",\"comment\":\"Tested in isolated sandbox.\"},\"wednesday\":{\"task\":\"Conducting privilege escalation testing using local exploits.\",\"comment\":\"Successfully proved root vulnerability.\"},\"thursday\":{\"task\":\"Reporting the exact steps to reproduce the exploit.\",\"comment\":\"Delivered detailed write-up.\"},\"friday\":{\"task\":\"Verifying the patches implemented by the dev team.\",\"comment\":\"Verified SSH version is upgraded.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(38, 10, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Setting up Anaconda environment and Jupyter notebooks.\",\"comment\":\"All libraries resolved.\"},\"tuesday\":{\"task\":\"Refreshing knowledge of Pandas, NumPy, and Matplotlib.\",\"comment\":\"Practiced array slices.\"},\"wednesday\":{\"task\":\"Loading CSV datasets and checking for missing values.\",\"comment\":\"Identified 15% null values in transaction logs.\"},\"thursday\":{\"task\":\"Performing basic data imputation and cleaning techniques.\",\"comment\":\"Used median imputation.\"},\"friday\":{\"task\":\"Presenting initial descriptive statistics to the supervisor.\",\"comment\":\"Created clean bar charts.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good initial overview. Keep detailing data operations.', 'Sarah demonstrated immediate competence with Pandas manipulation.'),
(39, 17, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Setting up Anaconda environment and Jupyter notebooks.\",\"comment\":\"All libraries resolved.\"},\"tuesday\":{\"task\":\"Refreshing knowledge of Pandas, NumPy, and Matplotlib.\",\"comment\":\"Practiced array slices.\"},\"wednesday\":{\"task\":\"Loading CSV datasets and checking for missing values.\",\"comment\":\"Identified 15% null values in transaction logs.\"},\"thursday\":{\"task\":\"Performing basic data imputation and cleaning techniques.\",\"comment\":\"Used median imputation.\"},\"friday\":{\"task\":\"Presenting initial descriptive statistics to the supervisor.\",\"comment\":\"Created clean bar charts.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Solid beginning. Document all libraries used.', 'Emmanuel displayed outstanding analytical promise.'),
(41, 10, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Plotting histograms and boxplots to detect outliers.\",\"comment\":\"Found anomalies in high-value columns.\"},\"tuesday\":{\"task\":\"Analyzing correlations between variables using heatmaps.\",\"comment\":\"Identified strong positive correlations between size and cost.\"},\"wednesday\":{\"task\":\"Grouping data by categories and calculating aggregations.\",\"comment\":\"Analyzed average spend per region.\"},\"thursday\":{\"task\":\"Cleaning text data using regular expressions.\",\"comment\":\"Extracted phone prefixes.\"},\"friday\":{\"task\":\"Drafting an exploratory analysis report.\",\"comment\":\"Report approved by supervisor.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Outlier detection is critical. Good job using boxplots.', 'Sarah is highly organized in presenting dataset reports.'),
(42, 17, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Plotting histograms and boxplots to detect outliers.\",\"comment\":\"Found anomalies in high-value columns.\"},\"tuesday\":{\"task\":\"Analyzing correlations between variables using heatmaps.\",\"comment\":\"Identified strong positive correlations between size and cost.\"},\"wednesday\":{\"task\":\"Grouping data by categories and calculating aggregations.\",\"comment\":\"Analyzed average spend per region.\"},\"thursday\":{\"task\":\"Cleaning text data using regular expressions.\",\"comment\":\"Extracted phone prefixes.\"},\"friday\":{\"task\":\"Drafting an exploratory analysis report.\",\"comment\":\"Report approved by supervisor.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent correlation insights.', 'Emmanuel has integrated well with our reporting unit.'),
(44, 10, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Writing complex SQL queries with nested subqueries and JOINs.\",\"comment\":\"Queried multi-tier customer tables.\"},\"tuesday\":{\"task\":\"Optimizing query performance by adding database indexes.\",\"comment\":\"Reduced runtimes from 8s to 0.4s.\"},\"wednesday\":{\"task\":\"Connecting python scripts to MySQL database using SQLAlchemy.\",\"comment\":\"Used ORM setups.\"},\"thursday\":{\"task\":\"Migrating clean CSV data into relational tables.\",\"comment\":\"Successfully inserted 10k rows.\"},\"friday\":{\"task\":\"Automating data extraction queries into a cron job.\",\"comment\":\"Pushed bash automation script.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Keep practicing SQL optimization.', 'Sarah helped clean up our legacy reporting queries.'),
(45, 17, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Writing complex SQL queries with nested subqueries and JOINs.\",\"comment\":\"Queried multi-tier customer tables.\"},\"tuesday\":{\"task\":\"Optimizing query performance by adding database indexes.\",\"comment\":\"Reduced runtimes from 8s to 0.4s.\"},\"wednesday\":{\"task\":\"Connecting python scripts to MySQL database using SQLAlchemy.\",\"comment\":\"Used ORM setups.\"},\"thursday\":{\"task\":\"Migrating clean CSV data into relational tables.\",\"comment\":\"Successfully inserted 10k rows.\"},\"friday\":{\"task\":\"Automating data extraction queries into a cron job.\",\"comment\":\"Pushed bash automation script.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'ORM connections are very standard. Good job.', 'Excellent implementation of the cron automation.'),
(47, 10, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Splitting data into training and testing sets.\",\"comment\":\"Applied stratified splitting.\"},\"tuesday\":{\"task\":\"Training a simple Linear Regression model for price forecasting.\",\"comment\":\"Identified coefficient values.\"},\"wednesday\":{\"task\":\"Evaluating model performance using MSE and R-squared metrics.\",\"comment\":\"Achieved R-squared of 0.82.\"},\"thursday\":{\"task\":\"Hyperparameter tuning using GridSearchCV.\",\"comment\":\"Optimized model parameters.\"},\"friday\":{\"task\":\"Saving the trained model using Joblib.\",\"comment\":\"Model saved to production artifacts.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Understanding metrics like R-squared is crucial. Well done.', 'Sarah worked meticulously on hyperparameter adjustments.'),
(48, 17, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Splitting data into training and testing sets.\",\"comment\":\"Applied stratified splitting.\"},\"tuesday\":{\"task\":\"Training a simple Linear Regression model for price forecasting.\",\"comment\":\"Identified coefficient values.\"},\"wednesday\":{\"task\":\"Evaluating model performance using MSE and R-squared metrics.\",\"comment\":\"Achieved R-squared of 0.82.\"},\"thursday\":{\"task\":\"Hyperparameter tuning using GridSearchCV.\",\"comment\":\"Optimized model parameters.\"},\"friday\":{\"task\":\"Saving the trained model using Joblib.\",\"comment\":\"Model saved to production artifacts.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Solid evaluation pipeline.', 'Emmanuel showed deep mathematical understanding of regression metrics.'),
(50, 10, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Building an interactive dashboard using Streamlit.\",\"comment\":\"Created sidebar controls.\"},\"tuesday\":{\"task\":\"Adding dropdowns and sliders to filter dashboard data.\",\"comment\":\"Allowed multi-region filters.\"},\"wednesday\":{\"task\":\"Integrating Plotly charts for dynamic visualizations.\",\"comment\":\"Implemented dynamic scatterplots.\"},\"thursday\":{\"task\":\"Deploying the Streamlit dashboard to Heroku.\",\"comment\":\"Encountered deployment logs issue, fixed buildpacks.\"},\"friday\":{\"task\":\"Presenting the working dashboard to the analytics team.\",\"comment\":\"Positive reception from stakeholders.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Great frontend representation of data.', 'Sarah successfully pushed her dashboard to staging.'),
(51, 17, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Building an interactive dashboard using Streamlit.\",\"comment\":\"Created sidebar controls.\"},\"tuesday\":{\"task\":\"Adding dropdowns and sliders to filter dashboard data.\",\"comment\":\"Allowed multi-region filters.\"},\"wednesday\":{\"task\":\"Integrating Plotly charts for dynamic visualizations.\",\"comment\":\"Implemented dynamic scatterplots.\"},\"thursday\":{\"task\":\"Deploying the Streamlit dashboard to Heroku.\",\"comment\":\"Encountered deployment logs issue, fixed buildpacks.\"},\"friday\":{\"task\":\"Presenting the working dashboard to the analytics team.\",\"comment\":\"Positive reception from stakeholders.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent work deploying a real dashboard.', 'The interactive Plotly visualizations were well received.'),
(53, 10, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Learning about Apache Spark architecture.\",\"comment\":\"Understood RDDs structure.\"},\"tuesday\":{\"task\":\"Reading parquet files using PySpark.\",\"comment\":\"Processed files in partitions.\"},\"wednesday\":{\"task\":\"Setting up an AWS S3 bucket for data storage.\",\"comment\":\"Configured bucket policy access.\"},\"thursday\":{\"task\":\"Running a basic ETL pipeline in the cloud.\",\"comment\":\"Extracted, transformed, and loaded data.\"},\"friday\":{\"task\":\"Writing documentation for the data pipeline.\",\"comment\":\"Saved as markdown file.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(54, 17, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Learning about Apache Spark architecture.\",\"comment\":\"Understood RDDs structure.\"},\"tuesday\":{\"task\":\"Reading parquet files using PySpark.\",\"comment\":\"Processed files in partitions.\"},\"wednesday\":{\"task\":\"Setting up an AWS S3 bucket for data storage.\",\"comment\":\"Configured bucket policy access.\"},\"thursday\":{\"task\":\"Running a basic ETL pipeline in the cloud.\",\"comment\":\"Extracted, transformed, and loaded data.\"},\"friday\":{\"task\":\"Writing documentation for the data pipeline.\",\"comment\":\"Saved as markdown file.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(56, 11, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Setting up visual editor and Git version control.\",\"comment\":\"Initialized local git repository.\"},\"tuesday\":{\"task\":\"Writing semantic HTML5 structures for a landing page.\",\"comment\":\"Used section, article, and footer.\"},\"wednesday\":{\"task\":\"Creating responsive CSS layouts using Flexbox and Grid.\",\"comment\":\"Familiarized with grid-template-columns.\"},\"thursday\":{\"task\":\"Implementing css media queries for mobile-first responsiveness.\",\"comment\":\"Adapted layouts for screen widths < 768px.\"},\"friday\":{\"task\":\"Pushing the code to GitHub and hosting on Netlify.\",\"comment\":\"Created dynamic deploy pipeline.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good initial progress. Semantic code is very clean.', 'Peter demonstrated immediate web layout abilities.'),
(57, 18, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Setting up visual editor and Git version control.\",\"comment\":\"Initialized local git repository.\"},\"tuesday\":{\"task\":\"Writing semantic HTML5 structures for a landing page.\",\"comment\":\"Used section, article, and footer.\"},\"wednesday\":{\"task\":\"Creating responsive CSS layouts using Flexbox and Grid.\",\"comment\":\"Familiarized with grid-template-columns.\"},\"thursday\":{\"task\":\"Implementing css media queries for mobile-first responsiveness.\",\"comment\":\"Adapted layouts for screen widths < 768px.\"},\"friday\":{\"task\":\"Pushing the code to GitHub and hosting on Netlify.\",\"comment\":\"Created dynamic deploy pipeline.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Solid beginning. Keep documenting css details.', 'Gloria has integrated well with our creative branch.'),
(59, 11, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Manipulating the DOM using vanilla JavaScript.\",\"comment\":\"Learned querySelector.\"},\"tuesday\":{\"task\":\"Creating dynamic content lists and event listeners.\",\"comment\":\"Implemented click event listings.\"},\"wednesday\":{\"task\":\"Validating login forms before submission.\",\"comment\":\"Checked passwords lengths.\"},\"thursday\":{\"task\":\"Fetching random user data from a public API.\",\"comment\":\"Parsed JSON response fields.\"},\"friday\":{\"task\":\"Creating a dark/light mode toggle.\",\"comment\":\"Pushed preferences to cookie session.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Vanilla JS experience is highly valuable.', 'Peter wrote a clean class selection logic for the light/dark toggle.'),
(60, 18, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Manipulating the DOM using vanilla JavaScript.\",\"comment\":\"Learned querySelector.\"},\"tuesday\":{\"task\":\"Creating dynamic content lists and event listeners.\",\"comment\":\"Implemented click event listings.\"},\"wednesday\":{\"task\":\"Validating login forms before submission.\",\"comment\":\"Checked passwords lengths.\"},\"thursday\":{\"task\":\"Fetching random user data from a public API.\",\"comment\":\"Parsed JSON response fields.\"},\"friday\":{\"task\":\"Creating a dark/light mode toggle.\",\"comment\":\"Pushed preferences to cookie session.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good JavaScript logic.', 'Gloria continues to show impressive frontend adaptation.'),
(62, 11, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Exploring Bootstrap utility classes and components.\",\"comment\":\"Practiced styling modal forms.\"},\"tuesday\":{\"task\":\"Re-building the layout using Tailwind CSS.\",\"comment\":\"Learned utility-first workflow concepts.\"},\"wednesday\":{\"task\":\"Creating customizable modal dialogs and dropdowns.\",\"comment\":\"Configured dynamic click attributes.\"},\"thursday\":{\"task\":\"Optimizing CSS files for production using PurgeCSS.\",\"comment\":\"Reduced css payload by 80%.\"},\"friday\":{\"task\":\"Presenting the redesigned UI to the client.\",\"comment\":\"Client approved layout design.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent work optimization.', 'Peter handled CSS compiling config setups with ease.'),
(63, 18, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Exploring Bootstrap utility classes and components.\",\"comment\":\"Practiced styling modal forms.\"},\"tuesday\":{\"task\":\"Re-building the layout using Tailwind CSS.\",\"comment\":\"Learned utility-first workflow concepts.\"},\"wednesday\":{\"task\":\"Creating customizable modal dialogs and dropdowns.\",\"comment\":\"Configured dynamic click attributes.\"},\"thursday\":{\"task\":\"Optimizing CSS files for production using PurgeCSS.\",\"comment\":\"Reduced css payload by 80%.\"},\"friday\":{\"task\":\"Presenting the redesigned UI to the client.\",\"comment\":\"Client approved layout design $$ \"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Very practical application designs.', 'Gloria is contributing well during client review meetings.'),
(65, 11, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Learning React JSX syntax and component structure.\",\"comment\":\"Created layout headers in React.\"},\"tuesday\":{\"task\":\"Passing data using Props and managing local State.\",\"comment\":\"Implemented state filters.\"},\"wednesday\":{\"task\":\"Handling side effects with the useEffect hook.\",\"comment\":\"Fetched dynamic database entries.\"},\"thursday\":{\"task\":\"Building a multi-step form wizard component.\",\"comment\":\"Created dynamic next/back buttons.\"},\"friday\":{\"task\":\"Structuring navigation using React Router.\",\"comment\":\"Defined private routing paths.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'React components look well-structured.', 'Peter has picked up state hooks faster than expected.'),
(66, 18, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Learning React JSX syntax and component structure.\",\"comment\":\"Created layout headers in React.\"},\"tuesday\":{\"task\":\"Passing data using Props and managing local State.\",\"comment\":\"Implemented state filters.\"},\"wednesday\":{\"task\":\"Handling side effects with the useEffect hook.\",\"comment\":\"Fetched dynamic database entries.\"},\"thursday\":{\"task\":\"Building a multi-step form wizard component.\",\"comment\":\"Created dynamic next/back buttons.\"},\"friday\":{\"task\":\"Structuring navigation using React Router.\",\"comment\":\"Defined private routing paths.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Private routing setup is a good security measure.', 'Gloria completed the dynamic multi-step signup components.'),
(68, 11, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Learning about global state using Context API.\",\"comment\":\"Set up context provider layers.\"},\"tuesday\":{\"task\":\"Integrating dynamic API requests with React application.\",\"comment\":\"Integrated Axios calls.\"},\"wednesday\":{\"task\":\"Storing user preferences in LocalStorage.\",\"comment\":\"Persisted theme settings.\"},\"thursday\":{\"task\":\"Creating custom loading skeletons for data fetching.\",\"comment\":\"Created CSS shimmer layout animations.\"},\"friday\":{\"task\":\"Deploying the React app to Vercel.\",\"comment\":\"Configured environment secrets on dashboard.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good practical coverage of modern frontend deployment.', 'Peter has designed a beautiful skeletal loading layout.'),
(69, 18, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Learning about global state using Context API.\",\"comment\":\"Set up context provider layers.\"},\"tuesday\":{\"task\":\"Integrating dynamic API requests with React application.\",\"comment\":\"Integrated Axios calls.\"},\"wednesday\":{\"task\":\"Storing user preferences in LocalStorage.\",\"comment\":\"Persisted theme settings.\"},\"thursday\":{\"task\":\"Creating custom loading skeletons for data fetching.\",\"comment\":\"Created CSS shimmer layout animations.\"},\"friday\":{\"task\":\"Deploying the React app to Vercel.\",\"comment\":\"Configured environment secrets on dashboard.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'State handling looks strong.', 'Excellent implementation of context state and theme management.'),
(71, 11, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Writing basic unit tests using Jest.\",\"comment\":\"Tested component mount logic.\"},\"tuesday\":{\"task\":\"Testing interactive buttons and forms.\",\"comment\":\"Simulated form submit events.\"},\"wednesday\":{\"task\":\"Optimizing image assets and using lazy loading.\",\"comment\":\"Implemented intersection observer.\"},\"thursday\":{\"task\":\"Debugging console errors and resolving memory leaks.\",\"comment\":\"Cleaned up event listeners.\"},\"friday\":{\"task\":\"Final code review and project handoff.\",\"comment\":\"Completed final deployment docs.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(72, 18, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Writing basic unit tests using Jest.\",\"comment\":\"Tested component mount logic.\"},\"tuesday\":{\"task\":\"Testing interactive buttons and forms.\",\"comment\":\"Simulated form submit events.\"},\"wednesday\":{\"task\":\"Optimizing image assets and using lazy loading.\",\"comment\":\"Implemented intersection observer.\"},\"thursday\":{\"task\":\"Debugging console errors and resolving memory leaks.\",\"comment\":\"Cleaned up event listeners.\"},\"friday\":{\"task\":\"Final code review and project handoff.\",\"comment\":\"Completed final deployment docs.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(74, 12, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Lab safety training and introduction to network infrastructure.\",\"comment\":\"Understood server room standards.\"},\"tuesday\":{\"task\":\"Terminating Ethernet cables (CAT6) with RJ45 connectors.\",\"comment\":\"Crimped 10 patch cords.\"},\"wednesday\":{\"task\":\"Testing cable continuity and diagnosing faulty connections.\",\"comment\":\"Used cable tester tool.\"},\"thursday\":{\"task\":\"Documenting network racks and labeling patch panels.\",\"comment\":\"Created detailed rack mapping schema.\"},\"friday\":{\"task\":\"Helping deploy wireless access points in the office.\",\"comment\":\"Mounted APs in West Wing.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good documentation of physical network rack layers.', 'Alice has integrated well with the hardware deployment team.'),
(75, 19, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Lab safety training and introduction to network infrastructure.\",\"comment\":\"Understood server room standards.\"},\"tuesday\":{\"task\":\"Terminating Ethernet cables (CAT6) with RJ45 connectors.\",\"comment\":\"Crimrep 10 patch cords.\"},\"wednesday\":{\"task\":\"Testing cable continuity and diagnosing faulty connections.\",\"comment\":\"Used cable tester tool.\"},\"thursday\":{\"task\":\"Documenting network racks and labeling patch panels.\",\"comment\":\"Created detailed rack mapping schema.\"},\"friday\":{\"task\":\"Helping deploy wireless access points in the office.\",\"comment\":\"Mounted APs in West Wing.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Solid beginning. Keep notes on wiring standards.', 'James showed strong physical layer implementation skills.'),
(77, 12, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Reviewing IPv4 addressing and CIDR notation.\",\"comment\":\"Reviewed routing prefix divisions.\"},\"tuesday\":{\"task\":\"Planning a subnet scheme for a new office department.\",\"comment\":\"Divided /24 space to 4 subnets.\"},\"wednesday\":{\"task\":\"Configuring static IP addresses on servers and printers.\",\"comment\":\"Assigned printer static mappings.\"},\"thursday\":{\"task\":\"Troubleshooting IP conflicts on the local network.\",\"comment\":\"Identified conflict with rogue devices.\"},\"friday\":{\"task\":\"Setting up a local DHCP server pool.\",\"comment\":\"Allocated leasing schedules.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Keep analyzing dynamic IP leasing settings.', 'Alice planned a very efficient subnet allocation map.'),
(78, 19, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Reviewing IPv4 addressing and CIDR notation.\",\"comment\":\"Reviewed routing prefix divisions.\"},\"tuesday\":{\"task\":\"Planning a subnet scheme for a new office department.\",\"comment\":\"Divided /24 space to 4 subnets.\"},\"wednesday\":{\"task\":\"Configuring static IP addresses on servers and printers.\",\"comment\":\"Assigned printer static mappings.\"},\"thursday\":{\"task\":\"Troubleshooting IP conflicts on the local network.\",\"comment\":\"Identified conflict with rogue devices.\"},\"friday\":{\"task\":\"Setting up a local DHCP server pool.\",\"comment\":\"Allocated leasing schedules.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent CIDR partitioning layout details.', 'James completed static server bindings without errors.'),
(80, 12, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Connecting to routers using console cables and PuTTY.\",\"comment\":\"Configured terminal speeds.\"},\"tuesday\":{\"task\":\"Configuring basic switch security settings (SSH, passwords).\",\"comment\":\"Set SSH encryption key layers.\"},\"wednesday\":{\"task\":\"Setting up Virtual LANs (VLANs) on a Cisco switch.\",\"comment\":\"Divided port segments to VLAN 10 and 20.\"},\"thursday\":{\"task\":\"Configuring Inter-VLAN routing on a Layer 3 switch.\",\"comment\":\"Established SVI configurations.\"},\"friday\":{\"task\":\"Testing network connectivity between different VLANs.\",\"comment\":\"Successful ping responses across VLANs.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'VLAN configurations are core logic.', 'Alice successfully partitioned test switch ports to VLANs.'),
(81, 19, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Connecting to routers using console cables and PuTTY.\",\"comment\":\"Configured terminal speeds.\"},\"tuesday\":{\"task\":\"Configuring basic switch security settings (SSH, passwords).\",\"comment\":\"Set SSH encryption key layers.\"},\"wednesday\":{\"task\":\"Setting up Virtual LANs (VLANs) on a Cisco switch.\",\"comment\":\"Divided port segments to VLAN 10 and 20.\"},\"thursday\":{\"task\":\"Configuring Inter-VLAN routing on a Layer 3 switch.\",\"comment\":\"Established SVI configurations.\"},\"friday\":{\"task\":\"Testing network connectivity between different VLANs.\",\"comment\":\"Successful ping responses across VLANs.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Excellent CLI access configuration.', 'James configured terminal password rules with high precision.'),
(83, 12, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Learning about static routing vs dynamic routing.\",\"comment\":\"Reviewed route priority levels.\"},\"tuesday\":{\"task\":\"Configuring OSPF routing protocol on three test routers.\",\"comment\":\"Set up area 0 configurations.\"},\"wednesday\":{\"task\":\"Testing OSPF path convergence and link failures.\",\"comment\":\"Monitored routing tables adjustment.\"},\"thursday\":{\"task\":\"Configuring NAT and PAT on the edge router.\",\"comment\":\"Translated private addresses to WAN interface.\"},\"friday\":{\"task\":\"Backing up router configurations to a TFTP server.\",\"comment\":\"Successfully backed up running config.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'OSPF path convergence is key to network scaling. Good job.', 'Alice configured NAT pools correctly on our test gateways.'),
(84, 19, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Learning about static routing vs dynamic routing.\",\"comment\":\"Reviewed route priority levels.\"},\"tuesday\":{\"task\":\"Configuring OSPF routing protocol on three test routers.\",\"comment\":\"Set up area 0 configurations.\"},\"wednesday\":{\"task\":\"Testing OSPF path convergence and link failures.\",\"comment\":\"Monitored routing tables adjustment.\"},\"thursday\":{\"task\":\"Configuring NAT and PAT on the edge router.\",\"comment\":\"Translated private addresses to WAN interface.\"},\"friday\":{\"task\":\"Backing up router configurations to a TFTP server.\",\"comment\":\"Successfully backed up running config.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Very systematic routing configurations.', 'James backup checks on TFTP servers were well-documented.'),
(86, 12, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Configuring WPA3 security on corporate wireless networks.\",\"comment\":\"Set up enterprise encryption parameters.\"},\"tuesday\":{\"task\":\"Setting up a guest Wi-Fi portal with speed limits.\",\"comment\":\"Implemented captive portal loops.\"},\"wednesday\":{\"task\":\"Writing standard Access Control Lists (ACLs) to block specific ports.\",\"comment\":\"Blocked internal access to SMTP port.\"},\"thursday\":{\"task\":\"Implementing MAC address filtering on wireless access points.\",\"comment\":\"Created dynamic allow rules lists.\"},\"friday\":{\"task\":\"Running a wireless survey to identify signal dead zones.\",\"comment\":\"Mapped signal strength using NetSpot software.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'ACL configurations require strict attention to order. Nice job.', 'Alice mapped wireless coverage anomalies accurately.'),
(87, 19, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Configuring WPA3 security on corporate wireless networks.\",\"comment\":\"Set up enterprise encryption parameters.\"},\"tuesday\":{\"task\":\"Setting up a guest Wi-Fi portal with speed limits.\",\"comment\":\"Implemented captive portal loops.\"},\"wednesday\":{\"task\":\"Writing standard Access Control Lists (ACLs) to block specific ports.\",\"comment\":\"Blocked internal access to SMTP port.\"},\"thursday\":{\"task\":\"Implementing MAC address filtering on wireless access points.\",\"comment\":\"Created dynamic allow rules lists.\"},\"friday\":{\"task\":\"Running a wireless survey to identify signal dead zones.\",\"comment\":\"Mapped signal strength using NetSpot software.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Solid security grouping.', 'James contributed very well in locking down our wireless networks.');
INSERT INTO `logbook` (`LogbookID`, `AttachmentID`, `WeekNumber`, `StartDate`, `EndDate`, `Activities`, `Status`, `EntryDate`, `SubmittedAt`, `AcademicSupervisorComments`, `HostSupervisorComments`) VALUES
(89, 12, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Setting up network monitoring using Zabbix/Nagios.\",\"comment\":\"Configured network agent listings.\"},\"tuesday\":{\"task\":\"Configuring SNMP on network devices to send alerts.\",\"comment\":\"Enabled SNMP traps on core switch.\"},\"wednesday\":{\"task\":\"Configuring a Site-to-Site VPN tunnel between offices.\",\"comment\":\"Set up IPsec Phase 1 rules.\"},\"thursday\":{\"task\":\"Troubleshooting VPN packet loss and connection drops.\",\"comment\":\"Adjusted MTU size to 1400.\"},\"friday\":{\"task\":\"Documenting the completed network topology.\",\"comment\":\"Drawn with Visio mapping tools.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(90, 19, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Setting up network monitoring using Zabbix/Nagios.\",\"comment\":\"Configured network agent listings.\"},\"tuesday\":{\"task\":\"Configuring SNMP on network devices to send alerts.\",\"comment\":\"Enabled SNMP traps on core switch.\"},\"wednesday\":{\"task\":\"Configuring a Site-to-Site VPN tunnel between offices.\",\"comment\":\"Set up IPsec Phase 1 rules.\"},\"thursday\":{\"task\":\"Troubleshooting VPN packet loss and connection drops.\",\"comment\":\"Adjusted MTU size to 1400.\"},\"friday\":{\"task\":\"Documenting the completed network topology.\",\"comment\":\"Drawn with Visio mapping tools.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(91, 7, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Logging into the ticketing system and learning queue management.\",\"comment\":\"Familiarized with dashboard tickets sorting.\"},\"tuesday\":{\"task\":\"Installing Windows 11 and Ubuntu on new desktop computers.\",\"comment\":\"Configured partition mappings.\"},\"wednesday\":{\"task\":\"Configuring hardware upgrades (RAM, SSDs) on client machines.\",\"comment\":\"Upgraded 4 support laptops.\"},\"thursday\":{\"task\":\"Installing drivers and resolving peripheral connection issues.\",\"comment\":\"Resolved sound driver errors.\"},\"friday\":{\"task\":\"Resolving simple user software installation tickets.\",\"comment\":\"Completed 5 general software setups.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Great entry. Practical hardware setups are crucial support skills.', 'Faith has proven highly capable with hardware adjustments.'),
(92, 14, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Logging into the ticketing system and learning queue management.\",\"comment\":\"Familiarized with dashboard tickets sorting.\"},\"tuesday\":{\"task\":\"Installing Windows 11 and Ubuntu on new desktop computers.\",\"comment\":\"Configured partition mappings.\"},\"wednesday\":{\"task\":\"Configuring hardware upgrades (RAM, SSDs) on client machines.\",\"comment\":\"Upgraded 4 support laptops.\"},\"thursday\":{\"task\":\"Installing drivers and resolving peripheral connection issues.\",\"comment\":\"Resolved sound driver errors.\"},\"friday\":{\"task\":\"Resolving simple user software installation tickets.\",\"comment\":\"Completed 5 general software setups.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good practical coverage. Keep documenting hardware issues.', 'Amina works very methodically to close queued tickets.'),
(93, 7, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Introduction to Active Directory Domain Services (AD DS).\",\"comment\":\"Learned about domain structures.\"},\"tuesday\":{\"task\":\"Creating user accounts, resetting passwords, and unlocking users.\",\"comment\":\"Handled 12 password resets tickets.\"},\"wednesday\":{\"task\":\"Grouping users into Organizational Units (OUs) for permissions.\",\"comment\":\"Created OUs for accounting team.\"},\"thursday\":{\"task\":\"Configuring Group Policy Objects (GPOs) for browser settings.\",\"comment\":\"Disabled extension installations.\"},\"friday\":{\"task\":\"Adding computer accounts to the local domain.\",\"comment\":\"Added 5 laptops to domain controllers.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Active Directory is very core to support roles. Good understanding of GPOs.', 'Faith configured the domain accounts correctly.'),
(94, 14, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Introduction to Active Directory Domain Services (AD DS).\",\"comment\":\"Learned about domain structures.\"},\"tuesday\":{\"task\":\"Creating user accounts, resetting passwords, and unlocking users.\",\"comment\":\"Handled 12 password resets tickets.\"},\"wednesday\":{\"task\":\"Grouping users into Organizational Units (OUs) for permissions.\",\"comment\":\"Created OUs for accounting team.\"},\"thursday\":{\"task\":\"Configuring Group Policy Objects (GPOs) for browser settings.\",\"comment\":\"Disabled extension installations.\"},\"friday\":{\"task\":\"Adding computer accounts to the local domain.\",\"comment\":\"Added 5 laptops to domain controllers.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good practical review. GPO limits are excellent tools.', 'Amina completed domain configurations without problems.'),
(95, 7, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Setting up automatic data backup tasks in Windows Server.\",\"comment\":\"Created schedule configurations.\"},\"tuesday\":{\"task\":\"Restoring files from shadow copies and daily backups.\",\"comment\":\"Verified restore capabilities.\"},\"wednesday\":{\"task\":\"Verifying integrity of offsite cloud backups.\",\"comment\":\"Validated hashes data match.\"},\"thursday\":{\"task\":\"Recovering user profiles from corrupted system drives.\",\"comment\":\"Transferred data via USB bridges.\"},\"friday\":{\"task\":\"Implementing access permissions on network shared folders.\",\"comment\":\"Set up folder permissions limits.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Backups and profile recovery are highly relevant topics. Nice details.', 'Good support response on shared drive folders setup.'),
(96, 14, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Setting up automatic data backup tasks in Windows Server.\",\"comment\":\"Created schedule configurations.\"},\"tuesday\":{\"task\":\"Restoring files from shadow copies and daily backups.\",\"comment\":\"Verified restore capabilities.\"},\"wednesday\":{\"task\":\"Verifying integrity of offsite cloud backups.\",\"comment\":\"Validated hashes data match.\"},\"thursday\":{\"task\":\"Recovering user profiles from corrupted system drives.\",\"comment\":\"Transferred data via USB bridges.\"},\"friday\":{\"task\":\"Implementing access permissions on network shared folders.\",\"comment\":\"Set up folder permissions limits.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Glad to see you checking restoration validation.', 'Excellent execution on folder backup scheduling.'),
(97, 7, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Configuring WSUS (Windows Server Update Services) to manage updates.\",\"comment\":\"Set up target client groups.\"},\"tuesday\":{\"task\":\"Approving patches for development and production environments.\",\"comment\":\"Approved KB security updates.\"},\"wednesday\":{\"task\":\"Troubleshooting failed security updates on client laptops.\",\"comment\":\"Cleaned SoftwareDistribution directory.\"},\"thursday\":{\"task\":\"Scanning client PCs for unauthorized software.\",\"comment\":\"Identified 3 unauthorized extensions.\"},\"friday\":{\"task\":\"Running antivirus scans and cleaning malware infections.\",\"comment\":\"Quarantined system trojans.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Good practical coverage of centralized updates and WSUS.', 'Faith helped clean up staging PC update conflicts.'),
(98, 14, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Configuring WSUS (Windows Server Update Services) to manage updates.\",\"comment\":\"Set up target client groups.\"},\"tuesday\":{\"task\":\"Approving patches for development and production environments.\",\"comment\":\"Approved KB security updates.\"},\"wednesday\":{\"task\":\"Troubleshooting failed security updates on client laptops.\",\"comment\":\"Cleaned SoftwareDistribution directory.\"},\"thursday\":{\"task\":\"Scanning client PCs for unauthorized software.\",\"comment\":\"Identified 3 unauthorized extensions.\"},\"friday\":{\"task\":\"Running antivirus scans and cleaning malware infections.\",\"comment\":\"Quarantined system trojans.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'WSUS configurations look clean.', 'Amina worked carefully on patching schedule checks.'),
(99, 7, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Learning Hyper-V and VMware ESXi virtualization.\",\"comment\":\"Studied hypervisor layers.\"},\"tuesday\":{\"task\":\"Creating and configuring Virtual Machines (VMs) on a host.\",\"comment\":\"Deployed test Ubuntu VM.\"},\"wednesday\":{\"task\":\"Allocating CPU, RAM, and storage dynamic resources to VMs.\",\"comment\":\"Assigned dynamic memory limits.\"},\"thursday\":{\"task\":\"Configuring network bridge adapters for virtual servers.\",\"comment\":\"Linked VM to WAN subnet.\"},\"friday\":{\"task\":\"Reviewing server temperature logs and hardware health.\",\"comment\":\"Logged server fan speed data.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Virtualization is very standard for corporate systems support. Excellent progress.', 'Faith created VM bridges correctly in our server sandbox.'),
(100, 14, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Learning Hyper-V and VMware ESXi virtualization.\",\"comment\":\"Studied hypervisor layers.\"},\"tuesday\":{\"task\":\"Creating and configuring Virtual Machines (VMs) on a host.\",\"comment\":\"Deployed test Ubuntu VM.\"},\"wednesday\":{\"task\":\"Allocating CPU, RAM, and storage dynamic resources to VMs.\",\"comment\":\"Assigned dynamic memory limits.\"},\"thursday\":{\"task\":\"Configuring network bridge adapters for virtual servers.\",\"comment\":\"Linked VM to WAN subnet.\"},\"friday\":{\"task\":\"Reviewing server temperature logs and hardware health.\",\"comment\":\"Logged server fan speed data.\"}}', 'Approved', '2026-06-14 08:52:58', '2026-06-14 05:52:58', 'Keep analyzing dynamic resource limits.', 'Amina has completed VM networking bridges without errors.'),
(101, 7, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Resolving complex print server and network printer issues.\",\"comment\":\"Cleaned spooler logs.\"},\"tuesday\":{\"task\":\"Troubleshooting VPN connection issues for remote staff.\",\"comment\":\"Configured firewall client ports.\"},\"wednesday\":{\"task\":\"Creating step-by-step user guides for standard software setups.\",\"comment\":\"Completed MS Teams setups document.\"},\"thursday\":{\"task\":\"Completing audit of office hardware assets.\",\"comment\":\"Audited 30 desks.\"},\"friday\":{\"task\":\"Final handoff of open support tickets.\",\"comment\":\"Closed all pending tickets.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(102, 14, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Resolving complex print server and network printer issues.\",\"comment\":\"Cleaned spooler logs.\"},\"tuesday\":{\"task\":\"Troubleshooting VPN connection issues for remote staff.\",\"comment\":\"Configured firewall client ports.\"},\"wednesday\":{\"task\":\"Creating step-by-step user guides for standard software setups.\",\"comment\":\"Completed MS Teams setups document.\"},\"thursday\":{\"task\":\"Completing audit of office hardware assets.\",\"comment\":\"Audited 30 desks.\"},\"friday\":{\"task\":\"Final handoff of open support tickets.\",\"comment\":\"Closed all pending tickets.\"}}', 'Pending', '2026-06-14 08:52:58', '2026-06-14 05:52:58', NULL, NULL),
(451, 1, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-09 00:00:00', '2026-05-08 21:00:00', NULL, NULL),
(452, 1, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-16 00:00:00', '2026-05-15 21:00:00', NULL, NULL),
(453, 1, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-23 00:00:00', '2026-05-22 21:00:00', NULL, NULL),
(454, 1, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-30 00:00:00', '2026-05-29 21:00:00', NULL, NULL),
(455, 1, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-06-06 00:00:00', '2026-06-05 21:00:00', NULL, NULL),
(456, 1, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-06-13 00:00:00', '2026-06-12 21:00:00', NULL, NULL),
(463, 2, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-09 00:00:00', '2026-05-08 21:00:00', NULL, NULL),
(464, 2, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-16 00:00:00', '2026-05-15 21:00:00', NULL, NULL),
(465, 2, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-23 00:00:00', '2026-05-22 21:00:00', NULL, NULL),
(466, 2, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-30 00:00:00', '2026-05-29 21:00:00', NULL, NULL),
(467, 2, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-06-06 00:00:00', '2026-06-05 21:00:00', NULL, NULL),
(468, 2, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-06-13 00:00:00', '2026-06-12 21:00:00', NULL, NULL),
(475, 3, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-09 00:00:00', '2026-05-08 21:00:00', 'Good Job', NULL),
(476, 3, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-16 00:00:00', '2026-05-15 21:00:00', NULL, NULL),
(477, 3, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-23 00:00:00', '2026-05-22 21:00:00', NULL, NULL),
(478, 3, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-30 00:00:00', '2026-05-29 21:00:00', NULL, NULL),
(479, 3, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-06-06 00:00:00', '2026-06-05 21:00:00', NULL, NULL),
(480, 3, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-06-13 00:00:00', '2026-06-12 21:00:00', NULL, NULL),
(487, 4, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-09 00:00:00', '2026-05-08 21:00:00', NULL, NULL),
(488, 4, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-16 00:00:00', '2026-05-15 21:00:00', NULL, NULL),
(489, 4, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-23 00:00:00', '2026-05-22 21:00:00', NULL, NULL),
(490, 4, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-05-30 00:00:00', '2026-05-29 21:00:00', NULL, NULL),
(491, 4, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-06-06 00:00:00', '2026-06-05 21:00:00', NULL, NULL),
(492, 4, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Pending', '2026-06-13 00:00:00', '2026-06-12 21:00:00', NULL, NULL),
(499, 5, 1, '2026-05-04', '2026-05-08', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-09 00:00:00', '2026-05-08 21:00:00', NULL, NULL),
(500, 5, 2, '2026-05-11', '2026-05-15', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-16 00:00:00', '2026-05-15 21:00:00', NULL, NULL),
(501, 5, 3, '2026-05-18', '2026-05-22', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-23 00:00:00', '2026-05-22 21:00:00', NULL, NULL),
(502, 5, 4, '2026-05-25', '2026-05-29', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-05-30 00:00:00', '2026-05-29 21:00:00', NULL, NULL),
(503, 5, 5, '2026-06-01', '2026-06-05', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-06-06 00:00:00', '2026-06-05 21:00:00', NULL, NULL),
(504, 5, 6, '2026-06-08', '2026-06-12', '{\"monday\":{\"task\":\"Developed software modules.\",\"comment\":\"Working well\"},\"tuesday\":{\"task\":\"Attended team meetings.\",\"comment\":\"Working well\"},\"wednesday\":{\"task\":\"Bug fixing and QA.\",\"comment\":\"Working well\"},\"thursday\":{\"task\":\"Code refactoring.\",\"comment\":\"Working well\"},\"friday\":{\"task\":\"Deployment and testing.\",\"comment\":\"Working well\"}}', 'Approved', '2026-06-13 00:00:00', '2026-06-12 21:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
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
  `EligibilityStatus` varchar(20) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentID`, `UserID`, `FirstName`, `LastName`, `Course`, `Faculty`, `YearOfStudy`, `PhoneNumber`, `Email`, `EligibilityStatus`, `Department`) VALUES
(1, 100, 'John', 'Kamau', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254701234567', 'michellewachira25@gmail.com', 'Cleared', 'Computer and Information Science'),
(2, 101, 'Mercy', 'Wanjiku', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254702345678', 'wanjiku.mercy@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(3, 102, 'David', 'Omondi', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254703456789', 'omondi.david@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(4, 103, 'Grace', 'Mutua', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254704567890', 'mutua.grace@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(5, 104, 'Patrick', 'Mwangi', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254705678901', 'mwangi.patrick@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(6, 105, 'Evans', 'Kiprop', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254706789012', 'kiprop.evans@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(7, 106, 'Faith', 'Chepngetich', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254707890123', 'chepngetich.faith@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(8, 107, 'Benson', 'Onyango', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254708901234', 'onyango.benson@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(9, 108, 'Joseph', 'Ochieng', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254709012345', 'ochieng.joseph@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(10, 109, 'Sarah', 'Nafula', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254710123456', 'nafula.sarah@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(11, 110, 'Peter', 'Njoroge', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254711234567', 'njoroge.peter@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(12, 111, 'Alice', 'Wambui', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254712345678', 'wambui.alice@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(13, 112, 'Charles', 'Otieno', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254713456789', 'otieno.charles@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(14, 113, 'Amina', 'Hassan', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254714567890', 'hassan.amina@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(15, 114, 'Brian', 'Mwenda', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254715678901', 'mwenda.brian@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(16, 115, 'Beatrice', 'Waweru', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254716789012', 'waweru.beatrice@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(17, 116, 'Emmanuel', 'Kipkurui', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254717890123', 'kipkurui.emmanuel@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(18, 117, 'Gloria', 'Cherotich', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254718901234', 'cherotich.gloria@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(19, 118, 'James', 'Maina', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254719012345', 'maina.james@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(20, 119, 'Cynthia', 'Mwende', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254720123456', 'mwende.cynthia@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(21, 120, 'Kevin', 'Wafula', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254721234567', 'wafula.kevin@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(22, 121, 'Jacqueline', 'Nyambura', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254722345678', 'nyambura.jacqueline@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(23, 122, 'Collins', 'Kipkemoi', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254723456789', 'kipkemoi.collins@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(24, 123, 'Linet', 'Awuor', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254724567890', 'awuor.linet@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(25, 124, 'Dennis', 'Mutuku', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254725678901', 'mutuku.dennis@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(26, 125, 'Sharon', 'Chebet', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254726789012', 'chebet.sharon@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(27, 126, 'Robert', 'Mureithi', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254727890123', 'mureithi.robert@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(28, 127, 'Mary', 'Atieno', 'Bachelor of Science in Information Technology', 'Faculty of Science', 3, '+254728901234', 'atieno.mary@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(29, 128, 'George', 'Ndwiga', 'Bachelor of Science in Computer Technology', 'Faculty of Science', 3, '+254729012345', 'ndwiga.george@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(30, 129, 'Valentine', 'Nduta', 'Bachelor of Science in Computer Science', 'Faculty of Science', 3, '+254730123456', 'nduta.valentine@std.cuea.edu', 'Eligible', 'Computer and Information Science'),
(31, 218, 'Wamuyu', 'Wachira', 'Bachelor of Science in Computer Science', 'Science', 3, '0701573708', '1049088@cuea.edu', 'Eligible', 'Computer Science'),
(32, 258, 'Anne', 'Wanjiku', 'Bachelor of Science in Computer Science', 'Science', 3, '0701573708', '1001234@cuea.edu', 'Eligible', 'Computer Science'),
(33, 259, 'Kevin', 'Omondi', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(34, 260, 'Faith', 'Jepchirchir', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(35, 261, 'Joseph', 'Mutua', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(36, 262, 'Brenda', 'Chepkemoi', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(37, 263, 'George', 'Mwangi', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(38, 264, 'Sheila', 'Achieng', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(39, 265, 'Isaac', 'Kiptoo', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(40, 266, 'Vivian', 'Moraa', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(41, 267, 'Dennis', 'Kipchoge', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(42, 268, 'Sharon', 'Njeri', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(43, 269, 'Peter', 'Karanja', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(44, 270, 'Miriam', 'Wasike', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(45, 271, 'David', 'Otieno', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(46, 272, 'Emily', 'Chemutai', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(47, 273, 'James', 'Kamau', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(48, 274, 'Maureen', 'Akinyi', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(49, 275, 'Sammy', 'Rono', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(50, 276, 'Hellen', 'Mwikali', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science'),
(51, 277, 'Stephen', 'Barasa', NULL, 'Science', NULL, NULL, NULL, 'Pending', 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `supervision`
--

DROP TABLE IF EXISTS `supervision`;
CREATE TABLE `supervision` (
  `SupervisionID` int(11) NOT NULL,
  `LecturerID` int(11) DEFAULT NULL,
  `AttachmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervision`
--

INSERT INTO `supervision` (`SupervisionID`, `LecturerID`, `AttachmentID`) VALUES
(1, 2, 1),
(2, 3, 2),
(3, 4, 3),
(4, 5, 4),
(5, 6, 5),
(6, 7, 6),
(7, 8, 7),
(8, 9, 8),
(9, 10, 9),
(10, 11, 10),
(11, 12, 11),
(12, 13, 12),
(13, 14, 13),
(14, 15, 14),
(15, 16, 15),
(16, 17, 16),
(17, 18, 17),
(18, 19, 18),
(19, 20, 19),
(20, 21, 20),
(21, 14, 1),
(22, 12, 2),
(23, 17, 3),
(24, 19, 4),
(25, 2, 5),
(26, 4, 6),
(27, 11, 7),
(28, 16, 8),
(29, 19, 9),
(30, 21, 10),
(31, 17, 11),
(32, 20, 12),
(33, 6, 13),
(34, 12, 14),
(35, 12, 15),
(36, 16, 16),
(37, 19, 17),
(38, 13, 18),
(39, 4, 19),
(40, 16, 20),
(41, 17, 21);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` varchar(30) NOT NULL,
  `Status` varchar(20) DEFAULT 'Active',
  `ResetToken` varchar(255) DEFAULT NULL,
  `ResetTokenExpiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `Role`, `Status`, `ResetToken`, `ResetTokenExpiry`) VALUES
(1, 'A001', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Admin', 'Active', NULL, NULL),
(10, 'STAFF-101', '$2y$10$Cld.7Ngc.YCNPNzOsYfosOspTd7iWTJvKE4UdXXlZD8HIA/k9KMMC', 'Lecturer', 'Active', NULL, NULL),
(11, 'STAFF-102', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(12, 'STAFF-103', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(13, 'STAFF-104', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(14, 'STAFF-105', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(15, 'STAFF-106', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(16, 'STAFF-107', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(17, 'STAFF-108', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(18, 'STAFF-109', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(19, 'STAFF-110', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(20, 'STAFF-111', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(21, 'STAFF-112', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(22, 'STAFF-113', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(23, 'STAFF-114', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(24, 'STAFF-115', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(25, 'STAFF-116', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(26, 'STAFF-117', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(27, 'STAFF-118', '$2y$10$Xa0vZ3hJf6Bff3m5T9XWS./NSJLJ3yeP/xGTU8Dwff/JiJlVorzgC', 'Lecturer', 'Active', NULL, NULL),
(28, 'STAFF-119', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(29, 'STAFF-120', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Lecturer', 'Active', NULL, NULL),
(100, '1000001', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Inactive', NULL, NULL),
(101, '1000002', '$2y$10$kn.48CMTE/Elvucj0LUkO.CSBNBCXBA6MBYNV2KOUsSAdGSmYo666', 'Student', 'Active', NULL, NULL),
(102, '1000003', '$2y$10$GtCdKpKXwQBmPzWIaHshLuIjjc1iYNFMEBbz3wx5By6VwlTRrr7Ae', 'Student', 'Active', NULL, NULL),
(103, '1000004', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(104, '1000005', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(105, '1000006', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(106, '1000007', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(107, '1000008', '$2y$10$4r55HgYIzm6Wnluosk0QueRNjJPGPukaXDOgzu4OENg4QUiLhskoq', 'Student', 'Active', NULL, NULL),
(108, '1000009', '$2y$10$5ttQZyoqXmqdRGPpf9XeF.G6E6nZ5a0P2633X3.YeuSdm6Tijwtvy', 'Student', 'Active', NULL, NULL),
(109, '1000010', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(110, '1000011', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(111, '1000012', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(112, '1000013', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(113, '1000014', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(114, '1000015', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(115, '1000016', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(116, '1000017', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(117, '1000018', '$2y$10$b2Bss8YFiQvNtRlWMKoWc.BeeggCTE.Vc6.pYG27wPEvEn1hU1Hd2', 'Student', 'Active', NULL, NULL),
(118, '1000019', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(119, '1000020', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(120, '1000021', '$2y$10$HaSyEbjMz.Nppspi4vxGKuOMZoAoDKOLR7Lir9aw00Ou/hG5D2My.', 'Student', 'Active', NULL, NULL),
(121, '1000022', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(122, '1000023', '$2y$10$cGnGwjj5EP2P0AE/5AxwuObr.zCaVVhJD/Tgk2G2KglhZAoZHjgb2', 'Student', 'Active', NULL, NULL),
(123, '1000024', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(124, '1000025', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(125, '1000026', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(126, '1000027', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(127, '1000028', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(128, '1000029', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(129, '1000030', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Student', 'Active', NULL, NULL),
(200, 'H001', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(201, 'H002', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(202, 'H003', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(203, 'H004', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(204, 'H005', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(205, 'H006', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(206, 'H007', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(207, 'H008', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(208, 'H009', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(209, 'H010', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(210, 'H011', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(211, 'H012', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(212, 'H013', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(213, 'H014', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(214, 'H015', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(215, 'H016', '$2y$10$h4MRNh8lQsg4OZF1EKmkqOtAidVnYVt4B3G4dVnA4RzpPXu1Ro/EC', 'Host Organization', 'Active', NULL, NULL),
(216, 'H017', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(217, 'H018', '$2y$10$rBWnjFO6Ysq3kwlXuTU8Xuwal32dSu3JECGRnTJkSbQ5Us79HGhn.', 'Host Organization', 'Active', NULL, NULL),
(218, '1049088', '$2y$10$0SazI1Xs4Tu/WVRpQL7it.jbkqTBS8x7Yn/McIjexE1dNLuIGSTxy', 'Student', 'Inactive', NULL, NULL),
(219, 'H019', '$2y$10$o3CfoJDGhOBLo3Ufdlc5BuJJNYWkexxl0Nw8ZLScOrUyYtuO7oW66', 'Host Organization', 'Active', NULL, NULL),
(248, 'STAFF-121', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(249, 'STAFF-122', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(250, 'STAFF-123', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(251, 'STAFF-124', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(252, 'STAFF-125', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(253, 'STAFF-126', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(254, 'STAFF-127', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(255, 'STAFF-128', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(256, 'STAFF-129', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(257, 'STAFF-130', '$2y$10$rTZlxaZPaDc.WGpgX07z1uBubwdZpw3MobPpRefqSoOgUu8BNpRD.', 'Lecturer', 'Active', NULL, NULL),
(258, '1001234', '$2y$10$eEsfKT7yM7U.kgWUkdKoVe5D9ksHQtC79VuAq.WdeimHJdDwLeC/i', 'Student', 'Active', NULL, NULL),
(259, '1005678', '$2y$10$CNoLkfbq6orks3/PvCATyOPdKqd/lJvZdYFYmEAHzobW2g049Zlp6', 'Student', 'Active', NULL, NULL),
(260, '1009012', '$2y$10$kjt1k.9WpTf1fdj8sWZ8We0qjyW/9PHo7rQWvK5n4S8MtPkOEL7CK', 'Student', 'Active', NULL, NULL),
(261, '1013456', '$2y$10$uZE7cuJXRI9f4M1jcHp1Q.wpRu8Y1uHWYztd4tzKhbdMEj2Tq.VPa', 'Student', 'Active', NULL, NULL),
(262, '1017890', '$2y$10$JyF3MTqojxhr/wDoVSYZreCRxfiMQ88UO5u1x10v2D8AZbKsYJXrS', 'Student', 'Active', NULL, NULL),
(263, '1022345', '$2y$10$C6UR8HfnNqPWuWdNlZfoZe.HU.8ZB7HxJW3R0zKkQswlU8dsojK1O', 'Student', 'Active', NULL, NULL),
(264, '1026789', '$2y$10$G/e0t1bw14.dic5HQuq.oOy9vNi430FK6zWm4kHwSua/ARRZ70PuK', 'Student', 'Active', NULL, NULL),
(265, '1031234', '$2y$10$3a.lq2S5akk0/Xjz/aYRCewc0bMepZUtbOCRlks870lfqyu4RaaWG', 'Student', 'Active', NULL, NULL),
(266, '1035678', '$2y$10$P.QG9DOHQQQOOiz5lxDFnORvO/nhDjiUYubyIEW6ZvZoXabRdSQSm', 'Student', 'Active', NULL, NULL),
(267, '1039012', '$2y$10$WFaMJDJcpIVGpMGxfKDU2.UW9nuEzXQufMfHhs7q7EtQ3awT4OApq', 'Student', 'Active', NULL, NULL),
(268, '1043456', '$2y$10$GD1K3oXBHlsJckgQ6WLNi.7GrMiM2g4Nn6vcCLHQmWeJY/kMTkXCm', 'Student', 'Active', NULL, NULL),
(269, '1047890', '$2y$10$uKzSI39iMrwVxrfEpiec2OoSW004gyyyb.4t3PWW4u2UQ/9wGCeX6', 'Student', 'Active', NULL, NULL),
(270, '1052345', '$2y$10$3hJQoDvtqK.Q00K1/nYutetkIL1SByTi9irQYUlL8W.jp6kqelJtS', 'Student', 'Active', NULL, NULL),
(271, '1056789', '$2y$10$AL8ZNvcBvW5wO7mghgE2x.qgTI7wsTL6zUuKQMr8SijKo4IU9NMxO', 'Student', 'Active', NULL, NULL),
(272, '1061234', '$2y$10$vkDLoUmpRAkpPBENp2slAuvUV6yb5IHmkbwzzM/KRuwSeE7AbsZJG', 'Student', 'Active', NULL, NULL),
(273, '1065678', '$2y$10$.dswuONihelP2OvUsNU.ce8HE7XSjQCwEnjcnhWR3.hrmXupKdRQ.', 'Student', 'Active', NULL, NULL),
(274, '1069012', '$2y$10$x2BY5vRVewSTP2km87FvU.4rwV0jlVXcRX6aU3RhNcQqkeQb.4Eqe', 'Student', 'Active', NULL, NULL),
(275, '1073456', '$2y$10$CXoVrXOF/26mtCUNxNMZI.Pt.ifdejPBcH0FZ/nulwyf4jNzE/RBe', 'Student', 'Active', NULL, NULL),
(276, '1077890', '$2y$10$Xo8cexw8OHROUdWxemDdDOEmd3U7UAseds8VxlzbvA3ANbga3RHCy', 'Student', 'Active', NULL, NULL),
(277, '1082345', '$2y$10$XJLsxa69mfV0BEiz4CGmXuTTSeWfP8YwYpuy3czKmL1hW8tRUtOpW', 'Student', 'Active', NULL, NULL),
(311, 'H020', '$2y$10$Jk/qfN/K42yXtHgFHJhM3uJourkYaFz8AJVaR4GhORQGZrDOoQ7CG', 'Host Organization', 'Active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`AssessmentID`),
  ADD UNIQUE KEY `unique_attachment_lecturer` (`AttachmentID`,`LecturerID`),
  ADD KEY `AttachmentID` (`AttachmentID`),
  ADD KEY `LecturerID` (`LecturerID`);

--
-- Indexes for table `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`AttachmentID`),
  ADD KEY `StudentID` (`StudentID`),
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
  ADD UNIQUE KEY `unique_week` (`AttachmentID`,`WeekNumber`);

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
  MODIFY `AssessmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `attachment`
--
ALTER TABLE `attachment`
  MODIFY `AttachmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `attachmentapplication`
--
ALTER TABLE `attachmentapplication`
  MODIFY `ApplicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `attachmentopportunity`
--
ALTER TABLE `attachmentopportunity`
  MODIFY `OpportunityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `finalreport`
--
ALTER TABLE `finalreport`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hostorganization`
--
ALTER TABLE `hostorganization`
  MODIFY `HostOrgID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `LecturerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `logbook`
--
ALTER TABLE `logbook`
  MODIFY `LogbookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=505;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `supervision`
--
ALTER TABLE `supervision`
  MODIFY `SupervisionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=312;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment`
--
ALTER TABLE `assessment`
  ADD CONSTRAINT `assessment_ibfk_1` FOREIGN KEY (`AttachmentID`) REFERENCES `attachment` (`AttachmentID`),
  ADD CONSTRAINT `assessment_ibfk_2` FOREIGN KEY (`LecturerID`) REFERENCES `lecturer` (`LecturerID`);

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
  ADD CONSTRAINT `logbook_ibfk_1` FOREIGN KEY (`AttachmentID`) REFERENCES `attachment` (`AttachmentID`) ON DELETE CASCADE;

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
