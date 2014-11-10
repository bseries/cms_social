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

namespace cms_social\models;

use textual\Modulation as Textual;
use lithium\util\Set;

// Needs raw data from the Instagram API.
class InstagramMedia extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

	public function id($entity) {
		return $entity->raw['id'];
	}

	public function author($entity) {
		return $entity->raw['user']['username'];
	}

	public function title($entity) {
		return $entity->raw['caption']['text'];
	}

	public function url($entity) {
		return $entity->raw['link'];
	}

	public function body($entity) {
		// Make links work with HTTPS, too. By default instagram URLs are HTTP.
		$image = str_replace('http://', '//', $entity->raw['images']['standard_resolution']);

		$html = '';
		$html .= "<img width=\"{$image['width']}\" height=\"{$image['height']}\" src=\"{$image['url']}\">";

		return $html;
	}

	public function published($entity) {
		return date('Y-m-d H:i:s', $entity->raw['created_time']);
	}
}

?>