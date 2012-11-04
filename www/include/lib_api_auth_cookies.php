<?php

	#################################################################

	function api_auth_cookies_has_auth(&$method, $key_row=null){

		$ok = ($GLOBALS['cfg']['user']['id']) ? 1 : 0;

		if (! $ok){
			return not_okay("Invalid user", 400);
		}

		if (isset($method['requires_perms'])){

			if ($method['requires_perms'] != 0){
				return not_okay("Insufficient permissions", 403);
			}
		}

		return okay()
	}

	#################################################################
?>
