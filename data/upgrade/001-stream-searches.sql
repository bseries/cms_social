ALTER TABLE `social_stream` ADD `search` VARCHAR(100)  NOT NULL  DEFAULT 'default'  AFTER `foreign_key`;
UPDATE social_stream SET search='default';
