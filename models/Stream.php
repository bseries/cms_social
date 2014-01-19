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
use cms_core\extensions\cms\Settings;
use textual\Modulation as Textual;
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

	public static function poll($frequency = null) {
		foreach (Settings::read('service.twitter') as $name => $config) {
			$results = Twitter::all($config);
			//var_dump($r);die;
			foreach ($results as $result) {
				$exists = Stream::find('count', [
					'conditions' => [
						'model' => 'cms_social\models\Twitter',
						'foreign_key' => $result['id'],
						'type' => 'tweet'
					]
				]);
				if ($exists) {
					continue;
				}
				$item = Stream::create([
					'excerpt' => Textual::limit($result['text'], 50),
					'model' => 'cms_social\models\Twitter',
					'foreign_key' => $result['id'],
					'type' => 'tweet',
					'raw' => json_encode($result),
					'published' => date('Y-m-d H:i:s', strtotime($result['created_at'])),
				]);
				$item->save();
			}
		}
	}
}

?>