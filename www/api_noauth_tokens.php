<?php

	# noauth - as in "not oauth"

	include("include/init.php");

	loadlib("api_keys");
	loadlib("api_noauth_access_tokens");

	features_ensure_enabled("api");

	login_ensure_loggedin();

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	$rsp = api_noauth_access_tokens_for_user($GLOBALS['cfg']['user'], $more);
	$tokens = array();

	foreach ($rsp['rows'] as $row){

		$row['app'] = api_keys_get_by_id($row['api_key_id']);
		$tokens[] = $row;
	}

	$GLOBALS['smarty']->assign_by_ref("tokens", $tokens);

	$perms_map = api_noauth_access_tokens_permissions_map();
	$GLOBALS['smarty']->assign_by_ref("permissions", $perms_map);

	$GLOBALS['smarty']->display("page_api_noauth_tokens.txt");
	exit();

?>
