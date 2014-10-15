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

use base_core\extensions\cms\Settings;

Settings::register('service.tumblr.default', [
	'autopublish' => false,
	'username' => null
]);
Settings::register('service.vimeo.default', [
	'autopublish' => false,
	'username' => null
]);
Settings::register('service.facebook.default', [
	'autopublish' => false,
	'appId' => null,
	'appSecret' => null,
	'pageUrl' => null
]);
Settings::register('service.twitter.default', [
	'autopublish' => false,
	'username' => null,
	'consumerKey' => null,
	'accessToken' => null,
	'accessTokenSecret' => null
]);

// Instagram Settings
//
// - How to get your Instagram Access Token -
//
//   1. Create app in Instagram developer interface.
//   2. As the redirect-URL use a non existent one on your website.
//   3. Open the following URL in your browser and note down the "code":
//      https://api.instagram.com/oauth/authorize/?client_id=[client_id]&redirect_uri=[redirect_uri]&response_type=code
//   4. Issue the following command to get the access token:
//      curl \
//        -F 'client_id=[your_client_id]' \
//        -F 'client_secret=[your_secret_key]' \
//        -F 'grant_type=authorization_code' \
//        -F 'redirect_uri=[redirect_url]' \
//        -F 'code=[code]' \
//        https://api.instagram.com/oauth/access_token; echo ''

// - How to get your Instagram user id -
//
// http://jelled.com/instagram/lookup-user-id
//
Settings::register('service.instagram.default', [
	'autopublish' => false,
	'username' => null,
	'userId' => null,
	'accessToken' => null
]);

?>