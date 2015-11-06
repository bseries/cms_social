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

namespace cms_social\controllers;

use lithium\g11n\Message;
use li3_flash_message\extensions\storage\FlashMessage;

use cms_social\models\Stream;

class StreamController extends \base_core\controllers\BaseController {

	protected $_model = 'cms_social\models\Stream';

	use \base_core\controllers\AdminIndexTrait;
	use \base_core\controllers\AdminPublishTrait;
	use \base_core\controllers\AdminPromoteTrait;

	public function admin_poll() {
		extract(Message::aliases());

		Stream::poll();
		FlashMessage::write($t('Successfully polled.', ['scope' => 'cms_social']), [
			'level' => 'success'
		]);

		return $this->redirect(['action' => 'index', 'library' => 'cms_social']);
	}
}

?>