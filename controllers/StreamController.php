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

namespace cms_social\controllers;

use cms_social\models\Stream;
use lithium\g11n\Message;
use li3_flash_message\extensions\storage\FlashMessage;

class StreamController extends \cms_core\controllers\BaseController {

	use \cms_core\controllers\AdminPublishTrait;

	public function admin_index() {
		$data = Stream::find('all', [
			'order' => ['published' => 'DESC']
		]);
		return compact('data');
	}

	public function admin_poll() {
		extract(Message::aliases());

		Stream::poll();
		FlashMessage::write($t('Successfully polled.'), ['level' => 'success']);

		return $this->redirect(['action' => 'index', 'library' => 'cms_social']);
	}
}

?>