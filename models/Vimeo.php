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

use lithium\storage\Cache;
use Guzzle\Http\Client;

class Vimeo extends \base_core\models\Base {

	protected $_meta = array(
		'connection' => false
	);

	public static function validate($id) {
		return (boolean) static::first($id);
	}

	public static function first($id) {
		$cacheKey = 'vimeo_videos_' . $id;

		if ($cached = Cache::read('default', $cacheKey)) {
			return $cached;
		}
		$result = static::_api('video/' . $id);

		if (!$result) {
			return false;
		}
		$result = static::create(array_shift($result));

		Cache::write('default', $cacheKey, $result);
		return $result;
	}

	public static function latest($username) {
		$cacheKey = 'vimeo_videos_latest_' . $username;

		if ($cached = Cache::read('default', $cacheKey)) {
			return $cached;
		}
		$results = static::_api($username . '/videos');

		$result = static::create(array_shift($results));
		Cache::write('default', $cacheKey, $result, '+1 day');
		return $result;
	}

	// @fixme Cache this.
	public static function all($username) {
		return static::_api($username . '/videos');
	}

	protected static function _api($url) {
		$client = new Client('https://vimeo.com/api/v2/');
		$request = $client->get($url . '.json');

		try {
			$response = $request->send();
		} catch (\Exception $e) {
			// var_dump($e->getMessage());
			return false;
		}
		return json_decode($response->getBody(), true);
	}
}


?>