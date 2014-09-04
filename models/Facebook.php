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

use lithium\core\Environment;
use lithium\storage\Cache;
use Facebook as FacebookClient;

class Facebook extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

	public static function pageLikeCount() {
		$cacheKey = 'fb_clepto_page_like_count';

		if ($cached = Cache::read('default', $cacheKey)) {
			return $cached;
		}

		$service = Environment::get('service.facebook');

		$client = new FacebookClient([
			'appId' => $service['appId'],
			'secret' => $service['appSecret'],
			'fileUpload' => false
		]);
		$result = $client->api($service['pageId']);

		Cache::write('default', $cacheKey, $result['likes']);
		return $result['likes'];
	}
}

?>