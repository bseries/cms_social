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

use lithium\core\Environment;
use lithium\storage\Cache;
use Guzzle\Http\Client;

class Instagram extends \cms_core\models\Base {

	protected $_meta = array(
		'connection' => false
	);

	public static function latestImage() {
		$service = Environment::get('service.instagram');
		$cacheKey = 'instagram_latest_image_' . md5(serialize($service));

		if ($cached = Cache::read('default', $cacheKey)) {
			return $cached;
		}
		$results = static::_api("users/{$service['userId']}/media/recent");
		$result = static::create(array_shift($results));
		Cache::write('default', $cacheKey, $result);
		return $result;
	}

	public static function latestImages($limit = null) {
		$service = Environment::get('service.instagram');
		$cacheKey = 'instagram_latest_image_' . md5(serialize($service) . $limit);

		if ($cached = Cache::read('default', $cacheKey)) {
			return $cached;
		}
		$results =  static::_api("users/{$service['userId']}/media/recent");

		if ($limit) {
			$results = array_slice($results, 0, $limit);
		}
		foreach ($results as &$result) {
			$result = static::create($result);
		}
		Cache::write('default', $cacheKey, $results);
		return $results;
	}

	protected static function _api($url) {
		$service = Environment::get('service.instagram');

		$client = new Client('https://api.instagram.com/v1/');
		$request = $client->get($url);

		$request->getQuery()->set('access_token', $service['accessToken']);
		//$request->addHeader('Accept-Charset', 'utf-8');

		try {
			$response = $request->send();
		} catch (\Exception $e) {
			return false;
		}
		$result = json_decode($r= $response->getBody(), true);
		return $result['data'];
	}
}

?>