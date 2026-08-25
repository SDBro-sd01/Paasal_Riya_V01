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
  PRIMARY KEY (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_service` (`service_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2057 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (2056,1,11,'A Super Service','2026-06-21 12:23:50','2026-06-21 12:23:50');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
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
INSERT INTO `favorites` VALUES (1,11,'2026-06-21 15:15:50'),(8,11,'2026-06-21 12:21:04');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_messages`
--

LOCK TABLES `post_messages` WRITE;
/*!40000 ALTER TABLE `post_messages` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (23,1,11,5,'2026-06-21 12:23:34','2026-06-21 12:23:34'),(24,8,11,5,'2026-06-21 15:13:02','2026-06-21 15:13:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_document_images`
--

LOCK TABLES `service_document_images` WRITE;
/*!40000 ALTER TABLE `service_document_images` DISABLE KEYS */;
INSERT INTO `service_document_images` VALUES (1,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_0_6107.png','2026-06-21 12:13:38'),(2,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_1_8675.png','2026-06-21 12:13:38'),(3,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_2_2630.png','2026-06-21 12:13:38'),(4,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_3_8715.png','2026-06-21 12:13:38'),(5,11,NULL,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11_documents/doc_1782044018_4_4562.jpg','2026-06-21 12:13:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_emails`
--

LOCK TABLES `service_emails` WRITE;
/*!40000 ALTER TABLE `service_emails` DISABLE KEYS */;
INSERT INTO `service_emails` VALUES (32,11,'abcdenglishlearning011@gmail.com');
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_images`
--

LOCK TABLES `service_images` WRITE;
/*!40000 ALTER TABLE `service_images` DISABLE KEYS */;
INSERT INTO `service_images` VALUES (38,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/1_1782044018_3404.png',1,1,'2026-06-21 12:13:38'),(39,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/2_1782044018_3765.png',2,2,'2026-06-21 12:13:38'),(40,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/3_1782044018_5188.png',3,3,'2026-06-21 12:13:38'),(41,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/4_1782044018_7697.png',4,4,'2026-06-21 12:13:38'),(42,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/5_1782044018_3784.png',5,5,'2026-06-21 12:13:38'),(43,11,'uploads/Sabaragamuwa/Ratnapura/Kasun School Services_11/6_1782044018_4895.png',6,6,'2026-06-21 12:13:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_schedules`
--

LOCK TABLES `service_schedules` WRITE;
/*!40000 ALTER TABLE `service_schedules` DISABLE KEYS */;
INSERT INTO `service_schedules` VALUES (5,11,'Morning Trip 1','Udawalawa Beriyar Junction','06:00:00',1),(6,11,'Afternoon Trip 1','R/ Emb / Embilipitiya Royal Collage','13:30:00',2);
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_schools`
--

LOCK TABLES `service_schools` WRITE;
/*!40000 ALTER TABLE `service_schools` DISABLE KEYS */;
INSERT INTO `service_schools` VALUES (35,11,'R/ Emb / Embilipitiya Royal Collage');
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_telephones`
--

LOCK TABLES `service_telephones` WRITE;
/*!40000 ALTER TABLE `service_telephones` DISABLE KEYS */;
INSERT INTO `service_telephones` VALUES (34,11,'0787216051');
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
INSERT INTO `services` VALUES (11,8,'Kasun Perera School Services','123456','Bus','Private Institute Transport','Kasun Perera','Kasun Perera','123456','Sabaragamuwa','Ratnapura','Udawalawa','Embilipitiya, Udawalawa, 96 Junction','N0 87, Samagi Maawatha, Walawa Junction, Kolambageara','','',0,NULL,NULL,0,NULL,NULL,'2026-06-21 12:13:38','2026-06-21 15:07:14','approved');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','An Admin','0787216051','deemanthasithum033@gmail.com','200132202326','Ratnapura','Sabaragamuwa','N0 87, Samagi Maawatha, Walawa Junction, Kolambageara','admin','$2y$10$j.2/3UoOpYDVH9/yvq/jyuvd/xWQtL7S0N8xlvcaQpMXC3MnbkC8K','2026-03-04 07:41:00'),(8,'kasun_owner','Kasun Perera','0771234567','kasun.owner@example.com','199012345678','Colombo','Western','No. 25, Galle Road, Colombo 03','Vehicle Owner','$2y$10$nV7N1dPlDseZ2zcoTUH3mOS3on.aakX7lnIrc3rJSzUyqBYS.FmGO','2026-06-14 15:01:23'),(9,'nimali_parent','Nimali Fernando','0719876543','nimali.parent@example.com','198765432109','Gampaha','Western','No. 78, Negombo Road, Ja-Ela','Parents','$2y$10$Nt4jOiyYXRYk2tkg17qDneGGQf/Gl28wSZBvW2KQCgFlXfAchuNVK','2026-06-14 15:01:23');
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

-- Dump completed on 2026-06-22 20:11:47
