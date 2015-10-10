<?php

// broken images
$title = elgg_echo('tidypics:utilities:broken_images');
$body = elgg_autop(elgg_echo('tidypics:utilities:broken_images:blurb'));
$submit = elgg_view('input/submit', array(
	'value' => elgg_echo('search'),
	'id' => 'elgg-tidypics-broken-images'
));

$body .=<<<HTML
	<p>
		$submit
	</p>
	<div id="elgg-tidypics-broken-images-results"></div>
HTML;

echo elgg_view_module('inline', $title, $body);

elgg_require_js('tidypics_mfp');