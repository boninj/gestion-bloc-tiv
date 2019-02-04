-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: real_tiv
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.04.1

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
  `gaz` varchar(16) NOT NULL,
  `etat` varchar(16) NOT NULL,
  PRIMARY KEY (`id`,`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des blocs du club';
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
  `date_achat` date NOT NULL,
  `observation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des detendeurs du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_element` int(11) NOT NULL,
  `type_element` varchar(64) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des inspecteurs TIV du club';
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
  `remarque` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_bloc` (`id_bloc`),
  KEY `id_inspecteur_tiv` (`id_inspecteur_tiv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_tiv`
--

DROP TABLE IF EXISTS `journal_tiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journal_tiv` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `element` varchar(64) NOT NULL,
  `id_element` int(16) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Journal de l''application';
/*!40101 SET character_set_client = @saved_cs_client */;

-- 
-- Structure de la table `palme`
-- 

CREATE TABLE `palme` (
  `id` int(16) NOT NULL,
  `modele` varchar(255) NOT NULL,
  `taille` varchar(16) NOT NULL,
  `date_achat` date NOT NULL,
  `observation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `personne`
--

DROP TABLE IF EXISTS `personne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personne` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `groupe` varchar(32) NOT NULL,
  `licence` varchar(32) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `code_postal` varchar(16) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `telephone_domicile` varchar(32) NOT NULL,
  `telephone_bureau` varchar(32) NOT NULL,
  `telephone_portable` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(255) NOT NULL,
  `niveau` varchar(32) NOT NULL,
  `date_obtention_niveau` date NOT NULL,
  `nombre_plongee` int(16) NOT NULL,
  `date_derniere_plongee` date NOT NULL,
  `type_assurance` varchar(32) NOT NULL,
  `date_derniere_maj` date NOT NULL,
  `qualifications` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Personne du club';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret`
--

DROP TABLE IF EXISTS `pret`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pret` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `id_personne` int(16) NOT NULL,
  `id_detendeur` int(16) NOT NULL,
  `id_stab` int(16) NOT NULL,
  `id_bloc` int(16) NOT NULL,
  `debut_pret` date NOT NULL,
  `fin_prevu` date NOT NULL,
  `fin_reel` date NOT NULL,
  `etat` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des prêts';
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
  `date_achat` date NOT NULL,
  `observation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Liste des stabs du club';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- 
-- Structure de la table `palme`
-- 

CREATE TABLE `masque` (
  `id` int(16) NOT NULL,
  `modele` varchar(255) NOT NULL,
  `taille` varchar(16) NOT NULL,
  `date_achat` date NOT NULL,
  `observation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dump completed on 2013-02-18 22:48:34
-- Édité à la main par boninj le 04/02/2019 à 23:22
