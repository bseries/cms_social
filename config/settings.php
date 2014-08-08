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

use cms_core\extensions\cms\Settings;

Settings::register('socialStream.autopublish', false);

Settings::register('service.tumblr.default.username');
Settings::register('service.vimeo.default.username');
Settings::register('service.facebook.default.appId');
Settings::register('service.facebook.default.appSecret');
Settings::register('service.facebook.default.pageUrl');
Settings::register('service.twitter.default.username');
Settings::register('service.twitter.default.consumerKey');
Settings::register('service.twitter.default.consumerSecret');
Settings::register('service.twitter.default.accessToken');
Settings::register('service.twitter.default.accessTokenSecret');

// Instagram Settings
//
// - How to get your Instagram Access Token -
//
//   1. Create app in Instagram developer interface.
//   2. As the redirect-URL use a non existent one on your website.
//   3. Open the following URL in your browser and note down the "code":
//      https://api.instagram.com/oauth/authorize/?client_id=[clientID]&redirect_uri=[redirectURI]&response_type=code
//   4. Issue the following command to get the access token:
//      curl -F 'client_id=[your_client_id]' -F 'client_secret=[your_secret_key]' -F 'grant_type=authorization_code' -F 'redirect_uri=[redirect_url]' -F 'code=[code]' https://api.instagram.com/oauth/access_token
//
// - How to get your Instagram user id -
//
// http://jelled.com/instagram/lookup-user-id
//
Settings::register('service.instagram.default.username');
Settings::register('service.instagram.default.userId');
Settings::register('service.instagram.default.accessToken');

?>