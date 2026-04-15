CREATE DATABASE  IF NOT EXISTS `presenze` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `presenze`;
-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: presenze
-- ------------------------------------------------------
-- Server version	8.0.32

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
-- Temporary view structure for view `ad_cena`
--

DROP TABLE IF EXISTS `ad_cena`;
/*!50001 DROP VIEW IF EXISTS `ad_cena`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ad_cena` AS SELECT 
 1 AS `Categoria`,
 1 AS `ID_USERNAME`,
 1 AS `ConteggioDiIDnome`,
 1 AS `Cena`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ad_colazione`
--

DROP TABLE IF EXISTS `ad_colazione`;
/*!50001 DROP VIEW IF EXISTS `ad_colazione`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ad_colazione` AS SELECT 
 1 AS `Categoria`,
 1 AS `ID_USERNAME`,
 1 AS `ConteggioDiIDnome`,
 1 AS `Colazione`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `ad_pranzo`
--

DROP TABLE IF EXISTS `ad_pranzo`;
/*!50001 DROP VIEW IF EXISTS `ad_pranzo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `ad_pranzo` AS SELECT 
 1 AS `Categoria`,
 1 AS `ID_USERNAME`,
 1 AS `ConteggioDiIDnome`,
 1 AS `Pranzo`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `contacategoria`
--

DROP TABLE IF EXISTS `contacategoria`;
/*!50001 DROP VIEW IF EXISTS `contacategoria`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `contacategoria` AS SELECT 
 1 AS `Categoria`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `CONTEGGIO`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `conteggioaventidiritto`
--

DROP TABLE IF EXISTS `conteggioaventidiritto`;
/*!50001 DROP VIEW IF EXISTS `conteggioaventidiritto`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `conteggioaventidiritto` AS SELECT 
 1 AS `Categoria`,
 1 AS `ID_USERNAME`,
 1 AS `ConteggioDiIDnome`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `conteggioconsumazioni`
--

DROP TABLE IF EXISTS `conteggioconsumazioni`;
/*!50001 DROP VIEW IF EXISTS `conteggioconsumazioni`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `conteggioconsumazioni` AS SELECT 
 1 AS `ConteggioDiConteggioDiIDrecord`,
 1 AS `Categoria`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Pagamento`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `conteggioconsumazionifa`
--

DROP TABLE IF EXISTS `conteggioconsumazionifa`;
/*!50001 DROP VIEW IF EXISTS `conteggioconsumazionifa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `conteggioconsumazionifa` AS SELECT 
 1 AS `ConteggioDiConteggioDiIDrecord`,
 1 AS `Categoria`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Pagamento`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `conteggioprenotazioni`
--

DROP TABLE IF EXISTS `conteggioprenotazioni`;
/*!50001 DROP VIEW IF EXISTS `conteggioprenotazioni`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `conteggioprenotazioni` AS SELECT 
 1 AS `ConteggioDiIDrecord`,
 1 AS `Categoria`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Pagamento`,
 1 AS `IDcat`,
 1 AS `Se`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `conteggioprenotazionifa`
--

DROP TABLE IF EXISTS `conteggioprenotazionifa`;
/*!50001 DROP VIEW IF EXISTS `conteggioprenotazionifa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `conteggioprenotazionifa` AS SELECT 
 1 AS `ConteggioDiIDrecord`,
 1 AS `Categoria`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Pagamento`,
 1 AS `IDcat`,
 1 AS `Se`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `nomi_liberi_cena`
--

DROP TABLE IF EXISTS `nomi_liberi_cena`;
/*!50001 DROP VIEW IF EXISTS `nomi_liberi_cena`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `nomi_liberi_cena` AS SELECT 
 1 AS `Grado`,
 1 AS `UO`,
 1 AS `Cognome`,
 1 AS `Nome`,
 1 AS `ID_USERNAME`,
 1 AS `IDnome`,
 1 AS `GIORNO`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `nomi_liberi_col`
--

DROP TABLE IF EXISTS `nomi_liberi_col`;
/*!50001 DROP VIEW IF EXISTS `nomi_liberi_col`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `nomi_liberi_col` AS SELECT 
 1 AS `Grado`,
 1 AS `UO`,
 1 AS `Cognome`,
 1 AS `Nome`,
 1 AS `ID_USERNAME`,
 1 AS `IDnome`,
 1 AS `GIORNO`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `nomi_liberi_pranzo`
--

DROP TABLE IF EXISTS `nomi_liberi_pranzo`;
/*!50001 DROP VIEW IF EXISTS `nomi_liberi_pranzo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `nomi_liberi_pranzo` AS SELECT 
 1 AS `Grado`,
 1 AS `UO`,
 1 AS `Cognome`,
 1 AS `Nome`,
 1 AS `ID_USERNAME`,
 1 AS `IDnome`,
 1 AS `GIORNO`,
 1 AS `Categoria`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pre_accessi`
--

DROP TABLE IF EXISTS `pre_accessi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_accessi` (
  `IDrecord` bigint unsigned NOT NULL AUTO_INCREMENT,
  `IDnome` int unsigned NOT NULL,
  `GIORNO` date NOT NULL,
  `PASTO` int unsigned NOT NULL DEFAULT '0',
  `COD_VAR` int DEFAULT NULL,
  `Ora_pren` datetime DEFAULT NULL,
  `USR` int unsigned DEFAULT NULL,
  `Ti_R` int unsigned DEFAULT NULL,
  `Se` int unsigned DEFAULT NULL,
  `Ora_cons_pr` datetime DEFAULT NULL,
  `Pagamento` tinyint unsigned DEFAULT '0',
  `Cons` int unsigned DEFAULT NULL,
  `Stampa` tinyint unsigned DEFAULT '0',
  `IP` varchar(15) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IDrecord`),
  UNIQUE KEY `NoDoppioni` (`IDnome`,`GIORNO`,`PASTO`)
) ENGINE=InnoDB AUTO_INCREMENT=2784085 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_accessi`
--

LOCK TABLES `pre_accessi` WRITE;
/*!40000 ALTER TABLE `pre_accessi` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_accessi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_accessi_bk`
--

DROP TABLE IF EXISTS `pre_accessi_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_accessi_bk` (
  `IDrecord` bigint unsigned NOT NULL AUTO_INCREMENT,
  `IDnome` int unsigned NOT NULL,
  `GIORNO` datetime NOT NULL,
  `PASTO` int unsigned NOT NULL DEFAULT '0',
  `COD_VAR` int DEFAULT NULL,
  `Ora_pren` datetime DEFAULT NULL,
  `USR` int unsigned DEFAULT NULL,
  `Ti_R` int unsigned DEFAULT NULL,
  `Se` int unsigned DEFAULT NULL,
  `Ora_cons_pr` datetime DEFAULT NULL,
  `Pagamento` tinyint unsigned DEFAULT '0',
  `Cons` int unsigned DEFAULT NULL,
  PRIMARY KEY (`IDrecord`)
) ENGINE=InnoDB AUTO_INCREMENT=2761620 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_accessi_bk`
--

LOCK TABLES `pre_accessi_bk` WRITE;
/*!40000 ALTER TABLE `pre_accessi_bk` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_accessi_bk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_categorie`
--

DROP TABLE IF EXISTS `pre_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_categorie` (
  `IDcat` int unsigned NOT NULL AUTO_INCREMENT,
  `Categoria` varchar(4) DEFAULT NULL,
  `Colazione` tinyint(1) NOT NULL,
  `Pranzo` tinyint(1) NOT NULL,
  `Cena` tinyint(1) NOT NULL,
  PRIMARY KEY (`IDcat`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_categorie`
--

LOCK TABLES `pre_categorie` WRITE;
/*!40000 ALTER TABLE `pre_categorie` DISABLE KEYS */;
INSERT INTO `pre_categorie` VALUES (1,'U',0,1,0),(2,'SU',0,1,0),(3,'TR',1,1,1),(4,'CIV',0,1,0),(5,'CC',0,1,0),(6,'VSP',0,1,0);
/*!40000 ALTER TABLE `pre_categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_elenconomi`
--

DROP TABLE IF EXISTS `pre_elenconomi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_elenconomi` (
  `IDnome` int unsigned NOT NULL AUTO_INCREMENT,
  `ID_PERS_MTR` int DEFAULT NULL,
  `Cognome` varchar(45) DEFAULT NULL,
  `Nome` varchar(45) DEFAULT NULL,
  `UO` varchar(5) DEFAULT NULL,
  `TipoOrario` int DEFAULT '1',
  `VTV` int DEFAULT '0',
  `ADMIN` int DEFAULT '0',
  `Forza` int DEFAULT '1',
  `TipoRazione` int DEFAULT '1',
  `CF` varchar(16) NOT NULL,
  `ModoSomm` int unsigned DEFAULT '1',
  `SedeSomm` int unsigned DEFAULT '1',
  `Categoria` int unsigned NOT NULL,
  `Foto` varchar(45) DEFAULT NULL,
  `IDgrado` int unsigned DEFAULT NULL,
  `Stip` int unsigned DEFAULT '0',
  `Cte` int unsigned DEFAULT '0',
  `email` varchar(45) DEFAULT NULL,
  `SISME` varchar(5) DEFAULT NULL,
  `TipoRazioneCe` int unsigned DEFAULT '1',
  `TipoRazioneCol` int unsigned DEFAULT '1',
  `FA` varchar(2) NOT NULL,
  `Grado` varchar(2) DEFAULT NULL,
  `Username` varchar(15) DEFAULT NULL,
  `Password` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`IDnome`,`CF`),
  UNIQUE KEY `CF` (`CF`)
) ENGINE=InnoDB AUTO_INCREMENT=12930 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_elenconomi`
--

LOCK TABLES `pre_elenconomi` WRITE;
/*!40000 ALTER TABLE `pre_elenconomi` DISABLE KEYS */;
INSERT INTO `pre_elenconomi` VALUES (12929,NULL,'admin','admin','44',1,2,1,1,1,'admin',1,1,1,NULL,7,0,0,NULL,NULL,1,1,'EI',NULL,'admin','admin');
/*!40000 ALTER TABLE `pre_elenconomi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `pre_giorni`
--

DROP TABLE IF EXISTS `pre_giorni`;
/*!50001 DROP VIEW IF EXISTS `pre_giorni`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pre_giorni` AS SELECT 
 1 AS `GIORNO`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pre_giornisett`
--

DROP TABLE IF EXISTS `pre_giornisett`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_giornisett` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `WEEKDAY` varchar(45) NOT NULL,
  `FESTIVO` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_giornisett`
--

LOCK TABLES `pre_giornisett` WRITE;
/*!40000 ALTER TABLE `pre_giornisett` DISABLE KEYS */;
INSERT INTO `pre_giornisett` VALUES (1,'Domenica ',1),(2,'Lunedì',0),(3,'Martedì',0),(4,'Mercoledì',0),(5,'Giovedì',0),(6,'Venerdì',0),(7,'Sabato',1);
/*!40000 ALTER TABLE `pre_giornisett` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_gradi`
--

DROP TABLE IF EXISTS `pre_gradi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_gradi` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Grado` varchar(25) NOT NULL,
  `Categoria` int unsigned NOT NULL,
  `Ordinamento` int unsigned NOT NULL,
  `Colazione` tinyint(1) DEFAULT NULL,
  `Pranzo` tinyint(1) DEFAULT NULL,
  `Cena` tinyint(1) DEFAULT NULL,
  `COD_GRADO` varchar(45) DEFAULT NULL,
  `Cat` char(3) NOT NULL,
  `FA` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_gradi`
--

LOCK TABLES `pre_gradi` WRITE;
/*!40000 ALTER TABLE `pre_gradi` DISABLE KEYS */;
INSERT INTO `pre_gradi` VALUES (1,'Mar.Magg.\"A\"',2,16,NULL,NULL,NULL,'S7','2','EI'),(2,'Mar.Magg.',2,16,NULL,NULL,NULL,'S8','2','EI'),(3,'Serg.Magg.',2,23,NULL,NULL,NULL,'EP','2','EI'),(4,'Grd.Aiut.',3,25,NULL,NULL,NULL,'BM','3','EI'),(5,'Al.Serg.',3,42,NULL,NULL,NULL,'E7','3','EI'),(6,'ASM',3,41,NULL,NULL,NULL,'E6','3','EI'),(7,'Gen.C.A.CSM',1,5,NULL,NULL,NULL,'S0','1','EI'),(8,'Gen.C.A.',1,6,NULL,NULL,NULL,'S1','1','EI'),(10,'Gen.D.',1,7,NULL,NULL,NULL,'S3','1','EI'),(11,'Gen.B.',1,8,NULL,NULL,NULL,'S4','1','EI'),(12,'1 Lgt.',2,15,NULL,NULL,NULL,'DC','2','EI'),(14,'Gen.',1,5,NULL,NULL,NULL,'EA','1','EI'),(15,'Magg.Gen.',1,7,NULL,NULL,NULL,'EC','1','EI'),(16,'Brig.Gen.',1,8,NULL,NULL,NULL,'ED','1','EI'),(17,'Col.',1,9,NULL,NULL,NULL,'EE','1','EI'),(18,'Ten.Col.',1,10,NULL,NULL,NULL,'EF','1','EI'),(19,'Magg.',1,11,NULL,NULL,NULL,'EG','1','EI'),(20,'Cap.',1,12,NULL,NULL,NULL,'EH','1','EI'),(21,'Ten.',1,13,NULL,NULL,NULL,'EI','1','EI'),(22,'S.Ten.',1,14,NULL,NULL,NULL,'EJ','1','EI'),(23,'1 Mar.',2,17,NULL,NULL,NULL,'EK','2','EI'),(24,'Mar.Ca.',2,18,NULL,NULL,NULL,'EL','2','EI'),(25,'Mar.Ord.',2,19,NULL,NULL,NULL,'EM','2','EI'),(26,'Mar.',2,20,NULL,NULL,NULL,'EN','2','EI'),(27,'Serg.Magg.A.',2,21,NULL,NULL,NULL,'BL','2','EI'),(28,'Ten.Gen.',1,6,NULL,NULL,NULL,'EB','1','EI'),(29,'Serg.Magg.Ca.',2,22,NULL,NULL,NULL,'EO','2','EI'),(30,'Serg.',2,24,NULL,NULL,NULL,'EQ','2','EI'),(31,'1 Grd.',3,26,NULL,NULL,NULL,'BN','3','EI'),(32,'Grd.Ca.',3,27,NULL,NULL,NULL,'BO','3','EI'),(33,'Grd.Sc.',3,28,NULL,NULL,NULL,'BP','3','EI'),(34,'Grd.',3,29,NULL,NULL,NULL,'BQ','3','EI'),(35,'C.le',3,39,NULL,NULL,NULL,'EW','3','EI'),(36,'AU',3,32,NULL,NULL,NULL,'E1','3','EI'),(37,'AUC',3,33,NULL,NULL,NULL,'E2','3','EI'),(38,'AM',3,34,NULL,NULL,NULL,'E3','3','EI'),(39,'AS',3,35,NULL,NULL,NULL,'E4','3','EI'),(40,'Sol.',3,40,NULL,NULL,NULL,'E5','3','EI'),(41,'C.le Magg.',3,38,NULL,NULL,NULL,'EX','3','EI'),(42,'Lgt.',2,16,NULL,NULL,NULL,'DB','2','EI'),(43,'ASM III anno',3,43,NULL,NULL,NULL,'E8','3','EI'),(44,'ASM II anno',3,44,NULL,NULL,NULL,'E9','3','EI'),(45,'ASM I anno',3,45,NULL,NULL,NULL,'EZ','3','EI'),(46,'A1F1',4,46,NULL,NULL,NULL,'A1F1','4','EI'),(47,'A1F2',4,47,NULL,NULL,NULL,'A1F2','4','CIV'),(48,'A1F3',4,48,NULL,NULL,NULL,'A1F3','4','CIV'),(49,'A2F1',4,49,NULL,NULL,NULL,'A2F1','4','CIV'),(50,'A2F2',4,50,NULL,NULL,NULL,'A2F2','4','CIV'),(51,'A2F3',4,51,NULL,NULL,NULL,'A2F3','4','CIV'),(52,'A2F4',4,52,NULL,NULL,NULL,'A2F4','4','CIV'),(53,'A3F1',4,53,NULL,NULL,NULL,'A3F1','4','CIV'),(54,'A3F2',4,54,NULL,NULL,NULL,'A3F2','4','CIV'),(55,'A3F3',4,55,NULL,NULL,NULL,'A3F3','4','CIV'),(56,'A3F4',4,56,NULL,NULL,NULL,'A3F4','4','CIV'),(57,'App, CC',5,57,NULL,NULL,NULL,NULL,'5','CC'),(58,'App, Sc, CC',5,58,NULL,NULL,NULL,NULL,'5','CC'),(59,'Vice Brig, CC',5,59,NULL,NULL,NULL,NULL,'5','CC'),(63,'Primo Aviere',3,63,NULL,NULL,NULL,NULL,'3','AM');
/*!40000 ALTER TABLE `pre_gradi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_ip`
--

DROP TABLE IF EXISTS `pre_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_ip` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `IP` varchar(15) NOT NULL,
  `Descr` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_ip`
--

LOCK TABLES `pre_ip` WRITE;
/*!40000 ALTER TABLE `pre_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_menu`
--

DROP TABLE IF EXISTS `pre_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_menu` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Pasto` int unsigned NOT NULL,
  `IDpiatto` int unsigned NOT NULL,
  `Giorno` date NOT NULL,
  `Sede` int unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_menu`
--

LOCK TABLES `pre_menu` WRITE;
/*!40000 ALTER TABLE `pre_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_mod_somm`
--

DROP TABLE IF EXISTS `pre_mod_somm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_mod_somm` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Mod_som` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_mod_somm`
--

LOCK TABLES `pre_mod_somm` WRITE;
/*!40000 ALTER TABLE `pre_mod_somm` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_mod_somm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `pre_nomiliberi`
--

DROP TABLE IF EXISTS `pre_nomiliberi`;
/*!50001 DROP VIEW IF EXISTS `pre_nomiliberi`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pre_nomiliberi` AS SELECT 
 1 AS `IDnome`,
 1 AS `GIORNO`,
 1 AS `Grado`,
 1 AS `Cognome`,
 1 AS `Nome`,
 1 AS `Sede`,
 1 AS `TipoRaz`,
 1 AS `ID_USERNAME`,
 1 AS `Pasto`,
 1 AS `UO`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `pre_nomiview`
--

DROP TABLE IF EXISTS `pre_nomiview`;
/*!50001 DROP VIEW IF EXISTS `pre_nomiview`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pre_nomiview` AS SELECT 
 1 AS `Grado`,
 1 AS `UO`,
 1 AS `Cognome`,
 1 AS `Nome`,
 1 AS `ID_USERNAME`,
 1 AS `IDnome`,
 1 AS `TipoRaz`,
 1 AS `Sede`,
 1 AS `Categoria`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pre_offlinedata`
--

DROP TABLE IF EXISTS `pre_offlinedata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_offlinedata` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `IDmaster` int unsigned NOT NULL,
  `Grado` varchar(20) NOT NULL,
  `Cognome` varchar(145) NOT NULL,
  `Nome` varchar(145) NOT NULL,
  `CF` varchar(16) NOT NULL,
  `Pasto` varchar(10) NOT NULL,
  `IDnome` int unsigned DEFAULT NULL,
  `Cat` int unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=688 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_offlinedata`
--

LOCK TABLES `pre_offlinedata` WRITE;
/*!40000 ALTER TABLE `pre_offlinedata` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_offlinedata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_offlinemaster`
--

DROP TABLE IF EXISTS `pre_offlinemaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_offlinemaster` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `SISME` varchar(5) NOT NULL,
  `DataMiss` date NOT NULL,
  `PDC` varchar(255) NOT NULL,
  `Motivo` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_offlinemaster`
--

LOCK TABLES `pre_offlinemaster` WRITE;
/*!40000 ALTER TABLE `pre_offlinemaster` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_offlinemaster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_orari`
--

DROP TABLE IF EXISTS `pre_orari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_orari` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `N_GIORNO` int unsigned NOT NULL,
  `GIORNO` char(10) NOT NULL,
  `ORA_IN` time NOT NULL,
  `ORA_OUT` time NOT NULL,
  `TIPO_ORARIO` int unsigned NOT NULL,
  `INTERVALLO` time NOT NULL,
  `STRTIME` int unsigned NOT NULL,
  `DESCRIZIONE` varchar(45) DEFAULT NULL,
  `FESTIVO` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_orari`
--

LOCK TABLES `pre_orari` WRITE;
/*!40000 ALTER TABLE `pre_orari` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_orari` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_orari_gg`
--

DROP TABLE IF EXISTS `pre_orari_gg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_orari_gg` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `IDnome` int unsigned NOT NULL,
  `ORA_IN` time DEFAULT NULL,
  `GIORNO` datetime NOT NULL,
  `CAUSA` varchar(3) DEFAULT NULL,
  `ORA_OUT` time DEFAULT NULL,
  `ORA_REG_OUT` datetime DEFAULT NULL,
  `INTERVALLO` time DEFAULT NULL,
  `VAL_DA` int unsigned DEFAULT NULL,
  `ORA_REG_IN` datetime DEFAULT NULL,
  `NOTE` varchar(155) DEFAULT NULL,
  `ORA_MOD_IN` datetime DEFAULT NULL,
  `ORA_MOD_OUT` datetime DEFAULT NULL,
  `ID_MOD_IN` int unsigned DEFAULT NULL,
  `ID_MOD_OUT` int unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_orari_gg`
--

LOCK TABLES `pre_orari_gg` WRITE;
/*!40000 ALTER TABLE `pre_orari_gg` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_orari_gg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_orari_gg_bk`
--

DROP TABLE IF EXISTS `pre_orari_gg_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_orari_gg_bk` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `IDnome` int unsigned NOT NULL,
  `ORA_IN` time DEFAULT NULL,
  `GIORNO` datetime DEFAULT NULL,
  `CAUSA` varchar(3) DEFAULT NULL,
  `ORA_OUT` time DEFAULT NULL,
  `ORA_REG_OUT` datetime DEFAULT NULL,
  `INTERVALLO` time DEFAULT NULL,
  `VAL_DA` int unsigned DEFAULT NULL,
  `ORA_REG_IN` datetime DEFAULT NULL,
  `NOTE` varchar(155) DEFAULT NULL,
  `ORA_MOD_IN` datetime DEFAULT NULL,
  `ORA_MOD_OUT` datetime DEFAULT NULL,
  `ID_MOD_IN` int unsigned DEFAULT NULL,
  `ID_MOD_OUT` int unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_orari_gg_bk`
--

LOCK TABLES `pre_orari_gg_bk` WRITE;
/*!40000 ALTER TABLE `pre_orari_gg_bk` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_orari_gg_bk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_piatti`
--

DROP TABLE IF EXISTS `pre_piatti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_piatti` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(255) NOT NULL,
  `Kcal` int DEFAULT NULL,
  `Perc` int DEFAULT NULL,
  `Portata` int DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_piatti`
--

LOCK TABLES `pre_piatti` WRITE;
/*!40000 ALTER TABLE `pre_piatti` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_piatti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `pre_presenti`
--

DROP TABLE IF EXISTS `pre_presenti`;
/*!50001 DROP VIEW IF EXISTS `pre_presenti`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pre_presenti` AS SELECT 
 1 AS `IDnome`,
 1 AS `GIORNO`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `pre_presenti_cena`
--

DROP TABLE IF EXISTS `pre_presenti_cena`;
/*!50001 DROP VIEW IF EXISTS `pre_presenti_cena`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pre_presenti_cena` AS SELECT 
 1 AS `IDnome`,
 1 AS `GIORNO`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `pre_presenti_col`
--

DROP TABLE IF EXISTS `pre_presenti_col`;
/*!50001 DROP VIEW IF EXISTS `pre_presenti_col`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pre_presenti_col` AS SELECT 
 1 AS `IDnome`,
 1 AS `GIORNO`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pre_sedi`
--

DROP TABLE IF EXISTS `pre_sedi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_sedi` (
  `IDsede` int unsigned NOT NULL AUTO_INCREMENT,
  `SEDE` varchar(45) NOT NULL,
  `FSede` char(2) NOT NULL DEFAULT '0',
  `Casse` char(2) NOT NULL,
  PRIMARY KEY (`IDsede`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_sedi`
--

LOCK TABLES `pre_sedi` WRITE;
/*!40000 ALTER TABLE `pre_sedi` DISABLE KEYS */;
INSERT INTO `pre_sedi` VALUES (1,'SALA MENSA','0',''),(2,'REPARTO DI CURA','0',''),(3,'SACCHETTO VIVERI','0','');
/*!40000 ALTER TABLE `pre_sedi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_servizi`
--

DROP TABLE IF EXISTS `pre_servizi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_servizi` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Note` varchar(45) NOT NULL,
  `ora_in` time NOT NULL,
  `durata` int unsigned NOT NULL,
  `SUN` tinyint(1) DEFAULT '0',
  `MON` tinyint(1) DEFAULT '0',
  `TUE` tinyint(1) DEFAULT '0',
  `WED` tinyint(1) DEFAULT '0',
  `THU` tinyint(1) DEFAULT '0',
  `FRI` tinyint(1) DEFAULT '0',
  `SAT` tinyint(1) DEFAULT '0',
  `ora_out` time NOT NULL,
  `CAUSALE` varchar(3) NOT NULL DEFAULT 'SRD',
  `SEDE` int unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_servizi`
--

LOCK TABLES `pre_servizi` WRITE;
/*!40000 ALTER TABLE `pre_servizi` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_servizi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_servizipersona`
--

DROP TABLE IF EXISTS `pre_servizipersona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_servizipersona` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `IDnome` int unsigned NOT NULL,
  `IDservizio` int unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_servizipersona`
--

LOCK TABLES `pre_servizipersona` WRITE;
/*!40000 ALTER TABLE `pre_servizipersona` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_servizipersona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_setup`
--

DROP TABLE IF EXISTS `pre_setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_setup` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Reparto` varchar(255) DEFAULT NULL,
  `Ditta_Rist` varchar(255) DEFAULT NULL,
  `Email` varchar(155) DEFAULT NULL,
  `LoginMode` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_setup`
--

LOCK TABLES `pre_setup` WRITE;
/*!40000 ALTER TABLE `pre_setup` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_setup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_tiporazione`
--

DROP TABLE IF EXISTS `pre_tiporazione`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_tiporazione` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `TipoRazione` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_tiporazione`
--

LOCK TABLES `pre_tiporazione` WRITE;
/*!40000 ALTER TABLE `pre_tiporazione` DISABLE KEYS */;
INSERT INTO `pre_tiporazione` VALUES (1,'ORD.'),(2,'MEDIA'),(3,'PES.');
/*!40000 ALTER TABLE `pre_tiporazione` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_turni`
--

DROP TABLE IF EXISTS `pre_turni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_turni` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `Descr` varchar(45) NOT NULL,
  `dalle` time NOT NULL,
  `alle` time NOT NULL,
  `sede` int unsigned NOT NULL,
  `Pasto` int unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_turni`
--

LOCK TABLES `pre_turni` WRITE;
/*!40000 ALTER TABLE `pre_turni` DISABLE KEYS */;
/*!40000 ALTER TABLE `pre_turni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_uo`
--

DROP TABLE IF EXISTS `pre_uo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_uo` (
  `ID_UO` int unsigned NOT NULL AUTO_INCREMENT,
  `COD_UN_OPER` varchar(6) NOT NULL,
  `DEN_UN_OPER` varchar(45) NOT NULL,
  `PRE_UN_OPER` varchar(45) NOT NULL,
  `SEDE` int NOT NULL,
  `RANGE_IN_DA` time DEFAULT '07:45:00',
  `RANGE_IN_A` time DEFAULT '08:15:00',
  `RANGE_OUT_DA` time DEFAULT '16:30:00',
  `RANGE_OUT_A` time DEFAULT '16:45:00',
  `ID_CTE` int unsigned DEFAULT NULL,
  `pre_uocol` varchar(45) DEFAULT NULL,
  `ObbligoCMD` tinyint unsigned DEFAULT '1',
  `IDturno` int unsigned DEFAULT NULL,
  `EFF` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID_UO`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_uo`
--

LOCK TABLES `pre_uo` WRITE;
/*!40000 ALTER TABLE `pre_uo` DISABLE KEYS */;
INSERT INTO `pre_uo` VALUES (44,'1','NON GESTITI','NON GESTITI',1,'08:00:00','08:00:00','16:00:00','16:00:00',161,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `pre_uo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_utentixunita`
--

DROP TABLE IF EXISTS `pre_utentixunita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_utentixunita` (
  `ID` int unsigned NOT NULL AUTO_INCREMENT,
  `IDnome` int unsigned NOT NULL,
  `ID_UO` int unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2062 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_utentixunita`
--

LOCK TABLES `pre_utentixunita` WRITE;
/*!40000 ALTER TABLE `pre_utentixunita` DISABLE KEYS */;
INSERT INTO `pre_utentixunita` VALUES (3,52,3),(6,52,6),(8,52,8),(12,52,12),(21,52,21),(26,9,2),(28,9,4),(29,9,5),(30,9,6),(31,9,7),(33,9,9),(34,9,10),(35,9,11),(36,9,12),(37,9,13),(38,9,14),(39,9,15),(40,9,16),(41,9,17),(42,9,18),(43,9,19),(44,9,20),(45,9,21),(46,9,22),(47,9,23),(48,9,24),(57,9,8),(63,43,25),(64,43,26),(66,43,28),(67,43,29),(70,43,32),(72,9,1),(73,9,3),(74,9,25),(75,9,26),(76,9,27),(78,9,29),(79,9,30),(80,9,31),(81,9,32),(82,9,28),(115,143,1),(116,143,3),(117,143,25),(118,143,26),(119,143,27),(121,143,29),(122,143,30),(123,143,31),(124,143,32),(125,143,28),(136,122,1),(137,122,3),(138,122,25),(139,122,26),(140,122,27),(141,122,28),(142,122,29),(143,122,30),(144,122,31),(145,122,32),(146,28,1),(147,28,3),(148,28,25),(149,28,26),(150,28,27),(152,28,29),(153,28,30),(154,28,31),(155,28,32),(156,43,1),(157,43,3),(160,43,27),(163,43,30),(164,43,31),(166,168,1),(167,168,3),(168,168,25),(169,168,26),(170,168,27),(171,168,28),(172,168,29),(173,168,30),(174,168,31),(175,168,32),(176,96,1),(177,96,3),(178,96,25),(179,96,26),(180,96,27),(181,96,28),(182,96,29),(183,96,30),(184,96,31),(185,96,32),(189,168,35),(190,168,34),(191,168,33),(195,519,33),(198,397,35),(200,404,34),(201,169,1),(202,169,3),(203,169,25),(204,169,26),(205,169,27),(207,169,29),(208,169,30),(209,169,31),(210,169,32),(214,718,1),(215,718,3),(216,718,25),(217,718,26),(218,718,27),(220,718,29),(221,718,30),(222,718,31),(223,718,32),(227,19,1),(229,19,25),(230,19,26),(233,19,29),(234,19,30),(237,19,33),(238,19,34),(240,153,1),(241,153,3),(242,153,25),(243,153,26),(244,153,27),(246,153,29),(247,153,30),(248,153,31),(249,153,32),(254,19,3),(257,19,27),(261,19,31),(262,19,32),(265,19,35),(266,19,36),(268,19,28),(269,19,37),(270,19,38),(279,19,39),(281,787,1),(282,787,3),(283,787,25),(284,787,26),(285,787,27),(287,787,29),(288,787,30),(289,787,31),(290,787,32),(302,19,40),(303,19,41),(304,19,42),(307,19,43),(313,799,40),(314,799,42),(315,799,43),(322,400,35),(323,986,35),(326,7,28),(327,7,33),(328,7,34),(329,7,35),(330,7,36),(331,7,37),(332,7,38),(333,7,39),(334,7,40),(335,7,41),(336,7,42),(337,7,43),(338,7,44),(349,1151,40),(351,1151,42),(352,1151,43),(354,1083,28),(359,1083,37),(360,1083,38),(363,1083,41),(367,408,34),(368,1179,43),(369,1179,40),(370,1179,42),(374,1370,45),(375,1371,45),(376,19,45),(377,529,33),(378,1088,43),(379,1088,40),(380,1088,42),(381,1200,43),(382,1200,40),(383,1200,42),(384,1212,43),(385,1212,40),(386,1212,42),(387,1245,34),(388,1246,34),(392,19,46),(393,1527,46),(394,1555,46),(401,9,38),(404,9,41),(407,9,44),(410,787,28),(411,787,33),(412,787,34),(413,787,35),(414,787,36),(415,787,37),(416,787,38),(417,787,39),(418,787,40),(419,787,41),(420,787,42),(421,787,43),(422,787,44),(423,787,45),(424,787,46),(426,787,47),(429,1676,47),(430,1630,28),(435,1630,37),(436,1630,38),(439,1630,41),(451,1777,48),(453,821,48),(455,808,48),(456,1676,48),(457,2021,38),(462,2044,49),(465,2222,48),(466,2381,49),(467,2315,49),(468,2225,48),(471,1083,50),(472,1630,50),(473,1083,44),(475,1630,44),(479,2315,51),(484,2659,48),(485,2664,48),(486,2577,49),(488,2579,49),(491,2572,49),(492,2574,49),(493,2406,49),(496,2587,49),(497,2621,49),(498,2709,49),(499,2650,49),(500,2770,48),(501,2600,49),(510,2937,53),(513,2929,53),(514,3028,52),(515,3029,52),(516,3030,52),(517,3031,52),(518,3032,52),(522,2937,54),(523,2938,53),(524,2938,54),(551,718,28),(556,718,37),(557,718,38),(560,718,41),(563,718,44),(569,718,50),(575,3337,55),(577,3328,55),(578,3331,55),(579,3352,55),(580,3030,56),(584,3467,56),(585,3530,56),(586,3529,56),(587,3490,56),(588,718,57),(589,718,58),(590,1083,57),(591,1083,58),(592,1630,57),(593,1630,58),(594,3529,57),(595,3651,57),(596,3690,57),(597,3691,57),(598,3692,57),(599,3693,57),(600,3421,58),(601,3813,58),(602,3811,58),(603,971,58),(605,1630,59),(606,1083,59),(607,718,59),(608,1083,60),(609,1630,60),(611,718,60),(612,164,28),(618,164,38),(621,164,41),(624,164,44),(630,164,50),(637,164,57),(638,164,58),(639,164,59),(640,164,60),(641,164,37),(642,4050,28),(643,4050,57),(644,4050,58),(645,4050,37),(646,4050,60),(647,4050,41),(648,4050,38),(649,4050,44),(650,4050,59),(651,4050,50),(652,4072,57),(653,4072,58),(654,4072,37),(655,4072,60),(656,4072,41),(657,4072,38),(658,4072,59),(659,4072,28),(660,4072,44),(661,4072,50),(668,4081,38),(671,4081,41),(680,4081,50),(688,4081,58),(690,4081,60),(691,1083,61),(692,4081,28),(697,4081,37),(704,4081,44),(717,4081,57),(719,4081,59),(721,4081,61),(722,1630,61),(723,718,61),(725,4099,61),(726,4100,61),(728,4160,61),(729,4112,61),(730,718,62),(731,1083,62),(732,4081,62),(733,1630,62),(735,4238,62),(736,4251,62),(738,4315,62),(739,4262,62),(740,4235,62),(741,4109,61),(742,1083,63),(746,718,63),(747,4507,63),(748,4508,63),(749,718,64),(751,1083,64),(752,4552,63),(754,4747,64),(789,4550,63),(790,4830,63),(791,2661,63),(792,4941,64),(794,2578,65),(795,5048,65),(798,5033,65),(801,718,65),(802,718,66),(804,5164,66),(807,718,67),(808,9,67),(809,5167,66),(810,5365,65),(812,2578,68),(813,5670,68),(814,5666,68),(815,5664,68),(816,5661,68),(817,5657,68),(818,2928,68),(820,5825,69),(827,4643,28),(830,4643,41),(831,4643,38),(832,4643,67),(838,6308,70),(840,6417,71),(841,6419,71),(842,6418,71),(845,6405,70),(846,6418,72),(847,6419,72),(848,6417,72),(849,6308,73),(850,6405,73),(851,6298,73),(868,718,74),(869,6977,74),(870,6978,74),(871,6979,74),(875,7097,75),(879,718,75),(881,718,39),(882,718,76),(884,7251,76),(888,2794,76),(889,7492,76),(892,4061,41),(893,4061,38),(894,4061,28),(895,4061,75),(896,4061,74),(897,4061,76),(898,4061,73),(899,4061,72),(900,4061,67),(901,4061,44),(902,718,77),(905,4061,77),(906,3165,77),(910,7692,78),(922,718,79),(924,7657,44),(925,7159,44),(927,5652,80),(928,8404,79),(932,718,81),(933,8580,81),(934,4643,81),(936,4321,82),(937,4321,79),(938,8711,82),(939,8711,79),(943,1720,83),(946,718,28),(947,718,33),(948,718,34),(949,718,35),(950,718,36),(951,718,37),(952,718,38),(953,718,39),(954,718,40),(955,718,41),(956,718,42),(957,718,43),(958,718,44),(959,718,45),(960,718,46),(961,718,47),(962,718,48),(963,718,49),(964,718,50),(965,718,51),(966,718,52),(967,718,53),(968,718,54),(969,718,55),(970,718,56),(971,718,57),(972,718,58),(973,718,59),(974,718,60),(975,718,61),(976,718,62),(977,718,63),(978,718,64),(979,718,65),(980,718,66),(981,718,67),(982,718,68),(983,718,69),(984,718,70),(985,718,71),(986,718,72),(987,718,73),(988,718,74),(989,718,75),(990,718,76),(991,718,77),(992,718,78),(993,718,79),(994,718,80),(995,718,81),(996,718,82),(997,718,83),(998,718,39),(1053,718,84),(1056,4643,84),(1058,5148,28),(1059,5148,33),(1060,5148,34),(1061,5148,35),(1062,5148,36),(1063,5148,37),(1064,5148,38),(1065,5148,39),(1066,5148,40),(1067,5148,41),(1068,5148,42),(1069,5148,43),(1070,5148,44),(1071,5148,45),(1072,5148,46),(1073,5148,47),(1074,5148,48),(1075,5148,49),(1076,5148,50),(1077,5148,51),(1078,5148,52),(1079,5148,53),(1080,5148,54),(1081,5148,55),(1082,5148,56),(1083,5148,57),(1084,5148,58),(1085,5148,59),(1086,5148,60),(1087,5148,61),(1088,5148,62),(1089,5148,63),(1090,5148,64),(1091,5148,65),(1092,5148,66),(1093,5148,67),(1094,5148,68),(1095,5148,69),(1096,5148,70),(1097,5148,71),(1098,5148,72),(1099,5148,73),(1100,5148,74),(1101,5148,75),(1102,5148,76),(1103,5148,77),(1104,5148,78),(1105,5148,79),(1106,5148,80),(1107,5148,81),(1108,5148,82),(1109,5148,83),(1110,5148,84),(1111,9147,82),(1112,9146,82),(1114,4643,85),(1117,718,85),(1118,718,79),(1119,9245,79),(1120,9243,79),(1121,9244,79),(1122,9348,83),(1123,9351,83),(1125,9375,83),(1126,8587,81),(1133,9693,86),(1136,9618,83),(1137,9796,81),(1138,9806,39),(1139,9806,38),(1141,6824,70),(1142,8519,45),(1143,8465,45),(1144,9987,45),(1145,10001,70),(1151,9973,70),(1216,9430,28),(1217,9430,41),(1218,9430,38),(1219,9430,70),(1220,9430,45),(1221,9430,67),(1222,9430,44),(1226,10312,47),(1228,10405,52),(1229,4643,47),(1230,4643,52),(1231,9430,47),(1232,9430,52),(1235,9,52),(1236,9,81),(1238,8018,38),(1239,10675,55),(1304,10769,81),(1307,3380,50),(1308,9,50),(1309,9430,55),(1310,9430,50),(1311,4643,55),(1312,4643,50),(1313,4643,44),(1314,9427,46),(1315,4643,46),(1379,5445,28),(1380,5445,33),(1381,5445,34),(1382,5445,35),(1383,5445,36),(1384,5445,37),(1385,5445,38),(1386,5445,39),(1387,5445,40),(1388,5445,41),(1389,5445,42),(1390,5445,43),(1391,5445,44),(1392,5445,45),(1393,5445,46),(1394,5445,47),(1395,5445,48),(1396,5445,49),(1397,5445,50),(1398,5445,51),(1399,5445,52),(1400,5445,53),(1401,5445,54),(1402,5445,55),(1403,5445,56),(1404,5445,57),(1405,5445,58),(1406,5445,59),(1407,5445,60),(1408,5445,61),(1409,5445,62),(1410,5445,63),(1411,5445,64),(1412,5445,65),(1413,5445,66),(1414,5445,67),(1415,5445,68),(1416,5445,69),(1417,5445,70),(1418,5445,71),(1419,5445,72),(1420,5445,73),(1421,5445,74),(1422,5445,75),(1423,5445,76),(1424,5445,77),(1425,5445,78),(1426,5445,79),(1427,5445,80),(1428,5445,81),(1429,5445,82),(1430,5445,83),(1431,5445,84),(1432,5445,85),(1433,5445,86),(1442,5445,39),(1443,10963,46),(1519,9,55),(1874,11130,89),(1900,1716,94),(1901,1717,94),(1902,4643,94),(1903,9430,94),(1904,9,94),(1905,9,33),(1906,9,37),(1907,9,45),(1908,9,46),(1909,9,47),(1910,9,57),(1911,9,58),(1912,9,61),(1913,9,62),(1914,9,63),(1915,9,64),(1916,9,65),(1917,9,66),(1918,9,68),(1919,9,69),(1920,9,70),(1921,9,72),(1922,9,73),(1923,9,74),(1924,9,75),(1925,9,76),(1926,9,77),(1927,9,78),(1928,9,79),(1929,9,80),(1930,9,82),(1931,9,83),(1932,9,84),(1933,9,86),(1934,9,89),(1936,9430,84),(1937,9430,89),(1938,4643,89),(1940,5445,89),(1941,5445,94),(1942,11371,95),(1943,9,95),(1944,11371,96),(1945,11543,97),(1949,10217,28),(1950,10217,37),(1951,10217,38),(1952,10217,41),(1953,10217,44),(1954,10217,45),(1955,10217,46),(1956,10217,47),(1957,10217,52),(1958,10217,55),(1959,10217,57),(1960,10217,58),(1961,10217,61),(1962,10217,62),(1963,10217,63),(1964,10217,64),(1965,10217,65),(1966,10217,66),(1967,10217,67),(1968,10217,68),(1969,10217,69),(1970,10217,70),(1971,10217,72),(1972,10217,73),(1973,10217,74),(1974,10217,75),(1975,10217,76),(1976,10217,77),(1977,10217,78),(1978,10217,79),(1979,10217,80),(1980,10217,81),(1981,10217,82),(1982,10217,83),(1983,10217,84),(1984,10217,86),(1985,10217,89),(1986,10217,94),(1987,10217,95),(1988,10217,97),(2012,4643,97),(2013,4643,95),(2014,9430,95),(2015,9430,97),(2016,8609,28),(2017,8609,37),(2018,8609,38),(2019,8609,41),(2020,8609,44),(2021,8609,45),(2022,8609,46),(2023,8609,47),(2024,8609,52),(2025,8609,55),(2026,8609,57),(2027,8609,58),(2028,8609,61),(2029,8609,62),(2030,8609,63),(2031,8609,64),(2032,8609,65),(2033,8609,66),(2034,8609,67),(2035,8609,68),(2036,8609,69),(2037,8609,70),(2038,8609,72),(2039,8609,73),(2040,8609,74),(2041,8609,75),(2042,8609,76),(2043,8609,77),(2044,8609,78),(2045,8609,79),(2046,8609,80),(2047,8609,81),(2048,8609,82),(2049,8609,83),(2050,8609,84),(2051,8609,86),(2052,8609,89),(2053,8609,94),(2054,8609,95),(2055,8609,97),(2056,12506,98),(2057,4643,98),(2058,9430,98),(2059,8609,98),(2060,10217,98),(2061,1282,98);
/*!40000 ALTER TABLE `pre_utentixunita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `prenotazioni`
--

DROP TABLE IF EXISTS `prenotazioni`;
/*!50001 DROP VIEW IF EXISTS `prenotazioni`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `prenotazioni` AS SELECT 
 1 AS `GIORNO`,
 1 AS `IDnome`,
 1 AS `SedePra`,
 1 AS `TiRaPr`,
 1 AS `IDpra`,
 1 AS `USR_pra`,
 1 AS `SedeCe`,
 1 AS `TiRaCe`,
 1 AS `IDCe`,
 1 AS `USR_ce`,
 1 AS `SedeCol`,
 1 AS `TiRaCol`,
 1 AS `IDCol`,
 1 AS `USR_col`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `riepilogomensa`
--

DROP TABLE IF EXISTS `riepilogomensa`;
/*!50001 DROP VIEW IF EXISTS `riepilogomensa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `riepilogomensa` AS SELECT 
 1 AS `Categoria`,
 1 AS `Se`,
 1 AS `IDcat`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Prenotati`,
 1 AS `Consumati`,
 1 AS `Pagamento`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `riepilogomensafa`
--

DROP TABLE IF EXISTS `riepilogomensafa`;
/*!50001 DROP VIEW IF EXISTS `riepilogomensafa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `riepilogomensafa` AS SELECT 
 1 AS `Categoria`,
 1 AS `Se`,
 1 AS `IDcat`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Prenotati`,
 1 AS `Consumati`,
 1 AS `Pagamento`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `select_giorni`
--

DROP TABLE IF EXISTS `select_giorni`;
/*!50001 DROP VIEW IF EXISTS `select_giorni`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `select_giorni` AS SELECT 
 1 AS `GIORNO`,
 1 AS `IDnome`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `selectcene`
--

DROP TABLE IF EXISTS `selectcene`;
/*!50001 DROP VIEW IF EXISTS `selectcene`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `selectcene` AS SELECT 
 1 AS `IDrecord`,
 1 AS `IDnome`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `USR`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `selectcol`
--

DROP TABLE IF EXISTS `selectcol`;
/*!50001 DROP VIEW IF EXISTS `selectcol`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `selectcol` AS SELECT 
 1 AS `IDrecord`,
 1 AS `IDnome`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `USR`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `selectgiorno`
--

DROP TABLE IF EXISTS `selectgiorno`;
/*!50001 DROP VIEW IF EXISTS `selectgiorno`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `selectgiorno` AS SELECT 
 1 AS `GIORNO`,
 1 AS `IDnome`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `selectpra`
--

DROP TABLE IF EXISTS `selectpra`;
/*!50001 DROP VIEW IF EXISTS `selectpra`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `selectpra` AS SELECT 
 1 AS `IDrecord`,
 1 AS `IDnome`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `USR`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `selezioneconsumazioni`
--

DROP TABLE IF EXISTS `selezioneconsumazioni`;
/*!50001 DROP VIEW IF EXISTS `selezioneconsumazioni`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `selezioneconsumazioni` AS SELECT 
 1 AS `ConteggioDiIDrecord`,
 1 AS `Categoria`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Pagamento`,
 1 AS `Ora_cons_pr`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `selezioneconsumazionifa`
--

DROP TABLE IF EXISTS `selezioneconsumazionifa`;
/*!50001 DROP VIEW IF EXISTS `selezioneconsumazionifa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `selezioneconsumazionifa` AS SELECT 
 1 AS `ConteggioDiIDrecord`,
 1 AS `Categoria`,
 1 AS `DEN_UN_OPER`,
 1 AS `GIORNO`,
 1 AS `PASTO`,
 1 AS `TipoRazione`,
 1 AS `SEDE`,
 1 AS `Pagamento`,
 1 AS `Ora_cons_pr`,
 1 AS `FA`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'presenze'
--

--
-- Dumping routines for database 'presenze'
--

--
-- Final view structure for view `ad_cena`
--

/*!50001 DROP VIEW IF EXISTS `ad_cena`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ad_cena` AS select 1 AS `Categoria`,1 AS `ID_USERNAME`,1 AS `ConteggioDiIDnome`,1 AS `Cena` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ad_colazione`
--

/*!50001 DROP VIEW IF EXISTS `ad_colazione`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ad_colazione` AS select 1 AS `Categoria`,1 AS `ID_USERNAME`,1 AS `ConteggioDiIDnome`,1 AS `Colazione` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ad_pranzo`
--

/*!50001 DROP VIEW IF EXISTS `ad_pranzo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `ad_pranzo` AS select 1 AS `Categoria`,1 AS `ID_USERNAME`,1 AS `ConteggioDiIDnome`,1 AS `Pranzo` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `contacategoria`
--

/*!50001 DROP VIEW IF EXISTS `contacategoria`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `contacategoria` AS select 1 AS `Categoria`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `CONTEGGIO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `conteggioaventidiritto`
--

/*!50001 DROP VIEW IF EXISTS `conteggioaventidiritto`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `conteggioaventidiritto` AS select 1 AS `Categoria`,1 AS `ID_USERNAME`,1 AS `ConteggioDiIDnome` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `conteggioconsumazioni`
--

/*!50001 DROP VIEW IF EXISTS `conteggioconsumazioni`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `conteggioconsumazioni` AS select 1 AS `ConteggioDiConteggioDiIDrecord`,1 AS `Categoria`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Pagamento`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `conteggioconsumazionifa`
--

/*!50001 DROP VIEW IF EXISTS `conteggioconsumazionifa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `conteggioconsumazionifa` AS select 1 AS `ConteggioDiConteggioDiIDrecord`,1 AS `Categoria`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Pagamento`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `conteggioprenotazioni`
--

/*!50001 DROP VIEW IF EXISTS `conteggioprenotazioni`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `conteggioprenotazioni` AS select 1 AS `ConteggioDiIDrecord`,1 AS `Categoria`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Pagamento`,1 AS `IDcat`,1 AS `Se`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `conteggioprenotazionifa`
--

/*!50001 DROP VIEW IF EXISTS `conteggioprenotazionifa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `conteggioprenotazionifa` AS select 1 AS `ConteggioDiIDrecord`,1 AS `Categoria`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Pagamento`,1 AS `IDcat`,1 AS `Se`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `nomi_liberi_cena`
--

/*!50001 DROP VIEW IF EXISTS `nomi_liberi_cena`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `nomi_liberi_cena` AS select 1 AS `Grado`,1 AS `UO`,1 AS `Cognome`,1 AS `Nome`,1 AS `ID_USERNAME`,1 AS `IDnome`,1 AS `GIORNO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `nomi_liberi_col`
--

/*!50001 DROP VIEW IF EXISTS `nomi_liberi_col`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `nomi_liberi_col` AS select 1 AS `Grado`,1 AS `UO`,1 AS `Cognome`,1 AS `Nome`,1 AS `ID_USERNAME`,1 AS `IDnome`,1 AS `GIORNO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `nomi_liberi_pranzo`
--

/*!50001 DROP VIEW IF EXISTS `nomi_liberi_pranzo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `nomi_liberi_pranzo` AS select 1 AS `Grado`,1 AS `UO`,1 AS `Cognome`,1 AS `Nome`,1 AS `ID_USERNAME`,1 AS `IDnome`,1 AS `GIORNO`,1 AS `Categoria` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pre_giorni`
--

/*!50001 DROP VIEW IF EXISTS `pre_giorni`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pre_giorni` AS select 1 AS `GIORNO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pre_nomiliberi`
--

/*!50001 DROP VIEW IF EXISTS `pre_nomiliberi`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pre_nomiliberi` AS select 1 AS `IDnome`,1 AS `GIORNO`,1 AS `Grado`,1 AS `Cognome`,1 AS `Nome`,1 AS `Sede`,1 AS `TipoRaz`,1 AS `ID_USERNAME`,1 AS `Pasto`,1 AS `UO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pre_nomiview`
--

/*!50001 DROP VIEW IF EXISTS `pre_nomiview`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pre_nomiview` AS select 1 AS `Grado`,1 AS `UO`,1 AS `Cognome`,1 AS `Nome`,1 AS `ID_USERNAME`,1 AS `IDnome`,1 AS `TipoRaz`,1 AS `Sede`,1 AS `Categoria` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pre_presenti`
--

/*!50001 DROP VIEW IF EXISTS `pre_presenti`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pre_presenti` AS select 1 AS `IDnome`,1 AS `GIORNO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pre_presenti_cena`
--

/*!50001 DROP VIEW IF EXISTS `pre_presenti_cena`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pre_presenti_cena` AS select 1 AS `IDnome`,1 AS `GIORNO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pre_presenti_col`
--

/*!50001 DROP VIEW IF EXISTS `pre_presenti_col`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pre_presenti_col` AS select 1 AS `IDnome`,1 AS `GIORNO` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `prenotazioni`
--

/*!50001 DROP VIEW IF EXISTS `prenotazioni`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `prenotazioni` AS select 1 AS `GIORNO`,1 AS `IDnome`,1 AS `SedePra`,1 AS `TiRaPr`,1 AS `IDpra`,1 AS `USR_pra`,1 AS `SedeCe`,1 AS `TiRaCe`,1 AS `IDCe`,1 AS `USR_ce`,1 AS `SedeCol`,1 AS `TiRaCol`,1 AS `IDCol`,1 AS `USR_col` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `riepilogomensa`
--

/*!50001 DROP VIEW IF EXISTS `riepilogomensa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `riepilogomensa` AS select 1 AS `Categoria`,1 AS `Se`,1 AS `IDcat`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Prenotati`,1 AS `Consumati`,1 AS `Pagamento`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `riepilogomensafa`
--

/*!50001 DROP VIEW IF EXISTS `riepilogomensafa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `riepilogomensafa` AS select 1 AS `Categoria`,1 AS `Se`,1 AS `IDcat`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Prenotati`,1 AS `Consumati`,1 AS `Pagamento`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `select_giorni`
--

/*!50001 DROP VIEW IF EXISTS `select_giorni`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `select_giorni` AS select 1 AS `GIORNO`,1 AS `IDnome` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `selectcene`
--

/*!50001 DROP VIEW IF EXISTS `selectcene`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `selectcene` AS select 1 AS `IDrecord`,1 AS `IDnome`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `USR` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `selectcol`
--

/*!50001 DROP VIEW IF EXISTS `selectcol`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `selectcol` AS select 1 AS `IDrecord`,1 AS `IDnome`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `USR` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `selectgiorno`
--

/*!50001 DROP VIEW IF EXISTS `selectgiorno`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `selectgiorno` AS select 1 AS `GIORNO`,1 AS `IDnome` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `selectpra`
--

/*!50001 DROP VIEW IF EXISTS `selectpra`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `selectpra` AS select 1 AS `IDrecord`,1 AS `IDnome`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `USR` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `selezioneconsumazioni`
--

/*!50001 DROP VIEW IF EXISTS `selezioneconsumazioni`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `selezioneconsumazioni` AS select 1 AS `ConteggioDiIDrecord`,1 AS `Categoria`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Pagamento`,1 AS `Ora_cons_pr`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `selezioneconsumazionifa`
--

/*!50001 DROP VIEW IF EXISTS `selezioneconsumazionifa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`presenze`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `selezioneconsumazionifa` AS select 1 AS `ConteggioDiIDrecord`,1 AS `Categoria`,1 AS `DEN_UN_OPER`,1 AS `GIORNO`,1 AS `PASTO`,1 AS `TipoRazione`,1 AS `SEDE`,1 AS `Pagamento`,1 AS `Ora_cons_pr`,1 AS `FA` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-30 10:50:24
