<?php

namespace MFP\TidypicsHelper;

use ElggBatch;

/**
 * Find and delete any images that don't have an image file
 *
 * Called through ajax
 *
 * @uses bool  $_REQUEST['delete'] Delete images
 * @uses array $_REQUEST['images'] Array of GUIDs to delete. (Required if delete == true)
 * 
 */

set_time_limit(0);
if (!is_dir(elgg_get_config('dataroot') . PLUGIN_ID)) {
	mkdir(elgg_get_config('dataroot') . PLUGIN_ID);
}
$logtime = get_input('time', false);

if (!$logtime) {
	error_log('invalid log time');
	exit;
}

$log = get_log_location($logtime);

// find or delete
$delete = (bool) get_input('delete', false);

$running_logtime = elgg_get_plugin_setting('current_log', PLUGIN_ID);
if ($running_logtime == $logtime && !$delete) {
	// this is a duplicate thread
	error_log('duplicate thread');
	exit;
}

elgg_set_plugin_setting('current_log', $logtime, PLUGIN_ID);


function batch_delete_images() {
	// delete
	$log = elgg_get_config('mfp_log');
	$log_time = elgg_get_config('mfp_logtime');

	$options = array(
		'type' => 'object',
		'subtype' => 'image',
		'metadata_name_value_pairs' => array(
			'name' => 'mfp_delete_check',
			'value' => $log_time
		),
		'limit' => 0
	);

	$images = new ElggBatch('elgg_get_entities_from_metadata', $options);
	$images->setIncrementOffset(false);

	$total = elgg_get_entities_from_metadata(array_merge($options, array('count' => true)));
	file_put_contents($log, "Starting deletion of {$total} images" . "\n", FILE_APPEND); 
	$i = 0;
	$count = 0;
	foreach ($images as $image) {
		$count++;

		if ($image->delete()) {
			$i++;
		}

		if ($count == 1 || !($count % 25)) {
			$time = date('m/d/Y g:i:s a');
			$j = $count - $i;
			$message = "Deleted {$i}, skipped {$j}, of {$total} images as of {$time}";
			file_put_contents($log, $message . "\n", FILE_APPEND);
		}
	}
	
	$message = '<div class="done">Completed: Deleted ' . $i . ', skipped ' . $j . ', of ' . $total . '</div>';
	file_put_contents($log, $message . "\n", FILE_APPEND); 
}



function batch_find_images() {
	$log = elgg_get_config('mfp_log');
	$logtime = elgg_get_config('mfp_logtime');
			
	// only search
	$options = array(
		'type' => 'object',
		'subtype' => 'image',
		'limit' => 0
	);

	$images = new ElggBatch('elgg_get_entities', $options);

	$count = 0;
	$bad_images = 0;
	$total = elgg_get_entities(array_merge($options, array('count' => true)));
	file_put_contents($log, "Starting scan of {$total} images" . "\n", FILE_APPEND); 
	foreach ($images as $image) {
		$count++;

		// don't use ->exists() because of #5207.
		if (!is_file($image->getFilenameOnFilestore())) {
			$bad_images++;
			$image->mfp_delete_check = $logtime;
		}

		if ($count == 1 || !($count % 25)) {
			$time = date('Y-m-d g:ia');
			$message = "Checked {$count} of {$total} images as of {$time}";
			file_put_contents($log, $message . "\n", FILE_APPEND);
		}
	}
	
	$message = '<div class="done"><a href="#" id="elgg-tidypics-broken-images-delete" data-time="' . $logtime . '">Delete ' . $bad_images . ' broken images</a></div>';
	file_put_contents($log, $message . "\n", FILE_APPEND); 
}


if ($delete) {
	file_put_contents($log, "Starting deletion of images" . "\n", FILE_APPEND);
	elgg_set_config('mfp_log', $log);
	elgg_set_config('mfp_logtime', $logtime);
	elgg_register_event_handler('shutdown', 'system', __NAMESPACE__ . '\\batch_delete_images');
	forward(REFERER);
} else {
	file_put_contents($log, "Starting scan of images" . "\n", FILE_APPEND);
	elgg_set_config('mfp_log', $log);
	elgg_set_config('mfp_logtime', $logtime);
	elgg_register_event_handler('shutdown', 'system', __NAMESPACE__ . '\\batch_find_images');
	forward(REFERER);
}
