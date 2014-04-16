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

use lithium\g11n\Message;
use cms_core\extensions\cms\Panes;

extract(Message::aliases());

$base = ['controller' => 'stream', 'library' => 'cms_social', 'admin' => true];
Panes::registerActions('cms_social', 'authoring', [
	$t('Social Stream') => ['action' => 'index'] + $base,
	$t('Refresh Social Stream') => ['action' => 'poll'] + $base
]);

?>