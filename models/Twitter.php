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

use lithium\storage\Cache;
use TwitterOAuth as Client;
use cms_social\models\TwitterTweets;

class Twitter extends \cms_core\models\Base {

	protected $_meta = array(
		'connection' => false
	);

	/*
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
	 */

	/*
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
	 */

	public static function all(array $config) {
		$results = static::_api('/statuses/user_timeline', $config, [
			// 'trim_user' => true,
			// 'exclude_replies' => true
		]);
		foreach ($results as &$result) {
			$result = TwitterTweets::create(['raw' => $result]);
		}
		return $results;
	}

	protected static function _api($url, array $config, array $params = []) {
		$connection = new Client(
			$config['consumerKey'],
			$config['consumerSecret'],
			$config['accessToken'],
			$config['accessTokenSecret']
		);
		$connection->decode_json = false;

		$params['screen_name'] = $config['username'];

		$result = $connection->get($url, $params);
		return json_decode($result, true);
	}
}

?>