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

// # Social Stream Searches
//
// Adds stream setting under the service key, to control
// streaming behavior.
//
// `'stream'` is either `false` to disable streaming
// entirely (the default) or an array of searches.
//
// Simple stream searches look like this:
// ```
// ['tag' => 'foo'],    // items with tag `foo`
// ['author' => 'bar'], // items authored by user `bar`
// ['search' => 'baz'], // items containing the term `baz`
// ```
//
// Named searches can be defined as follows the name of the
// search will be stored inside the `'search'` field of the
// stream item. It can later be used to retrieve results
// of certain searches only. The default search name is
// `'default'`.
//
// ```
// 'tagged' => ['tag' => 'foo'] // The name of the search is `'tagged'`.
// ['tag' => 'foo'] // The name of the search is `'default'`.
// ```
//
// A stream search may contain an additional filter function
// This allows to filter out unwanted items:
// ```
// ['author' => 'bar', 'filter' => function($item) {
//     return !$item->retweeted() && !$item->replied()
// }],
// ```
//
// The `'autopublish'` key allows to enable or disable
// (the default), autopublishing of the stream search:
// ```
// ['author' => 'bar', 'autopublish' => true]
// ```
Settings::write('service.twitter.default', [
	'stream' => false
] + Settings::read('service.twitter.default'));

Settings::write('service.vimeo.default', [
	'stream' => false
] + Settings::read('service.vimeo.default'));

Settings::write('service.instagram.default', [
	'stream' => false
] + Settings::read('service.instagram.default'));

?>