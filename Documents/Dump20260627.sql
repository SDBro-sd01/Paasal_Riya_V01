-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: paasal_riya_db_01
-- ------------------------------------------------------
-- Server version	9.2.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `about_content`
--

DROP TABLE IF EXISTS `about_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `about_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT 'About Us',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_content`
--

LOCK TABLES `about_content` WRITE;
/*!40000 ALTER TABLE `about_content` DISABLE KEYS */;
INSERT INTO `about_content` VALUES (1,'About Our Company','We are a team of passionate professionals...');
/*!40000 ALTER TABLE `about_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `about_sections`
--

DROP TABLE IF EXISTS `about_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `about_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon_class` varchar(100) DEFAULT 'fas fa-star',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_sections`
--

LOCK TABLES `about_sections` WRITE;
/*!40000 ALTER TABLE `about_sections` DISABLE KEYS */;
INSERT INTO `about_sections` VALUES (1,'Our Mission','Our Mission is a greate service','fas fa-bullseye',0);
/*!40000 ALTER TABLE `about_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL COMMENT 'වෙනස් වූ table එකේ නම',
  `record_id` int NOT NULL COMMENT 'බලපෑමට ලක්වූ record එකේ ID',
  `action` enum('INSERT','UPDATE','DELETE') NOT NULL COMMENT 'සිදුකළ ක්‍රියාව',
  `old_values` json DEFAULT NULL COMMENT 'පැරණි දත්ත (JSON format)',
  `new_values` json DEFAULT NULL COMMENT 'නව දත්ත (JSON format)',
  `changed_by` int DEFAULT NULL COMMENT 'ක්‍රියාව සිදුකළ user ID (NULL = system/public)',
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'වෙනස් වූ වේලාව',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP ලිපිනය',
  PRIMARY KEY (`id`),
  KEY `idx_audit_table_record` (`table_name`,`record_id`),
  KEY `idx_audit_changed_by` (`changed_by`),
  KEY `idx_audit_changed_at` (`changed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` VALUES (1,'users',37,'UPDATE','{\"nic\": \"125975465\", \"email\": \"asdfdgfd@fge.com\", \"mobile\": \"0711231459\", \"address\": \"dgrfyghfrfrh\", \"district\": \"Galle\", \"fullname\": \"example1111111\", \"province\": \"Southern\", \"username\": \"example1111111\", \"user_type\": \"admin\"}','{\"nic\": \"125975465\", \"email\": \"asdfdgfd@fge.com\", \"mobile\": \"0711231459\", \"address\": \"dgrfyghfrfrh\", \"district\": \"Galle\", \"fullname\": \"example1111111\", \"province\": \"Southern\", \"username\": \"example11111112\", \"user_type\": \"admin\"}',1,'2026-06-23 16:13:56','::1'),(2,'users',37,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Reset by admin to Temppass123\", \"password\": \"********\"}',1,'2026-06-23 16:15:52','::1'),(3,'users',14,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Reset by admin to Temppass123\", \"password\": \"********\"}',1,'2026-06-23 16:16:05','::1'),(4,'users',1,'UPDATE','{\"nic\": \"200132202326\", \"email\": \"deemanthasithum033@gmail.com\", \"mobile\": \"0787216051\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"An Admin ( Top )\", \"province\": \"Sabaragamuwa\", \"username\": \"admin\", \"user_type\": \"admin\"}','{\"nic\": \"200132202326\", \"email\": \"deemanthasithum033@gmail.com\", \"mobile\": \"0787216051\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"An Admin ( Top Level )\", \"province\": \"Sabaragamuwa\", \"username\": \"admin\", \"user_type\": \"admin\"}',1,'2026-06-23 16:16:54','::1'),(5,'users',1,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Self password changed via email verification\", \"password\": \"********\"}',1,'2026-06-23 16:18:44','::1'),(6,'users',38,'INSERT',NULL,'{\"nic\": \"45444545454545454\", \"email\": \"asdf545dgfd@fge.com\", \"mobile\": \"0711431459\", \"address\": \"45454545\", \"district\": \"Matara\", \"fullname\": \"example11111114545\", \"province\": \"Southern\", \"username\": \"example1111111454\", \"user_type\": \"Parents\"}',1,'2026-06-23 16:19:44','::1'),(7,'users',39,'INSERT',NULL,'{\"nic\": \"2001322023261\", \"email\": \"Exampletwo@gmail.com\", \"mobile\": \"0787216051\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwo\", \"user_type\": \"Parents\"}',39,'2026-06-23 16:24:27','::1'),(8,'users',39,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Reset by admin to Temppass123\", \"password\": \"********\"}',1,'2026-06-23 16:25:22','::1'),(9,'users',39,'UPDATE','{\"nic\": \"2001322023261\", \"email\": \"Exampletwo@gmail.com\", \"mobile\": \"0787216051\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwo\", \"user_type\": \"Parents\"}','{\"nic\": \"2001322023261\", \"email\": \"Exampletwo@gmail.com\", \"mobile\": \"0787216052\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwotwo\", \"user_type\": \"Parents\"}',1,'2026-06-23 16:25:46','::1'),(10,'users',39,'UPDATE','{\"nic\": \"2001322023261\", \"email\": \"Exampletwo@gmail.com\", \"mobile\": \"0787216052\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwotwo\", \"user_type\": \"Parents\"}','{\"nic\": \"2001322023261\", \"email\": \"Exampletwo@gmail.com\", \"mobile\": \"0787216052\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwotwotwo\", \"user_type\": \"Parents\"}',39,'2026-06-23 16:26:57','::1'),(11,'users',39,'UPDATE','{\"nic\": \"2001322023261\", \"email\": \"Exampletwo@gmail.com\", \"mobile\": \"0787216052\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwotwotwo\", \"user_type\": \"Parents\"}','{\"nic\": \"2001322023261\", \"email\": \"scribeclever@gmail.com\", \"mobile\": \"0787216052\", \"address\": \"N0 87, Samagi Maawatha, Walawa Junction, Kolambageara\", \"district\": \"Ratnapura\", \"fullname\": \"test two\", \"province\": \"Sabaragamuwa\", \"username\": \"exampletwotwotwo\", \"user_type\": \"Parents\"}',39,'2026-06-23 16:27:26','::1'),(12,'users',39,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Self password changed via email verification\", \"password\": \"********\"}',39,'2026-06-23 16:28:14','::1'),(13,'users',39,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Reset by admin to Temppass123\", \"password\": \"********\"}',1,'2026-06-24 15:58:34','::1'),(14,'users',39,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Reset by admin to Temppass123\", \"password\": \"********\"}',1,'2026-06-24 15:59:43','::1'),(15,'users',39,'DELETE','{\"email\": \"scribeclever@gmail.com\", \"user_id\": 39, \"username\": \"exampletwotwotwo\"}',NULL,39,'2026-06-24 16:01:46','::1'),(16,'users',38,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Reset by admin to Temppass123\", \"password\": \"********\"}',1,'2026-06-24 16:02:07','::1'),(17,'users',1,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Self password changed via email verification (not logged in)\", \"password\": \"********\"}',1,'2026-06-27 05:23:48','::1'),(18,'users',1,'UPDATE','{\"password\": \"********\"}','{\"note\": \"Self password changed via email verification (not logged in)\", \"password\": \"********\"}',1,'2026-06-27 05:28:52','::1');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent_comment_id` int DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_service` (`service_id`),
  KEY `parent_comment_id` (`parent_comment_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2083 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_content`
--

DROP TABLE IF EXISTS `contact_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `address` text,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `map_embed` text,
  `working_hours` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_content`
--

LOCK TABLES `contact_content` WRITE;
/*!40000 ALTER TABLE `contact_content` DISABLE KEYS */;
INSERT INTO `contact_content` VALUES (1,'123 Main Street, Colombo 07','+94 11 234 5678','info@yourcompany.lk','<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"300\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>','Mon - Fri : 9.00 AM - 5.00 PM');
/*!40000 ALTER TABLE `contact_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_emails`
--

DROP TABLE IF EXISTS `contact_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-envelope',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_emails`
--

LOCK TABLES `contact_emails` WRITE;
/*!40000 ALTER TABLE `contact_emails` DISABLE KEYS */;
INSERT INTO `contact_emails` VALUES (1,'deemanthasithum033@gmail.com','fas fa-envelope',0);
/*!40000 ALTER TABLE `contact_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_phones`
--

DROP TABLE IF EXISTS `contact_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_phones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(50) NOT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-phone-alt',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_phones`
--

LOCK TABLES `contact_phones` WRITE;
/*!40000 ALTER TABLE `contact_phones` DISABLE KEYS */;
INSERT INTO `contact_phones` VALUES (1,'0787216051','fas fa-phone-alt',0);
/*!40000 ALTER TABLE `contact_phones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_social_links`
--

DROP TABLE IF EXISTS `contact_social_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_social_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(100) DEFAULT NULL,
  `url` text NOT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-share-alt',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_social_links`
--

LOCK TABLES `contact_social_links` WRITE;
/*!40000 ALTER TABLE `contact_social_links` DISABLE KEYS */;
INSERT INTO `contact_social_links` VALUES (1,'Facebook ','gsfdgsgsdgsdg','fas fa-share-alt',0);
/*!40000 ALTER TABLE `contact_social_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_website_links`
--

DROP TABLE IF EXISTS `contact_website_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_website_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `url` text NOT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-globe',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_website_links`
--

LOCK TABLES `contact_website_links` WRITE;
/*!40000 ALTER TABLE `contact_website_links` DISABLE KEYS */;
INSERT INTO `contact_website_links` VALUES (1,'Web Site','asdasdsasf adgsfgsgfrgsg','fas fa-globe',0);
/*!40000 ALTER TABLE `contact_website_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`service_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (1,11,'2026-06-27 04:20:37'),(8,11,'2026-06-21 12:21:04');
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_messages`
--

DROP TABLE IF EXISTS `post_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `idx_post` (`post_id`),
  CONSTRAINT `post_messages_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  CONSTRAINT `post_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `post_messages_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_messages`
--

LOCK TABLES `post_messages` WRITE;
/*!40000 ALTER TABLE `post_messages` DISABLE KEYS */;
INSERT INTO `post_messages` VALUES (9,11,1,8,'Hello',0,'2026-06-22 17:29:53'),(10,11,8,1,'ok',0,'2026-06-22 17:30:43');
/*!40000 ALTER TABLE `post_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `post_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `reg_no` varchar(50) NOT NULL,
  `province` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `rating_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `unique_user_service` (`user_id`,`service_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (23,1,11,5,'2026-06-21 12:23:34','2026-06-21 12:23:34'),(24,8,11,5,'2026-06-21 15:13:02','2026-06-24 15:24:24');
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_options`
--

DROP TABLE IF EXISTS `report_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `option_text` varchar(255) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_options`
--

LOCK TABLES `report_options` WRITE;
/*!40000 ALTER TABLE `report_options` DISABLE KEYS */;
INSERT INTO `report_options` VALUES (1,'Spam / Misleading',1),(2,'Inappropriate Content',2),(3,'Fake or Fraudulent Listing',3),(4,'Duplicate Listing',4),(5,'Scam / Harassment',5),(6,'Other',99);
/*!40000 ALTER TABLE `report_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `user_id` int NOT NULL,
  `selected_options` varchar(500) DEFAULT NULL COMMENT 'JSON array of option ids',
  `custom_reason` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,10,1,'[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\"]','මේක test එකක්. ','2026-06-20 16:30:40'),(2,10,1,'[\"1\",\"2\"]','','2026-06-20 17:00:23');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `req_documents`
--

DROP TABLE IF EXISTS `req_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `req_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `req_documents`
--

LOCK TABLES `req_documents` WRITE;
/*!40000 ALTER TABLE `req_documents` DISABLE KEYS */;
INSERT INTO `req_documents` VALUES (1,'Vehicle Registration Certificate',1),(2,'Insurance Certificate',2),(3,'Revenue Licence',3),(4,'Driver Driving Licence',4);
/*!40000 ALTER TABLE `req_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_assistants`
--

DROP TABLE IF EXISTS `service_assistants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_assistants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `assistant_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_assistants_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_assistants`
--

LOCK TABLES `service_assistants` WRITE;
/*!40000 ALTER TABLE `service_assistants` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_assistants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_document_images`
--

DROP TABLE IF EXISTS `service_document_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_document_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `document_type_id` int DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `document_type_id` (`document_type_id`),
  CONSTRAINT `service_document_images_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_document_images`
--

LOCK TABLES `service_document_images` WRITE;
/*!40000 ALTER TABLE `service_document_images` DISABLE KEYS */;
INSERT INTO `service_document_images` VALUES (1,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_0_6107.png','2026-06-21 12:13:38'),(3,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_2_2630.png','2026-06-21 12:13:38'),(4,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_3_8715.png','2026-06-21 12:13:38'),(5,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_4_4562.jpg','2026-06-21 12:13:38'),(6,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun Perera A School Services_11_documents/doc_1782315396_0_5071.png','2026-06-24 15:36:36');
/*!40000 ALTER TABLE `service_document_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_emails`
--

DROP TABLE IF EXISTS `service_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_emails_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_emails`
--

LOCK TABLES `service_emails` WRITE;
/*!40000 ALTER TABLE `service_emails` DISABLE KEYS */;
INSERT INTO `service_emails` VALUES (36,11,'abcdenglishlearning011@gmail.com');
/*!40000 ALTER TABLE `service_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_images`
--

DROP TABLE IF EXISTS `service_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_mandatory` tinyint NOT NULL COMMENT '1=Front,2=Back,3=Left,4=Right,5=Seats,0=Optional',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  KEY `idx_service` (`service_id`),
  CONSTRAINT `service_images_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_images`
--

LOCK TABLES `service_images` WRITE;
/*!40000 ALTER TABLE `service_images` DISABLE KEYS */;
INSERT INTO `service_images` VALUES (38,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/1_1782044018_3404.png',1,1,'2026-06-21 12:13:38'),(39,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/2_1782044018_3765.png',2,2,'2026-06-21 12:13:38'),(40,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/3_1782044018_5188.png',3,3,'2026-06-21 12:13:38'),(42,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/5_1782044018_3784.png',5,5,'2026-06-21 12:13:38'),(43,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/6_1782044018_4895.png',6,6,'2026-06-21 12:13:38'),(44,11,'uploads/Sabaragamuwa/Ratnapura/Kasun Perera A School Services_11/4_1782315330_4680.png',4,4,'2026-06-24 15:35:30');
/*!40000 ALTER TABLE `service_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_schedules`
--

DROP TABLE IF EXISTS `service_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_schedules` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `place` varchar(255) NOT NULL,
  `time` time NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`schedule_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_schedules_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_schedules`
--

LOCK TABLES `service_schedules` WRITE;
/*!40000 ALTER TABLE `service_schedules` DISABLE KEYS */;
INSERT INTO `service_schedules` VALUES (13,11,'Morning Trip 1','Udawalawa Beriyar Junction','06:00:00',1),(14,11,'Afternoon Trip 1','R/ Emb / Embilipitiya Royal Collage','13:30:00',2);
/*!40000 ALTER TABLE `service_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_schools`
--

DROP TABLE IF EXISTS `service_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_schools` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `school_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_schools_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_schools`
--

LOCK TABLES `service_schools` WRITE;
/*!40000 ALTER TABLE `service_schools` DISABLE KEYS */;
INSERT INTO `service_schools` VALUES (39,11,'R/ Emb / Embilipitiya Royal Collage');
/*!40000 ALTER TABLE `service_schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_telephones`
--

DROP TABLE IF EXISTS `service_telephones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_telephones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_telephones_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_telephones`
--

LOCK TABLES `service_telephones` WRITE;
/*!40000 ALTER TABLE `service_telephones` DISABLE KEYS */;
INSERT INTO `service_telephones` VALUES (38,11,'0787216051');
/*!40000 ALTER TABLE `service_telephones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_websites`
--

DROP TABLE IF EXISTS `service_websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_websites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `website` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_websites_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_websites`
--

LOCK TABLES `service_websites` WRITE;
/*!40000 ALTER TABLE `service_websites` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_websites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `reg_no` varchar(50) NOT NULL,
  `vehicle_type` enum('Bus','Van','Three-wheeler') NOT NULL,
  `service_type` enum('School Transport','Private Institute Transport') NOT NULL,
  `owner` varchar(100) NOT NULL,
  `driver` varchar(100) NOT NULL,
  `driver_reg_no` varchar(50) NOT NULL,
  `province` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `home_town` varchar(100) NOT NULL,
  `areas_covered` text NOT NULL,
  `address` text NOT NULL,
  `description` text,
  `road_description` text,
  `has_morning` tinyint(1) DEFAULT '0',
  `morning_place` varchar(255) DEFAULT NULL,
  `morning_time` time DEFAULT NULL,
  `has_evening` tinyint(1) DEFAULT '0',
  `evening_place` varchar(255) DEFAULT NULL,
  `evening_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `reg_no` (`reg_no`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (11,8,'Kasun Perera A School Services','123456','Bus','Private Institute Transport','Kasun Perera','Kasun Perera','123456','Sabaragamuwa','Ratnapura','Udawalawa','Embilipitiya, Udawalawa, 96 Junction','N0 87, Samagi Maawatha, Walawa Junction, Kolambageara','','',0,NULL,NULL,0,NULL,NULL,'2026-06-21 12:13:38','2026-06-24 15:30:44','approved');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `bio` text,
  `photo` varchar(255) DEFAULT NULL,
  `folder` varchar(100) DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
INSERT INTO `team_members` VALUES (1,'Sithum Deemantha 01','Software Engineer','Bio is here dfdfd','uploads/Members/Sithum_Deemantha_01_1/Sithum_Deemantha_01.png','uploads/Members/Sithum_Deemantha_01_1',0);
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_audit_log`
--

DROP TABLE IF EXISTS `user_audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_audit_log` (
  `audit_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `changed_by_user_id` int NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  KEY `user_id` (`user_id`),
  KEY `changed_by_user_id` (`changed_by_user_id`),
  CONSTRAINT `user_audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_audit_log_ibfk_2` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_audit_log`
--

LOCK TABLES `user_audit_log` WRITE;
/*!40000 ALTER TABLE `user_audit_log` DISABLE KEYS */;
INSERT INTO `user_audit_log` VALUES (1,1,1,'PASSWORD_RESET','{\"reset_by_admin\":\"admin\"}','2026-06-21 16:07:38');
/*!40000 ALTER TABLE `user_audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_created_method`
--

DROP TABLE IF EXISTS `user_created_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_created_method` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `method` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_created_method_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_created_method`
--

LOCK TABLES `user_created_method` WRITE;
/*!40000 ALTER TABLE `user_created_method` DISABLE KEYS */;
INSERT INTO `user_created_method` VALUES (1,1,'Custom','2026-06-22 14:52:29'),(2,8,'Normal','2026-06-22 14:52:32'),(3,9,'Normal','2026-06-22 14:52:34'),(4,11,'Normal','2026-06-22 14:53:39'),(5,12,'Normal','2026-06-22 16:20:14'),(6,13,'Custom','2026-06-22 16:20:14'),(7,14,'Normal','2026-06-22 16:20:14'),(8,15,'Custom','2026-06-22 16:20:14'),(9,16,'Normal','2026-06-22 16:20:14'),(10,17,'Custom','2026-06-22 16:20:14'),(11,18,'Normal','2026-06-22 16:20:14'),(12,19,'Custom','2026-06-22 16:20:14'),(13,20,'Normal','2026-06-22 16:20:14'),(14,21,'Custom','2026-06-22 16:20:14'),(15,22,'Normal','2026-06-22 16:20:14'),(16,23,'Custom','2026-06-22 16:20:14'),(17,24,'Normal','2026-06-22 16:20:14'),(18,25,'Custom','2026-06-22 16:20:14'),(19,26,'Normal','2026-06-22 16:20:14'),(20,27,'Custom','2026-06-22 16:20:14'),(21,28,'Normal','2026-06-22 16:20:14'),(22,29,'Custom','2026-06-22 16:20:14'),(23,30,'Normal','2026-06-22 16:20:14'),(24,31,'Custom','2026-06-22 16:20:14'),(25,32,'Normal','2026-06-22 16:20:14'),(26,33,'Custom','2026-06-22 16:20:14'),(27,34,'Normal','2026-06-22 16:20:14'),(28,35,'Custom','2026-06-22 16:20:14'),(29,36,'Custom','2026-06-22 16:20:14'),(30,37,'Custom','2026-06-23 15:31:42'),(31,38,'Custom','2026-06-23 16:19:44');
/*!40000 ALTER TABLE `user_created_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `nic` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `district` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nic` (`nic`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','An Admin ( Top Level )','0787216051','deemanthasithum033@gmail.com','200132202326','Ratnapura','Sabaragamuwa','N0 87, Samagi Maawatha, Walawa Junction, Kolambageara','admin','$2y$10$BdGcv04jhv/0CMj4P97WKOdIF.t1QwYOh7fMXYpmOBwkjZ91.9Pai','2026-03-04 07:41:00'),(8,'kasun_owner','Kasun Perera','0771234567','kasun.owner@example.com','199012345678','Colombo','Western','No. 25, Galle Road, Colombo 03','Vehicle Owner','$2y$10$s.fumlGmozL9DlGgZWrt9OnTMEzed/QFompRyo4rW8pvcjdeqr5Au','2026-06-14 15:01:23'),(9,'nimali_parent','Nimali Fernando','0719876543','nimali.parent@example.com','198765432109','Gampaha','Western','No. 78, Negombo Road, Ja-Ela','Parents','$2y$10$Nt4jOiyYXRYk2tkg17qDneGGQf/Gl28wSZBvW2KQCgFlXfAchuNVK','2026-06-14 15:01:23'),(11,'exampleone123','test one','12345678','Example@gmail.com','4567245V','Ratnapura','Sabaragamuwa','N0 87, Samagi Maawatha, Walawa Junction, Kolambageara','admin','$2y$10$2fYOoDIc5F4SSpNpRAN4C.J39XtkF9luBZF17LIb8lT1c0lklQOE6','2026-06-22 14:53:39'),(12,'user01','Nimal Perera','0711234501','user01@gmail.com','200101001111','Colombo','Western','No 12, Colombo 03','Parents','$2y$10$zPZ4aU6VoNMIc9GVgfcIE.Es2KKh/.LzTwAU8qg67C5WoThPVeyby','2026-06-22 16:15:10'),(13,'user02','Sunil Silva','0711234502','user02@gmail.com','200101001112','Gampaha','Western','No 15, Gampaha','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(14,'user03','Kasun Fernando','0711234503','user03@gmail.com','200101001113','Kalutara','Western','No 20, Kalutara','Parents','$2y$10$GVMbEieuJ5hhUSNyOk2jquU4CO/U9ZkHx2XqeD4aQDBU4xNEU9CTW','2026-06-22 16:15:10'),(15,'user04','Chathura Jayasinghe','0711234504','user04@gmail.com','200101001114','Kandy','Central','No 18, Kandy','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(16,'user05','Ruwan Kumara','0711234505','user05@gmail.com','200101001115','Matale','Central','No 25, Matale','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(17,'user06','Dinesh Wijeratne','0711234506','user06@gmail.com','200101001116','Nuwara Eliya','Central','No 31, Nuwara Eliya','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(18,'user07','Amila Rathnayake','0711234507','user07@gmail.com','200101001117','Galle','Southern','No 10, Galle','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(19,'user08','Pradeep Madushan','0711234508','user08@gmail.com','200101001118','Matara','Southern','No 22, Matara','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(20,'user09','Lahiru Sampath','0711234509','user09@gmail.com','200101001119','Hambantota','Southern','No 05, Hambantota','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(21,'user10','Tharindu Lakshan','0711234510','user10@gmail.com','200101001120','Jaffna','Northern','No 14, Jaffna','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(22,'user11','Saman Bandara','0711234511','user11@gmail.com','200101001121','Kilinochchi','Northern','No 19, Kilinochchi','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(23,'user12','Gayan Perera','0711234512','user12@gmail.com','200101001122','Mannar','Northern','Vehicle Depot Road','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(24,'user13','Harsha De Silva','0711234513','user13@gmail.com','200101001123','Vavuniya','Northern','No 44, Vavuniya','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(25,'user14','Nuwan Hettiarachchi','0711234514','user14@gmail.com','200101001124','Batticaloa','Eastern','No 27, Batticaloa','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(26,'user15','Kamal Rajapaksha','0711234515','user15@gmail.com','200101001125','Ampara','Eastern','No 51, Ampara','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(27,'user16','Ravindu Jayawardena','0711234516','user16@gmail.com','200101001126','Trincomalee','Eastern','No 61, Trincomalee','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(28,'user17','Sachintha Abeysekara','0711234517','user17@gmail.com','200101001127','Kurunegala','North Western','No 11, Kurunegala','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(29,'user18','Dulaj Fernando','0711234518','user18@gmail.com','200101001128','Puttalam','North Western','No 78, Puttalam','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(30,'user19','Malith Gunasekara','0711234519','user19@gmail.com','200101001129','Anuradhapura','North Central','No 21, Anuradhapura','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(31,'user20','Janaka Weerasinghe','0711234520','user20@gmail.com','200101001130','Polonnaruwa','North Central','No 99, Polonnaruwa','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(32,'user21','Shehan Peris','0711234521','user21@gmail.com','200101001131','Badulla','Uva','No 101, Badulla','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(33,'user22','Ishan Wickramasinghe','0711234522','user22@gmail.com','200101001132','Moneragala','Uva','No 66, Moneragala','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(34,'user23','Roshan Gunawardena','0711234523','user23@gmail.com','200101001133','Ratnapura','Sabaragamuwa','No 55, Ratnapura','Parents','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(35,'user24','Asanka Wijesinghe','0711234524','user24@gmail.com','200101001134','Kegalle','Sabaragamuwa','No 88, Kegalle','Vehicle Owner','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(36,'admin01','System Admin','0711234525','admin01@gmail.com','200101001135','Colombo','Western','Admin Office','admin','$2y$10$abcdefghijklmnopqrstuv','2026-06-22 16:15:10'),(37,'example11111112','example1111111','0711231459','asdfdgfd@fge.com','125975465','Galle','Southern','dgrfyghfrfrh','admin','$2y$10$C7G1X5XSbjS9NtjizwSqNOJPiZtR/oV9bYAplT/d4IM2K1lgkE7R6','2026-06-23 15:31:42'),(38,'example1111111454','example11111114545','0711431459','asdf545dgfd@fge.com','45444545454545454','Matara','Southern','45454545','Parents','$2y$10$wOhQ8IRMr5M/E63jNHFRk.XvxEx9gvbvBcHcvkqj2rCgAKsz94..W','2026-06-23 16:19:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-27 13:47:04
