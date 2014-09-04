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
use TwitterOAuth as Client;
use cms_social\models\TwitterTweets;

class Twitter extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

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