-- MySQL dump 10.13  Distrib 5.5.24, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tiv
-- ------------------------------------------------------
-- Server version	5.5.24-0ubuntu0.12.04.1

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
-- Table structure for table `bloc`
--

DROP TABLE IF EXISTS `bloc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bloc` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `id_club` int(15) NOT NULL,
  `nom_proprietaire` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `constructeur` varchar(128) NOT NULL,
  `marque` varchar(128) NOT NULL,
  `numero` varchar(128) NOT NULL,
  `capacite` varchar(32) NOT NULL,
  `date_premiere_epreuve` date NOT NULL,
  `date_derniere_epreuve` date NOT NULL,
  `date_dernier_tiv` date NOT NULL,
  `pression_epreuve` int(5) NOT NULL,
  `pression_service` int(5) NOT NULL,
  PRIMARY KEY (`id`,`numero`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1 COMMENT='Liste des blocs du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detendeur`
--

DROP TABLE IF EXISTS `detendeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detendeur` (
  `id` int(15) NOT NULL,
  `modele` varchar(128) NOT NULL,
  `id_1ier_etage` varchar(64) NOT NULL,
  `id_2e_etage` varchar(64) NOT NULL,
  `id_octopus` varchar(64) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des detendeurs du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inspecteur_tiv`
--

DROP TABLE IF EXISTS `inspecteur_tiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspecteur_tiv` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `numero_tiv` varchar(16) NOT NULL,
  `adresse_tiv` varchar(255) NOT NULL,
  `telephone_tiv` varchar(32) NOT NULL,
  `actif` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des inspecteurs TIV du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inspection_tiv`
--

DROP TABLE IF EXISTS `inspection_tiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspection_tiv` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `id_bloc` int(16) NOT NULL,
  `id_inspecteur_tiv` int(16) NOT NULL,
  `date` date NOT NULL,
  `etat_exterieur` varchar(16) NOT NULL,
  `remarque_exterieur` varchar(255) NOT NULL,
  `etat_interieur` varchar(16) NOT NULL,
  `remarque_interieur` varchar(255) NOT NULL,
  `etat_filetage` varchar(16) NOT NULL,
  `remarque_filetage` varchar(255) NOT NULL,
  `etat_robineterie` varchar(16) NOT NULL,
  `remarque_robineterie` varchar(255) NOT NULL,
  `decision` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_bloc` (`id_bloc`),
  KEY `id_inspecteur_tiv` (`id_inspecteur_tiv`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stab`
--

DROP TABLE IF EXISTS `stab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stab` (
  `id` int(15) NOT NULL,
  `modele` varchar(128) NOT NULL,
  `taille` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des stabs du club';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-13 13:57:27
