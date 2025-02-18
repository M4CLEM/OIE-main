-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 12, 2024 at 04:57 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plmunoiedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `companylist`
--

CREATE TABLE `companylist` (
  `No` bigint(255) NOT NULL,
  `companyName` varchar(255) NOT NULL,
  `companyaddress` varchar(100) NOT NULL,
  `contactPerson` varchar(255) NOT NULL,
  `jobrole` varchar(100) NOT NULL,
  `jobdescription` longtext NOT NULL,
  `jobreq` longtext NOT NULL,
  `link` varchar(100) NOT NULL,
  `dept` varchar(100) NOT NULL DEFAULT 'CBA,CAS,CTE,CCJ,CITCS,ALUMNI'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `companylist`
--

INSERT INTO `companylist` (`No`, `companyName`, `companyaddress`, `contactPerson`, `jobrole`, `jobdescription`, `jobreq`, `link`, `dept`) VALUES
(13, 'TaskUs', 'Imus, Cavite', 'Juan Dela Cruz', 'IT Operation Intern', 'You will dedicate your internship hours working side-by-side our amazing Information Technology team members aiding in technical support, identifying computer hardware, software and telecommunications malfunction. \r\n\r\nWork on incoming service requests, and other ad-hoc tasks.\r\n\r\nYou will be a brand champion for TaskUs - contributing to talent attraction through different online media and by providing a #RidiculouslyGood recruitment experience to internal stakeholders and to candidates.\r\nYou will report onsite', '> You must be of legal age to qualify for an internship\r\n> You must have OJT units requirement\r\n\r\nSubmit your CV, scanned copy of school ID and scanned copy of a valid government-issued ID and birth certificate.\r\n\r\nEndorsement letter from your school indicating required number of hours for OJT.\r\n\r\nYou must be taking Data Science, Information Technology, Computer Engineering, Computer Science, Computer Applications, Software Development, and the like.', 'https://ph.indeed.com/IT-Internship-Philippines-jobs?vjk=4156c20704fc3517', 'CITCS'),
(14, 'Datawords', 'Makati City', '', 'Junior QA Specialist', 'Intern will be trained to do website testing and SEO audits for our client\'s websites. Our internship program is designed for Information Technology students who wants to start their career in quality assurance and website development. We are open for a permanent contract absorption after the end of internship. ', 'College student currently taking up Information Technology, Computer Science or other related courses Familiar with any bug tracking tools Basic Knowledge in HTML, CSS Knowledge in basic SEO testing Knowledge in E-Commerce websites Basic knowledge in Content Management Systems Mobile and desktop Quality assurance experience Basic knowledge in Microsoft Suite (Excel, PowerPoint, Outlook) Able to work in a collaborative global team environment Strong time management skills – ability to prioritize and meet deadlines Must have internship requirement of at least 3 months equivalent. ', 'https://ph.indeed.com/IT-Internship-Philippines-jobs?vjk=3f012ea5e34c0e6dERROR', 'CITCS'),
(16, 'Dashlabs.ai', 'Makati City', '', 'Infrastructure and Security', 'There is no monetary stipend or allowance for this internship.\r\nWhile no stipend, full access to resources will be provided.\r\nThis opening is purely for applicants wishing to gain skills, knowledge, and experience, while contributing to improving healthcare in the Philippines and other emerging countries.\r\nMost, if not all, of the projects and tasks that will be assigned are for pro bono public service, in the spirit of Dashlabs.ai\'s roots.\r\nWe will prioritize those who are required to do internships or on-the-job training (OJT) by their schools.\r\n', 'Priority will be given to those who are enrolled in College or University and have required internships/OJTs.\r\nAll others are welcome to apply.\r\nPlease indicate:\r\nrequired or personal internship\r\npreferred team/s\r\nstart and end dates\r\nschedule\r\nrequired hours\r\nonline only, onsite only, hybrid online/onsite\r\nfor those with onsite, must be fully vaccinated against COVID-19\r\nother caveats we need to be aware of\r\nAdditional Information: Remote-first job.\r\n', 'https://ph.indeed.com/jobs?q=IT+Internship+Philippines&start=10&pp=gQAPAAAAAAAAAAAAAAAB80ycwQAXAQABh', 'CITCS'),
(17, 'Dashlabs.ai', 'Makati City', '', 'Graphic Design, Motion, Video', 'There is no monetary stipend or allowance for this internship.\r\nWhile no stipend, full access to resources will be provided.\r\nThis opening is purely for applicants wishing to gain skills, knowledge, and experience, while contributing to improving healthcare in the Philippines and other emerging countries.\r\nMost, if not all, of the projects and tasks that will be assigned are for pro bono public service, in the spirit of Dashlabs.ai\'s roots.\r\nWe will prioritize those who are required to do internships or on-the-job training (OJT) by their schools\r\n', 'Priority will be given to those who are enrolled in College or University and have required internships/OJTs.\r\nAll others are welcome to apply.\r\nPlease include portfolio\r\nPlease indicate:\r\nrequired or personal internship\r\npreferred team/s\r\nstart and end dates\r\nschedule\r\nrequired hours\r\nonline only, onsite only, hybrid online/onsite\r\nfor those with onsite, must be fully vaccinated against COVID-19\r\nother caveats we need to be aware of\r\nAdditional Information: Remote-first job.\r\n', 'https://ph.indeed.com/jobs?q=IT+Internship+Philippines&start=10&pp=gQAPAAAAAAAAAAAAAAAB80ycwQAXAQABh', 'CITCS'),
(18, 'KMC MAG Solutions, Inc', 'Quezon City', '', 'IT Operational Support System Intern', 'Assist staff performing system back up and maintenance functions \r\nAssist network and hardware troubleshooting  \r\nAssist in updating user and technical documentation \r\nRun calls to troubleshoot desktop and PC problems \r\nProvide help desk support to staff requiring technical assistance \r\nAssist with building and deploying desktop computers and laptops images for deployment.  \r\nOther responsibilities as directed by supervisor. \r\n', 'Candidate must possess at least Bachelor\'s in Computer Science/Information Technology or equivalent  \r\nWith 300 to 800++ hours (about 1 month+) internship requirements \r\nWilling to be assigned in multiple locations? \r\nWilling to work on shifting schedule, Holiday, weekends, and Night shift? \r\nNo work experience required. \r\n', 'https://www.jobstreet.com.ph/en/job/internship-it-quezon-city-12640824?jobId=jobstreet-ph-job-126408', 'CITCS'),
(19, 'LOLPHILS INC.\r\n', 'Quezon City', '', 'IT Intern\r\n', 'Working with our IT and Growth teams\r\nCoding and software development\r\nTesting and quality assuring systems\r\nWriting user instructions of the systems\r\nImplementing search engine optimisation opportunities\r\n', 'Candidate must be currently pursuing a Bachelor\'s/College Degree in Computer Science/Information Technology or equivalent.\r\nPreferred skill(s): PHP, Javascript/Jquery, CSS3, HTML5, REACT Native\r\nWilling to work from home\r\n', 'https://www.jobstreet.com.ph/en/job/it-students-ojt-12661644?jobId=jobstreet-ph-job-12661644&section', 'CITCS'),
(32, 'Dashlabs.ai\r\n', 'Makati City', '', ' Marketing Intern', 'There is no monetary stipend or allowance for this internship.\r\nWhile no stipend, full access to resources will be provided.\r\nThis opening is purely for applicants wishing to gain skills, knowledge, and experience, while contributing to improving healthcare in the Philippines and other emerging countries.\r\n', 'Priority will be given to those who are enrolled in College or University and have required internships/OJTs.\r\nAll others are welcome to apply.\r\n', 'https://ph.indeed.com/jobs?q=IT+Internship+Philippines&start=10&pp=gQAPAAAAAAAAAAAAAAAB80ycwQAXAQABh', 'CBA'),
(34, 'Dice205 Co.', ' Mandaluyong City', '', 'Accounting Intern\r\n', 'Encodes BIR files to BIR systems\r\nEncodes transactions (expenses and collections) to Quickbooks\r\nCan handle sensitive and confidential information with honesty and integrity\r\n', 'Currently taking up BS Accountancy or BA Major in Financial Management\r\nProficient in MS Excel\r\nKnowledge on General Journal, Trial Balance, and Balance Sheet\r\n', 'https://www.jobstreet.com.ph/en/job/accounting-intern-1034173580?jobId=jobstreet-ph-job-1034173580&s', 'CBA'),
(35, 'Cambridge University Press & Assessment | Manila', 'NCR', '', 'Communication Intern', 'Revise existing internal communications articles published in our local pursuing potential website/page to appeal to external audience.\r\nCreate articles for SEO and Pursuing Potential Content Project\r\nCreating content for Employer Branding\r\n', 'A student of Mass Communications, Journalism or Marketing looking to complete your internship requirements (3rd year and up)\r\nPossess a keen eye for details\r\nInterested and have had exposure to writing content and articles\r\n', 'https://www.jobstreet.com.ph/en/job/communications-intern-12654738?jobId=jobstreet-ph-job-12654738&s', 'CBA'),
(29, 'China Banking Corporation (CBC)', 'NCR', '', 'PR & Publications Associate - Internal Communications', 'The PR & Publications Associate - Internal Communications provides strategic and proactive communications support that aligns with the communications strategies being executed within the Marketing Communications Department.', 'Candidate must possess at least a Bachelor\'s/College Degree in Mass Communications, Public Relations, Journalism, Creative Writing, Literature, English, or other related course.\r\nAt least an undergraduate internship with an assisgnment in any of the following areas: corporate communications, publishing, media, or public relations.\r\nExcellent communications skills (verbal and especially written) and interpersonal skills;\r\nNatural sense of urgency and initiative;\r\nWith the ability to work on multiple projects simultaneously\r\n', 'https://www.jobstreet.com.ph/en/job/pr-publications-associate-internal-communications-12653401?jobId', 'CAS'),
(36, 'Dice205 Co.', ' Mandaluyong City', '', 'Accounting Intern\r\n', 'Encodes BIR files to BIR systems\r\nEncodes transactions (expenses and collections) to Quickbooks\r\nCan handle sensitive and confidential information with honesty and integrity\r\n', 'Currently taking up BS Accountancy or BA Major in Financial Management\r\nProficient in MS Excel\r\nKnowledge on General Journal, Trial Balance, and Balance Sheet\r\n', 'https://www.jobstreet.com.ph/en/job/accounting-intern-1034173580?jobId=jobstreet-ph-job-1034173580&s', 'CBA'),
(28, 'Dashlabs.ai\r\n', 'Makati City', '', '  Human Resources', 'There is no monetary stipend or allowance for this internship.\r\nWhile no stipend, full access to resources will be provided.\r\nThis opening is purely for applicants wishing to gain skills, knowledge, and experience, while contributing to improving healthcare in the Philippines and other emerging countries.\r\nMost, if not all, of the projects and tasks that will be assigned are for pro bono public service, in the spirit of Dashlabs.ai\'s roots.\r\nWe will prioritize those who are required to do internships or on-the-job training (OJT) by their schools.\r\n', 'Priority will be given to those who are enrolled in College or University and have required internships/OJTs. required or personal internship\r\npreferred team/s\r\nstart and end dates\r\nschedule\r\nrequired hours\r\nonline only, onsite only, hybrid online/onsite\r\n\r\n', 'https://ph.indeed.com/jobs?q=IT+Internship+Philippines&start=10&pp=gQAPAAAAAAAAAAAAAAAB80ycwQAXAQABh', 'CBA'),
(37, 'Cambridge University Press & Assessment | Manila', 'NCR', '', 'Communication Intern', 'Revise existing internal communications articles published in our local pursuing potential website/page to appeal to external audience.\r\nCreate articles for SEO and Pursuing Potential Content Project\r\nCreating content for Employer Branding\r\n', 'A student of Mass Communications, Journalism or Marketing looking to complete your internship requirements (3rd year and up)\r\nPossess a keen eye for details\r\nInterested and have had exposure to writing content and articles\r\n', 'https://www.jobstreet.com.ph/en/job/communications-intern-12654738?jobId=jobstreet-ph-job-12654738&s', 'CBA'),
(26, 'Outsource Accelerator', 'Pasig City', '', 'Content Writer Intern', 'Writing a High-Quality Content. Creating content for Employer Branding\r\nProofread everything from email funnel campaigns, blog posts, social media content, and a variety of other projects.\r\nAbility to do basic graphic design\r\nAbility to work remotely in a fast-paced environment.', 'A student of Mass Communications, Journalism or Marketing looking to complete your internship requirements\r\nMust be a college student with at least 300 hours of the internship requirement.\r\nInterest and ability to do basic graphic designs\r\nHas a Memorandum of Agreement (MOA) from school\r\nCan manage regular working hours, Mon to Fri, 10am to 7pm\r\nNo work experience is required.', 'https://ph.indeed.com/IT-Internship-Philippines-jobs?vjk=28b47487ceb71b48&advn=7953858315539595', 'CAS'),
(38, 'Asian Development Bank (ADB)', 'Manila', '', 'Prevention and Compliance Division', 'Conduct research and identify features and capabilities of best-in-class case management systems used by comparable institutions.\r\nUnderstand the current capabilities of Quantum (OAI’s existing case management system) and identify potential enhancements for Quantum with the intent of enhancing its capabilities as a case repository to a comprehensive case and risk management tool.\r\nWork with internal (OAI) and external (ITD, developer) stakeholders on the development feasibility of the proposed enhancements.\r\n', 'Currently enrolled in a Masters or PhD program in Management, Risk Management, Development Management, Law, or other related disciplines.\r\nStrong analytical skills;\r\nExcellent oral and written communication skills in English, including the ability to clearly and concisely prepare, present, discuss issues, findings, and recommendations;\r\nWorks effectively with internal and external stakeholders.\r\n', ' https://www.jobstreet.com.ph/en/job-search/criminology-intern-jobs/', 'CCJ');

-- --------------------------------------------------------

--
-- Table structure for table `company_info`
--

CREATE TABLE `company_info` (
  `companyCode` int(11) NOT NULL,
  `companyName` varchar(255) DEFAULT NULL,
  `companyAddress` varchar(255) DEFAULT NULL,
  `trainerContact` varchar(255) DEFAULT NULL,
  `trainerEmail` varchar(255) DEFAULT NULL,
  `workType` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `student_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_info`
--

INSERT INTO `company_info` (`companyCode`, `companyName`, `companyAddress`, `trainerContact`, `trainerEmail`, `workType`, `status`, `student_email`) VALUES
(4, 'Pamantasan ng Lungsod ng Muntinlupa', 'University Rd. Poblacion Muntinlupa City', '09090909090', 'michaelbrown@email.com', 'PB', 'Pending', 'garcenico_bsit@plmun.edu.ph');

-- --------------------------------------------------------

--
-- Table structure for table `coordinator`
--

CREATE TABLE `coordinator` (
  `id` int(11) NOT NULL,
  `coord_num` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `Course` int(11) DEFAULT NULL,
  `CourseID` int(11) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_list`
--

CREATE TABLE `course_list` (
  `id` int(11) NOT NULL,
  `course` varchar(255) DEFAULT NULL,
  `course_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_list`
--

INSERT INTO `course_list` (`id`, `course`, `course_title`, `department`) VALUES
(11, 'BSCS', 'Bachelor of Science in Computer Science', 'CITCS'),
(17, 'BSIT', 'Bachelor of Science in Information Technology', 'CITCS'),
(18, 'ACT', 'Associate in Computer Technology', 'CITCS'),
(19, 'BSCRIM', 'Bachelor of Science in Criminology', 'CCJ'),
(20, 'BACOM', 'Bachelor of Arts in Communication', 'CAS'),
(21, 'BSPSYCH', 'Bachelor of Science in Psychology', 'CAS'),
(22, 'BSBA', 'Bachelor of Science in Business Administration', 'CBA'),
(24, 'BSN', 'Nursing', 'COM'),
(25, 'IM', 'Internal Medicine', 'COM');

-- --------------------------------------------------------

--
-- Table structure for table `department_list`
--

CREATE TABLE `department_list` (
  `department` varchar(255) DEFAULT NULL,
  `department_title` varchar(255) DEFAULT NULL,
  `id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_list`
--

INSERT INTO `department_list` (`department`, `department_title`, `id`) VALUES
('CITCS', 'College of Information Technology & Computer Studies ', 1),
('CCJ ', 'College of Criminal Justice', 6),
('CAS', 'College of Arts & Sciences', 7),
('CBA', 'College of Business Administration', 8),
('COM', 'College of Medicine', 13);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `student_ID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `document` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `student_ID`, `email`, `document`, `file_name`, `status`, `date`) VALUES
(21, 20133368, 'garcenico_bsit@plmun.edu.ph', 'COM', 'coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_20133368/COM/20133368_1690239720.pdf', 'Approved', '2024-03-06'),
(22, 20133368, 'garcenico_bsit@plmun.edu.ph', 'Endorsement Letter', 'coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_20133368/Endorsement Letter/CITCS-OJT07-EndrosementLetter_BSIT.docx.pdf', 'Approved', '2024-02-16'),
(44, 20133368, 'garcenico_bsit@plmun.edu.ph', 'Waiver_Intent', 'coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_20133368/Waiver_Intent/Nico Waiver Legal (1).docx', 'Pending', '2024-02-16'),
(50, 20133368, 'garcenico_bsit@plmun.edu.ph', 'Resume', 'coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_20133368/Resume/Resume (1).pdf', 'Pending', '2024-03-11'),
(51, 20133619, 'parrenoheidelberg_bscs@plmun.edu.ph', 'Resume', 'coordinator-portal/documents/CITCS/BSCS/2023-2024/2nd semester/4B/BSCS_20133619/Resume/RESUME.pdf', 'Pending', '2024-03-06'),
(52, 20133619, 'parrenoheidelberg_bscs@plmun.edu.ph', 'Waiver_Intent', 'coordinator-portal/documents/CITCS/BSCS/2023-2024/2nd semester/4B/BSCS_20133619/Waiver_Intent/RESUME.pdf', 'Pending', '2024-03-07'),
(53, 20133619, 'parrenoheidelberg_bscs@plmun.edu.ph', 'Resume', 'coordinator-portal/documents/CITCS/BSCS/2023-2024/2nd semester/4B/BSCS_20133619/Resume/20133619_1705245163.pdf', 'Pending', '2024-03-08'),
(54, 22143397, 'adayaceejay_bsit@plmun.edu.ph', 'Resume', 'coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_22143397/Resume/Resume_Garce_4A.pdf', 'Pending', '2024-03-11');

-- --------------------------------------------------------

--
-- Table structure for table `doneinternship`
--

CREATE TABLE `doneinternship` (
  `id` int(100) NOT NULL,
  `Fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listadviser`
--

CREATE TABLE `listadviser` (
  `id` int(100) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `course` varchar(255) NOT NULL,
  `dept` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `listadviser`
--

INSERT INTO `listadviser` (`id`, `fullName`, `email`, `section`, `course`, `dept`) VALUES
(17, 'Nico Garce', 'email@email.com', 'IT2A', 'BSIT', 'CITCS'),
(19, 'Nico Garce', 'email@email.com', 'IT2S', 'BSIT', 'CITCS'),
(20, 'Nico Garce', 'garcenico_bsit@plmun.edu.ph', 'IT2F', 'BSIT', 'CITCS');

-- --------------------------------------------------------

--
-- Table structure for table `logdata`
--

CREATE TABLE `logdata` (
  `id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `log_company` varchar(255) DEFAULT NULL,
  `log_course` varchar(255) DEFAULT NULL,
  `log_dept` varchar(255) DEFAULT NULL,
  `log_section` varchar(255) DEFAULT NULL,
  `student_num` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `is_approved` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ongoinginternship`
--

CREATE TABLE `ongoinginternship` (
  `id` int(100) NOT NULL,
  `Fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=Aria DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_report`
--

CREATE TABLE `performance_report` (
  `No` int(100) NOT NULL,
  `Fullname` varchar(100) NOT NULL,
  `JOBPER` int(100) NOT NULL,
  `ATW` int(100) NOT NULL,
  `CTL` int(100) NOT NULL,
  `QOW` int(100) NOT NULL,
  `IAIT` int(100) NOT NULL,
  `ATS` int(100) NOT NULL,
  `RWD` int(100) NOT NULL,
  `AAP` int(100) NOT NULL,
  `APPER` int(100) NOT NULL,
  `Score` int(100) NOT NULL,
  `Remarks` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `performance_report`
--

INSERT INTO `performance_report` (`No`, `Fullname`, `JOBPER`, `ATW`, `CTL`, `QOW`, `IAIT`, `ATS`, `RWD`, `AAP`, `APPER`, `Score`, `Remarks`) VALUES
(1, 'jessi', 3, 2, 3, 4, 2, 4, 3, 3, 4, 0, 'passed'),
(2, '[value-2]', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '[value-13]');

-- --------------------------------------------------------

--
-- Table structure for table `schoolyear`
--

CREATE TABLE `schoolyear` (
  `schoolyear` varchar(255) DEFAULT NULL,
  `schoolyearID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schoolyear`
--

INSERT INTO `schoolyear` (`schoolyear`, `schoolyearID`) VALUES
('', 2023),
('', 2023),
('', 0),
('', 0),
('', 0),
('', 2023),
('', 2023),
('', 2023),
('', 2023);

-- --------------------------------------------------------

--
-- Table structure for table `sections_list`
--

CREATE TABLE `sections_list` (
  `id` int(11) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections_list`
--

INSERT INTO `sections_list` (`id`, `department`, `course`, `section`) VALUES
(45, 'CITCS', 'BSIT', '4A'),
(46, 'CITCS', 'BSIT', '4B'),
(47, 'CITCS', 'BSIT', '4C'),
(48, 'CITCS', 'BSIT', '4D'),
(49, 'CITCS', 'BSIT', '4E'),
(50, 'CITCS', 'BSIT', '4F'),
(51, 'CITCS', 'BSIT', '4G'),
(53, 'CITCS', 'ACT', '4A'),
(54, 'CITCS', 'ACT', '4B'),
(55, 'CITCS', 'ACT', '4C'),
(56, 'CITCS', 'ACT', '4D'),
(57, 'CITCS', 'ACT', '4E'),
(58, 'CCJ', 'BSCRIM', '4A'),
(59, 'CCJ', 'BSCRIM', '4B'),
(60, 'CCJ', 'BSCRIM', '4C'),
(61, 'CCJ', 'BSCRIM', '4D'),
(62, 'CCJ', 'BSCRIM', '4E'),
(63, 'CAS', 'BACOM', '4A'),
(64, 'CAS', 'BACOM', '4B'),
(65, 'CAS', 'BACOM', '4C'),
(66, 'CAS', 'BACOM', '4D'),
(67, 'CAS', 'BACOM', '4E'),
(69, 'CAS', 'BSPSYCH', '4A'),
(70, 'CAS', 'BSPSYCH', '4B'),
(71, 'CAS', 'BSPSYCH', '4C'),
(72, 'CAS', 'BSPSYCH', '4D'),
(73, 'CAS', 'BSPSYCH', '4E'),
(74, 'CBA', 'BSBA', '4A'),
(75, 'CBA', 'BSBA', '4B'),
(76, 'CBA', 'BSBA', '4C'),
(77, 'CBA', 'BSBA', '4D'),
(78, 'CBA', 'BSBA', '4E'),
(82, 'CITCS', 'BSCS', '4A'),
(83, 'CITCS', 'BSCS', '4B'),
(84, 'CITCS', 'BSCS', '4C'),
(85, 'CITCS', 'BSCS', '4D'),
(86, 'COM', 'BSN', '4A'),
(87, 'COM', 'BSN', '4B'),
(88, 'COM', 'BSN', '4C'),
(89, 'COM', 'IM', '4A'),
(90, 'COM', 'IM', '4B'),
(91, 'COM', 'IM', '4C');

-- --------------------------------------------------------

--
-- Table structure for table `studentinfo`
--

CREATE TABLE `studentinfo` (
  `studentID` int(8) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `age` int(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `contactNo` bigint(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `course` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `company` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `school_year` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `objective` longtext NOT NULL,
  `skills` longtext NOT NULL,
  `seminars` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `studentinfo`
--

INSERT INTO `studentinfo` (`studentID`, `firstname`, `middlename`, `lastname`, `address`, `age`, `gender`, `contactNo`, `section`, `course`, `department`, `email`, `status`, `company`, `image`, `school_year`, `semester`, `objective`, `skills`, `seminars`) VALUES
(20133368, 'Nico Roell', 'De Castro', 'Garce', 'Alabang, Muntinlupa City', 22, 'male', 9091558980, '4A', 'BSIT', 'CITCS', 'garcenico_bsit@plmun.edu.ph', 'Deployed', 'Pamantasan ng Lungsod ng Muntinlupa', '../coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_20133368/1x1 formal.jpg', '2023-2024', '2nd semester', 'Looking for an internship that allows me to put my technical\r\nskills, creativity, and love for technology and web development into action. I\'m eager to work on exciting projects and gain practical experience in a vibrant, team-oriented setting.\r\n', 'PHP, JavaScript, HTML, CSS, MySQL', 'CITCS 12th IT Summit, CITCS 13th IT Summit'),
(20133619, 'Heidelberg', 'Medina', 'Parreno', '123', 22, 'male', 9455522806, '4B', 'BSCS', 'CITCS', 'parrenoheidelberg_bscs@plmun.edu.ph', 'Undeployed', '', '../coordinator-portal/documents/CITCS/BSCS/2023-2024/2nd semester/4B/BSCS_20133619/bastien-grivet-naix-and-the-dragon-sd.jpg', '2023-2024', '2nd semester', 'test', 'test', 'test'),
(22143397, 'Ceejay', 'Middle', 'Adaya', 'San Pedro Laguna', 22, 'male', 90909090909, 'IT2A', 'BSIT', 'CITCS', 'adayaceejay_bsit@plmun.edu.ph', 'Undeployed', '', '../coordinator-portal/documents/CITCS/BSIT/2023-2024/2nd semester/IT2A/BSIT_22143397/421231050_2614669938714839_8124363659616609660_n.png', '2023-2024', '2nd semester', 'Objective', 'None', 'None');

-- --------------------------------------------------------

--
-- Table structure for table `student_grade`
--

CREATE TABLE `student_grade` (
  `studentID` int(11) NOT NULL,
  `Fullname` varchar(100) NOT NULL,
  `dept` varchar(100) NOT NULL DEFAULT 'CBA,CAS,CCJ,CTE,CITCS,ALUMNI',
  `SenseofUrgency` int(100) DEFAULT NULL,
  `QualityofWork` int(100) NOT NULL,
  `ExecutionConcept` int(100) NOT NULL,
  `PromptnessandPunctuality` int(100) NOT NULL,
  `WorkEthics` int(100) NOT NULL,
  `Demeanor` int(100) NOT NULL,
  `FinalGrade` int(100) NOT NULL,
  `Remarks` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_grade`
--

INSERT INTO `student_grade` (`studentID`, `Fullname`, `dept`, `SenseofUrgency`, `QualityofWork`, `ExecutionConcept`, `PromptnessandPunctuality`, `WorkEthics`, `Demeanor`, `FinalGrade`, `Remarks`) VALUES
(19134451, 'Angela Delacruz', 'CAS', 0, 0, 0, 0, 0, 0, 0, ''),
(19347802, 'Cindy Perez', 'CITCS', 0, 0, 0, 0, 0, 0, 0, ''),
(19293887, 'Jessica Soroya\r\n', 'CITCS', 3, 0, 0, 0, 0, 0, 0, ''),
(19525681, 'Noli boy Corazon\r\n', 'CITCS', 0, 0, 0, 0, 0, 0, 0, ''),
(19354827, 'Allen dave Quiroz', 'CAS', 0, 0, 0, 0, 0, 0, 0, ''),
(19758465, 'Aldrin Dela Pena\r\n', 'CAS', 0, 0, 0, 0, 0, 0, 0, ''),
(19127385, 'Dhea Camacho\r\n', 'CTE', 3, 4, 2, 5, 3, 2, 0, ''),
(19128412, 'Brandon Gomez', 'CTE', 0, 0, 0, 0, 0, 0, 0, ''),
(19428297, 'Jedaya Borbo', 'CTE', 0, 0, 0, 0, 0, 0, 0, ''),
(19327895, ' Dexter Espina', 'CBA', 0, 0, 0, 0, 0, 0, 0, ''),
(19125038, 'Mark Icogo\r\n', 'CBA', 1, 1, 1, 1, 1, 1, 0, ''),
(19338997, 'Reymart San Sebastian\r\n', 'CBA', 0, 0, 0, 0, 0, 0, 0, ''),
(19904677, 'James Michael Parasigan\r\n', 'CCJ', 0, 0, 0, 0, 0, 0, 0, ''),
(19439087, 'Angel Deposoy \r\n', 'CCJ', 0, 0, 0, 0, 0, 0, 0, ''),
(19439877, 'Alan Delo Santos', 'CCJ', 0, 0, 0, 0, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `student_masterlist`
--

CREATE TABLE `student_masterlist` (
  `studentID` int(11) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `course` varchar(100) NOT NULL,
  `section` varchar(255) NOT NULL,
  `year` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_masterlist`
--

INSERT INTO `student_masterlist` (`studentID`, `lastName`, `firstName`, `course`, `section`, `year`) VALUES
(19127031, 'Borja', 'Carlo', 'BSIT', 'IT2S', '3RD YEAR'),
(20133257, 'Abrio', 'Chenelle', 'BSIT', 'IT2A', '4TH YEAR'),
(20133368, 'Garce', 'Nico Roell', 'BSIT', 'IT2A', '4TH YEAR'),
(21138438, 'Amores', 'Ronalyn', 'BSIT', 'IT2S', '3RD YEAR'),
(21238858, 'Ragma', 'Kim', 'BSIT', 'IT2S', '2ND YEAR'),
(21239004, 'Crisostomo', 'Aaron LUIS', 'BSIT', 'IT2S', '2ND YEAR'),
(21239323, 'Valdez', 'Michael', 'BSIT', 'IT2A', '3RD YEAR'),
(22142058, 'Delos Santos', 'Karl', 'BSIT', 'IT2A', '2ND YEAR'),
(22142154, 'Carisma', 'Joseph', 'BSIT', 'IT2A', '2ND YEAR'),
(22142156, 'De Vero ', 'Garry', 'BSIT', 'IT2A', '2ND YEAR'),
(22142160, 'Brillantes', 'Lee Justine', 'BSIT', 'IT2A', '2ND YEAR'),
(22142196, 'Sabida', 'Joy Ann', 'BSIT', 'IT2A', '2ND YEAR'),
(22142348, 'Bas', 'Arbhy Joy', 'BSIT', 'IT2A', '2ND YEAR'),
(22142369, 'Monares ', 'Yesha Lyn ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142401, 'Solana', 'Rosemarie', 'BSIT', 'IT2A', '2ND YEAR'),
(22142467, 'Sabuag', 'Jerwin', 'BSIT', 'IT2A', '2ND YEAR'),
(22142537, 'Marfil', 'Christian Paolo', 'BSIT', 'IT2A', '2ND YEAR'),
(22142593, 'Gaerlan', 'Clarenze Kyle', 'BSIT', 'IT2A', '2ND YEAR'),
(22142688, 'Endaya', 'Ernesto', 'BSIT', 'IT2A', '2ND YEAR'),
(22142697, 'Rivera', 'John Luie ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142758, 'Ecija', 'Rasheed Ryan', 'BSIT', 'IT2A', '2ND YEAR'),
(22142759, 'Rotor', 'Leoncio', 'BSIT', 'IT2A', '2ND YEAR'),
(22142761, 'Dela Cruz ', 'Jayron', 'BSIT', 'IT2A', '2ND YEAR'),
(22142786, 'Ventura', 'Edmar', 'BSIT', 'IT2A', '2ND YEAR'),
(22142791, 'Aloria', 'Alainna Kellyn', 'BSIT', 'IT2A', '2ND YEAR'),
(22142816, 'Donor', 'Ashley Huey ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142841, 'Cosca', 'Jan Lawrence', 'BSIT', 'IT2A', '2ND YEAR'),
(22142895, 'Reyes', 'John Lloyd', 'BSIT', 'IT2A', '2ND YEAR'),
(22142907, 'Marasigan', 'Von Adrian', 'BSIT', 'IT2A', '2ND YEAR'),
(22142908, 'Pumanes', 'Jhay Rhov ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142998, 'Carale', 'Cenon', 'BSIT', 'IT2A', '2ND YEAR'),
(22143086, 'Dela Cruz', 'Mark Anthony', 'BSIT', 'IT2A', '2ND YEAR'),
(22143087, 'De Mesa', 'Gabriel', 'BSIT', 'IT2A', '2ND YEAR'),
(22143148, 'Perpetua', 'Alexa', 'BSIT', 'IT2A', '2ND YEAR'),
(22143211, 'Grajo', 'Aro Jamil Prince', 'BSIT', 'IT2A', '2ND YEAR'),
(22143224, 'Casimero', 'John Dave', 'BSIT', 'IT2A', '2ND YEAR'),
(22143233, 'Del Rosario ', 'Jedhie', 'BSIT', 'IT2A', '2ND YEAR'),
(22143288, 'Raveche', 'Mariella', 'BSIT', 'IT2A', '2ND YEAR'),
(22143397, 'Adaya', 'Ceejay', 'BSIT', 'IT2A', '2ND YEAR'),
(22143811, 'Gonzaga', 'Lanz Mikhaile', 'BSIT', 'IT2A', '2ND YEAR'),
(22143854, 'Torres ', 'Felicity Raine ', 'BSIT', 'IT2A', '2ND YEAR'),
(22143855, 'Lumaad', 'Channa Mae', 'BSIT', 'IT2A', '2ND YEAR'),
(22143901, 'Flores', 'Julia Louisse', 'BSIT', 'IT2A', '2ND YEAR'),
(22143931, 'Castrence', 'Neil Zacarrii', 'BSIT', 'IT2A', '2ND YEAR'),
(22143947, 'Boclot', 'Sarji', 'BSIT', 'IT2A', '2ND YEAR'),
(22143980, 'Espinosa', 'John Romel', 'BSIT', 'IT2A', '2ND YEAR'),
(22144681, 'Magsayo', 'Shenarie Nicole', 'BSIT', 'IT2S', '2ND YEAR'),
(22144889, 'Pili', 'Shervin Rafael', 'BSIT', 'IT2S', '2ND YEAR'),
(22145211, 'Vargas', 'Josiah Anton', 'BSIT', 'IT2S', '2ND YEAR'),
(22145455, 'Fernando', 'Enrico', 'BSIT', 'IT2S', '2ND YEAR'),
(22145471, 'Celendron', 'Aliyah', 'BSIT', 'IT2S', '2ND YEAR'),
(22145957, 'Alvarez', 'Kenlister', 'BSIT', 'IT2S', '2ND YEAR'),
(22145981, 'Bregente', 'Charles', 'BSIT', 'IT2S', '2ND YEAR'),
(22146028, 'Marco', 'Shoghi Indy Cyril', 'BSIT', 'IT2S', '2ND YEAR'),
(22146045, 'Sale', 'Kyle', 'BSIT', 'IT2S', '2ND YEAR'),
(22146047, 'Sevillo', 'Mjay', 'BSIT', 'IT2S', '2ND YEAR'),
(22146131, 'Miras', 'Philip Angelo', 'BSIT', 'IT2S', '2ND YEAR'),
(22146186, 'San Jose', 'Mc Ephraem', 'BSIT', 'IT2A', '2ND YEAR'),
(22146203, 'Remando', 'Ma.gee Nayette', 'BSIT', 'IT2A', '2ND YEAR'),
(22146205, 'Joaquin', 'Redentor', 'BSIT', 'IT2A', '2ND YEAR'),
(22146208, 'Potian', 'Aja Jeulliyah', 'BSIT', 'IT2A', '2ND YEAR'),
(22146254, 'Obias', 'Mark Jhon', 'BSIT', 'IT2S', '2ND YEAR'),
(22146255, 'Arciaga', 'Dianna', 'BSIT', 'IT2S', '2ND YEAR'),
(22146308, 'Lato', 'Mark Arnel', 'BSIT', 'IT2S', '2ND YEAR'),
(22146313, 'Fernandez', 'Wareen Christian', 'BSIT', 'IT2A', '2ND YEAR'),
(22146316, 'Fernandez', 'Shella Grace Ann', 'BSIT', 'IT2A', '2ND YEAR'),
(22146320, 'Angeles', 'Ian Lloyd', 'BSIT', 'IT2S', '2ND YEAR'),
(22146323, 'Sebastian', 'Tyron', 'BSIT', 'IT2S', '2ND YEAR'),
(22146331, 'Monroy', 'Iver John', 'BSIT', 'IT2S', '2ND YEAR'),
(22146347, 'Estor', 'Remmie Rose', 'BSIT', 'IT2S', '2ND YEAR'),
(22146356, 'Carandang', 'Christa Mae', 'BSIT', 'IT2A', '2ND YEAR'),
(22146361, 'Gablines', 'Axle Brit', 'BSIT', 'IT2S', '2ND YEAR'),
(22146364, 'Musa', 'Adrian', 'BSIT', 'IT2S', '2ND YEAR'),
(22146371, 'Hermocilla', 'Piona', 'BSIT', 'IT2S', '2ND YEAR'),
(22146375, 'Larino', 'Marco Antonio', 'BSIT', 'IT2S', '2ND YEAR'),
(22146379, 'Bulfa', 'John Rey', 'BSIT', 'IT2S', '2ND YEAR'),
(22146382, 'Espino', 'Nichaela', 'BSIT', 'IT2S', '2ND YEAR'),
(22146400, 'Santillan', 'Reiner', 'BSIT', 'IT2S', '2ND YEAR'),
(22146409, 'Balondo', 'Christina Mae', 'BSIT', 'IT2S', '2ND YEAR'),
(22146411, 'Balondo', 'Jaymart', 'BSIT', 'IT2S', '2ND YEAR'),
(22146434, 'Garcia', 'Darryl', 'BSIT', 'IT2A', '2ND YEAR'),
(22146447, 'Noche', 'John Paul', 'BSIT', 'IT2S', '2ND YEAR'),
(22146456, 'Talines', 'Johnder', 'BSIT', 'IT2S', '2ND YEAR'),
(22146457, 'Dinapo', 'George', 'BSIT', 'IT2S', '2ND YEAR'),
(22146478, 'Cortez', 'Kylenn Rhyss', 'BSIT', 'IT2S', '2ND YEAR'),
(22146484, 'Suarez', 'Ridge Adrian', 'BSIT', 'IT2S', '2ND YEAR'),
(22146486, 'Reyes', 'Aldwin', 'BSIT', 'IT2S', '2ND YEAR'),
(22146488, 'Arandoque', 'Lean James', 'BSIT', 'IT2S', '2ND YEAR'),
(22146491, 'Manalad', 'Bryant Ray', 'BSIT', 'IT2S', '2ND YEAR'),
(22146492, 'Paraguya', 'Rosele Joy', 'BSIT', 'IT2S', '2ND YEAR'),
(22146495, 'Alvarez', 'Keisha Louise', 'BSIT', 'IT2S', '2ND YEAR'),
(22146515, 'Francisco', 'Joselito', 'BSIT', 'IT2S', '2ND YEAR'),
(22146596, 'Seda', 'Justine', 'BSIT', 'IT2S', '2ND YEAR'),
(22146745, 'Vilchez', 'Keenjhay', 'BSIT', 'IT2A', '2ND YEAR'),
(22146749, 'Tolda', 'Christian Jorge', 'BSIT', 'IT2S', '2ND YEAR'),
(22146899, 'Guazon', 'Rose Ann', 'BSIT', 'IT2S', '2ND YEAR'),
(22146900, 'Dawang', 'Gloria', 'BSIT', 'IT2S', '2ND YEAR'),
(23149603, 'Maralit', 'Ma. Cielo', 'BSIT', 'IT2A', '2ND YEAR');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `role`, `password`) VALUES
(2, 'qwerty', 'Teacher', 'qwerty'),
(4, 'ad', 'CIPA', 'ad'),
(5, 'stud', 'Student', 'stud'),
(6, 'tea', 'Teacher', 'tea'),
(8, 'C001', 'Coordinator', 'cood'),
(9, 'email@email.com', 'Adviser', 'viser'),
(32, 'testtt', 'Adviser', '123'),
(11, 'jessica', 'Student', 'jessi'),
(15, 'dorankevin_cba@plmun.edu.ph', 'Adviser', 'adviser'),
(17, 'eidohr@gmail.com', 'Student', '022502rje'),
(18, 'admin', 'CIPA', '123'),
(20, 'garcenico_bsit@plmun.edu.ph', 'Student', 'Garce0926'),
(24, 'janedoe@plmun.edu.ph', 'Adviser', 'pass1'),
(22, '001', 'CIPA', '123'),
(41, 'parrenoheidelberg_bscs@plmun.edu.ph', 'Student', '1233'),
(25, 'marysmith@plmun.edu.ph', 'Adviser', 'pass2'),
(26, 'jamesjohnson@plmun.edu.ph', 'Adviser', 'pass3'),
(27, 'patriciawilliams@plmun.edu.ph', 'Adviser', 'pass4'),
(28, 'lindajones@plmun.edu.ph', 'Adviser', 'pass5'),
(29, 'alexgarcia@plmun.edu.ph', 'Adviser', 'pass6'),
(45, 'abriochenelle_bsit@plmun.edu.ph', 'Student', 'abrio');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companylist`
--
ALTER TABLE `companylist`
  ADD PRIMARY KEY (`No`);

--
-- Indexes for table `company_info`
--
ALTER TABLE `company_info`
  ADD PRIMARY KEY (`companyCode`);

--
-- Indexes for table `coordinator`
--
ALTER TABLE `coordinator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`CourseID`);

--
-- Indexes for table `course_list`
--
ALTER TABLE `course_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_list`
--
ALTER TABLE `department_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doneinternship`
--
ALTER TABLE `doneinternship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listadviser`
--
ALTER TABLE `listadviser`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logdata`
--
ALTER TABLE `logdata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ongoinginternship`
--
ALTER TABLE `ongoinginternship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance_report`
--
ALTER TABLE `performance_report`
  ADD PRIMARY KEY (`No`);

--
-- Indexes for table `sections_list`
--
ALTER TABLE `sections_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studentinfo`
--
ALTER TABLE `studentinfo`
  ADD PRIMARY KEY (`studentID`);

--
-- Indexes for table `student_grade`
--
ALTER TABLE `student_grade`
  ADD PRIMARY KEY (`studentID`);

--
-- Indexes for table `student_masterlist`
--
ALTER TABLE `student_masterlist`
  ADD PRIMARY KEY (`studentID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companylist`
--
ALTER TABLE `companylist`
  MODIFY `No` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `company_info`
--
ALTER TABLE `company_info`
  MODIFY `companyCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coordinator`
--
ALTER TABLE `coordinator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `CourseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_list`
--
ALTER TABLE `course_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `department_list`
--
ALTER TABLE `department_list`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `doneinternship`
--
ALTER TABLE `doneinternship`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listadviser`
--
ALTER TABLE `listadviser`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `logdata`
--
ALTER TABLE `logdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ongoinginternship`
--
ALTER TABLE `ongoinginternship`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance_report`
--
ALTER TABLE `performance_report`
  MODIFY `No` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sections_list`
--
ALTER TABLE `sections_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
