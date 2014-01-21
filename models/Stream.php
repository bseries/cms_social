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

namespace cms_social\models;

use cms_social\models\Twitter;
use lithium\util\Inflector;
use cms_core\extensions\cms\Settings;
use lithium\util\Set;

class Stream extends \cms_core\models\Base {

	protected $_meta = array(
		'source' => 'social_stream'
	);

	protected $_actsAs = [
		'cms_core\extensions\data\behavior\Timestamp'
	];

	public function body($entity) {
		if ($entity->type === 'tweet') {
			return $this->raw($entity, 'text');
		}
	}

	public function raw($entity, $path) {
		$result = json_decode($entity->raw, true);
		return current(Set::extract($result, '/' . str_replace('.', '/', $path)));
	}

	public function type($entity, $separator = '/') {
		list(, $type) = explode('\models\\', $entity->model);
		return str_replace('_', $separator, Inflector::underscore(substr($type, 0, -1)));
	}

	public static function poll($frequency = null) {
		foreach (Settings::read('service.twitter') as $name => $config) {
			$results = Twitter::all($config);
			//var_dump($results);die;
			//
			foreach ($results as $result) {
				if ($result->retweeted() || $result->replied()) {
					continue;
				}
				$exists = Stream::find('count', [
					'conditions' => [
						'model' => $result->model(),
						'foreign_key' => $result->id(),
					]
				]);
				if ($exists) {
					continue;
				}
				$item = Stream::create([
					'author' => $config['username'],
					// Tweets don't have titles but excerpts.
					'excerpt' => $result->excerpt(),
					'body' => $result->body(),
					'model' => $result->model(),
					'foreign_key' => $result->id(),
					'raw' => json_encode($result->raw),
					'published' => $result->published()
				]);
				$item->save();
			}
		}
	}
}

?>