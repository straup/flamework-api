<?php

	include("include/init.php");

	features_ensure_enabled("api");

	login_ensure_loggedin();

	loadlib("api_keys");
	loadlib("api_oauth2_access_tokens");

	$token = request_str("access_token");

	if (! $token){
		error_404();
	}

	$token_row = api_oauth2_access_tokens_get_by_token($token);

	if (! $token_row){
		error_404();
	}

	if ($token_row['user_id'] != $GLOBALS['cfg']['user']['id']){
		error_403();
	}

	if (($token_row['expires']) && ($token_row['expires'] < time())){
		error_404();
	}

	$token_row['app'] = api_keys_get_by_id($token_row['api_key_id']);

	$crumb_key = 'this_api_key';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if (post_isset('delete') && crumb_check($crumb_key)){

		$conf = post_str("confirm");

		if ($conf){
			$rsp = api_oauth2_access_tokens_delete($token_row);
			$GLOBALS['smarty']->assign_by_ref("delete_rsp", $rsp);
		}		

		$GLOBALS['smarty']->assign_by_ref("token", $token_row);

		$GLOBALS['smarty']->display("page_api_oauth2_token_delete.txt");
		exit();
	}

	else if (post_isset('done') && crumb_check($crumb_key)){

		$perms = post_str("perms");

		if (! api_oauth2_access_tokens_is_valid_permission($perms)){
			$GLOBALS['smarty']->assign("error", "bad_perms");
		}

		else {

			$update = array(
				'perms' => $perms,
			);

			if ($update_ttl = post_isset("update_ttl")){

				$ttl = post_str("update_ttl");
				$ttl = ($ttl == '') ? -1 : intval($ttl);			

				if ($ttl >= 0){
					$update['expires'] = ($ttl) ? time() + $ttl : 0;
				}
			}

			$update_rsp = api_oauth2_access_tokens_update($token_row, $update);
			$GLOBALS['smarty']->assign_by_ref("update_rsp", $update_rsp);

			if ($update_rsp['ok']){
				$token_row = $update_rsp['token'];
			}
		}
	}

	else {}

	$GLOBALS['smarty']->assign_by_ref("token", $token_row);

	$perms_map = api_oauth2_access_tokens_permissions_map();
	$GLOBALS['smarty']->assign_by_ref("permissions", $perms_map);

	$ttl_map = api_oauth2_access_tokens_ttl_map();
	$GLOBALS['smarty']->assign_by_ref("ttl_map", $ttl_map);

	$GLOBALS['smarty']->display("page_api_oauth2_token.txt");
	exit();
?>
