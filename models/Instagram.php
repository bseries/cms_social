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

use Guzzle\Http\Client;
use cms_social\models\InstagramMedia;

class Instagram extends \cms_core\models\Base {

	protected $_meta = array(
		'connection' => false
	);

	// Gets all media.
	// @link http://instagram.com/developer/endpoints/users/#get_users_media_recent
	public static function all(array $config) {
		$results = static::_api("users/{$config['userId']}/media/recent", $config);

		foreach ($results as &$result) {
			$result = InstagramMedia::create(['raw' => $result]);
		}

		return $results;
	}

	protected static function _api($url, array $config, array $params = []) {
		$client = new Client('https://api.instagram.com/v1/');
		$request = $client->get($url);

		$request->getQuery()->set('access_token', $config['accessToken']);
		// $request->addHeader('Accept-Charset', 'utf-8');

		try {
			$response = $request->send();
		} catch (\Exception $e) {
			return false;
		}
		$result = json_decode($response->getBody(), true);
		return $result['data'];
	}
}

?>