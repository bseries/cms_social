<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
 */

namespace cms_social\models;

use Exception;
use InvalidArgumentException;
use base_core\extensions\cms\Settings;
use base_media\models\Media;
use base_social\models\Instagram;
use base_social\models\Twitter;
use lithium\analysis\Logger;
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
		'base_core\extensions\data\behavior\Polymorphic',
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
			if ($data === false) {
				$message = 'Twitter poll failed for stream search: ' . var_export($search, true);
				Logger::write('notice', $message);

				continue;
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
			if ($data === false) {
				$message = 'Instagram poll failed for stream search: ' . var_export($search, true);
				Logger::write('notice', $message);

				continue;
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
		Logger::debug('Determining actions for stream with ' . count($results) . ' item/s total.');

		foreach ($results as $result) {
			if ($filter && !$filter($result)) {
				continue;
			}

			static::pdo()->beginTransaction();

			$item = static::find('count', [
				'conditions' => [
					'model' => $result->model(),
					'foreign_key' => $result->id()
				]
			]);
			if ($item || !($url = $result->url())) {
				// 1. We cannot reliably determine if an update was updated upstream. Once
				// its added to our database it stays immutable. It's assumed upstream
				// updates rarely happen. If they happen the item can simply be deleted by
				// the user from our DB.
				//
				// 2. Require URL as the schema cannot live without it.
				static::pdo()->rollback();
				continue;
			}
			$data = [
				'author' => $result->author(),
				'url' => $url,
				'title' => $result->title(),
				'body' => $result->body(),
				'raw' => json_encode($result->raw),
				'published' => $result->published(),
				'tags' => $result->tags()
			];
			$message  = 'Processing new stream item with model `' . $result->model() . '` id `';
			$message .= $result->id() ."`:\n" . var_export($data, true);
			Logger::debug($message);

			$item = static::create([
				'model' => $result->model(),
				'foreign_key' => $result->id(),

				'search' => $name,

				// Moved here as when autopublish is enabled it would otherwise
				// force manually unpublised items to become published again.
				'is_published' => $autopublish
			]);
			try {
				// Using internal => true, to just link the main item, but
				// make local version off it. By using the internal scheme
				// remote provider make handlers will correctly pick it up.
				if ($cover = $result->cover(['internal' => false])) {
					if (!$id = static::_handleMedia($cover)) {
						Logger::info('Failed handling media for stream item; not adding item.');
						static::pdo()->rollback();
						continue;
					}
					$data['cover_media_id'] = $id;
				}
				foreach ($result->media(['internal' => false]) as $medium) {
					if (!$id = static::_handleMedia($medium)) {
						Logger::info('Failed handling media for stream item; not adding item.');
						static::pdo()->rollback();
						continue;
					}
					$data['media'][] = ['id' => $id];
				}
			} catch (Exception $e) {
				$message  = "Skipping; exception while handling media:\n";
				$message .= "exception: " . ((string) $e);
				Logger::debug($message);

				static::pdo()->rollback();
				continue;
			}

			// Always update data on items; we may have changed the method accessor return values.
			if (!$item->save($data)) {
				static::pdo()->rollback();
				return false;
			}
			static::pdo()->commit();
		}
		return true;
	}

	protected static function _handleMedia(array $item) {
		$file = Media::create(['source' => 'stream'] + $item);

		if ($file->can('download')) {
			$file->url = $file->download();
		}
		if ($file->can('transfer')) {
			$file->url = $file->transfer();
		}

		// Deliberately skipping error checks in deletes, as we are failing already and do
		// not consider a failed delete a fatal condition.
		if (!$file->save()) {
			// We cannot yet delete the records (it failed to save) but we will
			// try to remove the transferred file, to not leave orphaned files.
			$file->deleteUrl();
			return false;
		}
		if (!$file->makeVersions()) {
			// After returning false, wrapping code will cause a txn rollback. We do not
			// "know" about that here an ensure we do not leave orphaned/unused records.
			$file->delete();
			return false;
		}
		return $file->id;
	}

	/* Deprecated / BC */

	// @deprecated
	public function type($entity, $separator = '/') {
		trigger_error(__METHOD__ . ' is deprecated in favor of polyType()', E_USER_DEPRECATED);
		return $entity->polyType($separator);
	}
}

?>