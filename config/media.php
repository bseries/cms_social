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

use base_media\models\Media;
use base_media\models\MediaVersions;
use cms_social\models\Vimeo;
use \Exception;

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
		$isImageVersion = MediaVersions::assembly('image', $entity->version);
		// TODO The below line will always return true. Check why?
		// $isVideoVersion = MediaVersions::assembly('video', $entity->version);
		$isVideoVersion = strpos($entity->version, 'flux') !== false;

		// If this is a vimeo video version we just use the parent
		// object in templates and don't store the url again here.
		if ($isVideoVersion) {
			return null;
		}

		// We will further only actually make vimeo poster images. Thus
		// any other versions are skipped.
		if (!$isImageVersion) {
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