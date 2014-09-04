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

use lithium\net\http\Router;

$persist = ['persist' => ['admin', 'controller']];

Router::connect(
	'/admin/social-stream',
	['controller' => 'stream', 'action' => 'index', 'library' => 'cms_social', 'admin' => true],
	$persist
);
Router::connect(
	'/admin/social-stream/{:action}/{:id:[0-9]+}',
	['controller' => 'stream', 'library' => 'cms_social', 'admin' => true],
	$persist
);
Router::connect(
	'/admin/social-stream/{:action}/{:args}',
	['controller' => 'stream', 'library' => 'cms_social', 'admin' => true],
	$persist
);


?>