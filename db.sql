-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 20, 2013 at 08:05 AM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `symbiosis`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesslevels`
--

CREATE TABLE IF NOT EXISTS `accesslevels` (
  `accessLevel` tinyint(2) NOT NULL,
  `languageId` smallint(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`accessLevel`,`languageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accesslevels`
--

INSERT INTO `accesslevels` (`accessLevel`, `languageId`, `title`) VALUES
(0, 1, 'Кто угодно'),
(0, 2, 'Anybody'),
(1, 1, 'Пользователь'),
(1, 2, 'User'),
(8, 1, 'Модератор'),
(8, 2, 'Moderator'),
(9, 1, 'Администратор'),
(9, 2, 'Adminitrator');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `abbr` varchar(10) NOT NULL,
  `code` varchar(10) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `titleEn` varchar(255) DEFAULT NULL,
  `isEnabled` tinyint(1) NOT NULL DEFAULT '1',
  `isDefault` tinyint(1) NOT NULL DEFAULT '0',
  `position` smallint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `abbr` (`abbr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `abbr`, `code`, `title`, `titleEn`, `isEnabled`, `isDefault`, `position`) VALUES
(1, 'ru', 'ru', 'Русский', 'Russian', 1, 1, 1),
(2, 'en', 'en-GB', 'English', 'English/Uk', 1, 0, 2),
(3, 'am', 'hy', 'Հայերեն', 'Armenian', 0, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `languageId` smallint(3) NOT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  `alias` varchar(200) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `isHome` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `isHidden` tinyint(1) NOT NULL DEFAULT '0',
  `is404` tinyint(1) NOT NULL DEFAULT '0',
  `parentId` int(10) NOT NULL DEFAULT '0',
  `redirectId` int(10) NOT NULL DEFAULT '0',
  `accessLevel` smallint(2) NOT NULL DEFAULT '0',
  `template` varchar(200) NOT NULL,
  `title` varchar(200) DEFAULT '',
  `keywords` varchar(1000) DEFAULT '',
  `description` varchar(200) DEFAULT '',
  `symbiont` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`languageId`),
  UNIQUE KEY `name` (`alias`,`languageId`,`link`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `languageId`, `position`, `alias`, `link`, `isHome`, `isActive`, `isHidden`, `is404`, `parentId`, `redirectId`, `accessLevel`, `template`, `title`, `keywords`, `description`, `symbiont`) VALUES
(1, 1, 2, 'main', '', 1, 1, 0, 0, 0, 0, 0, 'slides/slides', 'Главная', '', '', 'Notes.categories.id=0'),
(1, 2, 2, 'main', '', 1, 1, 0, 0, 0, 0, 0, 'slides/slides', 'Main', '', '', 'Notes.categories.id=0'),
(2, 1, 10, '404', '', 0, 1, 1, 1, 0, 0, 0, 'slides/slides-404', '404', '', '', 'Text'),
(2, 2, 10, '404', '', 0, 1, 1, 1, 0, 0, 0, 'slides/slides-404', '404', '', '', 'Text'),
(3, 1, 1, 'admin', '', 0, 1, 0, 0, 0, 0, 0, 'admin/admin', 'Админ', '', '', 'Admin'),
(3, 2, 1, 'admin', '', 0, 1, 0, 0, 0, 0, 0, 'admin/admin', 'Admin', '', '', 'Admin'),
(4, 1, 3, 'about_us', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'О нас', '', '', 'Text'),
(4, 2, 3, 'about_us', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'About us', '', '', 'Text'),
(5, 1, 6, 'contact_us', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Контакты', '', '', ''),
(5, 2, 6, 'contact_us', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Contact us', '', '', ''),
(7, 1, 8, 'sign_up', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Регистрация', '', '', ''),
(7, 2, 8, 'sign_up', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Sign up', '', '', ''),
(9, 1, 9, 'card', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Корзина', '', '', ''),
(9, 2, 9, 'card', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Card', '', '', ''),
(10, 1, 4, 'blog', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Блог', '', '', 'Notes'),
(10, 2, 4, 'blog', '', 0, 1, 0, 0, 0, 0, 0, 'slides/slides-inside', 'Blog', '', '', 'Notes');

-- --------------------------------------------------------

--
-- Table structure for table `scategories`
--

CREATE TABLE IF NOT EXISTS `scategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageId` smallint(3) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  `parentId` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `for` varchar(255) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`,`languageId`),
  UNIQUE KEY `alias` (`alias`,`languageId`,`for`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `scategories`
--

INSERT INTO `scategories` (`id`, `languageId`, `alias`, `parentId`, `title`, `for`, `cover`, `position`, `settings`) VALUES
(2, 1, 'blog', 0, 'Блог', 'notes', '', 1, '{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"slides"}'),
(2, 2, 'blog', 0, 'Blog', 'notes', '', 2, '{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"slides"}'),
(3, 1, 'microgallery', 0, 'Микрогалерея', 'media', '', 2, '{"order":"0","template":"prettyPhoto","poster":{"width":"64","height":"64"},"path":"slides"}'),
(3, 2, 'microgallery', 0, 'Microgallery', 'media', '', 3, '{"order":"0","template":"prettyPhoto","poster":{"width":"64","height":"64"},"path":"slides"}'),
(4, 1, 'slides', 0, 'Слайды', 'media', '', 1, '{"order":"0","template":"list","poster":{"width":"1001","height":"400"},"path":"slides"}'),
(4, 2, 'slides', 0, 'Slides', 'media', '', 4, '{"order":"0","template":"list","poster":{"width":"1001","height":"400"},"path":"slides"}'),
(5, 1, 'general', 0, 'Основное', 'goods', '', 5, '{"order":"1","template":"grid","poster":{"width":"100","height":"100"},"path":"goods"}'),
(5, 2, 'general', 0, 'General', 'goods', '', 5, '{"order":"1","template":"grid","poster":{"width":"100","height":"100"},"path":"goods"}'),
(6, 1, 'photogallery', 0, 'Фотогалерея', 'media', '', 3, '{"order":"1","template":"prettyPhoto","poster":{"width":"140","height":"140"},"path":"slides"}'),
(6, 2, 'photogallery', 0, 'Photogallery', 'media', '', 6, '{"order":"1","template":"prettyPhoto","poster":{"width":"140","height":"140"},"path":"slides"}'),
(7, 1, 'aaa', 0, 'aaa', 'notes', '', 3, '{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"notes"}'),
(7, 2, 'aaa', 0, 'aaa', 'notes', '', 7, '{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"notes"}'),
(8, 1, 'test', 2, 'test', 'notes', '', 2, '{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"notes"}'),
(8, 2, 'test', 2, 'test', 'notes', '', 8, '{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"notes"}');

-- --------------------------------------------------------

--
-- Table structure for table `scomments`
--

CREATE TABLE IF NOT EXISTS `scomments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageId` smallint(3) unsigned NOT NULL,
  `parentId` int(10) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `for` varchar(255) NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `smenu`
--

CREATE TABLE IF NOT EXISTS `smenu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageId` int(10) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`languageId`),
  UNIQUE KEY `alias` (`alias`,`languageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `smenu`
--

INSERT INTO `smenu` (`id`, `languageId`, `alias`, `title`, `position`) VALUES
(1, 1, 'top', 'Верхнее', 1),
(1, 2, 'top', 'Top', 1);

-- --------------------------------------------------------

--
-- Table structure for table `smenuitems`
--

CREATE TABLE IF NOT EXISTS `smenuitems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageId` smallint(3) unsigned NOT NULL,
  `menuId` int(10) unsigned NOT NULL,
  `parentId` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`languageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `smenuitems`
--

INSERT INTO `smenuitems` (`id`, `languageId`, `menuId`, `parentId`, `title`, `link`, `image`, `position`) VALUES
(1, 1, 1, 0, 'Главная', 'ru/main/', '', 1),
(1, 2, 1, 0, 'Main', 'en/main/', '', 1),
(2, 1, 1, 0, 'О нас', 'ru/about_us/', '', 2),
(2, 2, 1, 0, 'About us', 'en/about_us/', '', 2),
(3, 1, 1, 0, 'Контакты', 'ru/contact_us/', '', 4),
(3, 2, 1, 0, 'Contact us', 'en/contact_us/', '', 4),
(4, 1, 1, 0, 'Товары', 'ru/goods/', '', 3),
(4, 2, 1, 0, 'Goods', 'en/goods/', '', 3);

-- --------------------------------------------------------

--
-- Table structure for table `snotes`
--

CREATE TABLE IF NOT EXISTS `snotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageId` smallint(3) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  `categoryId` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `cover` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `text` longtext NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`languageId`),
  UNIQUE KEY `alias` (`languageId`,`alias`,`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stags`
--

CREATE TABLE IF NOT EXISTS `stags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languageId` smallint(3) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `popularity` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`languageId`),
  UNIQUE KEY `languageId` (`languageId`,`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `stags`
--

INSERT INTO `stags` (`id`, `languageId`, `alias`, `title`, `popularity`) VALUES
(1, 1, 'symbiosis', 'Симбиоз', 4),
(1, 2, 'symbiosis', 'Symbiosis', 4),
(2, 1, 'plugin', 'Плагин', 3),
(2, 2, 'plugin', 'Plugin', 3);

-- --------------------------------------------------------

--
-- Table structure for table `stagsconnections`
--

CREATE TABLE IF NOT EXISTS `stagsconnections` (
  `symbiont` varchar(255) NOT NULL,
  `itemId` int(10) unsigned NOT NULL,
  `tagId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`symbiont`,`itemId`,`tagId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stagsconnections`
--

INSERT INTO `stagsconnections` (`symbiont`, `itemId`, `tagId`) VALUES
('Notes', 26, 1),
('Notes', 26, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `accessLevel` smallint(2) NOT NULL DEFAULT '1',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `firstName` varchar(250) NOT NULL,
  `middleName` varchar(255) NOT NULL DEFAULT '',
  `lastName` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `country` varchar(250) NOT NULL,
  `city` varchar(250) NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `timezone` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `accessLevel`, `isActive`, `firstName`, `middleName`, `lastName`, `email`, `country`, `city`, `sex`, `date`, `timezone`) VALUES
(1, 'admin', '1a1dc91c907325c69271ddf0c944bc72', 9, 1, 'Admin', 'Admin', '', '', '', '', 1, '0000-00-00 00:00:00', '+4:00');

-- --------------------------------------------------------

--
-- Table structure for table `usersauth`
--

CREATE TABLE IF NOT EXISTS `usersauth` (
  `userId` int(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`userId`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
