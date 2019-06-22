/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  Hery Kurniawan
 * Created: Jun 23, 2019
 */

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


CREATE TABLE `tracker_device` (
  `tracker_device_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kind` varchar(16) DEFAULT NULL,
  `model` varchar(64) DEFAULT NULL,
  `platform` varchar(64) DEFAULT NULL,
  `platform_version` varchar(16) DEFAULT NULL,
  `is_mobile` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tracker_device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
