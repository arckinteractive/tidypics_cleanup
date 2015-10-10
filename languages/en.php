<?php

$english = array(
		// broken images
			'tidypics:utilities:broken_images' => 'Find Broken Images',
			'tidypics:utilities:broken_images:blurb' =>
	'This tool finds and allows you to delete images that do not have associated data files.
	If you have many images, this will take a long time.
	
	Warning: If your images are not showing up after making changes the site settings or data path, '
	. 'running this tool will likely delete all images from the database. '
	. 'Visit the Elgg community site for troubleshooting tips.',

		'tidypics:utilities:broken_images:found_images' => 'Broken images found: %s',
		'tidypics:utilities:broken_images:delete' => 'Delete all broken images.',
		'tidypics:utilities:broken_images:deleted_images' => 'Broken images removed: %s',
);

add_translation("en", $english);
