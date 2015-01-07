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

use TwitterOAuth\Auth\SingleUserAuth as ClientAuth;
use TwitterOAuth\Serializer\ArraySerializer as ClientSerializer;
use cms_social\models\TwitterTweets;
use Exception;

class Twitter extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

	public static function all(array $config) {
		$results = static::_api('/statuses/user_timeline', $config, [
			// Show tweets by us only.
			'screen_name' => $config['username'],
			'exclude_replies' => true
		]);

		foreach ($results as &$result) {
			$result = TwitterTweets::create(['raw' => $result]);
		}
		return $results;
	}

	protected static function _api($url, array $config, array $params = []) {
		$connection = new ClientAuth([
			'consumer_key' => $config['consumerKey'],
			'consumer_secret' => $config['consumerSecret'],
			'oauth_token' => $config['accessToken'],
			'oauth_token_secret' => $config['accessTokenSecret']
		], new ClientSerializer());

		return $connection->get($url, $params);
	}
}

?>