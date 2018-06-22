CREATE TABLE `social_stream` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL,
  `foreign_key` varchar(100) NOT NULL,
  `cover_media_id` int(11) unsigned DEFAULT NULL,
  `search` varchar(100) NOT NULL DEFAULT 'default',
  `author` varchar(250) NOT NULL DEFAULT '',
  `title` varchar(250) DEFAULT '',
  `body` text NOT NULL,
  `tags` varchar(250) DEFAULT NULL,
  `raw` text NOT NULL,
  `url` varchar(250) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `is_promoted` tinyint(1) NOT NULL DEFAULT '0',
  `published` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model` (`model`,`foreign_key`)
) ENGINE=InnoDB;
