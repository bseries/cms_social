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

use cms_media\models\Media;
use cms_media\models\MediaVersions;
use cms_social\models\Vimeo;
use mm\Mime\Type;
use Exception;

// Registers Media and MediaVersions schemes.

// Allows storing `vimeo://[ID]` style URLs but allow
// grouping the file by video type.
Media::registerScheme('vimeo', [
	'mime_type' => 'application/x-vimeo',
	'type' => 'video'
]);

// Uses Vimeo's thumbnail and generates our local versions off it. Will
// not store/link versions for the video files themselves as those cannot
// be reached through the Vimeo API. This handler doesn't actuall make the
// files itself but uses a generic file make handler to do so.
MediaVersions::registerScheme('vimeo', [
	'make' => function($entity) {
		$version = MediaVersions::assembly($entity->name(), $entity->version);

		// This handler only makes images. We don't want to download videos.
		// Leave this version untoudched and just store the vimeo link.
		if (!isset($version['convert']) || Type::guessName($version['convert']) !== 'image') {
			return null; // Indicate skip.
		}

		// This changes the scheme of the entity, thus
		// it capabilities.
		$video = Vimeo::first(str_replace('vimeo://', '', $entity->url));
		$entity->url = $video->thumbnail_large;

		if (!$entity->can('download')) {
			$message  = "Can't download vimeo poster URL `{$entity->url}`. ";
			$message .= "You need to register a http scheme with downloading enabled to do so.";
			throw new Exception($message);
		}
		$entity->url = $entity->download();

		$handler = MediaVersions::registeredScheme('file', 'make');
		return $handler($entity);
	}
]);

?>