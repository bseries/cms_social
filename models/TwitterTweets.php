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

namespace cms_social\models;

use textual\Modulation as Textual;
use lithium\util\Set;

// Needs untruncated, untrimmed raw data from Twitter API.
class TwitterTweets extends \cms_core\models\Base {

	protected $_meta = array(
		'connection' => false
	);

	public function id($entity) {
		return $entity->raw['id'];
	}

	public function author($entity) {
		return $entity->raw['user']['screen_name'];
	}

	public function url($entity) {
		return 'https://twitter.com/' . $entity->raw['user']['screen_name'] . '/status/' . $entity->raw['id'];
	}

	public function retweeted($entity) {
		return $entity->raw['retweeted'];
	}

	public function replied($entity) {
		return (boolean) $entity->raw['in_reply_to_status_id'];
	}

	public function excerpt($entity) {
		return Textual::limit($entity->raw['text'], 20);
	}

	public function body($entity) {
		$text = $entity->raw['text'];
		$entities = $entity->raw['entities'];

		foreach ($entities['hashtags'] as $item) {
			$text = str_replace(
				'#' . $item['text'],
				'<a class="tweet-hashtag" href="https://search.twitter.com/search?q=' . $item['text'] . '" target="new">' . '#' . $item['text'] . '</a>',
				$text
			);
		}
		foreach ($entities['urls'] as $item) {
			$text = str_replace(
				$item['url'],
				"<a class=\"tweet-url\" href=\"{$item['expanded_url']}\" target=\"new\">" . Textual::limit($item['display_url'], 21) . "</a>",
				$text
			);
		}
		foreach ($entities['user_mentions'] as $item) {
			$text = str_replace(
				'@' . $item['screen_name'],
				"<a class=\"tweet-user-mention\" href=\"https://twitter.com/{$item['screen_name']}\" target=\"new\">@{$item['screen_name']}</a>",
				$text
			);
		}

		// Twitter not always includes this parameter.
		if (isset($entities['media'])) {
			foreach ($entities['media'] as $item) {
				if ($item['type'] == 'photo') {
					$text = str_replace(
						$item['url'],
						// "<img src=\"{$item['media_url_https']}\">",
						"<a class=\"tweet-media\" href=\"{$item['media_url_https']}\" target=\"new\">{$item['display_url']}</a>",
						$text
					);
				}
			}
		}
		$text = Textual::autoLink($text, ['html' => true]);

		return $text;
	}

	public function published($entity) {
		return date('Y-m-d H:i:s', strtotime($entity->raw['created_at']));
	}
}

?>