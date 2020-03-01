-- MySQL dump 10.13  Distrib 5.7.26, for Linux (x86_64)
--
-- Host: localhost    Database: cstest
-- ------------------------------------------------------
-- Server version	5.7.26-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `kh4tr_assets`
--

DROP TABLE IF EXISTS `kh4tr_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kh4tr_assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `desc` varchar(1000) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `state` tinyint(4) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kh4tr_assets`
--

LOCK TABLES `kh4tr_assets` WRITE;
/*!40000 ALTER TABLE `kh4tr_assets` DISABLE KEYS */;
INSERT INTO `kh4tr_assets` VALUES (1,'Oranges','oranges','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.','pic-oranges.jpg',1,'2020-02-29 14:09:07'),(2,'Lemons','lemons','Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.','pic-lemons.jpg',1,'2020-02-29 14:11:01'),(3,'Pomeloes','pomeloes','Facilisi etiam dignissim diam quis enim. In vitae turpis massa sed elementum tempus egestas sed sed.','pic-pomeloes.jpg',1,'2020-02-29 14:11:57'),(4,'Limes','limes','Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.','pic-limes.jpg',1,'2020-02-29 14:12:43'),(5,'Clementines','clementines','Vulputate mi sit amet mauris commodo quis imperdiet. Nulla porttitor massa id neque aliquam vestibulum.','pic-clementines.jpg',1,'2020-02-29 14:13:45'),(6,'Bananas','bananas','Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.','pic-bananas.jpg',1,'2020-02-29 14:14:28'),(7,'Još narandži','more-oranges','Još narandži - Test opis srpskom latinicom.','pic-oranges.jpg',1,'2020-02-29 14:25:27'),(8,'Jош лимуна','more-lemons','Jош лимуна - Тест опис српском ћирилицом.','pic-lemons.jpg',1,'2020-02-29 14:26:44'),(9,'Još pomeloa','more-pomeloes','Još pomeloa - Test opis srpskom latinicom.','pic-pomeloes.jpg',1,'2020-02-29 14:29:51');
/*!40000 ALTER TABLE `kh4tr_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kh4tr_comments`
--

DROP TABLE IF EXISTS `kh4tr_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kh4tr_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255) NOT NULL,
  `text` varchar(500) DEFAULT NULL,
  `parent` int(10) unsigned DEFAULT NULL,
  `state` tinyint(4) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kh4tr_comments`
--

LOCK TABLES `kh4tr_comments` WRITE;
/*!40000 ALTER TABLE `kh4tr_comments` DISABLE KEYS */;
INSERT INTO `kh4tr_comments` VALUES (1,'Админ','Здраво!',0,1,'2020-02-29 14:06:57'),(2,'John','What\'s the price?',1,1,'2020-02-29 15:07:59'),(3,'Admin','To be announced soon.',1,1,'2020-02-29 15:10:45'),(4,'John','How soon is that \'soon\'?',1,1,'2020-02-29 15:12:02'),(5,'Admin','Very soon sir :)',1,1,'2020-02-29 15:12:27'),(6,'John','Nice frontpage comments though',0,1,'2020-02-29 15:46:42'),(7,'Vasa','U čemu je razlika između ovih narandži i onih sa naslovom \'Orange\'?',7,1,'2020-02-29 19:55:00'),(8,'Maria','Where does it come from?',1,1,'2020-02-29 19:56:51'),(9,'Admin','We have various suppliers from different countries.',1,1,'2020-02-29 19:57:37'),(10,'Admin','Uglavnom u tome da ste dobrodošli da komentarišete na srpskom na ovoj strani.',7,1,'2020-02-29 20:00:36'),(11,'ABC','Nice',3,1,'2020-02-29 20:02:58'),(12,'BCA','Very nice :)',0,0,'2020-02-29 20:14:55'),(13,'BCA','Very nice :)',0,0,'2020-02-29 20:20:18'),(14,'BCA','Very nice :)',0,0,'2020-02-29 20:24:45');
/*!40000 ALTER TABLE `kh4tr_comments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-01  1:43:38
