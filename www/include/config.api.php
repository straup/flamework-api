<?php

	$GLOBALS['cfg']['enable_feature_api'] = 1;

	$GLOBALS['cfg']['api'] = array(

		'default_format' => 'json',

		'valid_formats' => array(
			'json',
		),

		'methods' => array(

			'test.echo' => array(
				'documented' => 1,
				'enabled' => 1,
				'library' => 'api_test',
			),

			'test.error' => array(
				'documented' => 1,
				'enabled' => 1,
				'library' => 'api_test',
			),

		),
	);

?>