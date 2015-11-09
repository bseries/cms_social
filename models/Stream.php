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

use Exception;
use InvalidArgumentException;
use base_core\extensions\cms\Settings;
use base_media\models\Media;
use base_social\models\Instagram;
use base_social\models\Twitter;
use lithium\util\Inflector;
use lithium\util\Set;

// TODO Implement Vimeo Polling and arbitrary base social sources.
class Stream extends \base_core\models\Base {

	protected $_meta = [
		'source' => 'social_stream'
	];

	public $belongsTo = [
		'CoverMedia' => [
			'to' => 'base_media\models\Media',
			'key' => 'cover_media_id'
		]
	];

	protected $_actsAs = [
		'base_core\extensions\data\behavior\Sluggable',
		'base_core\extensions\data\behavior\Timestamp',
		'li3_taggable\extensions\data\behavior\Taggable',
		'base_media\extensions\data\behavior\Coupler' => [
			'bindings' => [
				'cover' => [
					'type' => 'direct',
					'to' => 'cover_media_id'
				],
				'media' => [
					'type' => 'joined',
					'to' => 'base_media\models\MediaAttachments'
				]
		]
		],
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

	// Aliased from singular to make it similar to posts.
	public function authors($entity, array $options = []) {
		$options += ['serialized' => true];

		return $options['serialized'] ? $entity->author : [$entity->author];
	}

	/* Polling */

	public static function poll() {
		foreach (['twitter', 'instagram'] as $service) {
			if (!$settings = Settings::read('service.' . $service)) {
				throw new Exception("No settings found for service `{$service}`.");
			}
			$method = '_poll' . ucfirst($service);

			foreach ($settings as $s) {
				if (!$s['stream']) {
					continue;
				}
				if (!is_array($s['stream'])) {
					throw new InvalidArgumentException('`stream` option has wrong type.');
				}
				if (!is_array(current($s['stream'])) || is_numeric(key($s['stream']))) {
					throw new InvalidArgumentException('`stream` option must be an array of named arrays.');
				}
				if (!static::{$method}($s)) {
					return false;
				}
			}
		}
		return true;
	}

	protected static function _pollTwitter(array $config) {
		foreach ($config['stream'] as $name => $search) {
			if (isset($search['author'])) {
				$data = Twitter::allByAuthor($search['author'], $config);
			} elseif (isset($search['tag'])) {
				$data = Twitter::allByTag($search['tag'], $config);
			} elseif (isset($search['search'])) {
				$data = Twitter::search($search['search'], $config);
			} else {
				throw new Exception('No supported stream search action found.');
			}
			static::_update(
				$data,
				is_numeric($name) ? 'default' : $name,
				isset($search['filter']) ? $search['filter'] : null,
				!empty($search['autopublish'])
			);
		}
		return true;
	}

	protected static function _pollInstagram(array $config) {
		foreach ($config['stream'] as $name => $search) {
			if (isset($search['author'])) {
				$data = Instagram::allMediaByAuthor($search['author'], $config);
			} else {
				throw new Exception('No supported stream search action found.');
			}
			static::_update(
				$data,
				is_numeric($name) ? 'default' : $name,
				isset($search['filter']) ? $search['filter'] : null,
				!empty($search['autopublish'])
			);
		}
		return true;
	}

	protected static function _update(array $results, $name, $filter, $autopublish) {
		foreach ($results as $result) {
			if ($filter && !$filter($result)) {
				continue;
			}
			$item = static::find('first', [
				'conditions' => [
					'model' => $result->model(),
					'foreign_key' => $result->id()
				]
			]);
			$data = [
				'author' => $result->author(),
				'url' => $result->url(),
				'title' => $result->title(),
				'body' => $result->body(),
				'raw' => json_encode($result->raw),
				'published' => $result->published(),
				'tags' => $result->tags()
			];

			if (!$item) {
				$item = static::create([
					'model' => $result->model(),
					'foreign_key' => $result->id(),

					'search' => $name,

					// Moved here as when autopublish is enabled it would otherwise
					// force manually unpublised items to become published again.
					'is_published' => $autopublish
				]);

				// Using internal => true, to just link the main item, but
				// make local version off it. By using the internal scheme
				// remote provider make handlers will correctly pick it up.
				if ($cover = $result->cover(['internal' => true])) {
					$data['media_cover_id'] = $this->_handleMedia($cover);
				}
				foreach ($result->media(['internal' => true]) as $medium) {
					$data['media'][] = $this->_handleMedia($medium);
				}
			}

			// Always update data on items; we may have changed the method accessor return values.
			if (!$item->save($data)) {
				return false;
			}
		}
		return true;
	}

	protected function _handleTransfer($item) {
		$file = Media::create($item);

		if ($file->can('download')) {
			$file->url = $file->download();
		}
		if ($file->can('transfer')) {
			$file->url = $file->transfer();
		}
		try {
			$file->save();
			$file->makeVersions();
		} catch (Exception $e) {
			$message  = "Exception while saving transfer:\n";
			$message .= "with media entity: " . var_export($file->data(), true) . "\n";
			$message .= "exception: " . ((string) $e);
			Logger::debug($message);

			return false;
		}
		return $file->id;
	}
}

?>