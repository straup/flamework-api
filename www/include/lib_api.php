<?php

 	#################################################################

	# HEY LOOK! RUNNING CODE!!!

	loadlib("api_config");
	api_config_init();

 	#################################################################

	loadlib("api_output");
	loadlib("api_log");

	loadlib("api_auth");
	loadlib("api_keys");
	loadlib("api_keys_utils");
	loadlib("api_throttle");
	loadlib("api_utils");

	#################################################################

	function api_dispatch($method){

		if (! $GLOBALS['cfg']['enable_feature_api']){
			api_output_error(999, 'API disabled');
		}

		$method = filter_strict($method);
		$api_key = request_str("api_key");
		$access_token = request_str("access_token");

		# Log the basics

		api_log(array(
			'api_key' => $api_key,
			'method' => $method,
			'access_token' => $access_token,
			'remote_addr' => $_SERVER['REMOTE_ADDR'],
		));

		$methods = $GLOBALS['cfg']['api']['methods'];

		if ((! $method) || (! isset($methods[$method]))){
			api_output_error(404, "Method '{$enc_method}' not found");
		}

		$method_row = $methods[$method];

		if (! $method_row['enabled']){
			$enc_method = htmlspecialchars($method);
			api_output_error(404, "Method '{$enc_method}' not found");
		}

		$method_row['name'] = $method;

		if ($GLOBALS['cfg']['api_auth_type'] == 'oauth2'){

			if ($_SERVER['REQUEST_METHOD'] != 'POST'){
				api_output_error(405, 'Method not allowed');
			}
		}

		else if (isset($method_row['request_method'])){

			if ($_SERVER['REQUEST_METHOD'] != $method_row['request_method']){
				api_output_error(405, 'Method not allowed');
			}
		}

		# Okay – now we get in to validation and authorization. Which means a
		# whole world of pedantic stupid if we're using Oauth2. Note that you
		# could use OAuth2 and require API keys be passed explictly but since
		# that's not part of the spec if you enable the two features simultaneously
		# don't be surprised when hilarity ensues. Good times. (20121026/straup)

		# First API keys
 
		$key_row = null;

		if (features_is_enabled("api_require_keys")){

			if (! $api_key){
				api_output_error(999, "Required API key is missing");
			}

			$key_row = api_keys_get_by_key($api_key);
			api_keys_utils_ensure_valid_key($key_row);
		}

		# Second auth-y bits

		if ($GLOBALS['cfg']['api_auth_type'] == 'oauth2'){
			api_auth_ensure_auth($method_row, $key_row);
		}

		else if ($method_row['requires_auth']){
			api_auth_ensure_auth($method_row, $key_row);
		}

		else {}

		# Crumbs... because they are tastey

		if ($method_row['requires_crumb']){
			api_auth_ensure_crumb($method_row);
		}

		# GO!

		loadlib($method_row['library']);

		$parts = explode(".", $method);
		$method = array_pop($parts);

		$func = "{$method_row['library']}_{$method}";
		call_user_func($func);

		exit();
	}

	#################################################################

?>
