<?php

namespace MFP\TidypicsHelper;

const PLUGIN_ID = 'tidypics_mfp';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
	elgg_extend_view('admin/settings/photos/delete_image', 'tidypics_mfp/image_check');
	elgg_register_action("photos/admin/broken_images", __DIR__ . "/actions/broken_images.php", 'admin');
	
	elgg_register_ajax_view('tidypics_mfp/log');
}

function get_last_log_line($filename) {
	$line = false;
	$f = false;
	if (file_exists($filename)) {
		$f = @fopen($filename, 'r');
	}

	if ($f === false) {
		return false;
	} else {
		$cursor = -1;

		fseek($f, $cursor, SEEK_END);
		$char = fgetc($f);

		/**
		 * Trim trailing newline chars of the file
		 */
		while ($char === "\n" || $char === "\r") {
			fseek($f, $cursor--, SEEK_END);
			$char = fgetc($f);
		}

		/**
		 * Read until the start of file or first newline char
		 */
		while ($char !== false && $char !== "\n" && $char !== "\r") {
			/**
			 * Prepend the new char
			 */
			$line = $char . $line;
			fseek($f, $cursor--, SEEK_END);
			$char = fgetc($f);
		}
	}

	return $line;
}


function get_log_location($time) {
	return elgg_get_config('dataroot') . PLUGIN_ID . '/' . $time . '.txt';
}