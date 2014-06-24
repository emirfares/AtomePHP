/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : atome
Target Host     : localhost:3306
Target Database : atome
Date: 2014-06-25 00:55:45
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for ban_ip
-- ----------------------------
DROP TABLE IF EXISTS `ban_ip`;
CREATE TABLE `ban_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `raison` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ban_ip
-- ----------------------------

-- ----------------------------
-- Table structure for gameservers
-- ----------------------------
DROP TABLE IF EXISTS `gameservers`;
CREATE TABLE `gameservers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `system_port` int(11) NOT NULL,
  `community` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of gameservers
-- ----------------------------
INSERT INTO `gameservers` VALUES ('1', '5.168.202.32', '5555', '4040', '0');

-- ----------------------------
-- Table structure for player_accounts
-- ----------------------------
DROP TABLE IF EXISTS `player_accounts`;
CREATE TABLE `player_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `question` varchar(300) NOT NULL,
  `reponse` varchar(300) NOT NULL,
  `subscriptionDate` text NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `pays` varchar(100) NOT NULL,
  `characters` text NOT NULL,
  `gifts` text NOT NULL,
  `gmlevel` tinyint(2) NOT NULL,
  `is_banned` tinyint(1) NOT NULL,
  `points` int(11) NOT NULL,
  `cadeau` int(11) DEFAULT '0',
  `lastIP` text,
  `last_time` text,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=531 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of player_accounts
-- ----------------------------
INSERT INTO `player_accounts` VALUES ('530', 'test', 'test', 'mdrpppppp', '', '', '', '', '', '', '', '1,1', '', '0', '0', '0', '0', '', '');
INSERT INTO `player_accounts` VALUES ('510', 'emir', 'emir', 'unki', '', '', '', '', '', '', '', 'Xayqto,27|Siypdeki,0', '', '10', '0', '0', '0', null, null);
