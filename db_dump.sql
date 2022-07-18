-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: portal
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,3,1,'Comment Content +','2022-05-07 17:43:43',1),(2,3,2,'Another Comment','2022-05-07 19:27:06',0),(3,3,1,'test edit 3','2022-05-07 20:32:54',1),(4,3,1,'dd :-o\n:-)\n','2022-05-14 15:55:19',1);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20220430193912','2022-04-30 21:43:06',59),('DoctrineMigrations\\Version20220501151837','2022-05-01 17:20:09',60),('DoctrineMigrations\\Version20220501164350','2022-05-01 18:44:07',36),('DoctrineMigrations\\Version20220501175548','2022-05-01 19:59:56',51),('DoctrineMigrations\\Version20220501180511','2022-05-01 20:05:14',34),('DoctrineMigrations\\Version20220503141521','2022-05-03 16:16:07',49),('DoctrineMigrations\\Version20220503215649','2022-05-03 23:58:26',580),('DoctrineMigrations\\Version20220504130802','2022-05-04 15:13:16',35),('DoctrineMigrations\\Version20220506160520','2022-05-06 18:05:46',151),('DoctrineMigrations\\Version20220506185610','2022-05-06 20:56:38',38),('DoctrineMigrations\\Version20220507154245','2022-05-07 17:42:53',157),('DoctrineMigrations\\Version20220507154629','2022-05-07 17:46:33',60),('DoctrineMigrations\\Version20220507162512','2022-05-07 18:25:20',36),('DoctrineMigrations\\Version20220508004941','2022-05-08 02:49:58',38),('DoctrineMigrations\\Version20220515202722','2022-05-15 22:27:56',151);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friendships`
--

DROP TABLE IF EXISTS `friendships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friendships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `u1last` datetime NOT NULL,
  `u2last` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friendships`
--

LOCK TABLES `friendships` WRITE;
/*!40000 ALTER TABLE `friendships` DISABLE KEYS */;
INSERT INTO `friendships` VALUES (13,1,2,2,'2022-05-17 23:36:25','2022-05-17 01:16:57'),(15,1,3,2,'2022-05-17 23:36:30','2022-05-17 01:11:12'),(21,4,1,2,'2022-05-10 00:59:12','2022-05-17 23:36:24'),(22,5,1,1,'2022-05-17 22:53:26','2022-05-17 22:53:26');
/*!40000 ALTER TABLE `friendships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL,
  `recipient` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,2,'test','2022-05-15 22:33:34'),(2,1,2,'test','2022-05-15 23:16:26'),(3,2,1,'test2','2022-05-15 23:16:39'),(4,1,3,'test3','2022-05-16 12:15:21'),(5,1,3,'test 4','2022-05-16 12:35:17'),(6,1,3,'test 5','2022-05-16 12:35:26'),(7,1,2,'test','2022-05-16 12:36:19'),(8,1,2,'s c','2022-05-16 12:45:59'),(9,1,2,'c c c','2022-05-16 12:53:31'),(10,2,1,'s','2022-05-16 12:57:40'),(11,1,2,'t','2022-05-16 12:57:53'),(12,2,1,'ping','2022-05-16 12:58:28'),(13,1,2,'pong','2022-05-16 12:58:40'),(14,1,2,'test','2022-05-16 13:39:19'),(15,1,2,'s','2022-05-16 13:40:14'),(16,2,1,'test','2022-05-16 13:45:38'),(17,1,2,'test','2022-05-16 13:45:45'),(18,1,2,'&lt;br&gt;','2022-05-17 00:06:00'),(19,2,1,'&lt;hr&gt;','2022-05-17 00:10:25'),(20,2,1,'&quot;','2022-05-17 00:15:52'),(21,2,1,'test','2022-05-17 00:23:41'),(22,2,1,'+','2022-05-17 00:37:22'),(23,2,1,'test','2022-05-17 00:41:51'),(24,2,1,'test','2022-05-17 00:44:04'),(25,2,1,'test','2022-05-17 01:09:18'),(26,3,1,'test','2022-05-17 01:10:18');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'Test','Test Content!',1,'2022-05-01 15:07:58',0,NULL),(2,'Test 2','test content',1,'2022-05-01 18:18:04',0,NULL),(3,'All Test','### Lorem ipsum dolor sit amet, consectetur adipiscing elit. \r\nAenean efficitur porttitor pretium. Nullam non urna eu enim feugiat vestibulum. Ut blandit rutrum urna, a viverra arcu mattis vel. Vestibulum tempor urna nisl. Quisque semper orci vel turpis tempus volutpat. Phasellus ultrices euismod metus, in accumsan leo lobortis vel. Nam ac risus feugiat orci lacinia varius. Duis dapibus congue libero ut ultricies.\r\n* [link](example.com)\r\n* 2\r\n* B-)\r\n\r\nAenean pellentesque mattis arcu, vel tristique tellus varius non. Pellentesque diam tellus, rhoncus ac massa eu, aliquam maximus dolor. Praesent tristique felis at purus convallis, sed bibendum nisl auctor. Aliquam tempor facilisis nibh. Curabitur commodo ex a fringilla tempus.\r\n\r\n```javascript\r\nlet s = \"String\";\r\nalert(s);\r\n```',1,'2022-05-01 18:18:48',1,'[\"\\/uploads\\/62840cb251e44.jpg\",\"\\/uploads\\/62840cc50c444.jpg\"]'),(4,'Test 4','Test Content!',2,'2022-05-01 18:27:40',0,NULL),(5,'test','test content',1,'2022-05-01 19:58:32',0,NULL),(6,'Test rem','removed post',1,'2022-05-01 20:00:55',2,NULL),(7,'Test 5','Test Content!!!',1,'2022-05-01 20:02:13',0,NULL),(8,'Title','Lorem ipsum dolor sit amet, consectetur adipiscing elit. :) Vivamus eu risus pretium, euismod sapien id, luctus ligula. Nunc sit amet ultrices ipsum, a mattis metus. In placerat leo a neque fermentum porttitor. Proin non scelerisque orci. Nunc imperdiet condimentum est, vitae rhoncus justo rhoncus et. Duis vitae nisi at sem malesuada venenatis non ac sem. Nunc ligula erat, interdum sit amet rhoncus at, cursus ut nisl. Ut accumsan dignissim leo, a tincidunt ipsum ullamcorper in. Nulla rutrum ipsum a risus egestas ultrices. Morbi et gravida metus, vel accumsan elit. Aliquam erat volutpat. Sed nec enim quis sem condimentum mattis faucibus non urna. Mauris dapibus, tortor aliquam scelerisque luctus, leo quam lacinia justo, ornare accumsan enim tellus quis massa.',3,'2022-05-01 20:05:51',0,'[\"\\/uploads\\/62840d7b6d389.jpg\"]'),(9,'test','ccc',1,'2022-05-04 19:54:04',1,NULL),(10,'test','| Url        | Text           | Link  |\r\n| ------------- |:-------------:| -----:|\r\n| example.com      | example | [example](example.com) |',4,'2022-05-04 20:11:24',1,NULL),(11,'Lorem ipsum','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sed erat at ex luctus feugiat ac et mauris. Morbi eleifend non massa nec interdum. Nulla finibus felis purus, nec feugiat ante interdum eget. Proin sit amet diam eu ipsum elementum tristique vel bibendum libero. Etiam eu facilisis ipsum.\r\n1. Praesent id libero suscipit\r\n2. lacinia dui vitae\r\n3. sagittis neque\r\n\r\nInteger eget ornare augue. Sed dictum ut diam at faucibus. Donec varius quis felis at scelerisque. Integer egestas neque eget massa tristique varius. Integer vitae scelerisque nulla, sit amet gravida dui. Etiam eget risus id nisl finibus sollicitudin. Aliquam in erat vel nisi interdum bibendum. Aenean fringilla non erat eu gravida. Mauris maximus porta felis nec ornare.',4,'2022-05-06 23:10:12',0,'[\"\\/uploads\\/62840ef48445e.jpg\",\"\\/uploads\\/62840efa1dad8.jpg\"]'),(12,'test','sc',1,'2022-05-08 20:21:16',1,NULL),(13,'new','test',1,'2022-05-08 20:33:38',1,NULL),(14,'test','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ac massa nunc. Vivamus efficitur arcu in dictum pulvinar. Morbi vitae odio ac urna condimentum semper. Mauris lobortis neque mauris, in egestas velit pellentesque sed. Donec id vehicula justo. Integer ullamcorper a orci at tristique. Nullam sit amet ipsum eget risus vulputate aliquet ornare *nec quam*.\r\n**Pellentesque quis sem rhoncus sem placerat ornare.**\r\nVestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;',1,'2022-05-13 22:42:30',0,NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reactions`
--

DROP TABLE IF EXISTS `reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reactions`
--

LOCK TABLES `reactions` WRITE;
/*!40000 ALTER TABLE `reactions` DISABLE KEYS */;
INSERT INTO `reactions` VALUES (1,3,2,1,'2022-05-06 18:07:13',0),(2,3,4,1,'2022-05-06 18:07:16',0),(3,3,3,-1,'2022-05-06 18:07:29',0),(15,2,1,1,'2022-05-08 03:35:50',1),(16,12,1,1,'2022-05-08 20:23:53',0),(20,13,1,1,'2022-05-09 01:16:22',0),(28,4,1,1,'2022-05-14 18:08:01',1),(40,3,1,1,'2022-05-14 18:29:11',0),(41,14,1,1,'2022-05-14 22:33:09',0),(43,3,5,1,'2022-05-17 22:39:14',0),(45,4,1,-1,'2022-05-17 23:24:25',0);
/*!40000 ALTER TABLE `reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pfp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','[\"ROLE_ADMIN\"]','$2y$13$l9lPIqLHTmzlYZQ0NB7ppeWtUsBPcS2Dhi6Rp1SvMXx/VM0XHxWaC','/pfps/627683d206834.png','#### Lorem ipsum dolor sit amet, consectetur adipiscing elit. \r\nMorbi ultricies diam in justo accumsant ultricies. Curabitur eleifend lacinia ante, eu sollicitudin nisi blandit sit amet.'),(2,'test_user','[]','$2y$13$1eZrmNDmbzV.pFX9tOUwi.UevR7rytD5VU83bmkhX8dTRF8YYkrrG','/pfps/6282db4336a1f.jpg','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi malesuada nibh diam, at suscipit lacus gravida ut. Donec euismod a justo id tempus. Aliquam id dui ante. Sed ut pellentesque quam.'),(3,'test_user2','[]','$2y$13$Htd1rF3ikfKA0a2cA61dTeYQa6JCPg/AzpjGrWLApGkgj4o3qtzFe','/pfps/6282dba09fab9.jpg','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus non est eget sapien ullamcorper auctor gravida id lorem. Maecenas efficitur vestibulum auctor. In condimentum vestibulum ante.'),(4,'test_user3','[]','$2y$13$gbmKecVf2V5W.KN9LkMhRuOFCzdy0bgzFGhnpd8wt3odT5DyTZUMO',NULL,NULL),(5,'test_user4','[]','$2y$13$vpm8o30pCdvD98gnvh7ue.ZqhbY47GS2lIGciSeVADXgbBRXIUJ2.',NULL,NULL);
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

-- Dump completed on 2022-05-17 23:49:02
