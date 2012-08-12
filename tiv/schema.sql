-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Dim 12 Août 2012 à 15:56
-- Version du serveur: 5.5.24
-- Version de PHP: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `tiv`
--

-- --------------------------------------------------------

--
-- Structure de la table `bloc`
--

CREATE TABLE IF NOT EXISTS `bloc` (
  `id` int(15) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des blocs du club';

-- --------------------------------------------------------

--
-- Structure de la table `bloc_sauvegarde`
--

CREATE TABLE IF NOT EXISTS `bloc_sauvegarde` (
  `id` int(15) NOT NULL,
  `id_club` int(15) NOT NULL,
  `nom_proprietaire` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `constructeur` varchar(128) NOT NULL,
  `marque` varchar(128) NOT NULL,
  `numero` varchar(128) NOT NULL,
  `capacite` varchar(32) NOT NULL,
  `date_premiere_epreuve` date NOT NULL,
  `date_derniere_epreuve` date NOT NULL,
  `pression_epreuve` int(5) NOT NULL,
  `pression_service` int(5) NOT NULL,
  PRIMARY KEY (`id`,`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des blocs du club';

-- --------------------------------------------------------

--
-- Structure de la table `detendeur`
--

CREATE TABLE IF NOT EXISTS `detendeur` (
  `id` int(15) NOT NULL,
  `modele` varchar(128) NOT NULL,
  `id_1ier_etage` varchar(64) NOT NULL,
  `id_2e_etage` varchar(64) NOT NULL,
  `id_octopus` varchar(64) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des detendeurs du club';

-- --------------------------------------------------------

--
-- Structure de la table `inspecteur_tiv`
--

CREATE TABLE IF NOT EXISTS `inspecteur_tiv` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `numero_tiv` varchar(16) NOT NULL,
  `adresse_tiv` varchar(255) NOT NULL,
  `telephone_tiv` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des inspecteurs TIV du club';

-- --------------------------------------------------------

--
-- Structure de la table `inspection_tiv`
--

CREATE TABLE IF NOT EXISTS `inspection_tiv` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `id_bloc` int(16) NOT NULL,
  `id_inspecteur_tiv` int(16) NOT NULL,
  `date` date NOT NULL,
  `remarque` int(16) NOT NULL,
  `decision` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_bloc` (`id_bloc`),
  KEY `id_inspecteur_tiv` (`id_inspecteur_tiv`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `stab`
--

CREATE TABLE IF NOT EXISTS `stab` (
  `id` int(15) NOT NULL,
  `modele` varchar(128) NOT NULL,
  `taille` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Liste des stabs du club';

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `inspection_tiv`
--
ALTER TABLE `inspection_tiv`
  ADD CONSTRAINT `inspection_tiv_ibfk_1` FOREIGN KEY (`id_bloc`) REFERENCES `bloc` (`id`),
  ADD CONSTRAINT `inspection_tiv_ibfk_2` FOREIGN KEY (`id_inspecteur_tiv`) REFERENCES `inspection_tiv` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
