CREATE TABLE `social_stream` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL,
  `foreign_key` varchar(100) NOT NULL,
  `author` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(250) DEFAULT '',
  `excerpt` varchar(250) DEFAULT NULL,
  `body` text NOT NULL,
  `raw` text NOT NULL,
  `url` varchar(250) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `published` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model` (`model`,`foreign_key`),
  KEY `is_published` (`is_published`),
  KEY `published` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
