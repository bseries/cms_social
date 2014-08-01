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

Panes::register('external.socialStream', [
	'title' => $t('Social Stream'),
	'url' => ['controller' => 'stream', 'action' => 'index', 'library' => 'cms_social', 'admin' => true]
]);

?>