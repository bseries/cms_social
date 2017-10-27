<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
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
		set_time_limit(60 * 5);

		extract(Message::aliases());

		if (Stream::poll()) {
			FlashMessage::write($t('Successfully polled.', ['scope' => 'cms_social']), [
				'level' => 'success'
			]);
		} else {
			FlashMessage::write($t('Failed polling.', ['scope' => 'cms_social']), [
				'level' => 'error'
			]);
		}
		return $this->redirect(['action' => 'index', 'library' => 'cms_social']);
	}
}

?>