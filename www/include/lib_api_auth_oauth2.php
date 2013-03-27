<?php

	loadlib("api_oauth2_access_tokens");

	#################################################################

	function api_auth_oauth2_has_auth(&$method, $key_row=null){

		$access_token = post_str("access_token");

		if (! $access_token){
			return not_okay('Required access token missing', 400);
		}

		$token_row = api_oauth2_access_tokens_get_by_token($access_token);

		if (! $token_row){
			return not_okay('Invalid access token', 400);
		}

		if (($token_row['expires']) && ($token_row['expires'] < time())){
			return not_okay('Invalid access token', 400);
		}

		# I find it singularly annoying that we have to do this here
		# but OAuth gets what [redacted] wants. See also: notes in
		# lib_api.php around ln 65 (20121026/straup)

		$key_row = api_keys_get_by_id($token_row['api_key_id']);
		$rsp = api_keys_utils_is_valid_key($key_row);

		if (! $rsp['ok']){
			return $rsp;
		}

		if (isset($method['requires_perms'])){

			if ($token_row['perms'] < $method['requires_perms']){
				return not_okay('Insufficient permissions', 403);
			}
		}

		$user = users_get_by_id($token_row['user_id']);

		if ((! $user) || ($user['deleted'])){
			return not_okay('Not a valid user', 400);
		}

		return array(
			'ok' => 1,
			'access_token' => $token_row,
			'api_key' => $key_row,
			'user' => $user,
		);
	}

	#################################################################

	# the end
