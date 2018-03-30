/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50719
 Source Host           : localhost:3306
 Source Schema         : miaocraft

 Target Server Type    : MySQL
 Target Server Version : 50719
 File Encoding         : 65001

 Date: 30/03/2018 14:53:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for miao_serverinfo
-- ----------------------------
DROP TABLE IF EXISTS `miao_serverinfo`;
CREATE TABLE `miao_serverinfo`  (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `server_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'mc',
  `server_add_day` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `server_end_day` datetime(0) NULL DEFAULT NULL,
  `server_memory` int(11) UNSIGNED NULL DEFAULT 1024,
  `server_port` int(11) UNSIGNED NULL DEFAULT NULL,
  `server_rcon_port` int(11) NULL DEFAULT NULL,
  `server_rcon_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '123',
  `server_jarpath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'spigot-1.11.2-R0.1-SNAPSHOT.jar',
  `server_publishjar` tinyint(4) UNSIGNED NULL DEFAULT 1,
  `server_on` tinyint(4) NULL DEFAULT 0,
  `user_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`sid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of miao_serverinfo
-- ----------------------------
INSERT INTO `miao_serverinfo` VALUES (1, 1, 1, 'mc', '2018-03-30 14:42:25', '2018-08-20 00:00:00', 1024, 25565, 1500, '123', 'spigot-1.11.2-R0.1-SNAPSHOT.jar', 0, 1, 'admin');
INSERT INTO `miao_serverinfo` VALUES (2, 2, 102, 'mc', '2018-03-30 14:52:59', '2019-03-30 00:00:00', 2048, 43288, 45315, NULL, 'spigot-1.11.2-R0.1-SNAPSHOT.jar', 1, 1, 'luanxu');

-- ----------------------------
-- Table structure for miao_shop
-- ----------------------------
DROP TABLE IF EXISTS `miao_shop`;
CREATE TABLE `miao_shop`  (
  `shopid` int(11) NOT NULL AUTO_INCREMENT,
  `shop_server_tag` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `shop_server_avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'index.jpg',
  `shop_server_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `shop_server_core` tinyint(4) UNSIGNED NOT NULL DEFAULT 24,
  `shop_server_memory` int(11) NULL DEFAULT 1024,
  `shop_server_price` int(5) UNSIGNED NULL DEFAULT 100,
  `shop_server_month` tinyint(5) UNSIGNED NULL DEFAULT 1,
  `shop_server_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'mc',
  PRIMARY KEY (`shopid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of miao_shop
-- ----------------------------
INSERT INTO `miao_shop` VALUES (1, '优惠', '1.jpg', '新手套餐', 24, 1024, 100, 1, 'mc');
INSERT INTO `miao_shop` VALUES (2, '妹纸套餐', '1.jpg', '妹纸专属', 24, 2048, 100, 12, 'mc');

-- ----------------------------
-- Table structure for miao_userinfo
-- ----------------------------
DROP TABLE IF EXISTS `miao_userinfo`;
CREATE TABLE `miao_userinfo`  (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `money` int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 103 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of miao_userinfo
-- ----------------------------
INSERT INTO `miao_userinfo` VALUES (1, 3395);
INSERT INTO `miao_userinfo` VALUES (102, 1600);

SET FOREIGN_KEY_CHECKS = 1;
