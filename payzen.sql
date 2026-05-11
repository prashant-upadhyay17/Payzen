-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 29, 2026 at 06:58 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u458731110_hrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `logs_json` longtext DEFAULT NULL,
  `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `role_id`, `email`, `password`, `first_name`, `last_name`, `logs_json`, `is_deleted`, `is_active`, `created_at`) VALUES
(1, 1, 'admin@aadinkpharma.com', '$2y$10$fraaLb1gqfUKEwIkCXwGFu9WhnBYACXOUHdo9tOUyFuCy4XQXaQi6', 'Prashant', 'Upadhyay', '[{\"time\":\"2026-04-28 23:59:28\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:50:05\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:47:25\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:46:07\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:46:03\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:45:04\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:44:12\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:42:59\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:40:23\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:39:58\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:37:32\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:29:45\",\"message\":\"Payslip generated for AADP-VAR-250002 (MAR 2026)\"},{\"time\":\"2026-04-28 21:29:45\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 21:29:17\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 21:27:46\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:09:39\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 21:09:01\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-28 20:52:21\",\"message\":\"Employee login: umsappl@gmail.com\"},{\"time\":\"2026-04-28 20:49:59\",\"message\":\"Payslip generated for AADP-VAR-250002 (MAR 2026)\"},{\"time\":\"2026-04-28 20:49:59\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 20:49:42\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 20:48:33\",\"message\":\"Updated employee: AADP-AZA-250001\"},{\"time\":\"2026-04-28 20:46:37\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 11:57:29\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 11:55:34\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-28 11:55:17\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-28 11:53:50\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-28 11:53:48\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-28 02:00:13\",\"message\":\"Employee login: pankaj.indev@gmail.com\"},{\"time\":\"2026-04-28 01:59:49\",\"message\":\"Payslip generated for AADP-VAR-250002 (APR 2026)\"},{\"time\":\"2026-04-28 01:59:49\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 01:59:44\",\"message\":\"Payslip generated for AADP-VAR-250002 (FEB 2026)\"},{\"time\":\"2026-04-28 01:59:44\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 01:59:39\",\"message\":\"Payslip generated for AADP-VAR-250002 (JAN 2026)\"},{\"time\":\"2026-04-28 01:59:39\",\"message\":\"Updated employee: AADP-VAR-250002\"},{\"time\":\"2026-04-28 01:59:23\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-28 01:58:28\",\"message\":\"Employee login: pankaj.indev@gmail.com\"},{\"time\":\"2026-04-28 01:52:50\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-27 19:39:39\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 18:13:38\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 18:09:52\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:18:31\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:13:51\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:13:07\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:13:05\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:09:11\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:09:02\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:07:32\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:07:18\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:06:07\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:05:33\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:02:40\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:02:24\",\"message\":\"Payslip generated for AADP-AZA-250001 (APR 2026)\"},{\"time\":\"2026-04-27 17:02:24\",\"message\":\"Updated employee: AADP-AZA-250001\"},{\"time\":\"2026-04-27 17:02:10\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:00:48\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:00:32\",\"message\":\"Employee login: rajanikant@aadinkpharma.com\"},{\"time\":\"2026-04-27 17:00:07\",\"message\":\"Email sent: Payslip for JAN 2026 \\u2192 rajanikant@aadinkpharma.com (AADP-AZA-250001)\"},{\"time\":\"2026-04-27 16:59:47\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 16:56:36\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 16:55:53\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 16:48:49\",\"message\":\"Email sent: Payslip for JAN 2026 \\u2192 pankaj.indev@gmail.com (AADP-VAR-250002)\"},{\"time\":\"2026-04-27 16:46:07\",\"message\":\"Email sent: Payslip for JAN 2026 \\u2192 rajanikant@aadinkpharma.com (AADP-AZA-250001)\"},{\"time\":\"2026-04-27 16:33:44\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-27 10:12:46\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-27 10:03:24\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-27 10:03:13\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-27 07:31:05\",\"message\":\"Employee login: upendra@aadinkpharma.com\"},{\"time\":\"2026-04-26 16:53:48\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-26 16:47:45\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-26 16:47:06\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-26 16:44:56\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-26 16:41:55\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-26 16:36:13\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:43:07\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:42:36\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:42:05\",\"message\":\"Employee login: upendra@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:39:28\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:26:35\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:24:58\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:19:56\",\"message\":\"Admin login: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:16:45\",\"message\":\"Payslip generated for EMP004 (JAN 2026)\"},{\"time\":\"2026-04-25 11:15:56\",\"message\":\"Payslip generated for EMP004 (JAN 2026)\"},{\"time\":\"2026-04-25 11:15:25\",\"message\":\"Email sent: Payslip for JAN 2026 \\u2192 upendra@aadinkpharma.com (EMP001)\"},{\"time\":\"2026-04-25 11:13:46\",\"message\":\"Soft-deleted employee: EMP003\"},{\"time\":\"2026-04-25 11:08:41\",\"message\":\"Soft-deleted admin ID: 2\"},{\"time\":\"2026-04-25 11:08:37\",\"message\":\"Updated admin user: hr.manager@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:08:11\",\"message\":\"Soft-deleted admin ID: 2\"},{\"time\":\"2026-04-25 11:07:12\",\"message\":\"Updated employee: EMP001\"},{\"time\":\"2026-04-25 11:07:03\",\"message\":\"Updated employee: EMP001\"},{\"time\":\"2026-04-25 11:06:44\",\"message\":\"Updated employee: EMP001\"},{\"time\":\"2026-04-25 11:04:08\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 11:01:51\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:41:10\",\"message\":\"Employee login: priya.d@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:40:30\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:36:53\",\"message\":\"Employee login: prashant@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:30:13\",\"message\":\"Employee login: upendra@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:29:30\",\"message\":\"Admin login: admin@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:28:56\",\"message\":\"Employee login: upendra@aadinkpharma.com\"},{\"time\":\"2026-04-25 10:28:30\",\"message\":\"Employee login: prashant@aadinkpharma.com\"}]', 'N', 'Y', '2026-04-25 07:46:40'),
(2, 2, 'hr.manager@aadinkpharma.com', '$2y$10$1hvH25oJtcyWIiPmwqRj/etXRDVYa/De6wFEkJh0CIxE5Z57L6BgO', 'Secondary', 'HR', '[]', 'N', 'Y', '2026-04-25 07:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(11) NOT NULL,
  `designation_name` varchar(150) NOT NULL,
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `emp_code` varchar(100) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `package` decimal(10,2) DEFAULT 0.00,
  `gross_monthly` decimal(10,2) DEFAULT 0.00,
  `paid_leaves_taken` decimal(10,2) DEFAULT 0.00,
  `salary_config_json` text DEFAULT NULL,
  `payslips_json` longtext DEFAULT NULL,
  `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `role_id`, `emp_code`, `mobile`, `email`, `password`, `first_name`, `last_name`, `designation`, `package`, `gross_monthly`, `paid_leaves_taken`, `salary_config_json`, `payslips_json`, `is_deleted`, `is_active`, `created_at`) VALUES
(1, 3, 'AADP-AZA-250001', '8700438570', 'rajanikant@aadinkpharma.com', '$2y$10$fRox13gq8Wmpjl.HY/oAie1S8cWgN/e5feS0qgv8Dh2YoztUU2B5q', 'Rajnikant', 'Mauraya', 'Sales Officer', 100000.00, 8000.00, 2.00, '[]', '[{\"month\":\"JAN\",\"year\":\"2026\",\"generated_date\":\"2026-04-27 12:56:11\",\"gross\":\"8000.00\",\"basic\":\"4000.00\",\"da\":\"2400.00\",\"conveyance\":\"0\",\"medical\":\"0\",\"special\":\"1600.00\",\"pf\":\"480.00\",\"esi\":\"60.00\",\"pt\":\"0\",\"leave_deduction\":\"0\",\"total_deductions\":\"540.00\",\"net_salary\":\"7460.00\",\"employer_pf\":\"520.00\",\"employer_esi\":\"260.00\",\"ctc\":\"8780.00\",\"status\":\"Paid\"},{\"month\":\"APR\",\"year\":\"2026\",\"generated_date\":\"2026-04-27 17:02:24\",\"gross\":\"8000.00\",\"basic\":\"4000.00\",\"da\":\"2400.00\",\"conveyance\":\"0\",\"medical\":\"0\",\"special\":\"1600.00\",\"pf\":\"480.00\",\"esi\":\"60.00\",\"pt\":\"0\",\"leave_deduction\":\"0\",\"total_deductions\":\"540.00\",\"net_salary\":\"7460.00\",\"employer_pf\":\"520.00\",\"employer_esi\":\"260.00\",\"ctc\":\"8780.00\",\"status\":\"Paid\"}]', 'N', 'Y', '2026-04-27 10:54:16'),
(2, 3, 'AADP-BAN-250001', '8545942929', 'deepak.kumar@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Deepak', 'Kumar', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(3, 3, 'AADP-PRA-250001', '8181841536', 'sujeet.kumar@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Sujeet', 'Kumar', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(4, 3, 'AADP-PRA-250002', '9793237358', 'rahul.shukla@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Rahul', 'Shukla', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(5, 3, 'AADP-GHA-250001', '8853330909', 'akash.singh@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Akash', 'Singh', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(6, 3, 'AADP-GHA-250002', '8858134513', 'sajan.kashyap@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Sajan', 'kashyap', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(7, 3, 'AADP-DEO-250001', '6394943399', 'upendra.yadav@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Upendra', 'Yadav', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(8, 3, 'AADP-DEO-250002', '9369376589', 'sujeetshukla@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Sujeet ', 'Shukla', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(9, 3, 'AADP-JAU-250001', '9555751657', 'rajansingh@aadinkpharma.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Rajan', 'Singh', 'Sales Officer', 100000.00, 8000.00, 0.00, '', '', 'N', 'Y', '2026-04-27 10:54:16'),
(10, 3, 'AADP-VAR-250002', '9721649543', 'umsappl@gmail.com', '$2y$10$qoTwIIlXIX/TKOgWtr9Qn.xA0qh9Yk6Tcr2P69ls6VUuRviEtCUe.', 'Amit Kumar', 'Chaubey', 'Sales Officer', 100000.00, 8000.00, 0.00, '{\"percBasic\":\"50\",\"percDA\":\"30\",\"amtConveyance\":\"0\",\"amtMedical\":\"0\",\"percPF\":\"12\",\"percESI\":\"0.75\",\"amtPT\":\"0\",\"percEmpPF\":\"13\",\"percEmpESI\":\"3.25\",\"amtBasic\":\"4000.00\",\"amtDA\":\"2400.00\",\"amtSpecial\":\"1600.00\",\"amtPF\":\"480.00\",\"amtESI\":\"60.00\"}', '[{\"month\":\"JAN\",\"year\":\"2026\",\"generated_date\":\"2026-04-28 01:59:39\",\"gross\":\"8000.00\",\"basic\":\"4000.00\",\"da\":\"2400.00\",\"conveyance\":\"0\",\"medical\":\"0\",\"special\":\"1600.00\",\"pf\":\"480.00\",\"esi\":\"60.00\",\"pt\":\"0\",\"leave_deduction\":\"0\",\"total_deductions\":\"540.00\",\"net_salary\":\"7460.00\",\"employer_pf\":\"520.00\",\"employer_esi\":\"260.00\",\"ctc\":\"8780.00\",\"status\":\"Paid\"},{\"month\":\"FEB\",\"year\":\"2026\",\"generated_date\":\"2026-04-28 01:59:44\",\"gross\":\"8000.00\",\"basic\":\"4000.00\",\"da\":\"2400.00\",\"conveyance\":\"0\",\"medical\":\"0\",\"special\":\"1600.00\",\"pf\":\"480.00\",\"esi\":\"60.00\",\"pt\":\"0\",\"leave_deduction\":\"0\",\"total_deductions\":\"540.00\",\"net_salary\":\"7460.00\",\"employer_pf\":\"520.00\",\"employer_esi\":\"260.00\",\"ctc\":\"8780.00\",\"status\":\"Paid\"},{\"month\":\"APR\",\"year\":\"2026\",\"generated_date\":\"2026-04-28 01:59:49\",\"gross\":\"8000.00\",\"basic\":\"4000.00\",\"da\":\"2400.00\",\"conveyance\":\"0\",\"medical\":\"0\",\"special\":\"1600.00\",\"pf\":\"480.00\",\"esi\":\"60.00\",\"pt\":\"0\",\"leave_deduction\":\"0\",\"total_deductions\":\"540.00\",\"net_salary\":\"7460.00\",\"employer_pf\":\"520.00\",\"employer_esi\":\"260.00\",\"ctc\":\"8780.00\",\"status\":\"Paid\"},{\"month\":\"MAR\",\"year\":\"2026\",\"generated_date\":\"2026-04-28 21:29:45\",\"gross\":\"8000.00\",\"basic\":\"4000.00\",\"da\":\"2400.00\",\"conveyance\":\"0\",\"medical\":\"0\",\"special\":\"1600.00\",\"pf\":\"480.00\",\"esi\":\"60.00\",\"pt\":\"0\",\"leave_deduction\":\"0\",\"total_deductions\":\"540.00\",\"net_salary\":\"7460.00\",\"employer_pf\":\"520.00\",\"employer_esi\":\"260.00\",\"ctc\":\"8780.00\",\"status\":\"Paid\"}]', 'N', 'Y', '2026-04-27 10:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(3, 'Employee'),
(2, 'HR');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emp_code` (`emp_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `emp_code_2` (`emp_code`),
  ADD UNIQUE KEY `emp_code_3` (`emp_code`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
