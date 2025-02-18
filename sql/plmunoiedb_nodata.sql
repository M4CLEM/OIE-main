-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 19, 2024 at 02:19 PM
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
  `studentID` int(11) NOT NULL,
  `student_email` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL
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
-- Table structure for table `criteria_list`
--

CREATE TABLE `criteria_list` (
  `id` int(11) NOT NULL,
  `program` varchar(255) NOT NULL,
  `criteria` varchar(255) NOT NULL,
  `percentage` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria_list`
--

INSERT INTO `criteria_list` (`id`, `program`, `criteria`, `percentage`, `description`, `status`) VALUES
(1, 'BSIT', 'Quality of Work', '20', 'Assesses the overall excellence, accuracy, and thoroughness of an individual’s work.', ''),
(18, 'BSIT', 'Sense of Urgency', '10', 'Refers to how promptly and seriously an individual responds to tasks that require immediate attention. ', ''),
(19, 'BSIT', 'Execution Concept', '20', 'Evaluates how well an individual translates ideas into action.', ''),
(20, 'BSIT', 'Promptness and Punctuality', '30', 'Focuses on an individual’s timeliness in meeting deadlines and attending scheduled events.', ''),
(21, 'BSIT', 'Work Ethics', '10', 'Work ethics encompasses an individual’s moral principles, integrity, and behavior in the workplace. ', ''),
(22, 'BSIT', 'Demeanor', '10', 'Refers to an individual’s overall attitude, behavior, and interpersonal interactions.', '');

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
-- Table structure for table `sections_list`
--

CREATE TABLE `sections_list` (
  `id` int(11) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `school_year` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `contactNo` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `course` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `companyCode` varchar(255) DEFAULT NULL,
  `trainerEmail` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `school_year` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `objective` longtext NOT NULL,
  `skills` longtext NOT NULL,
  `seminars` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_grade`
--

CREATE TABLE `student_grade` (
  `id` int(11) NOT NULL,
  `criteria` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `studentID` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(20133257, 'Abrio', 'Chenelle', 'BSIT', 'IT2P', '4TH YEAR'),
(20133315, 'Collamar', 'Malcolm', 'BSIT', 'IT2P', '2ND YEAR'),
(20133368, 'Garce', 'Nico Roell', 'BSIT', 'IT2A', '4TH YEAR'),
(20133619, 'Parreno', 'Heidel', 'BSCS', 'IT4B', '4TH YEAR'),
(20134261, 'Clorado', 'Claire jasmine', 'BSIT', 'IT2P', '4TH YEAR'),
(21137744, 'Llander', 'Sherwin', 'BSIT', 'IT2P', '2ND YEAR'),
(21138438, 'Amores', 'Ronalyn', 'BSIT', 'IT2S', '3RD YEAR'),
(21238858, 'Ragma', 'Kim', 'BSIT', 'IT2S', '2ND YEAR'),
(21239004, 'Crisostomo', 'Aaron LUIS', 'BSIT', 'IT2S', '2ND YEAR'),
(21239027, 'Dujali', 'Vincent', 'BSIT', 'IT2C', '2ND YEAR'),
(21239323, 'Valdez', 'Michael', 'BSIT', 'IT2A', '3RD YEAR'),
(22142058, 'Delos Santos', 'Karl', 'BSIT', 'IT2A', '2ND YEAR'),
(22142093, 'Janda', 'Bryan Lyndon', 'BSIT', 'IT2C', '2ND YEAR'),
(22142154, 'Carisma', 'Joseph', 'BSIT', 'IT2A', '2ND YEAR'),
(22142156, 'De Vero ', 'Garry', 'BSIT', 'IT2A', '2ND YEAR'),
(22142160, 'Brillantes', 'Lee Justine', 'BSIT', 'IT2A', '2ND YEAR'),
(22142196, 'Sabida', 'Joy Ann', 'BSIT', 'IT2A', '2ND YEAR'),
(22142224, 'Tanjusay ', 'Marven Jude ', 'BSIT', 'IT2C', '2ND YEAR'),
(22142257, 'Malinao ', 'Chrisville ', 'BSIT', 'IT2C', '2ND YEAR'),
(22142348, 'Bas', 'Arbhy Joy', 'BSIT', 'IT2A', '2ND YEAR'),
(22142369, 'Monares ', 'Yesha Lyn ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142401, 'Solana', 'Rosemarie', 'BSIT', 'IT2A', '2ND YEAR'),
(22142432, 'Napilitan', 'Kenpaolo ', 'BSIT', 'IT2C', '2ND YEAR'),
(22142467, 'Sabuag', 'Jerwin', 'BSIT', 'IT2A', '2ND YEAR'),
(22142477, 'Balquin', 'Maria Angelica', 'BSIT', 'IT2C', '2ND YEAR'),
(22142528, 'Biasura', 'Hilary Joyce', 'BSIT', 'IT2C', '2ND YEAR'),
(22142531, 'Buenconsejo', 'Erysa Marie', 'BSIT', 'IT2C', '2ND YEAR'),
(22142532, 'Bucasas', 'Hazel', 'BSIT', 'IT2C', '2ND YEAR'),
(22142537, 'Marfil', 'Christian Paolo', 'BSIT', 'IT2A', '2ND YEAR'),
(22142591, 'Rosquites', 'Charlie', 'BSIT', 'IT2C', '2ND YEAR'),
(22142593, 'Gaerlan', 'Clarenze Kyle', 'BSIT', 'IT2A', '2ND YEAR'),
(22142594, 'Villamente ', 'Adrian James', 'BSIT', 'IT2C', '2ND YEAR'),
(22142598, 'San Jose', 'John Paul', 'BSIT', 'IT2C', '2ND YEAR'),
(22142603, 'Butial', 'Janna', 'BSIT', 'IT2C', '2ND YEAR'),
(22142606, 'Briones ', 'Princess ', 'BSIT', 'IT2C', '2ND YEAR'),
(22142611, 'Pacumbaba', 'Mike John', 'BSIT', 'IT2C', '2ND YEAR'),
(22142649, 'Aclan', 'Christian', 'BSIT', 'IT2C', '2ND YEAR'),
(22142650, 'Abarra', 'Lanemenmhey', 'BSIT', 'IT2C', '2ND YEAR'),
(22142688, 'Endaya', 'Ernesto', 'BSIT', 'IT2A', '2ND YEAR'),
(22142697, 'Rivera', 'John Luie ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142715, 'Prevendido', 'Raven', 'BSIT', 'IT2C', '2ND YEAR'),
(22142721, 'Fortun', 'Rolando', 'BSIT', 'IT2C', '2ND YEAR'),
(22142725, 'Mariquit', 'Jhonel', 'BSIT', 'IT2C', '2ND YEAR'),
(22142749, 'Dawat', 'Prince David ', 'BSIT', 'IT2C', '2ND YEAR'),
(22142758, 'Ecija', 'Rasheed Ryan', 'BSIT', 'IT2A', '2ND YEAR'),
(22142759, 'Rotor', 'Leoncio', 'BSIT', 'IT2A', '2ND YEAR'),
(22142761, 'Dela Cruz ', 'Jayron', 'BSIT', 'IT2A', '2ND YEAR'),
(22142778, 'Caleta', 'Yhuan Andrei', 'BSIT', 'IT2C', '2ND YEAR'),
(22142786, 'Ventura', 'Edmar', 'BSIT', 'IT2A', '2ND YEAR'),
(22142791, 'Aloria', 'Alainna Kellyn', 'BSIT', 'IT2A', '2ND YEAR'),
(22142799, 'Heyres', 'Renniel ', 'BSIT', 'IT2C', '2ND YEAR'),
(22142816, 'Donor', 'Ashley Huey ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142826, 'Paz', 'Ejay Kenneth', 'BSIT', 'IT2C', '2ND YEAR'),
(22142840, 'Bejenia', 'Karl', 'BSIT', 'IT2C', '2ND YEAR'),
(22142841, 'Cosca', 'Jan Lawrence', 'BSIT', 'IT2A', '2ND YEAR'),
(22142870, 'Pirante', 'Ivan', 'BSIT', 'IT2C', '2ND YEAR'),
(22142895, 'Reyes', 'John Lloyd', 'BSIT', 'IT2A', '2ND YEAR'),
(22142907, 'Marasigan', 'Von Adrian', 'BSIT', 'IT2A', '2ND YEAR'),
(22142908, 'Pumanes', 'Jhay Rhov ', 'BSIT', 'IT2A', '2ND YEAR'),
(22142977, 'Idquila', 'Mariel', 'BSIT', 'IT2C', '2ND YEAR'),
(22142989, 'Bisnar', 'Steffany', 'BSIT', 'IT2C', '2ND YEAR'),
(22142998, 'Carale', 'Cenon', 'BSIT', 'IT2A', '2ND YEAR'),
(22143051, 'Lapaot', 'Jonas', 'BSIT', 'IT2P', '2ND YEAR'),
(22143086, 'Dela Cruz', 'Mark Anthony', 'BSIT', 'IT2A', '2ND YEAR'),
(22143087, 'De Mesa', 'Gabriel', 'BSIT', 'IT2A', '2ND YEAR'),
(22143148, 'Perpetua', 'Alexa', 'BSIT', 'IT2A', '2ND YEAR'),
(22143211, 'Grajo', 'Aro Jamil Prince', 'BSIT', 'IT2A', '2ND YEAR'),
(22143215, 'Estores', 'Jericho Josh ', 'BSIT', 'IT2C', '2ND YEAR'),
(22143224, 'Casimero', 'John Dave', 'BSIT', 'IT2A', '2ND YEAR'),
(22143233, 'Del Rosario ', 'Jedhie', 'BSIT', 'IT2A', '2ND YEAR'),
(22143238, 'Torrefiel', 'Denise ', 'BSIT', 'IT2C', '2ND YEAR'),
(22143288, 'Raveche', 'Mariella', 'BSIT', 'IT2A', '2ND YEAR'),
(22143369, 'Malipico', 'Cris Julius', 'BSIT', 'IT2C', '2ND YEAR'),
(22143371, 'Bustillo', 'John Rafael', 'BSIT', 'IT2C', '2ND YEAR'),
(22143397, 'Adaya', 'Ceejay', 'BSIT', 'IT2A', '2ND YEAR'),
(22143478, 'Reparep', 'Harbie', 'BSIT', 'IT2C', '2ND YEAR'),
(22143487, 'Encio', 'John Daver', 'BSIT', 'IT2C', '2ND YEAR'),
(22143488, 'Lagos', 'Juhnbert Ken', 'BSIT', 'IT2C', '2ND YEAR'),
(22143490, 'Gumaya', 'Aron Llyod', 'BSIT', 'IT2C', '2ND YEAR'),
(22143492, 'Licuben', 'Denver', 'BSIT', 'IT2C', '2ND YEAR'),
(22143614, 'Calderon ', 'Jericho ', 'BSIT', 'IT2C', '2ND YEAR'),
(22143620, 'Britos', 'James Cromwel', 'BSIT', 'IT2C', '2ND YEAR'),
(22143754, 'Francisco', 'Kim Cedric', 'BSIT', 'IT2C', '2ND YEAR'),
(22143775, 'Rodriguez', 'Jennifer', 'BSIT', 'IT2C', '2ND YEAR'),
(22143811, 'Gonzaga', 'Lanz Mikhaile', 'BSIT', 'IT2A', '2ND YEAR'),
(22143812, 'Rosel', 'Khristian Rei', 'BSIT', 'IT2C', '2ND YEAR'),
(22143829, 'Lupena', 'Hans Christian', 'BSIT', 'IT2P', '3RD YEAR'),
(22143854, 'Torres ', 'Felicity Raine ', 'BSIT', 'IT2A', '2ND YEAR'),
(22143855, 'Lumaad', 'Channa Mae', 'BSIT', 'IT2A', '2ND YEAR'),
(22143901, 'Flores', 'Julia Louisse', 'BSIT', 'IT2A', '2ND YEAR'),
(22143931, 'Castrence', 'Neil Zacarrii', 'BSIT', 'IT2A', '2ND YEAR'),
(22143944, 'Mater', 'Johaina Pauline', 'BSIT', 'IT2C', '2ND YEAR'),
(22143947, 'Boclot', 'Sarji', 'BSIT', 'IT2A', '2ND YEAR'),
(22143980, 'Espinosa', 'John Romel', 'BSIT', 'IT2A', '2ND YEAR'),
(22144495, 'Panolino', 'Princess May', 'BSIT', 'IT2P', '2ND YEAR'),
(22144501, 'Jasmin', 'Zhaila ', 'BSIT', 'IT2C', '2ND YEAR'),
(22144542, 'Libarnes', 'Gio ', 'BSIT', 'IT2C', '2ND YEAR'),
(22144595, 'Libiran', 'Steven', 'BSIT', 'IT2C', '2ND YEAR'),
(22144681, 'Magsayo', 'Shenarie Nicole', 'BSIT', 'IT2S', '2ND YEAR'),
(22144692, 'Gabilan', 'Cyrus', 'BSIT', 'IT2P', '2ND YEAR'),
(22144876, 'Andrade', 'James Ronel', 'BSIT', 'IT2P', '2ND YEAR'),
(22144889, 'Pili', 'Shervin Rafael', 'BSIT', 'IT2S', '2ND YEAR'),
(22145030, 'Tamboy', 'Rowlene Ahlondra', 'BSIT', 'IT2P', '2ND YEAR'),
(22145211, 'Vargas', 'Josiah Anton', 'BSIT', 'IT2S', '2ND YEAR'),
(22145453, 'Pajaron', 'Jovanie', 'BSIT', 'IT2P', '2ND YEAR'),
(22145455, 'Fernando', 'Enrico', 'BSIT', 'IT2S', '2ND YEAR'),
(22145471, 'Celendron', 'Aliyah', 'BSIT', 'IT2S', '2ND YEAR'),
(22145553, 'Garcera', 'Mark Anthony', 'BSIT', 'IT2P', '2ND YEAR'),
(22145692, 'Ibeng', 'Cristian', 'BSIT', 'IT2P', '2ND YEAR'),
(22145754, 'Quijano', 'Mark', 'BSIT', 'IT2P', '2ND YEAR'),
(22145764, 'Egualan', 'Andrei', 'BSIT', 'IT2P', '2ND YEAR'),
(22145792, 'Ortega', 'Ernest Edrian', 'BSIT', 'IT2P', '2ND YEAR'),
(22145807, 'Berroya', 'Cris Julian', 'BSIT', 'IT2P', '2ND YEAR'),
(22145813, 'Ebrada', 'Paul Ellard', 'BSIT', 'IT2P', '2ND YEAR'),
(22145860, 'Odavar', 'Aldwin', 'BSIT', 'IT2P', '2ND YEAR'),
(22145865, 'Roldan', 'Janna', 'BSIT', 'IT2P', '2ND YEAR'),
(22145868, 'Flores', 'Matt', 'BSIT', 'IT2P', '2ND YEAR'),
(22145875, 'Belga', 'Breecha', 'BSIT', 'IT2P', '2ND YEAR'),
(22145880, 'Beldad', 'Tristan Dave', 'BSIT', 'IT2P', '2ND YEAR'),
(22145881, 'Pantaleon', 'Orly', 'BSIT', 'IT2P', '2ND YEAR'),
(22145890, 'Yusingco', 'Serge Oland', 'BSIT', 'IT2P', '2ND YEAR'),
(22145892, 'Quilay', 'Cristine Schei', 'BSIT', 'IT2P', '2ND YEAR'),
(22145896, 'Valdez', 'Lorenzo', 'BSIT', 'IT2P', '2ND YEAR'),
(22145904, 'Samaniego', 'Crisanto', 'BSIT', 'IT2P', '2ND YEAR'),
(22145923, 'Haloc', 'Mmarco', 'BSIT', 'IT2P', '2ND YEAR'),
(22145931, 'Dela Cruz', 'Cathylene Fate', 'BSIT', 'IT2P', '2ND YEAR'),
(22145932, 'Dionisio', 'Niña', 'BSIT', 'IT2P', '2ND YEAR'),
(22145934, 'Berdin', 'Carl Andre', 'BSIT', 'IT2P', '2ND YEAR'),
(22145940, 'Dionisio', 'Marc Anthony', 'BSIT', 'IT2P', '2ND YEAR'),
(22145951, 'Togño', 'Russell', 'BSIT', 'IT2P', '2ND YEAR'),
(22145956, 'Gabronino', 'JOHN KOBe', 'BSIT', 'IT2P', '2ND YEAR'),
(22145957, 'Alvarez', 'Kenlister', 'BSIT', 'IT2S', '2ND YEAR'),
(22145981, 'Bregente', 'Charles', 'BSIT', 'IT2S', '2ND YEAR'),
(22145986, 'Pili', 'Ricky', 'BSIT', 'IT2P', '2ND YEAR'),
(22145989, 'Escalon', 'Camella', 'BSIT', 'IT2P', '2ND YEAR'),
(22145990, 'Sotto', 'Steven Jann', 'BSIT', 'IT2P', '2ND YEAR'),
(22145993, 'Masculino', 'Justine', 'BSIT', 'IT2P', '2ND YEAR'),
(22146028, 'Marco', 'Shoghi Indy Cyril', 'BSIT', 'IT2S', '2ND YEAR'),
(22146031, 'Casa', 'Aj Charles', 'BSIT', 'IT2P', '2ND YEAR'),
(22146032, 'Castolo', 'Neryl Francine', 'BSIT', 'IT2P', '2ND YEAR'),
(22146045, 'Sale', 'Kyle', 'BSIT', 'IT2S', '2ND YEAR'),
(22146047, 'Sevillo', 'Mjay', 'BSIT', 'IT2S', '2ND YEAR'),
(22146131, 'Miras', 'Philip Angelo', 'BSIT', 'IT2S', '2ND YEAR'),
(22146186, 'San Jose', 'Mc Ephraem', 'BSIT', 'IT2A', '2ND YEAR'),
(22146200, 'Se', 'William', 'BSIT', 'IT2P', '2ND YEAR'),
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
(22146464, 'Ordinario', 'John Jenric', 'BSIT', 'IT2P', '2ND YEAR'),
(22146478, 'Cortez', 'Kylenn Rhyss', 'BSIT', 'IT2S', '2ND YEAR'),
(22146484, 'Suarez', 'Ridge Adrian', 'BSIT', 'IT2S', '2ND YEAR'),
(22146486, 'Reyes', 'Aldwin', 'BSIT', 'IT2S', '2ND YEAR'),
(22146488, 'Arandoque', 'Lean James', 'BSIT', 'IT2S', '2ND YEAR'),
(22146491, 'Manalad', 'Bryant Ray', 'BSIT', 'IT2S', '2ND YEAR'),
(22146492, 'Paraguya', 'Rosele Joy', 'BSIT', 'IT2S', '2ND YEAR'),
(22146495, 'Alvarez', 'Keisha Louise', 'BSIT', 'IT2S', '2ND YEAR'),
(22146515, 'Francisco', 'Joselito', 'BSIT', 'IT2S', '2ND YEAR'),
(22146596, 'Seda', 'Justine', 'BSIT', 'IT2S', '2ND YEAR'),
(22146631, 'Bueno', 'Rommel', 'BSIT', 'IT2P', '2ND YEAR'),
(22146736, 'Orcia', 'Norleen', 'BSIT', 'IT2C', '2ND YEAR'),
(22146743, 'Bestar', 'Marvin', 'BSIT', 'IT2C', '2ND YEAR'),
(22146745, 'Vilchez', 'Keenjhay', 'BSIT', 'IT2A', '2ND YEAR'),
(22146749, 'Tolda', 'Christian Jorge', 'BSIT', 'IT2S', '2ND YEAR'),
(22146794, 'Encio', 'Jocel', 'BSIT', 'IT2P', '2ND YEAR'),
(22146835, 'Dimaano', 'Noli', 'BSIT', 'IT2P', '2ND YEAR'),
(22146840, 'Reyes', 'Mj Denroe', 'BSIT', 'IT2C', '2ND YEAR'),
(22146854, 'Castillo', 'Ryan Josh', 'BSIT', 'IT2P', '2ND YEAR'),
(22146899, 'Guazon', 'Rose Ann', 'BSIT', 'IT2S', '2ND YEAR'),
(22146900, 'Dawang', 'Gloria', 'BSIT', 'IT2S', '2ND YEAR'),
(22146989, 'Nuqui', 'John Carlo', 'BSIT', 'IT2P', '2ND YEAR'),
(23149603, 'Maralit', 'Ma. Cielo', 'BSIT', 'IT2A', '2ND YEAR'),
(23150895, 'Sarmiento', 'Shandy', 'BSIT', 'IT2P', '2ND YEAR'),
(23150994, 'Bosogon', 'Joanne', 'BSIT', 'IT2P', '2ND YEAR'),
(23151044, 'Duran', 'Ryki Leigh', 'BSIT', 'IT2C', '2ND YEAR'),
(23151493, 'Ponce', 'Alyssa Mae', 'BSIT', 'IT2C', '2ND YEAR'),
(23152088, 'Ybiosa', 'Narciso Iv', 'BSIT', 'IT2P', '2ND YEAR'),
(23152850, 'Pelagio', 'Franz Kenneth Andrew', 'BSIT', 'IT2C', '2ND YEAR');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `companyName` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `role`, `password`, `program`, `companyName`) VALUES
(4, 'admin', 'CIPA', '12345', NULL, NULL),
(51, 'BSCS_Coor', 'Coordinator', '12345', 'BSCS', NULL),
(50, 'BSIT_Coor', 'Coordinator', '12345', 'BSIT', NULL);

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
-- Indexes for table `course_list`
--
ALTER TABLE `course_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `criteria_list`
--
ALTER TABLE `criteria_list`
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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `companyCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `course_list`
--
ALTER TABLE `course_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `criteria_list`
--
ALTER TABLE `criteria_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `department_list`
--
ALTER TABLE `department_list`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `listadviser`
--
ALTER TABLE `listadviser`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `logdata`
--
ALTER TABLE `logdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sections_list`
--
ALTER TABLE `sections_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `student_grade`
--
ALTER TABLE `student_grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
