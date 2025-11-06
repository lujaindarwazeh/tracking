CREATE DATABASE  IF NOT EXISTS `tracking` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `tracking`;
-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tracking
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_name_unique` (`name`),
  UNIQUE KEY `company_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company`
--

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
INSERT INTO `company` VALUES (1,'2025-10-26 07:56:11','2025-10-26 07:56:11','ITG','0597406783','s12027928@stu.najah.edu'),(2,'2025-10-27 07:22:51','2025-10-27 07:22:51','Pits','059740677','s12027735@stu.najah.edu');
/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companyevent`
--

DROP TABLE IF EXISTS `companyevent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companyevent` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `companyevent_company_id_foreign` (`company_id`),
  KEY `companyevent_event_id_foreign` (`event_id`),
  CONSTRAINT `companyevent_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE,
  CONSTRAINT `companyevent_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companyevent`
--

LOCK TABLES `companyevent` WRITE;
/*!40000 ALTER TABLE `companyevent` DISABLE KEYS */;
INSERT INTO `companyevent` VALUES (1,NULL,NULL,1,1),(2,NULL,NULL,1,2),(3,NULL,NULL,1,3),(4,NULL,NULL,2,5);
/*!40000 ALTER TABLE `companyevent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companyusers`
--

DROP TABLE IF EXISTS `companyusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companyusers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Manager','Employee','Admin','Driver') NOT NULL DEFAULT 'Employee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `recipient_id` bigint(20) unsigned DEFAULT NULL,
  `location` point NOT NULL DEFAULT point(0,0),
  `speed` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companyusers_email_unique` (`email`),
  KEY `companyusers_company_id_foreign` (`company_id`),
  KEY `companyusers_created_by_foreign` (`created_by`),
  KEY `companyusers_recipient_id_foreign` (`recipient_id`),
  CONSTRAINT `companyusers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE,
  CONSTRAINT `companyusers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `companyusers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `companyusers_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `companyusers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companyusers`
--

LOCK TABLES `companyusers` WRITE;
/*!40000 ALTER TABLE `companyusers` DISABLE KEYS */;
INSERT INTO `companyusers` VALUES (1,'Lujain','darwazeh','lujain.darwazeh123@gmail.com','$2y$12$EtrQhbuhP5Ah8tfcInbvHO4KE49LuJvtxEwBZl.tYgY/8nsoSObvy','Admin','2025-10-26 08:30:09','2025-10-26 08:30:09',1,NULL,NULL,_binary '\0\0\0\0\0\0\0\r\à- A@£’:M@@',NULL),(3,'luje','darwazeh','s12027928@stu.najah.edu','$2y$12$vqI3.xzCi1StRmvjhIpAeeXISUE.h1JBYsi1UqX9yc0zO4lmxMVW6','Manager','2025-10-26 09:00:48','2025-10-26 09:00:48',1,1,NULL,_binary '\0\0\0\0\0\0\0\r\à- A@£’:M@@',NULL),(4,'jana','darwazeh','jana@gmail.com','$2y$12$MK1tSILqKhhqoPwlVcyM6uWD.9Y8Z9dJCxj/zXyzbR6tLRXpMq35K','Employee','2025-10-26 09:04:36','2025-10-26 09:04:36',1,1,3,_binary '\0\0\0\0\0\0\0\r\à- @@£’:MœA@',NULL),(5,'lana','darwazeh','lana@gmail.com','$2y$12$iChQx2.VIqMuiySWzNkgsuJEt5tisQnLb0lNfteEZ.bVbFT/4tLsK','Employee','2025-10-26 11:53:43','2025-10-26 11:53:43',1,1,3,_binary '\0\0\0\0\0\0\0\r\à- A@£’:M@@',NULL),(6,'aya','darwazeh','aya@gmail.com','$2y$12$YpnJVualhyawQ10zm0uvTuldgHiTALGcTXJzZR7nKpLv/wSQ16KV6','Employee','2025-10-26 12:21:59','2025-10-28 08:48:27',1,1,3,_binary '\0\0\0\0\0\0\0\r\à- A@£’:MC@',130),(7,'lama','darwazeh','lama@gmail.com','$2y$12$HxrqfHyp9YtyKRNAYKAd5.ktXTi2t3goKgTkZ1PySPEnt5C8OloC2','Admin','2025-10-27 07:24:44','2025-10-27 07:24:44',2,NULL,NULL,_binary '\0\0\0\0\0\0\0\r\à- A@£’:MC@',130),(8,'masa','darwazeh','masa@gmail.com','$2y$12$lAArskejU5LhY9xqOlgqEuIJYdGvGZYKTM5VjHWj7wYNp4uIe/jja','Employee','2025-10-27 07:26:50','2025-10-28 08:53:44',2,7,NULL,_binary '\0\0\0\0\0\0\0\r\à- A@£’:MC@',40),(9,'lolo','darwazeh','s12324383@stu.najeh.edu','$2y$12$RmjhKkKdQw06r6EDPW6/6.0TwqOFET9b4Dt6n.l8oSocr2Wh8Mx3K','Employee','2025-10-28 09:03:17','2025-10-28 09:08:31',2,7,NULL,_binary '\0\0\0\0\0\0\0\r\à- A@\\\Âõ(C@',40);
/*!40000 ALTER TABLE `companyusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `devicename` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `device_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device`
--

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;
/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES (1,'2025-10-26 09:28:10','2025-10-26 09:28:10','enter zone A'),(2,'2025-10-26 11:58:20','2025-10-26 11:58:20','enter zone B'),(3,'2025-10-26 12:09:05','2025-10-26 12:09:05','speed more than 120'),(4,'2025-10-26 18:51:05','2025-10-26 18:51:05','enter zone C'),(5,'2025-10-27 07:21:28','2025-10-27 07:21:28','speed more than 90');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (21,'default','{\"uuid\":\"df942ff6-68a4-42b9-8bc8-08d3408427b2\",\"displayName\":\"App\\\\Jobs\\\\CheckUserLocationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\CheckUserLocationJob\",\"command\":\"O:29:\\\"App\\\\Jobs\\\\CheckUserLocationJob\\\":0:{}\"}}',0,NULL,1761373691,1761373691);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_10_18_070929_create_vehicle_table',1),(5,'2025_10_18_071508_create_device_table',1),(6,'2025_10_18_072026_create_provider_table',1),(7,'2025_10_19_073739_create_ways_table',1),(8,'2025_10_19_073808_create_points_table',1),(9,'2025_10_20_075415_update_points_table_to_json',1),(10,'2025_10_20_090727_createtablepolygones',1),(11,'2025_10_22_151153_create_company_table',2),(13,'2025_10_22_151213_create_companyusers_table',3),(14,'2025_10_22_151235_create_zones_table',4),(15,'2025_10_22_151248_create_event_table',4),(17,'2025_10_22_161355_update_companyusers_table',5),(18,'2025_10_23_063714_update_zones_table',6),(19,'2025_10_25_123914_create_event_table',7),(20,'2025_10_25_141903_creat_userevent_table',8),(21,'2025_10_26_090820_creat_company_table',9),(22,'2025_10_26_091413_creat_companyusers_table',9),(23,'2025_10_26_093049_creat_zone_table',9),(24,'2025_10_26_093503_update_companyuser_table',9),(25,'2025_10_26_094523_update_event_table',9),(26,'2025_10_26_094725_update_userevent_table',9),(27,'2025_10_26_101459_update_companyuser_table',10),(28,'2025_10_26_101750_update_companyuser_table',11),(29,'2025_10_26_111038_update_zone_table',12),(30,'2025_10_26_141648_update_companyusers_table',13),(31,'2025_10_26_193121_create_companyevent_table',14),(32,'2025_10_27_144310_add_active_to_userevent_table',15);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `points`
--

DROP TABLE IF EXISTS `points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `points` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `way_id` bigint(20) unsigned NOT NULL,
  `locations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`locations`)),
  PRIMARY KEY (`id`),
  KEY `points_way_id_foreign` (`way_id`),
  CONSTRAINT `points_way_id_foreign` FOREIGN KEY (`way_id`) REFERENCES `ways` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `points`
--

LOCK TABLES `points` WRITE;
/*!40000 ALTER TABLE `points` DISABLE KEYS */;
INSERT INTO `points` VALUES (1,'2025-10-22 12:10:54','2025-10-22 12:10:54',1,'[{\"x\":36.25,\"y\":38.22},{\"x\":38.26,\"y\":37.23},{\"x\":39.27,\"y\":34.24}]');
/*!40000 ALTER TABLE `points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polygons`
--

DROP TABLE IF EXISTS `polygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polygons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `way_id` bigint(20) unsigned NOT NULL,
  `coordinate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`coordinate`)),
  `geometry` geometry NOT NULL,
  PRIMARY KEY (`id`),
  KEY `polygons_way_id_foreign` (`way_id`),
  CONSTRAINT `polygons_way_id_foreign` FOREIGN KEY (`way_id`) REFERENCES `ways` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polygons`
--

LOCK TABLES `polygons` WRITE;
/*!40000 ALTER TABLE `polygons` DISABLE KEYS */;
INSERT INTO `polygons` VALUES (1,'2025-10-22 12:10:59','2025-10-22 12:10:59',1,'[{\"x\":39.26999642956018,\"y\":34.23980003187264},{\"x\":39.26998662188998,\"y\":34.23980044793619},{\"x\":39.26997684644882,\"y\":34.23980134473813},{\"x\":39.2699671267866,\"y\":34.23980272011801},{\"x\":39.269957486318816,\"y\":34.239804570762395},{\"x\":39.26994794827022,\"y\":34.23980689221293},{\"x\":39.269938535618806,\"y\":34.239809678877045},{\"x\":39.26992927104047,\"y\":34.23981292404141},{\"x\":39.269920176854384,\"y\":34.239816619888146},{\"x\":39.26991127496925,\"y\":34.239820757513634},{\"x\":39.26990258683049,\"y\":34.239825326949976},{\"x\":39.26989413336859,\"y\":34.23983031718899},{\"x\":39.269885934948675,\"y\":34.23983571620876},{\"x\":39.26987801132145,\"y\":34.239841511002574},{\"x\":39.26987038157565,\"y\":34.239847687610265},{\"x\":39.269863064091986,\"y\":34.23985423115184},{\"x\":39.26985607649891,\"y\":34.239861125863335},{\"x\":39.269849435630164,\"y\":34.239868355134796},{\"x\":39.26984315748417,\"y\":34.23987590155027},{\"x\":39.26983725718553,\"y\":34.23988374692977},{\"x\":39.26983174894859,\"y\":34.23989187237311},{\"x\":39.269826646043164,\"y\":34.23990025830536},{\"x\":39.26982196076261,\"y\":34.2399088845241},{\"x\":39.26981770439417,\"y\":34.239917730248},{\"x\":39.269813887191816,\"y\":34.23992677416694},{\"x\":39.26981051835151,\"y\":34.23993599449332},{\"x\":38.259837104566586,\"y\":37.2298572887675},{\"x\":36.24991163003549,\"y\":38.219820582193265},{\"x\":36.249894469540685,\"y\":38.219830107910255},{\"x\":36.24988626044701,\"y\":38.21983549068693},{\"x\":36.24987832536189,\"y\":38.21984126978094},{\"x\":36.249870683401625,\"y\":38.21984743126996},{\"x\":36.24986335297637,\"y\":38.21985396031042},{\"x\":36.24985635174577,\"y\":38.21986084117327},{\"x\":36.24984969657639,\"y\":38.21986805728193},{\"x\":36.24984340350113,\"y\":38.21987559125215},{\"x\":36.249837487680544,\"y\":38.21988342493394},{\"x\":36.249831963366375,\"y\":38.21989153945528},{\"x\":36.249826843867176,\"y\":38.21989991526757},{\"x\":36.24982214151626,\"y\":38.21990853219275},{\"x\":36.24981786764202,\"y\":38.21991736947188},{\"x\":36.24981403254058,\"y\":38.2199264058152},{\"x\":36.249810645451035,\"y\":38.21993561945335},{\"x\":36.24980771453319,\"y\":38.21994498818988},{\"x\":36.24980524684787,\"y\":38.21995448945468},{\"x\":36.24980324833995,\"y\":38.21996410035837},{\"x\":36.24980172382402,\"y\":38.219973797747436},{\"x\":36.24980067697276,\"y\":38.219983558260026},{\"x\":36.249800110308136,\"y\":38.219993358382204},{\"x\":36.249800025195285,\"y\":38.220003174504626},{\"x\":36.249800421839254,\"y\":38.22001298297939},{\"x\":36.24980129928449,\"y\":38.22002276017702},{\"x\":36.24980265541716,\"y\":38.22003248254339},{\"x\":36.24980448697021,\"y\":38.22004212665646},{\"x\":36.249806789531284,\"y\":38.22005166928273},{\"x\":36.24980955755329,\"y\":38.22006108743317},{\"x\":36.24981278436783,\"y\":38.22007035841864},{\"x\":36.24981646220124,\"y\":38.220079459904504},{\"x\":36.249820582193266,\"y\":38.22008836996451},{\"x\":36.249825134418515,\"y\":38.22009706713352},{\"x\":36.249830107910256,\"y\":38.220105530459314},{\"x\":36.249835490686934,\"y\":38.220113739552986},{\"x\":36.249841269780944,\"y\":38.22012167463811},{\"x\":36.24984743126996,\"y\":38.220129316598374},{\"x\":36.24985396031042,\"y\":38.22013664702363},{\"x\":36.24986084117327,\"y\":38.22014364825423},{\"x\":36.24986805728193,\"y\":38.22015030342361},{\"x\":36.24987559125215,\"y\":38.22015659649887},{\"x\":36.24988342493394,\"y\":38.220162512319455},{\"x\":36.249891539455284,\"y\":38.22016803663362},{\"x\":36.24989991526757,\"y\":38.22017315613282},{\"x\":36.24990853219275,\"y\":38.22017785848374},{\"x\":36.24991736947188,\"y\":38.22018213235798},{\"x\":36.2499264058152,\"y\":38.22018596745942},{\"x\":36.24993561945335,\"y\":38.220189354548964},{\"x\":36.24994498818988,\"y\":38.22019228546681},{\"x\":36.24995448945468,\"y\":38.22019475315213},{\"x\":36.24996410035837,\"y\":38.22019675166005},{\"x\":36.24997379774744,\"y\":38.22019827617598},{\"x\":36.24998355826003,\"y\":38.22019932302724},{\"x\":36.249993358382206,\"y\":38.22019988969186},{\"x\":36.25000317450463,\"y\":38.220199974804714},{\"x\":36.25001298297939,\"y\":38.220199578160745},{\"x\":36.25002276017702,\"y\":38.22019870071551},{\"x\":36.25003248254339,\"y\":38.220197344582836},{\"x\":36.25004212665646,\"y\":38.220195513029786},{\"x\":36.25005166928273,\"y\":38.220193210468715},{\"x\":36.250061087433174,\"y\":38.22019044244671},{\"x\":36.25007035841864,\"y\":38.220187215632166},{\"x\":36.250079459904505,\"y\":38.22018353779876},{\"x\":36.25008836996451,\"y\":38.22017941780673},{\"x\":38.26008836996451,\"y\":37.23017941780673},{\"x\":38.26008872503075,\"y\":37.230179242486365},{\"x\":38.26009741316951,\"y\":37.23017467305002},{\"x\":38.26010586663141,\"y\":37.23016968281101},{\"x\":38.260114065051326,\"y\":37.23016428379124},{\"x\":38.26012198867855,\"y\":37.230158488997425},{\"x\":38.26012961842435,\"y\":37.230152312389734},{\"x\":38.260136935908015,\"y\":37.23014576884816},{\"x\":38.26014392350109,\"y\":37.230138874136664},{\"x\":38.26015056436984,\"y\":37.2301316448652},{\"x\":38.26015684251583,\"y\":37.23012409844973},{\"x\":38.26016274281447,\"y\":37.23011625307023},{\"x\":38.26016825105141,\"y\":37.23010812762689},{\"x\":38.26017335395684,\"y\":37.23009974169464},{\"x\":38.26017803923739,\"y\":37.2300911154759},{\"x\":38.26018229560583,\"y\":37.230082269751996},{\"x\":38.260186112808185,\"y\":37.23007322583306},{\"x\":38.26018948164849,\"y\":37.23006400550668},{\"x\":39.270189481648494,\"y\":34.24006400550668},{\"x\":39.2701948428793,\"y\":34.240045124853346},{\"x\":39.27019682235412,\"y\":34.24003551001154},{\"x\":39.270198327666655,\"y\":34.240025809623},{\"x\":39.27019935519047,\"y\":34.24001604705682},{\"x\":39.27019990245017,\"y\":34.24000624583188},{\"x\":39.270199968127365,\"y\":34.23999642956018},{\"x\":39.270199552063815,\"y\":34.239986621889976},{\"x\":39.27019865526187,\"y\":34.23997684644882},{\"x\":39.270197279882,\"y\":34.239967126786595},{\"x\":39.27019542923761,\"y\":34.239957486318815},{\"x\":39.27019310778707,\"y\":34.23994794827022},{\"x\":39.27019032112296,\"y\":34.239938535618805},{\"x\":39.270187075958596,\"y\":34.23992927104047},{\"x\":39.27018338011186,\"y\":34.23992017685438},{\"x\":39.27017924248637,\"y\":34.23991127496925},{\"x\":39.27017467305003,\"y\":34.23990258683049},{\"x\":39.270169682811016,\"y\":34.23989413336859},{\"x\":39.270164283791246,\"y\":34.239885934948674},{\"x\":39.27015848899743,\"y\":34.23987801132145},{\"x\":39.27015231238974,\"y\":34.23987038157565},{\"x\":39.27014576884817,\"y\":34.239863064091985},{\"x\":39.27013887413667,\"y\":34.23985607649891},{\"x\":39.27013164486521,\"y\":34.23984943563016},{\"x\":39.270124098449735,\"y\":34.23984315748417},{\"x\":39.270116253070235,\"y\":34.23983725718553},{\"x\":39.2701081276269,\"y\":34.23983174894859},{\"x\":39.270099741694644,\"y\":34.23982664604316},{\"x\":39.27009111547591,\"y\":34.23982196076261},{\"x\":39.270082269752,\"y\":34.23981770439417},{\"x\":39.270073225833066,\"y\":34.239813887191815},{\"x\":39.27006400550668,\"y\":34.23981051835151},{\"x\":39.27005463098543,\"y\":34.2398076059891},{\"x\":39.27004512485335,\"y\":34.239805157120706},{\"x\":39.27003551001154,\"y\":34.23980317764588},{\"x\":39.270025809623,\"y\":34.23980167233335},{\"x\":39.27001604705682,\"y\":34.239800644809534},{\"x\":39.27000624583188,\"y\":34.23980009754983},{\"x\":39.26999642956018,\"y\":34.23980003187264}]',_binary '\æ\0\0\0\0\0\0\0\0”\0\0\0¶5>¢C@ƒ<wÄ±A@ÿ¯\ïëŽ¢C@Þ¹ôÇ±A@cï™Ž¢C@ó—zÏ±A@’EfHŽ¢C@3Û±A@\\†‡÷¢C@‹mŠ\ê±A@±¸„§¢C@y´þ±A@\Ú4X¢C@Ed²A@­\×\n¢C@?õœ0²A@`Ž¾Œ¢C@zºO²A@Eh\ásŒ¢C@%6Sr²A@y\Åÿ*Œ¢C@W\0¨˜²A@¥ä‹¢C@@u„Â²A@¢ PŸ‹¢C@\Â\Ã\Î\ï²A@€D\Ø\\‹¢C@Yýj ³A@cƒ\×‹¢C@V\';T³A@3VußŠ¢C@\\M‹³A@H˜×¤Š¢C@•õÄ³A@p\"mŠ¢C@Sš´A@\Ñ8x8Š¢C@\ã \è@´A@VmùŠ¢C@\áó·‚´A@”\ÄØ‰¢C@˜5\áÆ´A@!,ö­‰¢C@›\Ü9\rµA@è›¨†‰¢C@‚†–UµA@œ ôb‰¢C@§’ÊŸµA@l¿\îB‰¢C@«=¨\ëµA@\á7¬&‰¢C@´½\09¶A@N3WB!C@w\0±ökB@=ò²\ZýB@þÀ±#C@s¿ŠüB@Jšd#C@z+\âEüB@€Á‘#C@L´QüB@˜<\Â#C@6¸6\ÃûB@µ\ëõ#C@OÀ¸…ûB@ô¶°,$C@¹ýJûB@PAif$C@\Û)ûB@\0»ñ¢$C@P”_\ÞúB@\î\Î$\â$C@s¿¬úB@\nƒ\Û#%C@¸h~úB@SP\íg%C@E	vSúB@\Ó;0®%C@„\Ð,úB@Šðxö%C@N½)úB@Úš@&C@ý\ëý\çùB@f@hŒ&C@\Ò3”\ËùB@Šc²\Ù&C@²\Zþ²ùB@\ê˜I(\'C@X\ÊJžùB@zhýw\'C@ÿ‡ùB@¨ªœ\È\'C@€\'½€ùB@­¦õ(C@\ïõwùB@81\Ök(C@\Ã\'4sùB@]\Ë¾(C@z`}rùB@¼Ác)C@\Î)\ÑuùB@\ÃK«b)C@mv-}ùB@\0«¯´)C@?¼ˆùB@pJ>*C@/÷\ê—ùB@«\Ý$W*C@…­;«ùB@ñ1§*C@¶õs\ÂùB@\ç\Ò2ö*C@\Ä}…\ÝùB@øC+C@”_üùB@\ÕgQ+C@¢1\ïúB@\Û+C@\îEúB@Z¤$,C@\îƒ\×núB@\é~k,C@´ðþ›úB@\âc\à¯,C@<sy\ÌúB@\Ûpò,C@¥%)\0ûB@&×‹2-C@˜\'\î6ûB@\r\Ï	p-C@ô±¦pûB@G\ÖÄª-C@¤+/­ûB@V´˜\â-C@’?b\ìûB@ûb.C@®ó.üB@\ÏI.C@÷À*rüB@¤{Zw.C@w¬m¸üB@†L¢.C@.a¶\0ýB@Ø¾¾\É.C@\ÃJ\ØJýB@Ò˜\í.C@\n±¥–ýB@_£\Ä\r/C@.\Ô\ï\ãýB@Š[.*/C@Ž	‡2þB@ªt\ÄB/C@\Ù:‚þB@\ÅwW/C@L\Ú\ÒþB@]ˆ;h/C@Q3$ÿB@\Ügu/C@Ü¡vÿB@m€\Í}/C@<I\ÈÿB@™gŽ‚/C@`2¡\Z\0 B@\â.Eƒ/C@g¼\èl\0 B@Žeñ/C@¤\í¾\0 B@\ï•x/C@»{ B@\Ó4m/C@ONba B@-˜\×]/C@•ðn± B@\×\á†J/C@‹Cp\0 B@¦™N3/C@´5N B@˜=/C@yØŽš B@Lûbù.C@\Ã\rM\å B@º]\Ó\Ö.C@¤ˆa“J!C@›\Ø\ç„vB@_\\–J!C@7YoƒvB@+«=\ßJ!C@\Z]vB@ÿV\'&K!C@\Z>3vB@P\íjK!C@š\ËóvB@$,e­K!C@’W\ÕuB@A\íe\íK!C@h‡¡uB@q\Z\È*L!C@\0B£juB@\\\ØeeL!C@Eú\Ì0uB@\0L!C@A<(ôtB@\Ó7\Å\ÑL!C@ynÚ´tB@NDM!C@{›\nstB@–\Üx1M!C@\ÄY\á.tB@ƒDG\\M!C@Á²ˆ\èsB@¼Ô”ƒM!C@\Ú, sB@PI§M!C@µü÷UsB@8±N\ÇM!C@±Q\Z\nsB@\Ã8‘\ãM!C@¨\ÑÁ¼rB@¥³¥‘•¢C@ŠL\ÖjºA@sÛž¾•¢C@™gtÌ¹A@>¿9Ï•¢C@j°\Ì{¹A@a\ÚÛ•¢C@kCm*¹A@Àøxä•¢C@\çOˆØ¸A@ô3é•¢C@‚WP†¸A@_>é•¢C@ø3¸A@Áæ•¢C@[?²\á·A@\ï\â™Þ•¢C@¿¥±·A@\ÚGÓ•¢C@\î\Ô(>·A@W\rŠÃ•¢C@¸J\í¶A@i\Æ°•¢C@\rHG¶A@u°˜•¢C@6\ÄQN¶A@£…w}•¢C@h<š\0¶A@hÀv^•¢C@¼žP´µA@½DÁ;•¢C@¡÷£iµA@‹zl•¢C@\ÕT\Â µA@¢ë”¢C@©\ØÙ´A@ ·E¾”¢C@þ¯•´A@‰}©”¢C@\ÜÓšR´A@ŒS\ÙY”¢C@¿š´A@†-õ\"”¢C@\å7Õ³A@\Ë\åé“¢C@¤\'šš³A@\Ç\'z¬“¢C@pÿ\äb³A@ÿY,m“¢C@-\È:.³A@‡\\+“¢C@²ü»ü²A@JE3ç’¢C@j#‡Î²A@GžÚ ’¢C@}»¸£²A@`ô}X’¢C@D+k|²A@;\èI’¢C@ø¯¶X²A@7=lÂ‘¢C@\ÈN±8²A@.½u‘¢C@=\Çn²A@žp&‘¢C@‡\0²A@=Ø±Ö¢C@oŸu\ï±A@!\n†¢C@¤»\ÚÞ±A@´ª4¢C@S:Ò±A@‹À\Åâ¢C@\"‚›É±A@&È¢C@\îFÅ±A@¶5>¢C@ƒ<wÄ±A@');
/*!40000 ALTER TABLE `polygons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider`
--

DROP TABLE IF EXISTS `provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `device_id` bigint(20) unsigned NOT NULL,
  `providername` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_device_id_foreign` (`device_id`),
  CONSTRAINT `provider_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider`
--

LOCK TABLES `provider` WRITE;
/*!40000 ALTER TABLE `provider` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5i6rnxBqRaWNzKIFXUbWL1VFuwgA9sVGjAHhGeYX',NULL,'127.0.0.1','PostmanRuntime/7.49.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzBreE9GaWdab3ZzVEJybWREVmFQeHhSQko3TndROENuemxYQWtaMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1761204407),('p7zwwYGA3mlp4DcvT8e0qWpOQMIDGWKmsASKLoQY',NULL,'127.0.0.1','PostmanRuntime/7.49.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTzNON2ZQRjRMYWJYUUZlOGtSaXJEeHFsVmlCd1VpRkR2T3ZTRXRIWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1761153124);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userevent`
--

DROP TABLE IF EXISTS `userevent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `userevent` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `userevent_user_id_foreign` (`user_id`),
  KEY `userevent_event_id_foreign` (`event_id`),
  CONSTRAINT `userevent_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE,
  CONSTRAINT `userevent_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `companyusers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userevent`
--

LOCK TABLES `userevent` WRITE;
/*!40000 ALTER TABLE `userevent` DISABLE KEYS */;
INSERT INTO `userevent` VALUES (1,'2025-10-26 10:00:02','2025-10-26 10:00:02',4,1,1),(2,'2025-10-26 11:53:54','2025-10-26 11:53:54',4,1,1),(3,'2025-10-26 11:58:41','2025-10-26 11:58:41',3,2,1),(4,'2025-10-26 11:58:44','2025-10-26 11:58:44',5,2,1),(5,'2025-10-26 19:33:31','2025-10-26 19:33:31',4,1,1),(6,'2025-10-26 19:33:46','2025-10-26 19:33:46',3,2,1),(7,'2025-10-26 19:33:48','2025-10-26 19:33:48',5,2,1),(8,'2025-10-26 19:33:52','2025-10-26 19:33:52',6,2,0),(9,'2025-10-27 08:05:28','2025-10-27 08:05:28',4,1,1),(10,'2025-10-27 08:05:42','2025-10-27 08:05:42',3,2,1),(11,'2025-10-27 08:05:43','2025-10-27 08:05:43',5,2,1),(12,'2025-10-27 08:05:46','2025-10-27 08:05:46',6,2,0),(13,'2025-10-27 08:07:41','2025-10-27 08:07:41',6,3,0),(14,'2025-10-27 17:46:16','2025-10-27 17:46:16',6,3,1),(15,'2025-10-28 08:53:44','2025-10-28 08:53:44',8,1,1),(16,'2025-10-28 08:53:57','2025-10-28 08:53:57',8,2,1),(17,'2025-10-28 09:03:40','2025-10-28 09:03:40',9,1,0),(18,'2025-10-28 09:03:46','2025-10-28 09:03:46',9,2,0);
/*!40000 ALTER TABLE `userevent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `namecar` varchar(100) NOT NULL,
  `serialnumber` varchar(50) NOT NULL,
  `lastupdatetime` datetime DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `odometer` double DEFAULT NULL,
  `drivername` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle`
--

LOCK TABLES `vehicle` WRITE;
/*!40000 ALTER TABLE `vehicle` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ways`
--

DROP TABLE IF EXISTS `ways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ways`
--

LOCK TABLES `ways` WRITE;
/*!40000 ALTER TABLE `ways` DISABLE KEYS */;
INSERT INTO `ways` VALUES (1,'2025-10-22 12:10:49','2025-10-22 12:10:49','main road');
/*!40000 ALTER TABLE `ways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zone`
--

DROP TABLE IF EXISTS `zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zone` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `radius` double NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `center_coordinates` point NOT NULL DEFAULT point(0,0),
  PRIMARY KEY (`id`),
  KEY `zone_company_id_foreign` (`company_id`),
  CONSTRAINT `zone_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zone`
--

LOCK TABLES `zone` WRITE;
/*!40000 ALTER TABLE `zone` DISABLE KEYS */;
INSERT INTO `zone` VALUES (4,'2025-10-26 09:16:45','2025-10-26 09:16:45','A','Nablus AL jadedeh',30,1,_binary '\0\0\0\0\0\0\0\r\à- @@£’:MœA@'),(5,'2025-10-26 09:24:19','2025-10-26 09:24:19','B','Nablus-Rafidia',30,1,_binary '\0\0\0\0\0\0\0\r\à- A@£’:M@@'),(6,'2025-10-27 07:29:14','2025-10-27 07:29:14','A','Nablus AL jadedeh',30,2,_binary '\0\0\0\0\0\0\0\r\à- A@£’:MC@'),(7,'2025-10-27 07:29:32','2025-10-27 07:29:32','B','Nablus-Rafidia',30,2,_binary '\0\0\0\0\0\0\0\r\à- A@£’:MC@');
/*!40000 ALTER TABLE `zone` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-28 19:04:53
