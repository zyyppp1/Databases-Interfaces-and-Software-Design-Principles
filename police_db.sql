-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： mariadb
-- 生成日期： 2023-12-11 21:15:02
-- 服务器版本： 10.8.8-MariaDB-1:10.8.8+maria~ubu2204
-- PHP 版本： 8.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `police_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `AuditLog`
--

CREATE TABLE `AuditLog` (
  `Log_ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Action` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `Timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `AuditLog`
--

INSERT INTO `AuditLog` (`Log_ID`, `Username`, `Action`, `Description`, `Timestamp`) VALUES
(244, 'zyp', 'Add Person', 'Added new person: yep', '2023-12-11 03:01:57'),
(245, 'zyp', 'Add Vehicle', 'Added new vehicle: 123312', '2023-12-11 03:01:57'),
(246, 'zyp', 'File Incident Report', 'Filed a new incident report', '2023-12-11 03:01:57'),
(247, 'zyp', 'Add Person', 'Added new person: 324324', '2023-12-11 03:05:20'),
(248, 'zyp', 'Add Vehicle', 'Added new vehicle: 21323', '2023-12-11 03:05:20'),
(249, 'zyp', 'File Incident Report', 'Filed a new incident report', '2023-12-11 03:05:20'),
(250, 'zyp', 'Search Incident', 'Searched for incident report: ', '2023-12-11 03:09:44'),
(251, 'zyp', 'Update Incident', 'Updated incident report. Details: Incident ID: 1 | People ID: 8 -> 8 | Vehicle ID: 15 -> 15 | Incident Report: \'40mph in a 30 limit111\' -> \'40mph in a 30 limit11121123\'', '2023-12-11 03:10:06'),
(252, 'zyp', 'Search Incident', 'Searched for incident report: ', '2023-12-11 03:10:08'),
(253, 'zyp', 'Search Ahdit', 'Searched for: ', '2023-12-11 17:26:35');

-- --------------------------------------------------------

--
-- 表的结构 `Fines`
--

CREATE TABLE `Fines` (
  `Fine_ID` int(11) NOT NULL,
  `Fine_Amount` int(11) NOT NULL,
  `Fine_Points` int(11) NOT NULL,
  `Incident_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `Fines`
--

INSERT INTO `Fines` (`Fine_ID`, `Fine_Amount`, `Fine_Points`, `Incident_ID`) VALUES
(1, 2000, 6, 3),
(2, 50, 0, 2),
(3, 500, 3, 4);

-- --------------------------------------------------------

--
-- 表的结构 `Incident`
--

CREATE TABLE `Incident` (
  `Incident_ID` int(11) NOT NULL,
  `Vehicle_ID` int(11) DEFAULT NULL,
  `People_ID` int(11) DEFAULT NULL,
  `Incident_Date` date NOT NULL,
  `Incident_Report` varchar(500) NOT NULL,
  `Offence_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `Incident`
--

INSERT INTO `Incident` (`Incident_ID`, `Vehicle_ID`, `People_ID`, `Incident_Date`, `Incident_Report`, `Offence_ID`) VALUES
(1, 15, 8, '2017-12-01', '40mph in a 30 limit11121123', 1),
(2, 20, 8, '2017-11-01', 'Double parked', 4),
(3, 13, 4, '2017-09-17', '110mph on motorway', 1),
(4, 14, 2, '2017-08-22', 'Failur111132e to stop at a red light - travelling 25mph', 8),
(5, 13, 4, '2017-10-17', 'Not wearing a seatbelt on the M1', 3),
(28, 15, 8, '2023-12-23', '1111', 6),
(29, 13, 4, '2023-12-15', '2222', 4),
(30, 13, 4, '2024-01-03', '333', 5),
(31, 66, NULL, '2023-12-27', '1444', 1),
(35, 67, 30, '2023-12-13', '1231414', 1),
(36, 68, 31, '2023-12-13', '14214', 1);

-- --------------------------------------------------------

--
-- 表的结构 `Offence`
--

CREATE TABLE `Offence` (
  `Offence_ID` int(11) NOT NULL,
  `Offence_description` varchar(50) NOT NULL,
  `Offence_maxFine` int(11) NOT NULL,
  `Offence_maxPoints` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `Offence`
--

INSERT INTO `Offence` (`Offence_ID`, `Offence_description`, `Offence_maxFine`, `Offence_maxPoints`) VALUES
(1, 'Speeding', 1000, 3),
(2, 'Speeding on a motorway', 2500, 6),
(3, 'Seat belt offence', 500, 0),
(4, 'Illegal parking', 500, 0),
(5, 'Drink driving', 10000, 11),
(6, 'Driving without a licence', 10000, 0),
(7, 'Traffic light offences', 1000, 3),
(8, 'Cycling on pavement', 500, 0),
(9, 'Failure to have control of vehicle', 1000, 3),
(10, 'Dangerous driving', 1000, 11),
(11, 'Careless driving', 5000, 6),
(12, 'Dangerous cycling', 2500, 0);

-- --------------------------------------------------------

--
-- 表的结构 `Ownership`
--

CREATE TABLE `Ownership` (
  `People_ID` int(11) DEFAULT NULL,
  `Vehicle_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `Ownership`
--

INSERT INTO `Ownership` (`People_ID`, `Vehicle_ID`) VALUES
(3, 12),
(8, 20),
(4, 15),
(4, 13),
(1, 16),
(2, 14),
(5, 17),
(6, 18),
(7, 21),
(15, 46),
(16, 47),
(16, 48),
(17, 49),
(22, 57),
(22, 58),
(22, 58),
(20, 59),
(20, 59),
(20, 60),
(20, 60),
(26, 61),
(26, 61),
(26, 62),
(26, 62),
(1, 64),
(1, 64),
(29, 65);

-- --------------------------------------------------------

--
-- 表的结构 `People`
--

CREATE TABLE `People` (
  `People_ID` int(11) NOT NULL,
  `People_name` varchar(50) NOT NULL,
  `People_address` varchar(50) DEFAULT NULL,
  `People_licence` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `People`
--

INSERT INTO `People` (`People_ID`, `People_name`, `People_address`, `People_licence`) VALUES
(1, 'James Smith', '23 Barnsdale Road, Leicester', 'SMITH92LDOFJJ829'),
(2, 'Jennifer Allen', '46 Bramcote Drive, Nottingham', 'ALLEN88K23KLR9B3'),
(3, 'John Myers', '323 Derby Road, Nottingham', 'MYERS99JDW8REWL3'),
(4, 'James Smith', '26 Devonshire Avenue, Nottingham', 'SMITHR004JFS20TR'),
(5, 'Terry Brown', '7 Clarke Rd, Nottingham', 'BROWND3PJJ39DLFG'),
(6, 'Mary Adams', '38 Thurman St, Nottingham', 'ADAMSH9O3JRHH107'),
(7, 'Neil Becker', '6 Fairfax Close, Nottingham', 'BECKE88UPR840F9R'),
(8, 'Angela Smith', '30 Avenue Road, Grantham', 'SMITH222LE9FJ5DS'),
(9, 'Xene Medora', '22 House Drive, West Bridgford', 'MEDORH914ANBB223'),
(15, '1', '1', '1'),
(16, '', '', '23'),
(17, '424323', '2321', '1233'),
(18, '123', '23123', '32323'),
(19, '123', '23123', '3232'),
(20, '123', '2131', '123'),
(21, '123123', '12313', '231'),
(22, '1233', '32323', '323'),
(23, '123123', '123213123', '123123'),
(24, '123232', '4324325', '24212'),
(25, '234', '234', '234'),
(26, '123213', '12313', '4214'),
(27, 'yep', '1', '1272414'),
(28, '12341', '3123', '1244421'),
(29, '1231', '23131', '3231231'),
(30, 'yep', '13', '1314'),
(31, '324324', '123213', '12414');

-- --------------------------------------------------------

--
-- 表的结构 `Users`
--

CREATE TABLE `Users` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `Users`
--

INSERT INTO `Users` (`username`, `password`, `role`) VALUES
('12132', '111', 'police'),
('1234', '111', 'police'),
('daniels', 'copper99', 'admin'),
('hzy', '111', 'police'),
('mcnulty', 'plod123', 'police'),
('zyp', '111', 'admin'),
('zyp1', '111', 'police'),
('zyp11', '123', 'police');

-- --------------------------------------------------------

--
-- 表的结构 `Vehicle`
--

CREATE TABLE `Vehicle` (
  `Vehicle_ID` int(11) NOT NULL,
  `Vehicle_type` varchar(20) NOT NULL,
  `Vehicle_colour` varchar(20) NOT NULL,
  `Vehicle_licence` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `Vehicle`
--

INSERT INTO `Vehicle` (`Vehicle_ID`, `Vehicle_type`, `Vehicle_colour`, `Vehicle_licence`) VALUES
(12, 'Ford Fiesta', 'Blue', 'LB15AJL'),
(13, 'Ferrari 458', 'Red', 'MY64PRE'),
(14, 'Vauxhall Astra', 'Silver', 'FD65WPQ'),
(15, 'Honda Civic', 'Green', 'FJ17AUG'),
(16, 'Toyota Prius', 'Silver', 'FP16KKE'),
(17, 'Ford Mondeo', 'Black', 'FP66KLM'),
(18, 'Ford Focus', 'White', 'DJ14SLE'),
(20, 'Nissan Pulsar', 'Red', 'NY64KWD'),
(21, 'Renault Scenic', 'Silver', 'BC16OEA'),
(22, 'Hyundai i30', 'Grey', 'AD223NG'),
(46, '1', '1', '1'),
(47, '1', '1', '1'),
(48, '1', '1', '1'),
(49, '123', '12333', '123'),
(50, '231231', '12323', '323244'),
(51, '123', '323', '3232'),
(52, '123', '123', '233'),
(53, '213', '2323', '32'),
(54, '123123', '12412414', '123'),
(55, '23', '112', '1123'),
(56, '52', '11', '11'),
(57, '23', '4324', '123'),
(58, '23', '4324', '123'),
(59, '123', '123', '123'),
(60, '123', '123', '123'),
(61, '1231234', '1', '1234'),
(62, '1231234', '1', '1234'),
(63, '12412', '4214', '3123'),
(64, 'benz', 'benz', '13'),
(65, '1231231', '3213', '12313'),
(66, '3213123', '123131', '312313'),
(67, '13213', '123123', '123312'),
(68, '324', '123214214', '21323');

--
-- 转储表的索引
--

--
-- 表的索引 `AuditLog`
--
ALTER TABLE `AuditLog`
  ADD PRIMARY KEY (`Log_ID`);

--
-- 表的索引 `Fines`
--
ALTER TABLE `Fines`
  ADD PRIMARY KEY (`Fine_ID`),
  ADD KEY `fk_fines_incident` (`Incident_ID`);

--
-- 表的索引 `Incident`
--
ALTER TABLE `Incident`
  ADD PRIMARY KEY (`Incident_ID`),
  ADD KEY `fk_incident_offence` (`Offence_ID`),
  ADD KEY `fk_incident_people` (`People_ID`),
  ADD KEY `fk_incident_vehicle` (`Vehicle_ID`);

--
-- 表的索引 `Offence`
--
ALTER TABLE `Offence`
  ADD PRIMARY KEY (`Offence_ID`);

--
-- 表的索引 `Ownership`
--
ALTER TABLE `Ownership`
  ADD KEY `fk_ownership_vehicle` (`Vehicle_ID`),
  ADD KEY `fk_ownership_people` (`People_ID`) USING BTREE;

--
-- 表的索引 `People`
--
ALTER TABLE `People`
  ADD PRIMARY KEY (`People_ID`);

--
-- 表的索引 `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`username`);

--
-- 表的索引 `Vehicle`
--
ALTER TABLE `Vehicle`
  ADD PRIMARY KEY (`Vehicle_ID`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `AuditLog`
--
ALTER TABLE `AuditLog`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- 使用表AUTO_INCREMENT `Fines`
--
ALTER TABLE `Fines`
  MODIFY `Fine_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `Incident`
--
ALTER TABLE `Incident`
  MODIFY `Incident_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- 使用表AUTO_INCREMENT `People`
--
ALTER TABLE `People`
  MODIFY `People_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `Vehicle`
--
ALTER TABLE `Vehicle`
  MODIFY `Vehicle_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- 限制导出的表
--

--
-- 限制表 `Fines`
--
ALTER TABLE `Fines`
  ADD CONSTRAINT `fk_fines_incident` FOREIGN KEY (`Incident_ID`) REFERENCES `Incident` (`Incident_ID`);

--
-- 限制表 `Incident`
--
ALTER TABLE `Incident`
  ADD CONSTRAINT `fk_incident_offence` FOREIGN KEY (`Offence_ID`) REFERENCES `Offence` (`Offence_ID`),
  ADD CONSTRAINT `fk_incident_vehicle` FOREIGN KEY (`Vehicle_ID`) REFERENCES `Vehicle` (`Vehicle_ID`);

--
-- 限制表 `Ownership`
--
ALTER TABLE `Ownership`
  ADD CONSTRAINT `fk_ownership_people` FOREIGN KEY (`People_ID`) REFERENCES `People` (`People_ID`),
  ADD CONSTRAINT `fk_ownership_vehicle` FOREIGN KEY (`Vehicle_ID`) REFERENCES `Vehicle` (`Vehicle_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
