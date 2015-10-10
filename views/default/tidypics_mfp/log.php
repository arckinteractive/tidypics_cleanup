<?php

namespace MFP\TidypicsHelper;

if (!elgg_is_admin_logged_in()) {
	return;
}

$logtime = get_input('time');
if (!$logtime) {
	$logtime = elgg_get_plugin_setting('current_log', PLUGIN_ID);
}

$log = get_log_location($logtime);

echo get_last_log_line($log);