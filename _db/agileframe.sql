-- phpMyAdmin SQL Dump
-- version 3.3.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 15 Paź 2013, 12:13
-- Wersja serwera: 5.5.24
-- Wersja PHP: 5.3.13

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `agileframe`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_admins`
--

CREATE TABLE IF NOT EXISTS `ag_admins` (
  `adm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `adm_login` varchar(64) DEFAULT NULL,
  `adm_pass` varchar(40) DEFAULT NULL,
  `adm_status` enum('NEW','ACTIVE','BLOCKED','DELETED') NOT NULL DEFAULT 'NEW',
  `adm_role` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `adm_adg_id` int(10) unsigned DEFAULT NULL,
  `adm_name` varchar(50) DEFAULT NULL,
  `adm_surname` varchar(50) NOT NULL,
  `adm_email` varchar(100) DEFAULT NULL,
  `adm_create_date` datetime DEFAULT NULL,
  `adm_avatar` varchar(255) DEFAULT NULL,
  `adm_cms_bg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`adm_id`),
  UNIQUE KEY `ulogin` (`adm_login`),
  KEY `login` (`adm_login`,`adm_pass`),
  KEY `id_grupy` (`adm_adg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

--
-- Zrzut danych tabeli `ag_admins`
--

INSERT INTO `ag_admins` (`adm_id`, `adm_login`, `adm_pass`, `adm_status`, `adm_role`, `adm_adg_id`, `adm_name`, `adm_surname`, `adm_email`, `adm_create_date`, `adm_avatar`, `adm_cms_bg`) VALUES
(2, 'admin', '47b9f2e3bd66af788368eaa3f7467e31', 'ACTIVE', 'ADMIN', NULL, 'John', 'Smith', 'mkapinos@agileo.pl', '2010-12-12 12:12:12', '/admin/h54/0be/3fd/editor-avatar.png', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_attachments`
--

CREATE TABLE IF NOT EXISTS `ag_attachments` (
  `att_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `att_adm_id` int(11) unsigned DEFAULT NULL,
  `att_usr_id` int(11) unsigned DEFAULT NULL,
  `att_cfr_id` int(10) unsigned DEFAULT NULL,
  `att_cem_id` int(10) unsigned DEFAULT NULL,
  `att_copy_att_id` int(10) unsigned DEFAULT NULL,
  `att_status` enum('NEW','ACTIVE','TRANSFORMING','BLOCKED','ERROR','DELETED') COLLATE utf8_polish_ci NOT NULL DEFAULT 'NEW',
  `att_gallery_count` smallint(5) unsigned NOT NULL,
  `att_type` enum('IMAGE','FLASH','AUDIO','VIDEO','FILE') COLLATE utf8_polish_ci NOT NULL,
  `att_source` enum('LOCAL','YOUTUBE','VENEO') COLLATE utf8_polish_ci NOT NULL DEFAULT 'LOCAL',
  `att_source_id` varchar(50) COLLATE utf8_polish_ci DEFAULT NULL,
  `att_url` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `att_url_crop` varchar(20) COLLATE utf8_polish_ci DEFAULT NULL,
  `att_thumb` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `att_name` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `att_description` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `att_create_date` datetime NOT NULL,
  `att_filesize` mediumint(9) NOT NULL DEFAULT '0',
  `att_mime_type` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `att_file_width` int(5) DEFAULT NULL,
  `att_file_height` int(5) DEFAULT NULL,
  PRIMARY KEY (`att_id`),
  KEY `att_adm_id` (`att_adm_id`),
  KEY `att_usr_id` (`att_usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_attachments`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_attachments_parents`
--

CREATE TABLE IF NOT EXISTS `ag_attachments_parents` (
  `atp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `atp_att_id` int(11) unsigned NOT NULL,
  `atp_parent` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `atp_parent_id` int(11) unsigned NOT NULL,
  `atp_parent_group` varchar(30) COLLATE utf8_polish_ci NOT NULL DEFAULT 'default',
  `atp_weight` smallint(5) unsigned NOT NULL DEFAULT '9999',
  `atp_create_date` datetime NOT NULL,
  `atp_description` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `atp_main` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`atp_id`),
  UNIQUE KEY `atp_att_id` (`atp_att_id`,`atp_parent_id`,`atp_parent`,`atp_parent_group`),
  KEY `atp_wight` (`atp_weight`),
  KEY `atp_main` (`atp_main`),
  KEY `atp_parent` (`atp_parent`,`atp_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_attachments_parents`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_changes_log`
--

CREATE TABLE IF NOT EXISTS `ag_changes_log` (
  `acg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acg_object_name` varchar(50) NOT NULL,
  `acg_object_id` int(10) unsigned NOT NULL,
  `acg_available` tinyint(4) NOT NULL DEFAULT '0',
  `acg_old_data` text,
  `acg_app_name` varchar(30) NOT NULL,
  `acg_owner_name` varchar(50) DEFAULT NULL,
  `acg_owner_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`acg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_changes_log`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_editors_embeds`
--

CREATE TABLE IF NOT EXISTS `ag_editors_embeds` (
  `aee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aee_parent` varchar(50) NOT NULL,
  `aee_parent_id` int(10) unsigned NOT NULL,
  `aee_field_name` varchar(50) NOT NULL DEFAULT 'description',
  `aee_position` int(11) NOT NULL,
  `aee_editor_embed` varchar(1000) NOT NULL,
  `aee_object_name` varchar(50) NOT NULL,
  `aee_object_id` int(11) NOT NULL,
  `aee_type` varchar(50) NOT NULL,
  `aee_class` varchar(50) DEFAULT NULL,
  `aee_href` varchar(255) DEFAULT NULL,
  `aee_align` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`aee_id`),
  KEY `parent_field` (`aee_parent`,`aee_parent_id`,`aee_field_name`,`aee_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_editors_embeds`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_mail`
--

CREATE TABLE IF NOT EXISTS `ag_mail` (
  `aml_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aml_thread_aml_id` int(10) unsigned NOT NULL,
  `aml_to_parent_name` varchar(50) NOT NULL,
  `aml_to_parent_id` int(10) unsigned NOT NULL,
  `aml_to_emails` varchar(255) DEFAULT NULL,
  `aml_from_parent_name` varchar(50) NOT NULL,
  `aml_from_parent_id` int(10) unsigned NOT NULL,
  `aml_status` enum('NEW','READ','DELETED') NOT NULL DEFAULT 'NEW',
  `aml_create_date` datetime NOT NULL,
  `aml_title` varchar(255) NOT NULL,
  `aml_lead` varchar(255) NOT NULL,
  `aml_mail` text NOT NULL,
  PRIMARY KEY (`aml_id`),
  KEY `to` (`aml_to_parent_name`,`aml_to_parent_id`,`aml_status`,`aml_create_date`),
  KEY `from` (`aml_from_parent_name`,`aml_from_parent_id`,`aml_create_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_mail`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_publication_log`
--

CREATE TABLE IF NOT EXISTS `ag_publication_log` (
  `pub_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pub_object_name` varchar(50) NOT NULL,
  `pub_object_id` int(10) unsigned NOT NULL,
  `pub_owner_name` varchar(50) DEFAULT NULL,
  `pub_owner_id` int(10) unsigned DEFAULT NULL,
  `pub_available` enum('ACTIVE','DRAFT') NOT NULL,
  `pub_public_date` datetime NOT NULL,
  `pub_public_end_date` datetime DEFAULT NULL,
  `pub_check_date` datetime NOT NULL,
  `pub_done_date` datetime DEFAULT NULL,
  `pub_done_status` enum('DONE','CANCELED','LOG') DEFAULT NULL,
  PRIMARY KEY (`pub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_publication_log`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `ag_users`
--

CREATE TABLE IF NOT EXISTS `ag_users` (
  `usr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usr_is_public` tinyint(4) NOT NULL DEFAULT '0',
  `usr_email` varchar(255) NOT NULL,
  `usr_pass` char(32) NOT NULL,
  `usr_status` enum('ACTIVE','BLOCKED','DELETED','NEW') NOT NULL DEFAULT 'NEW',
  `usr_type` enum('STANDARD','SOCIAL','MIXED') NOT NULL DEFAULT 'STANDARD',
  `usr_create_date` datetime NOT NULL,
  `usr_update_date` datetime DEFAULT NULL,
  `usr_nick` varchar(255) DEFAULT NULL,
  `usr_name` varchar(255) DEFAULT NULL,
  `usr_surname` varchar(100) DEFAULT NULL,
  `usr_avatar` varchar(255) DEFAULT NULL,
  `usr_description` text NOT NULL,
  `usr_auth_token` varchar(32) DEFAULT NULL,
  `usr_geo_address` varchar(255) DEFAULT NULL,
  `usr_geo_lat` double DEFAULT NULL,
  `usr_geo_lng` double DEFAULT NULL,
  `usr_geo_zoom` tinyint(3) unsigned DEFAULT NULL,
  `usr_soc_update_date` datetime DEFAULT NULL,
  `usr_soc_facebook` varchar(20) DEFAULT NULL,
  `usr_soc_googleplus` varchar(20) DEFAULT NULL,
  `usr_soc_tweeter` varchar(20) DEFAULT NULL,
  `usr_soc_linkedin` varchar(20) DEFAULT NULL,
  `usr_last_activity` datetime DEFAULT NULL,
  `usr_most_popular` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_most_active` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_stat_art_views_total` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_stat_art_used` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_stat_cuart_views_total` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_stat_cuart_used` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_stat_blogit_views_total` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_stat_blogit_count` int(10) unsigned NOT NULL DEFAULT '0',
  `usr_role` enum('STANDARD','POLITICIAN') NOT NULL DEFAULT 'STANDARD',
  `usr_role_label` varchar(50) DEFAULT NULL,
  `usr_role_cfr_id` int(10) unsigned DEFAULT NULL,
  `usr_contact` text,
  `usr_bg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`usr_id`),
  UNIQUE KEY `usr_email` (`usr_email`),
  KEY `usr_auth_token` (`usr_auth_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `ag_users`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `log_translate`
--

CREATE TABLE IF NOT EXISTS `log_translate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app` varchar(50) NOT NULL,
  `key` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `request_uri` varchar(255) NOT NULL,
  `is_ok` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ` (`key`,`app`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `log_translate`
--

SET FOREIGN_KEY_CHECKS=1;
