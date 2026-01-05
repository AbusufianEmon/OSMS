-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2018 at 01:23 PM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";





CREATE TABLE `adminlogin_tb` (
  `a_login_id` int(11) NOT NULL,
  `a_name` varchar(60) COLLATE utf8_bin NOT NULL,
  `a_email` varchar(60) COLLATE utf8_bin NOT NULL,
  `a_password` varchar(60) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `adminlogin_tb` (`a_login_id`, `a_name`, `a_email`, `a_password`) VALUES
(1, 'admin', 'admin@gmail.com', '123456');



CREATE TABLE `assets_tb` (
  `pid` int(11) NOT NULL,
  `pname` varchar(60) COLLATE utf8_bin NOT NULL,
  `pdop` date NOT NULL,
  `pava` int(11) NOT NULL,
  `ptotal` int(11) NOT NULL,
  `poriginalcost` int(11) NOT NULL,
  `psellingcost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `assets_tb` (`pid`, `pname`, `pdop`, `pava`, `ptotal`, `poriginalcost`, `psellingcost`) VALUES
(1, 'Keyboard', '2018-10-03', 3, 10, 400, 500),
(3, 'Mouse', '2018-10-02', 18, 30, 500, 600),
(4, 'Rode Mic', '2018-10-20', 9, 10, 15000, 18000);


CREATE TABLE `assignwork_tb` (
  `rno` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `request_info` text COLLATE utf8_bin NOT NULL,
  `request_desc` text COLLATE utf8_bin NOT NULL,
  `requester_name` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_add1` text COLLATE utf8_bin NOT NULL,
  `requester_add2` text COLLATE utf8_bin NOT NULL,
  `requester_city` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_state` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_zip` int(11) NOT NULL,
  `requester_email` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_mobile` bigint(11) NOT NULL,
  `assign_tech` varchar(60) COLLATE utf8_bin NOT NULL,
  `assign_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `assignwork_tb` (`rno`, `request_id`, `request_info`, `request_desc`, `requester_name`, `requester_add1`, `requester_add2`, `requester_city`, `requester_state`, `requester_zip`, `requester_email`, `requester_mobile`, `assign_tech`, `assign_date`) VALUES
(6, 49, 'Mic not working', 'my mic is not working', 'emon', '6565', 'Col', 'syl', 'Jh', 6565, 'emon@gmail.com', 656567, 'emon', '2018-10-14'),
(7, 50, 'Jack and Jones', 'Hello There have you seen this movie', 'emon', '123', 'Sector Five', 'syl', 'temuki', 123456, 'emon@gmail.com', 234234234, 'emon', '2018-10-16'),
(8, 50, 'Jack and Jones', 'Hello There have you seen this movie', 'emon', '123', 'Sector Five', 'syl', 'temuki', 123456, 'emon@gmail.com', 234234234, 'emon', '2018-10-21'),
(9, 52, 'LCD Not working', 'my lcd is not working properly', 'emon', 'HOuse No. 123', 'temukhi', 'syl', 'Jh', 12345, 'emon@gmail.com', 234566, 'emon', '2018-10-21'),
(10, 52, 'Rode Mic Note Working', 'my rode mic is not working properly', 'emon', 'house no 234', 'Sec 3', 'syl', 'syl', 674534, 'user@gmail.com', 1234566782, 'Tech1', '2018-10-21');


CREATE TABLE `customer_tb` (
  `custid` int(11) NOT NULL,
  `custname` varchar(60) COLLATE utf8_bin NOT NULL,
  `custadd` varchar(60) COLLATE utf8_bin NOT NULL,
  `cpname` varchar(60) COLLATE utf8_bin NOT NULL,
  `cpquantity` int(11) NOT NULL,
  `cpeach` int(11) NOT NULL,
  `cptotal` int(11) NOT NULL,
  `cpdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `customer_tb` (`custid`, `custname`, `custadd`, `cpname`, `cpquantity`, `cpeach`, `cptotal`, `cpdate`) VALUES
(1, 'Emon', 'emon', 'Mouse', 1, 600, 600, '2018-10-13'),
(2, 'Emon', 'emon', 'Mouse', 2, 600, 600, '2018-10-13'),
(3, 'Emon', 'emon', 'Mouse', 5, 600, 3000, '2018-10-13'),
(4, 'Emon', 'emon', 'Mouse', 2, 600, 1200, '2018-10-13'),
(5, 'Emon', 'somethingadd', 'Mouse', 1, 600, 600, '2018-10-13'),
(6, 'Emon', 'someoneadd', 'Keyboard', 1, 500, 500, '2018-10-13'),
(7, 'Emon', 'emon', 'Keyboard', 1, 500, 500, '2018-10-09'),
(8, 'Emon', 'emon', 'Keyboard', 2, 500, 1000, '2018-10-21'),
(9, 'Emon', 'emon', 'Keyboard', 1, 500, 500, '2018-10-20'),
(10, 'Emon', 'asdsa', 'Keyboard', 1, 500, 500, '2018-10-20'),
(11, 'Emon', 'emon', 'Samsung LCD', 1, 12000, 12000, '2018-10-20'),
(12, 'John', 'dasdsa', 'Keyboard', 1, 500, 500, '2018-10-20'),
(13, 'Gon', 'asdsad', 'Keyboard', 1, 500, 500, '2018-10-20'),
(14, 'Killua', 'asdasd', 'Samsung LCD', 1, 12000, 12000, '2018-10-20'),
(15, 'Putin', 'dfsdf', 'Samsung LCD', 1, 12000, 12000, '2018-10-20'),
(16, 'Emon', 'sadsad', 'Samsung LCD', 1, 12000, 12000, '2018-10-20'),
(17, 'Kim', 'fgfdgfdg', 'Samsung LCD', 1, 12000, 12000, '2018-10-20'),
(18, 'Joffery', 'fgdf', 'Mouse', 1, 600, 600, '2018-10-20'),
(19, 'Emon', 'emon', 'Samsung LCD', 1, 12000, 12000, '2018-10-20'),
(20, 'Trump', 'sdfdsf', 'Mouse', 1, 600, 600, '2018-10-20'),
(21, 'Emon', 'emon', 'Rode Mic', 1, 18000, 18000, '2018-10-20');


CREATE TABLE `requesterlogin_tb` (
  `r_login_id` int(11) NOT NULL,
  `r_name` varchar(60) COLLATE utf8_bin NOT NULL,
  `r_email` varchar(60) COLLATE utf8_bin NOT NULL,
  `r_password` varchar(60) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `requesterlogin_tb` (`r_login_id`, `r_name`, `r_email`, `r_password`) VALUES
(1, '  emon', 'emon@gmail.com', '1234'),
(2, '  User', 'user@gmail.com', '1234'),
(3, '', 'user2emon@gmail.com', '1234');



CREATE TABLE `submitrequest_tb` (
  `request_id` int(11) NOT NULL,
  `request_info` text COLLATE utf8_bin NOT NULL,
  `request_desc` text COLLATE utf8_bin NOT NULL,
  `requester_name` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_add1` text COLLATE utf8_bin NOT NULL,
  `requester_add2` text COLLATE utf8_bin NOT NULL,
  `requester_city` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_state` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_zip` int(11) NOT NULL,
  `requester_email` varchar(60) COLLATE utf8_bin NOT NULL,
  `requester_mobile` bigint(11) NOT NULL,
  `request_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



INSERT INTO `submitrequest_tb` (`request_id`, `request_info`, `request_desc`, `requester_name`, `requester_add1`, `requester_add2`, `requester_city`, `requester_state`, `requester_zip`, `requester_email`, `requester_mobile`, `request_date`) VALUES
(50, 'Jack and Jones', 'Hello There have you seen this movie', 'emon', '123', 'Sector Five', 'emon', 'syl', 123456, 'emon@gmail.com', 234234234, '2018-10-13'),
(51, 'asdsadsa', 'asdsadsa', 'dasdsad', 'asdasd', 'sdsadsa', 'asdsad', 'sadasd', 1413123, 'dsadas@gmail.com', 4131323, '2018-10-20'),
(52, 'Rode Mic Note Working', 'my rode mic is not working properly', 'Sam', 'house no 234', 'Sec 3', 'syl', 'bangladesh', 674534, 'user@gmail.com', 1234566782, '2018-10-20');


CREATE TABLE `technician_tb` (
  `empid` int(11) NOT NULL,
  `empName` varchar(60) COLLATE utf8_bin NOT NULL,
  `empCity` varchar(60) COLLATE utf8_bin NOT NULL,
  `empMobile` bigint(11) NOT NULL,
  `empEmail` varchar(60) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `technician_tb` (`empid`, `empName`, `empCity`, `empMobile`, `empEmail`) VALUES
(12, 'emon', 'syl 4', 1234, 'emon@gmail.com');

ALTER TABLE `adminlogin_tb`
  ADD PRIMARY KEY (`a_login_id`);


ALTER TABLE `assets_tb`
  ADD PRIMARY KEY (`pid`);


ALTER TABLE `assignwork_tb`
  ADD PRIMARY KEY (`rno`);


ALTER TABLE `customer_tb`
  ADD PRIMARY KEY (`custid`);


ALTER TABLE `requesterlogin_tb`
  ADD PRIMARY KEY (`r_login_id`);


ALTER TABLE `submitrequest_tb`
  ADD PRIMARY KEY (`request_id`);


ALTER TABLE `technician_tb`
  ADD PRIMARY KEY (`empid`);


ALTER TABLE `adminlogin_tb`
  MODIFY `a_login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `assets_tb`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `assignwork_tb`
  MODIFY `rno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;


ALTER TABLE `customer_tb`
  MODIFY `custid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;


ALTER TABLE `requesterlogin_tb`
  MODIFY `r_login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


ALTER TABLE `submitrequest_tb`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

ALTER TABLE `technician_tb`
  MODIFY `empid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

