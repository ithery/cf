
/**
 * Author:  Hery Kurniawan
 * Created: June 06, 2022
 */


DROP TABLE IF EXISTS `chat_conversation`;
CREATE TABLE `chat_conversation` (
  `chat_conversation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user1_id` bigint(20) unsigned DEFAULT NULL,
  `user1_type` varchar(255) DEFAULT NULL,
  `user2_id` bigint(20) unsigned DEFAULT NULL,
  `user2_type` varchar(255) DEFAULT NULL,
  `connection` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `channel` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `chat_room_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_message_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`chat_conversation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `chat_conversation_user`;
CREATE TABLE `chat_conversation_user` (
  `chat_conversation_user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `chat_room_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`chat_conversation_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
