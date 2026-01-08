-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 08, 2026 at 10:19 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ocpb`
--

-- --------------------------------------------------------

--
-- Table structure for table `ocps_account`
--

CREATE TABLE `ocps_account` (
  `sl` int(11) NOT NULL auto_increment,
  `ac_code` varchar(20) default NULL,
  `ocq_company` int(11) default NULL,
  `bank` varchar(70) default NULL,
  `branch` varchar(100) default NULL,
  `ac_type` varchar(50) default NULL,
  `acc_number` varchar(50) default NULL,
  `chequebook` varchar(50) default NULL,
  `leafs` int(5) default NULL,
  `chqbookdate` datetime default NULL,
  `entrydate` datetime default NULL,
  `balance` double(15,2) default NULL,
  PRIMARY KEY  (`sl`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=95 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_adjustment`
--

CREATE TABLE `ocps_adjustment` (
  `id` int(11) NOT NULL auto_increment,
  `adjustment_bank` int(11) default NULL,
  `adjustment_company` int(11) default NULL,
  `adjustment_type` varchar(20) default NULL,
  `adjustment_account` varchar(50) default NULL,
  `adjustment_date` date default NULL,
  `adjustment_reason` varchar(255) default NULL,
  `adjustment_amount` double(11,2) default NULL,
  `entry_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11861 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_admin`
--

CREATE TABLE `ocps_admin` (
  `oa_id` int(4) NOT NULL auto_increment,
  `oa_login` varchar(100) NOT NULL,
  `oa_password` varchar(100) NOT NULL,
  `oa_name` varchar(100) NOT NULL,
  `oa_department` varchar(250) NOT NULL,
  `oa_last_login` datetime NOT NULL,
  `oa_active` tinyint(2) NOT NULL,
  PRIMARY KEY  (`oa_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_bank`
--

CREATE TABLE `ocps_bank` (
  `ocq_id` int(11) NOT NULL auto_increment,
  `ocq_bank` varchar(70) default NULL,
  PRIMARY KEY  (`ocq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_beneficiary`
--

CREATE TABLE `ocps_beneficiary` (
  `ob_id` int(4) NOT NULL auto_increment,
  `ob_name` varchar(250) NOT NULL,
  PRIMARY KEY  (`ob_id`),
  UNIQUE KEY `ob_name` (`ob_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2854 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_cheque`
--

CREATE TABLE `ocps_cheque` (
  `ocq_id` int(11) NOT NULL auto_increment,
  `ocq_bank` varchar(50) default NULL,
  `ocq_accno` varchar(50) default NULL,
  `ocq_chqno` varchar(50) default NULL,
  `ocq_company` varchar(50) NOT NULL,
  `ocq_onbehalf` varchar(100) default NULL COMMENT '10 -> Personal',
  `ocq_signatory` varchar(150) default NULL COMMENT '4 -> Personal',
  `ocq_beneficiary` varchar(250) NOT NULL,
  `ocq_amount` double(11,2) NOT NULL,
  `ocq_purpose` varchar(250) default NULL,
  `ocq_date` date NOT NULL,
  `ocq_type` tinyint(2) NOT NULL,
  `ocq_status` tinyint(2) NOT NULL,
  `ocq_prepare_datetime` datetime NOT NULL,
  `ocq_print_datetime` datetime NOT NULL,
  `ocq_chqbook_datetime` datetime default NULL,
  PRIMARY KEY  (`ocq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53732 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_chequebook`
--

CREATE TABLE `ocps_chequebook` (
  `sl` int(11) NOT NULL auto_increment,
  `oc_id` int(11) default NULL,
  `bank` varchar(70) default NULL,
  `account` varchar(30) default NULL,
  `chqbook_number` varchar(70) default NULL,
  `leafs` int(5) default NULL,
  `inuse` int(3) default NULL,
  `entrydate` datetime default NULL,
  PRIMARY KEY  (`sl`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_company`
--

CREATE TABLE `ocps_company` (
  `oc_id` int(4) NOT NULL auto_increment,
  `oc_name` varchar(250) NOT NULL,
  PRIMARY KEY  (`oc_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_settings`
--

CREATE TABLE `ocps_settings` (
  `os_id` int(4) NOT NULL auto_increment,
  `os_name` varchar(250) NOT NULL,
  `os_value` varchar(250) NOT NULL,
  PRIMARY KEY  (`os_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `ocps_signatory`
--

CREATE TABLE `ocps_signatory` (
  `ocq_id` int(11) NOT NULL auto_increment,
  `ocq_signatory` varchar(70) default NULL,
  `ocq_designation` varchar(50) default NULL,
  `company_id` int(11) default NULL,
  PRIMARY KEY  (`ocq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;
