SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `forex` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `forex`;

DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `currencyid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT 'Japanese Yen',
  `shortname` varchar(45) NOT NULL DEFAULT 'JPY',
  `buyvalue` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `sellvalue` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`currencyid`),
  KEY `shortname` (`shortname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `currency` (`currencyid`, `name`, `shortname`, `buyvalue`, `sellvalue`) VALUES
(1,	'United States Dollar',	'USD',	1.0000,	1.0000),
(2,	'Japanese Yen',	'JPY',	100.0000,	101.0000);

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `newsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newstext` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`newsid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `news` (`newsid`, `newstext`, `time`) VALUES
(1,	'This is a test. This is only a test.',	1448794687),
(2,	'This is another test.',	1448877289);

DROP TABLE IF EXISTS `startendtime`;
CREATE TABLE `startendtime` (
  `timeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '999',
  PRIMARY KEY (`timeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `startendtime` (`timeid`, `starttime`, `endtime`) VALUES
(1,	1448793686,	1457174486);

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `transid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transtype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `currencyid` int(10) unsigned DEFAULT NULL,
  `amount` decimal(30,2) unsigned NOT NULL,
  `rate` decimal(10,4) unsigned NOT NULL,
  `receiveamt` decimal(50,2) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `userkey` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`transid`),
  KEY `currencyid` (`currencyid`),
  KEY `userkey` (`userkey`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`currencyid`) REFERENCES `currency` (`currencyid`),
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`userkey`) REFERENCES `users` (`userkey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userkey` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `userid` varchar(45) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(100) NOT NULL,
  `usertype` tinyint(3) unsigned NOT NULL,
  `networth` decimal(50,2) unsigned NOT NULL DEFAULT '10000000.00',
  PRIMARY KEY (`userkey`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`userkey`, `name`, `userid`, `password`, `salt`, `usertype`, `networth`) VALUES
(1,	'Ingsoc Inner Party',	'ingsoc',	'bf78cf7fb431e7443728e540f837100b8c9a636a3d9a4b7d5a5716de49e88893e62d054d41d8373a42435835d32f1847e6174fa504cad5407cbca6494d2b2ab8',	'v3[3u{Xl&c9n.t$}B(e=qj2OW(7gi1oN',	2,	10000000.00);

DROP TABLE IF EXISTS `valuechanges`;
CREATE TABLE `valuechanges` (
  `valuechangeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currencyid` int(10) unsigned DEFAULT NULL,
  `newbuyvalue` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `newsellvalue` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `changegroup` int(10) unsigned NOT NULL DEFAULT '0',
  `yetcompleted` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`valuechangeid`),
  KEY `yetcompleted` (`yetcompleted`),
  KEY `currencyid` (`currencyid`),
  KEY `changegroup` (`changegroup`),
  CONSTRAINT `valuechanges_ibfk_2` FOREIGN KEY (`currencyid`) REFERENCES `currency` (`currencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `valuechanges` (`valuechangeid`, `currencyid`, `newbuyvalue`, `newsellvalue`, `time`, `changegroup`, `yetcompleted`) VALUES
(1,	2,	100.0000,	101.0000,	1448793741,	0,	0),
(2,	2,	1.0000,	1.0000,	1448892975,	1,	0),
(3,	2,	2.0000,	2.0000,	1448892981,	2,	0),
(4,	2,	3.0000,	3.0000,	1448892985,	3,	0),
(5,	2,	4.0000,	4.0000,	1448892990,	4,	0),
(6,	2,	6.0000,	6.0000,	1448892995,	5,	0),
(7,	2,	9.0000,	9.0000,	1448893002,	6,	0),
(8,	2,	11.0000,	11.0000,	1448893007,	7,	0),
(9,	2,	33.0000,	33.0000,	1448893012,	8,	0),
(10,	2,	45.0000,	45.0000,	1448893019,	9,	0),
(11,	2,	34.0000,	34.0000,	1448893025,	10,	0),
(12,	2,	1.0000,	1.0000,	1448893200,	11,	0),
(13,	2,	2.0000,	2.0000,	1448893206,	12,	0),
(14,	2,	50.0000,	50.0000,	1448893210,	13,	0),
(15,	2,	150.0000,	150.0000,	1448893217,	14,	0),
(16,	2,	100.0000,	100.0000,	1448895879,	15,	0),
(17,	2,	105.0000,	105.0000,	1448895902,	16,	0),
(18,	2,	100.0000,	101.0000,	1448793741,	17,	0),
(19,	2,	1.0000,	1.0000,	1448892975,	18,	0),
(20,	2,	2.0000,	2.0000,	1448892981,	19,	0),
(21,	2,	3.0000,	3.0000,	1448892985,	20,	0),
(22,	2,	4.0000,	4.0000,	1448892990,	21,	0),
(23,	2,	6.0000,	6.0000,	1448892995,	22,	0),
(24,	2,	9.0000,	9.0000,	1448893002,	23,	0),
(25,	2,	11.0000,	11.0000,	1448893007,	24,	0),
(26,	2,	33.0000,	33.0000,	1448893012,	25,	0),
(27,	2,	45.0000,	45.0000,	1448893019,	26,	0),
(28,	2,	34.0000,	34.0000,	1448893025,	27,	0),
(29,	2,	1.0000,	1.0000,	1448893200,	28,	0),
(30,	2,	2.0000,	2.0000,	1448893206,	29,	0),
(31,	2,	50.0000,	50.0000,	1448893210,	30,	0),
(32,	2,	150.0000,	150.0000,	1448893217,	31,	0),
(33,	2,	100.0000,	100.0000,	1448895879,	32,	0),
(34,	2,	105.0000,	105.0000,	1448895902,	33,	0),
(49,	2,	100.0000,	101.0000,	1448793741,	34,	0),
(50,	2,	1.0000,	1.0000,	1448892975,	35,	0),
(51,	2,	2.0000,	2.0000,	1448892981,	36,	0),
(52,	2,	3.0000,	3.0000,	1448892985,	37,	0),
(53,	2,	4.0000,	4.0000,	1448892990,	38,	0),
(54,	2,	6.0000,	6.0000,	1448892995,	39,	0),
(55,	2,	9.0000,	9.0000,	1448893002,	40,	0),
(56,	2,	11.0000,	11.0000,	1448893007,	41,	0),
(57,	2,	33.0000,	33.0000,	1448893012,	42,	0),
(58,	2,	45.0000,	45.0000,	1448893019,	43,	0),
(59,	2,	34.0000,	34.0000,	1448893025,	44,	0),
(60,	2,	1.0000,	1.0000,	1448893200,	45,	0),
(61,	2,	2.0000,	2.0000,	1448893206,	46,	0),
(62,	2,	50.0000,	50.0000,	1448893210,	47,	0),
(63,	2,	150.0000,	150.0000,	1448893217,	48,	0),
(64,	2,	100.0000,	100.0000,	1448895879,	49,	0),
(65,	2,	105.0000,	105.0000,	1448895902,	50,	0),
(66,	2,	100.0000,	101.0000,	1448793741,	51,	0),
(67,	2,	1.0000,	1.0000,	1448892975,	52,	0),
(68,	2,	2.0000,	2.0000,	1448892981,	53,	0),
(69,	2,	3.0000,	3.0000,	1448892985,	54,	0),
(70,	2,	4.0000,	4.0000,	1448892990,	55,	0),
(71,	2,	6.0000,	6.0000,	1448892995,	56,	0),
(72,	2,	9.0000,	9.0000,	1448893002,	57,	0),
(73,	2,	11.0000,	11.0000,	1448893007,	58,	0),
(74,	2,	33.0000,	33.0000,	1448893012,	59,	0),
(75,	2,	45.0000,	45.0000,	1448893019,	60,	0),
(76,	2,	34.0000,	34.0000,	1448893025,	61,	0),
(77,	2,	1.0000,	1.0000,	1448893200,	62,	0),
(78,	2,	2.0000,	2.0000,	1448893206,	63,	0),
(79,	2,	50.0000,	50.0000,	1448893210,	64,	0),
(80,	2,	150.0000,	150.0000,	1448893217,	65,	0),
(81,	2,	100.0000,	100.0000,	1448895879,	66,	0),
(82,	2,	105.0000,	105.0000,	1448895902,	67,	0),
(83,	2,	100.0000,	101.0000,	1448895962,	68,	0);

DROP TABLE IF EXISTS `wallet`;
CREATE TABLE `wallet` (
  `valueid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userkey` bigint(20) unsigned DEFAULT NULL,
  `currencyid` int(10) unsigned DEFAULT NULL,
  `amount` decimal(50,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`valueid`),
  KEY `userkey` (`userkey`),
  KEY `currencyid` (`currencyid`),
  CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`userkey`) REFERENCES `users` (`userkey`),
  CONSTRAINT `wallet_ibfk_2` FOREIGN KEY (`currencyid`) REFERENCES `currency` (`currencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `wallet` (`valueid`, `userkey`, `currencyid`, `amount`) VALUES
(1,	1,	1,	10000000.00),
(2,	1,	2,	0.00);
