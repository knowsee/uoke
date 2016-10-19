SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `2u_order`;
CREATE TABLE `2u_order` (
  `orderId` char(15) NOT NULL,
  `userId` mediumint(8) unsigned NOT NULL,
  `orderPay` float(10,2) NOT NULL DEFAULT '0.00',
  `orderType` varchar(20) NOT NULL,
  `orderMoney` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `orderDiscount` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `orderCreatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `orderPayedtime` int(10) unsigned NOT NULL DEFAULT '0',
  `orderStatus` int(1) unsigned NOT NULL DEFAULT '0',
  `orderCpstatus` int(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `2u_service`;
CREATE TABLE `2u_service` (
  `ip` int(10) NOT NULL,
  `route_one` char(15) NOT NULL,
  `route_two` char(15) DEFAULT NULL,
  `route_three` char(15) DEFAULT NULL,
  `ipList` mediumtext,
  `network_A` char(4) DEFAULT NULL,
  `network_B` char(4) DEFAULT NULL,
  `network_C` char(4) DEFAULT NULL,
  `updateTime` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `2u_service` (`ip`, `route_one`, `route_two`, `route_three`, `ipList`, `network_A`, `network_B`, `network_C`, `updateTime`) VALUES
(122, '222', '222', '22', NULL, NULL, NULL, NULL, 1111);

DROP TABLE IF EXISTS `2u_user`;
CREATE TABLE `2u_user` (
  `userId` mediumint(8) NOT NULL,
  `userName` char(100) NOT NULL,
  `userEmail` char(100) DEFAULT NULL,
  `userBirth` char(10) DEFAULT '1999-01-01',
  `userRealID` varchar(20) DEFAULT NULL,
  `userPhone` varchar(15) DEFAULT NULL,
  `userRegtime` int(10) DEFAULT NULL,
  `userLogintime` int(10) DEFAULT NULL,
  `userStatus` int(1) NOT NULL DEFAULT '0',
  `userAdmin` int(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `2u_order`
  ADD PRIMARY KEY (`orderId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `orderStatus` (`orderStatus`);

ALTER TABLE `2u_service`
  ADD KEY `ip` (`ip`);

ALTER TABLE `2u_user`
  ADD PRIMARY KEY (`userId`);
