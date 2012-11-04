<?php

	#################################################################

	loadlib("api_oauth2_tokens");

	function api_oauth2_tokens_utils_get_from_url($more=array()){

		$token = request_str("access_token");

		if (! $token){
			error_404();
		}

		$token_row = api_oauth2_tokens_get_by_token($token);

		if (! $token_row){
			error_404();
		}

		if ($more['ensure_isown']){

			if (! $token_row['user_id'] != $GLOBALS['cfg']['user']['id']){
				error_403();
			}
		}

		return $token_row;
	}

	#################################################################

?>
