<?php

	loadlib("api_oauth1_5_access_tokens");
	loadlib("api_oauth1_5");

	#################################################################

	function api_auth_oauth1_5_has_auth(&$method, $key_row=null){

		$access_token = request_str("access_token");
		$api_sig = request_str("api_sig");

		if (! $access_token){
			return not_okay('Required access token missing', 400);
		}

		if (! $api_sig){
			return not_okay('Required API signature missing', 400);
		}

		$token_row = api_oauth1_5_access_tokens_get_by_token($access_token);

		if (! $token_row){
			return not_okay('Invalid access token', 400);
		}

		if (($token_row['expires']) && ($token_row['expires'] < time())){
			return not_okay('Invalid access token', 400);
		}

		if (isset($method['requires_perms'])){

			if ($token_row['perms'] < $method['requires_perms']){
				return not_okay('Insufficient permissions', 403);
			}
		}

		# TO DO : check API signature here

		$user = users_get_by_id($token_row['user_id']);

		if ((! $user) || ($user['deleted'])){
			return not_okay('Not a valid user', 400);
		}

		$GLOBALS['cfg']['api_access_token'] = $token_row;
		$GLOBALS['cfg']['user'] = $user;

		return okay();
	}

	#################################################################
?>
