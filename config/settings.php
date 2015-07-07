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

namespace cms_social\config;

use base_core\extensions\cms\Settings;

// Two settings for each social service are available:
//
// 1. autopublish, allows to automatically publish new
//    entities in stream.
// 2. stream, allows to in or exclude service from
//    social stream.

Settings::write('service.tumblr.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.tumblr.default'));

Settings::write('service.vimeo.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.vimeo.default'));

Settings::write('service.facebook.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.vimeo.default'));

Settings::write('service.twitter.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.twitter.default'));

Settings::write('service.instagram.default', [
	'autopublish' => false,
	'stream' => false
] + Settings::read('service.twitter.default'));

?>