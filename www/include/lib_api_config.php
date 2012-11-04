<?php

	#################################################################

	function api_config_init(){

		# THIS IS NOT AWESOME. PLEASE MAKE ME BETTER.
		# (ON THE OTHER HAND, IT WORKS...)

		$api_config = FLAMEWORK_INCLUDE_DIR . "config.api.json";
		$fh = fopen($api_config, "r");

		if (! $fh){
			_api_config_freakout_and_die();
		}

		$data = fread($fh, filesize($api_config));
		fclose($fh);

		$api_config = json_decode($data, "as hash");

		if (! $api_config){
			_api_config_freakout_and_die();
		}

		$GLOBALS['cfg']['api'] = $api_config;

	}

	#################################################################

	function _api_config_freakout_and_die($reason=null){

		$msg = "The API is currently throwing a temper tantrum. That's not good.";

		if ($reason){
			$msg .= " This is what we know so far: {$reason}.";
		}

		# Because if we're here it's probably because the actual config
		# file is busted (20121026/straup)

		if (! isset($GLOBALS['cfg']['api']['default_format'])){
			$GLOBALS['cfg']['api']['default_format'] = 'json';
		}

		loadlib("api_output");
		loadlib("api_log");

		api_output_error(500, $msg);
		exit();
	}

	#################################################################
?>
