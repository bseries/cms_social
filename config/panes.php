<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
 */

namespace cms_social\config;

use lithium\g11n\Message;
use base_core\extensions\cms\Panes;

extract(Message::aliases());

Panes::register('cms.socialStream', [
	'title' => $t('Social Stream', ['scope' => 'cms_social']),
	'url' => ['controller' => 'stream', 'action' => 'index', 'library' => 'cms_social', 'admin' => true],
	'weight' => 45
]);

?>