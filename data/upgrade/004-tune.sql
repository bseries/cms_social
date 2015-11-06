ALTER TABLE `social_stream` CHANGE `is_published` `is_published` TINYINT(1)  NOT NULL  DEFAULT '0';
ALTER TABLE `social_stream` DROP INDEX `is_published`;
ALTER TABLE `social_stream` DROP INDEX `published`;
