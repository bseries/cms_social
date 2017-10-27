<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
 */

namespace cms_social\config;

use base_core\async\Jobs;
use cms_social\models\Stream;

Jobs::recur('cms_social:stream', function() {
	return Stream::poll();
}, [
	'frequency' => Jobs::FREQUENCY_MEDIUM
]);

?>