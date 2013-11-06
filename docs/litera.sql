-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 06 2013 г., 23:34
-- Версия сервера: 5.5.23-log
-- Версия PHP: 5.3.26

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `litera`
--
CREATE DATABASE IF NOT EXISTS `litera` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `litera`;

-- --------------------------------------------------------

--
-- Структура таблицы `content_page`
--

DROP TABLE IF EXISTS `content_page`;
CREATE TABLE IF NOT EXISTS `content_page` (
  `link_id` int(10) unsigned DEFAULT NULL,
  `page_title` varchar(255) NOT NULL COMMENT 'английское название для системы',
  `parent_page` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext,
  PRIMARY KEY (`page_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `content_page`
--

INSERT INTO `content_page` (`link_id`, `page_title`, `parent_page`, `title`, `content`) VALUES
(1, 'contacti', NULL, 'Контакты', '<p>Контакты</p>\r\n');

-- --------------------------------------------------------

--
-- Структура таблицы `menu_handler`
--

DROP TABLE IF EXISTS `menu_handler`;
CREATE TABLE IF NOT EXISTS `menu_handler` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `menu_handler`
--

INSERT INTO `menu_handler` (`id`, `title`, `controller`) VALUES
(1, 'Контент', 'Contentpage'),
(2, 'Документы', 'Document'),
(3, 'Новости', 'News'),
(4, 'Обращения', 'Vote'),
(6, 'Календарь', 'Calendar'),
(7, 'Партнеры', 'Partners'),
(8, 'Гостевая книга', 'GuestBook');

-- --------------------------------------------------------

--
-- Структура таблицы `menu_item`
--

DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE IF NOT EXISTS `menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `handler_id` int(10) unsigned NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `menu_item`
--

INSERT INTO `menu_item` (`id`, `title`, `link`, `parent_id`, `handler_id`, `is_visible`, `sort_order`) VALUES
(1, 'Контакты', 'kontaktyi', NULL, 1, 1, 2),
(2, 'Новости', 'novosti', NULL, 3, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `menu_menu`
--

DROP TABLE IF EXISTS `menu_menu`;
CREATE TABLE IF NOT EXISTS `menu_menu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Дамп данных таблицы `menu_menu`
--

INSERT INTO `menu_menu` (`id`, `title`, `code`) VALUES
(16, 'Верхнее меню', 'verhnee_menu'),
(17, 'Нижнее меню', 'nijnee_menu');

-- --------------------------------------------------------

--
-- Структура таблицы `menu_menu_item`
--

DROP TABLE IF EXISTS `menu_menu_item`;
CREATE TABLE IF NOT EXISTS `menu_menu_item` (
  `menu_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  PRIMARY KEY (`menu_id`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `menu_menu_item`
--

INSERT INTO `menu_menu_item` (`menu_id`, `item_id`) VALUES
(16, 1),
(16, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `link_id` bigint(20) NOT NULL,
  `date_public` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `short_text` longtext,
  `full_text` longtext,
  `date_create` date NOT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `link_id`, `date_public`, `title`, `file`, `short_text`, `full_text`, `date_create`, `category_id`) VALUES
(1, 2, '2013-11-04 00:00:00', 'Проверка', 'img_04-11-2013-00-06-22.gif', 'вррвр кркурукрукр укркурукрукркурукру', '', '2013-11-04', 3),
(2, 2, '2013-11-04 00:00:00', 'Цвет', 'img_06-11-2013-23-33-24.gif', 'еновн оне', '<p>fgnfgnfg nfgngfnfnfnf</p>', '2013-11-04', 3),
(3, 2, '2013-11-04 00:00:00', 'Еще раз', NULL, 'аптка керекрк', '<p><span style="color:#FF8C00"><strong>ке керекрекркерке</strong></span></p>', '2013-11-04', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `news_category`
--

DROP TABLE IF EXISTS `news_category`;
CREATE TABLE IF NOT EXISTS `news_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `date_create` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `news_category`
--

INSERT INTO `news_category` (`id`, `title`, `date_create`) VALUES
(3, 'Информация', '2013-04-13');

-- --------------------------------------------------------

--
-- Структура таблицы `phone`
--

DROP TABLE IF EXISTS `phone`;
CREATE TABLE IF NOT EXISTS `phone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `post_index` varchar(255) NOT NULL,
  `date_create` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `phone`
--

INSERT INTO `phone` (`id`, `address`, `phone`, `post_index`, `date_create`) VALUES
(1, '652432,  Кемеровская область, Кемеровский район, <br>поселок Разведчик, ул. Васюхичева д. 31</br>', '601-723', '', '2012-06-05');

-- --------------------------------------------------------

--
-- Структура таблицы `tm_acl_role`
--

DROP TABLE IF EXISTS `tm_acl_role`;
CREATE TABLE IF NOT EXISTS `tm_acl_role` (
  `tm_user_role_id` int(10) unsigned NOT NULL,
  `tm_user_resource_id` int(10) unsigned NOT NULL,
  `is_allow` tinyint(1) NOT NULL DEFAULT '0',
  `privileges` varchar(255) NOT NULL,
  PRIMARY KEY (`tm_user_role_id`,`tm_user_resource_id`),
  KEY `tm_user_resource_id` (`tm_user_resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tm_acl_role`
--

INSERT INTO `tm_acl_role` (`tm_user_role_id`, `tm_user_resource_id`, `is_allow`, `privileges`) VALUES
(1, 1, 1, 'show'),
(1, 2, 1, 'show'),
(1, 3, 1, 'show'),
(1, 4, 1, 'show'),
(1, 5, 1, 'show'),
(1, 6, 1, 'show'),
(1, 7, 1, 'show'),
(1, 8, 1, 'show'),
(1, 15, 1, 'show,show-attribute-hash,show-attribute-type,show-resource,show-role'),
(1, 19, 1, 'show'),
(1, 20, 1, 'show'),
(1, 21, 1, 'show'),
(1, 22, 1, 'show'),
(1, 23, 1, 'show'),
(1, 24, 1, 'show'),
(1, 25, 1, 'show'),
(1, 59, 1, 'show'),
(1, 67, 1, 'show'),
(1, 68, 1, 'show'),
(1, 69, 1, 'show'),
(1, 70, 1, 'show'),
(1, 71, 1, 'show'),
(1, 72, 1, 'show'),
(1, 97, 1, 'show'),
(1, 98, 1, 'show'),
(1, 99, 1, 'show'),
(5, 1, 1, 'show'),
(5, 2, 1, 'show'),
(5, 3, 1, 'show'),
(5, 4, 1, 'show'),
(5, 5, 0, 'show'),
(5, 6, 0, 'show'),
(5, 7, 0, 'show'),
(5, 8, 0, 'show'),
(5, 15, 0, 'show'),
(5, 19, 0, 'show'),
(5, 20, 0, 'show'),
(5, 21, 0, 'show'),
(5, 22, 0, 'show'),
(5, 23, 0, 'show'),
(5, 24, 0, 'show'),
(5, 25, 0, 'show'),
(5, 59, 0, 'show'),
(5, 67, 0, 'show'),
(5, 68, 0, 'show'),
(5, 69, 0, 'show'),
(5, 70, 0, 'show'),
(5, 71, 0, 'show'),
(5, 72, 0, 'show'),
(5, 97, 0, 'show'),
(5, 98, 0, 'show'),
(5, 99, 0, 'show');

-- --------------------------------------------------------

--
-- Структура таблицы `tm_user`
--

DROP TABLE IF EXISTS `tm_user`;
CREATE TABLE IF NOT EXISTS `tm_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `date_create` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  KEY `fk_tm_user_tm_user_role1` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `tm_user`
--

INSERT INTO `tm_user` (`id`, `login`, `password`, `role_id`, `date_create`) VALUES
(1, 'admin', '123', 1, '2011-11-16 16:26:00');

-- --------------------------------------------------------

--
-- Структура таблицы `tm_user_attribute`
--

DROP TABLE IF EXISTS `tm_user_attribute`;
CREATE TABLE IF NOT EXISTS `tm_user_attribute` (
  `user_id` int(10) unsigned NOT NULL,
  `attribute_key` varchar(255) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `attribute_value` text NOT NULL,
  `is_fill` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`attribute_key`),
  KEY `fk_tm_task_attribute_tm_task1` (`user_id`),
  KEY `fk_tm_document_attribute_tm_document_attribute_type1` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tm_user_attribute`
--

INSERT INTO `tm_user_attribute` (`user_id`, `attribute_key`, `type_id`, `attribute_value`, `is_fill`) VALUES
(1, 'name', 1, 'Администратор', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `tm_user_attribute_type`
--

DROP TABLE IF EXISTS `tm_user_attribute_type`;
CREATE TABLE IF NOT EXISTS `tm_user_attribute_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `handler` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `tm_user_attribute_type`
--

INSERT INTO `tm_user_attribute_type` (`id`, `title`, `handler`, `description`) VALUES
(1, 'Строка', 'TM_Attribute_AttributeType', 'Любое строковое значение'),
(2, 'Текст', 'TM_Attribute_AttributeTypeText', 'Многострочный  текст'),
(3, 'Список', 'TM_Attribute_AttributeTypeList', 'Список из возможных вариантов');

-- --------------------------------------------------------

--
-- Структура таблицы `tm_user_hash`
--

DROP TABLE IF EXISTS `tm_user_hash`;
CREATE TABLE IF NOT EXISTS `tm_user_hash` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `attribute_key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `list_value` text,
  PRIMARY KEY (`attribute_key`),
  KEY `fk_tm_document_hash_tm_task_attribute_type1` (`type_id`),
  KEY `fk_tm_document_hash_tm_task1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tm_user_hash`
--

INSERT INTO `tm_user_hash` (`user_id`, `attribute_key`, `title`, `type_id`, `list_value`) VALUES
(NULL, 'name', 'Имя', 1, ' ');

-- --------------------------------------------------------

--
-- Структура таблицы `tm_user_resource`
--

DROP TABLE IF EXISTS `tm_user_resource`;
CREATE TABLE IF NOT EXISTS `tm_user_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `rtitle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=433 ;

--
-- Дамп данных таблицы `tm_user_resource`
--

INSERT INTO `tm_user_resource` (`id`, `title`, `rtitle`) VALUES
(1, 'login', 'Вход'),
(2, 'login/index', 'Вход'),
(3, 'login/logout', 'Выход'),
(4, 'index/index', 'Главная'),
(5, 'user', 'Пользователи'),
(6, 'user/add', ''),
(7, 'user/edit', 'Пользователи/Редактировать'),
(8, 'user/delete', ''),
(15, 'user/index', 'Пользователи'),
(19, 'user/addRole', 'Пользователи/Добавить роль'),
(20, 'user/editRole', ''),
(21, 'user/deleteRole', ''),
(22, 'user/addResource', ''),
(23, 'user/editResource', ''),
(24, 'user/deleteResource', ''),
(25, 'user/fillResource', ''),
(59, 'user/showRoleAcl', ''),
(67, 'user/addAttributeType', ''),
(68, 'user/editAttributeType', ''),
(69, 'user/deleteAttributeType', ''),
(70, 'user/addAttributeHash', ''),
(71, 'user/editAttributeHash', ''),
(72, 'user/deleteAttributeHash', ''),
(97, 'user/viewAttributeType', 'Пользователи/Показать типы атрибутов'),
(98, 'user/viewResource', 'Пользователи/Показать ресурсы'),
(99, 'user/viewHash', 'Пользователи/Показать список атрибутов'),
(411, 'error/error', 'error/error'),
(412, 'img/logo.png', 'img/logo.png'),
(413, 'img/slide1.jpg', 'img/slide1.jpg'),
(414, 'img/slide3.jpg', 'img/slide3.jpg'),
(415, 'img/slide4.jpg', 'img/slide4.jpg'),
(416, 'img/slide2.jpg', 'img/slide2.jpg'),
(417, 'menu/index', 'menu/index'),
(418, 'menu/add', 'menu/add'),
(419, 'menu/contentpagelist', 'menu/contentpagelist'),
(420, 'contentpage/index', 'contentpage/index'),
(421, 'contentpage/add', 'contentpage/add'),
(422, 'contentpage/edit', 'contentpage/edit'),
(423, 'contentpage/delete', 'contentpage/delete'),
(424, 'menu/edit', 'menu/edit'),
(425, 'Contentpage/view', 'Contentpage/view'),
(426, 'News/index', 'News/index'),
(427, 'news/add', 'news/add'),
(428, 'news/edit', 'news/edit'),
(429, 'js/jquery.min.map', 'js/jquery.min.map'),
(430, 'favicon.ico/index', 'favicon.ico/index'),
(431, 'News/view', 'News/view'),
(432, 'images/_main.jpg', 'images/_main.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `tm_user_role`
--

DROP TABLE IF EXISTS `tm_user_role`;
CREATE TABLE IF NOT EXISTS `tm_user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `rtitle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `tm_user_role`
--

INSERT INTO `tm_user_role` (`id`, `title`, `rtitle`) VALUES
(1, 'admin', 'Администратор'),
(5, 'guest', 'Гость');

-- --------------------------------------------------------

--
-- Структура таблицы `vote_email`
--

DROP TABLE IF EXISTS `vote_email`;
CREATE TABLE IF NOT EXISTS `vote_email` (
  `link_id` int(10) unsigned NOT NULL,
  `val` varchar(255) NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `tm_acl_role`
--
ALTER TABLE `tm_acl_role`
  ADD CONSTRAINT `tm_acl_role_ibfk_1` FOREIGN KEY (`tm_user_role_id`) REFERENCES `tm_user_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tm_acl_role_ibfk_2` FOREIGN KEY (`tm_user_resource_id`) REFERENCES `tm_user_resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tm_user`
--
ALTER TABLE `tm_user`
  ADD CONSTRAINT `tm_user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `tm_user_role` (`id`);

--
-- Ограничения внешнего ключа таблицы `tm_user_attribute`
--
ALTER TABLE `tm_user_attribute`
  ADD CONSTRAINT `tm_user_attribute_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tm_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tm_user_attribute_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `tm_user_attribute_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tm_user_hash`
--
ALTER TABLE `tm_user_hash`
  ADD CONSTRAINT `tm_user_hash_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `tm_user_attribute_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
