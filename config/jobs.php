<?php
/**
 * CMS Social
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * Licensed under the AD General Software License v1.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * You should have received a copy of the AD General Software
 * License. If not, see http://atelierdisko.de/licenses.
 */

namespace cms_social\config;

use base_core\extensions\cms\Jobs;
use cms_social\models\Stream;

Jobs::recur('cms_social:stream', function() {
	Stream::poll();
}, [
	'frequency' => Jobs::FREQUENCY_MEDIUM
]);

?>