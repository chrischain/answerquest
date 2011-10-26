/*
 Navicat Premium Data Transfer

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 50516
 Source Host           : localhost
 Source Database       : HACKATHON

 Target Server Type    : MySQL
 Target Server Version : 50516
 File Encoding         : utf-8

 Date: 10/26/2011 14:35:21 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `questions`
-- ----------------------------
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` bigint(20) NOT NULL,
  `text` varchar(1024) NOT NULL,
  `type` smallint(6) NOT NULL,
  `code` varchar(255) NOT NULL,
  `points` int(11) NOT NULL,
  `required` bit(1) NOT NULL,
  `choices` varchar(1024) NOT NULL,
  `answer` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `questions`
-- ----------------------------
BEGIN;
INSERT INTO `questions` VALUES ('1', '1', 'What type of musical instruments are at Pete\'s bar?', '0', '', '0', b'0', '', 'Pianos'), ('2', '1', 'What establishment is at 30.268569,-97.736081 ?', '0', '', '0', b'0', '', 'Stubbs BBQ'), ('3', '1', 'What does it say on the wall over the band at Chuggin Monkey?', '0', '', '0', b'0', '', 'Chuggin Monkey'), ('4', '1', 'What landmark is at 30.268658,-97.737443 ?', '0', '', '0', b'0', '', 'Dirty Dog Saloon'), ('5', '1', 'What does the sticker on the cash register at Hopdoddy Burger Bar ?', '0', '', '0', b'0', '', 'A Texas Longhorn.');
COMMIT;

-- ----------------------------
--  Table structure for `responses`
-- ----------------------------
DROP TABLE IF EXISTS `responses`;
CREATE TABLE `responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `series_id` bigint(20) NOT NULL,
  `question_id` bigint(20) NOT NULL,
  `response` varchar(1024) NOT NULL,
  `result` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `series`
-- ----------------------------
DROP TABLE IF EXISTS `series`;
CREATE TABLE `series` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `min_responses` int(11) NOT NULL,
  `max_responses` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `allow_anonymous` bit(1) NOT NULL,
  `active` bit(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `series`
-- ----------------------------
BEGIN;
INSERT INTO `series` VALUES ('1', 'Austin Bar Crawl', '94352812-7602-47c1-a069-f0b1f4170af9', 'A quest to see how many hotspots you can find in Austin, TX.', '0', '0', '0', b'0', b'0'), ('2', 'Austin Longhorn Roundup', '55248f51-262a-435b-8c9a-2899ba918dc7', 'Roundup all of the longhorn cow statues in Austin, TX.', '0', '0', '0', b'0', b'0'), ('3', 'SenchaCon Quest', '88589976-41b4-4f65-acec-fb5b9b30e556', 'Find the EXT-JS expert at the SenchaCon conference in Austin, TX.', '0', '0', '0', b'0', b'0');
COMMIT;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` smallint(6) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `users`
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'admin', '1', 'password'), ('2', 'user', '0', 'password'), ('3', 'don', '1', 'password');
COMMIT;

