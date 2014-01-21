<?php
/**
 * Bureau Social
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 */

use lithium\g11n\Message;
use cms_core\extensions\cms\Panes;
use cms_core\extensions\cms\Settings;
use cms_media\models\Media;
use cms_media\models\MediaVersions;
use cms_social\models\Vimeo;
use lithium\core\Libraries;

Libraries::add('twitteroauth', [
	'path' => dirname(__DIR__) . '/libraries/twitteroauth/twitteroauth',
	'prefix' => false,
	'transform' => function($class, $config) {
		if ($class != 'TwitterOAuth') {
			return false;
		}
		return $config['path'] . '/twitteroauth.php';
	}
]);
Libraries::add('facebook', array(
	'path' => dirname(__DIR__) . '/libraries/facebook/src',
	'bootstrap' => 'facebook.php'
));

extract(Message::aliases());

Panes::register('cms_social', 'stream', [
	'title' => $t('Social Stream'),
	'group' => Panes::GROUP_AUTHORING,
	'url' => ['controller' => 'stream', 'library' => 'cms_social', 'admin' => true]
]);

Settings::register('cms_social', 'service.tumblr.default.username');
Settings::register('cms_social', 'service.vimeo.default.username');
Settings::register('cms_social', 'service.facebook.default.appId');
Settings::register('cms_social', 'service.facebook.default.appSecret');
Settings::register('cms_social', 'service.facebook.default.pageUrl');
Settings::register('cms_social', 'service.twitter.default.username');
Settings::register('cms_social', 'service.twitter.default.consumerKey');
Settings::register('cms_social', 'service.twitter.default.consumerSecret');
Settings::register('cms_social', 'service.twitter.default.accessToken');
Settings::register('cms_social', 'service.twitter.default.accessTokenSecret');
Settings::register('cms_social', 'service.instagram.default.username');
Settings::register('cms_social', 'service.instagram.default.userId');
Settings::register('cms_social', 'service.instagram.default.accessToken');

// Registers Media and MediaVersions schemes.

// Allows storing `vimeo://[ID]` style URLs but allow
// grouping the file by video type.
Media::registerScheme('vimeo', [
	'mime_type' => 'application/x-vimeo',
	'type' => 'video'
]);

// Uses Vimeo's thumbnail and generates our local versions off it. Will
// not store/link versions for the video files themselves as  those cannot
// be reached through the Vimeo API.
MediaVersions::registerScheme('vimeo', [
	'make' => function($entity) {
		// @fixme Do not hardcode this. Check if convert target is
		// image mime type instead.
		if (strpos($entity->version, 'fix') === false) {
			return null; // Indicate skip.
		}
		// This changes the scheme of the entity, thus
		// it capabilities.
		$video = Vimeo::first(str_replace('vimeo://', '', $entity->url));
		$entity->url = $video->thumbnail_large;

		if (!$entity->can('download')) {
			return null;
		}
		$entity->url = $entity->download();

		$handler = MediaVersions::registeredScheme('file', 'make');
		return $handler($entity);
	}
]);

?>