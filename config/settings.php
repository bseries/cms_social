<?php
/**
 * CMS Social
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 */

use base_core\extensions\cms\Settings;

// Two settings for each social service are available:
//
// 1. autopublish, allows to automatically publish new
//    entities in stream.
// 2. stream, allows to in or exclude service from
//    social stream.

Settings::register('service.tumblr.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.tumblr.default'));

Settings::register('service.vimeo.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.vimeo.default'));

Settings::register('service.facebook.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.vimeo.default'));

Settings::register('service.twitter.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.twitter.default'));

Settings::register('service.instagram.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.twitter.default'));

?>