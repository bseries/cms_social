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

namespace cms_social\models;

use cms_social\models\Twitter;
use cms_social\models\Instagram;
use lithium\util\Inflector;
use base_core\extensions\cms\Settings;
use lithium\util\Set;

class Stream extends \base_core\models\Base {

	protected $_meta = [
		'source' => 'social_stream'
	];

	protected static $_actsAs = [
		'base_core\extensions\data\behavior\Timestamp'
	];

	public function raw($entity, $path) {
		$result = json_decode($entity->raw, true);
		return current(Set::extract($result, '/' . str_replace('.', '/', $path)));
	}

	public function type($entity, $separator = '/') {
		list(, $type) = explode('\models\\', $entity->model);
		return str_replace('_', $separator, Inflector::underscore(Inflector::singularize($type)));
	}

	public static function poll() {
		$settings = Settings::read('service.twitter');
		foreach ($settings as $s) {
			if (isset($s['accessToken'])) {
				static::_pollTwitter($s);
			}
		}

		$settings = Settings::read('service.instagram');
		foreach ($settings as $s) {
			if (isset($s['accessToken'])) {
				static::_pollInstagram($s);
			}
		}
	}

	protected static function _pollTwitter($config) {
		$results = Twitter::all($config);

		foreach ($results as $result) {
			var_dump($result);
			die;
			if ($result->retweeted() || $result->replied()) {
				continue;
			}
			$item = Stream::find('first', [
				'conditions' => [
					'model' => $result->model(),
					'foreign_key' => $result->id()
				]
			]);
			if (!$item) {
				$item = Stream::create([
					'model' => $result->model(),
					'foreign_key' => $result->id(),
					// Moved here as when autopublish is enabled it would otherwise
					// force manually unpublised items to become published again.
					'is_published' => $config['autopublish']
				]);
			}
			// Always update data on items; we may have changed the method accessor return values.
			$data = [
				'author' => $result->author(),
				'url' => $result->url(),
				// Tweets don't have titles but excerpts.
				'excerpt' => $result->excerpt(),
				'body' => $result->body(),
				'raw' => json_encode($result->raw),
				'published' => $result->published()
				// Do not set is_published here, see note above.
			];
			$item->save($data);
		}
	}

	protected static function _pollInstagram($config) {
		$results = Instagram::all($config);

		if (!$results) {
			return $results;
		}
		foreach ($results as $result) {
			$item = Stream::find('first', [
				'conditions' => [
					'model' => $result->model(),
					'foreign_key' => $result->id()
				]
			]);
			if (!$item) {
				$item = Stream::create([
					'model' => $result->model(),
					'foreign_key' => $result->id(),
					// Moved here as when autopublish is enabled it would otherwise
					// force manually unpublised items to become published again.
					'is_published' => $config['autopublish']
				]);
			}
			// Always update data on items.
			$data = [
				'author' => $result->author(),
				'url' => $result->url(),
				'title' => $result->title(),
				'body' => $result->body(),
				'raw' => json_encode($result->raw),
				'published' => $result->published()
				// Do not set is_published here, see note above.
			];
			$item->save($data);
		}
	}
}

?>