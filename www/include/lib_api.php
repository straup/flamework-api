<?php

	loadlib("api_auth");
	loadlib("api_keys");
	loadlib("api_output");

	#################################################################

	function api_dispatch($method){

		if (! $GLOBALS['cfg']['enable_feature_api']){
			api_output_error(999, 'API disabled');
		}

		$methods = $GLOBALS['cfg']['api']['methods'];

		if ((! $method) || (! isset($methods[$method]))){
			api_output_error(404, 'Method not found');
		}

		$method_row = $methods[$method];

		if (! $method_row['enabled']){
			api_output_error(404, 'Method not found');
		}

		# Personally, I prefer to just call the functions from
		# lib_sanitize but there are here if you need them...

		$args = (isset($method_row['require_post_args'])) ? $_POST : $_GET;

		# TO DO: check API keys here

		# TO DO: actually check auth here (whatever that means...)

		if ($method_row['requires_auth']){
			api_auth_ensure_auth($args);
		}

		loadlib($method_row['library']);

		$parts = explode(".", $method);
		$method = array_pop($parts);

		$func = "{$method_row['library']}_{$method}";
		call_user_func($func, $args);

		exit();
	}

	#################################################################

?>
