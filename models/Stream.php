<?php
/**
 * CMS Social
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * Licensed under the AD General Software License v1.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * You should have received a copy of the AD General Software
 * License. If not, see http://atelierdisko.de/licenses.
 */

namespace cms_social\models;

use lithium\util\Inflector;
use lithium\util\Set;

use base_social\models\Twitter;
use base_social\models\Instagram;
use base_core\extensions\cms\Settings;

class Stream extends \base_core\models\Base {

	protected $_meta = [
		'source' => 'social_stream'
	];

	protected $_actsAs = [
		'base_core\extensions\data\behavior\Timestamp',
		'base_core\extensions\data\behavior\Searchable' => [
			'fields' => [
				'author',
				'title'
			]
		]
	];

	public function raw($entity, $path) {
		$result = json_decode($entity->raw, true);
		return current(Set::extract($result, '/' . str_replace('.', '/', $path)));
	}

	public function type($entity, $separator = '/') {
		list(, $type) = explode('\models\\', $entity->model);
		return str_replace('_', $separator, Inflector::underscore(Inflector::singularize($type)));
	}

	/* Polling */

	public static function poll() {
		foreach (['twitter', 'instagram'] as $service) {
			if (!$settings = Settings::read('service.' . $service)) {
				throw new Exception("No settings found for service `{$service}`.");
			}
			$method = '_poll' . ucfirst($service);

			foreach ($settings as $s) {
				if ($s['stream']) {
					if (!static::{$method}($s)) {
						return false;
					}
				}
			}
		}
		return true;
	}

	protected static function _pollTwitter(array $config) {
		$normalize = function($item) {
			return [
				'author' => $item->author(),
				'url' => $item->url(),
				// Tweets don't have titles but excerpts.
				'excerpt' => $item->excerpt(),
				'body' => $item->body(),
				'raw' => json_encode($item->raw),
				'published' => $item->published()
			];
		};

		foreach ($config['stream'] as $name => $search) {
			if (isset($search['author'])) {
				$data = Twitter::allByAuthor($search['author'], $config);
			} elseif (isset($search['tag'])) {
				$data = Twitter::allByTag($search['tag'], $config);
			} elseif (isset($search['search'])) {
				$data = Twitter::search($search['search'], $config);
			} else {
				throw Exception('No supported stream search action found.');
			}
			static::_update(
				$data,
				$normalize,
				is_numeric($name) ? 'default' : $name,
				isset($search['filter']) ? $search['filter'] : null,
				!empty($search['autopublish'])
			);
		}
		return true;
	}

	protected static function _pollInstagram(array $config) {
		$normalize = function($item) {
			return [
				'author' => $item->author(),
				'url' => $item->url(),
				'title' => $item->title(),
				'body' => $item->body(),
				'raw' => json_encode($item->raw),
				'published' => $item->published()
			];
		};
		foreach ($config['stream'] as $name => $search) {
			if (isset($search['author'])) {
				$data = Instagram::allMediaByAuthor($search['author'], $config);
			} else {
				throw Exception('No supported stream search action found.');
			}
			static::_update(
				$data,
				$normalize,
				is_numeric($name) ? 'default' : $name,
				isset($search['filter']) ? $search['filter'] : null,
				!empty($search['autopublish'])
			);
		}
		return true;
	}

	protected static function _update(array $results, $normalize, $name, $filter, $autopublish) {
		foreach ($results as $result) {
			if ($filter && !$filter($result)) {
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

					'search' => $name,

					// Moved here as when autopublish is enabled it would otherwise
					// force manually unpublised items to become published again.
					'is_published' => $autopublish
				]);
			}
			// Always update data on items; we may have changed the method accessor return values.
			if (!$item->save($normalize($result))) {
				return false;
			}
		}
		return true;
	}
}

?>