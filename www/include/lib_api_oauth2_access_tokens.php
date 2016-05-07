<?php

	#################################################################

	# TO DO: put me in the config?
	# (20121103/straup)

	function api_oauth2_access_tokens_permissions_map($string_keys=0){

		$map = array(
			'0' => 'login',
			'1' => 'read',
			'2' => 'write',
			'3' => 'delete',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function api_oauth2_access_tokens_permissions_id_to_label($id){

		$map = api_oauth2_access_tokens_permissions_map();
		return (isset($map[$id])) ? $map[$id] : null;
	}

	#################################################################

	function api_oauth2_access_tokens_ttl_map($string_keys=0){

		$map = array(
			'0' => 'until I revoke it',
			'3600' => 'for one hour',
			'21600' => 'for six hours',
			'86400' => 'for a day',
			'604800' => 'for one week',
			'2592000' => 'for a month',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function api_oauth2_access_tokens_is_valid_permission($perm, $str_perm=0){
		$map = api_oauth2_access_tokens_permissions_map($str_perm);
		return (isset($map[$perm])) ? 1 : 0;
	}

	#################################################################

	function api_oauth2_access_tokens_get_by_id($id){

		$cache_key = "oauth2_access_token_id_{$id}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
		 	return $cache['data'];
		}

		$enc_id = AddSlashes($id);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE id='{$enc_id}'";
		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_oauth2_access_tokens_get_by_token($token){

		$cache_key = "oauth2_access_token_{$token}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_token = AddSlashes($token);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE access_token='{$enc_token}'";
		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_oauth2_access_tokens_for_user(&$user, $more=array()){

		$enc_user = AddSlashes($user['id']);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE user_id='{$enc_user}' AND (expires=0 OR expires > UNIX_TIMESTAMP(NOW()))";

		if (features_is_enabled(array("api_site_keys", "api_site_tokens"))){
			$sql .= " AND api_key_role_id=0";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		return $rsp;		
	}

	#################################################################

	function api_oauth2_access_tokens_for_key(&$key, $more=array()){

		$enc_key = AddSlashes($key['id']);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE api_key_id='{$enc_key}' AND (expires=0 OR expires > UNIX_TIMESTAMP(NOW()))";

		if (features_is_enabled(array("api_site_keys", "api_site_tokens"))){
			# pretty sure we don't want to filter on this
			# but just in case... (20130711/straup)
			# $sql .= " AND api_key_role_id=0";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		return $rsp;		
	}

	#################################################################

	function api_oauth2_access_tokens_count_for_key(&$key){

		$more = array(
			'per_page' => 1,
		);

		$rsp = api_oauth2_access_tokens_for_key($key, $more);
		return $rsp['pagination']['total_count'];
	}

	#################################################################

	function api_oauth2_access_tokens_get_for_user_and_key(&$user, &$key){

		$cache_key = "oauth2_access_token_uk_{$user['id']}_{$key['id']}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			# return $cache['data'];
		}

		$enc_user = AddSlashes($user['id']);
		$enc_key = AddSlashes($key['id']);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE user_id='{$enc_user}' AND api_key_id='{$enc_key}'  AND (expires=0 OR expires > UNIX_TIMESTAMP(NOW()))";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_oauth2_access_tokens_create(&$key, &$user, $perms, $ttl=0){

		$id = dbtickets_create(64);

		$token = api_oauth2_access_tokens_generate_token();
		$now = time();

		$row = array(
			'id' => $id,
			'perms' => $perms,
			'api_key_id' => $key['id'],
			'user_id' => $user['id'],
			'access_token' => $token,
			'created' => $now,
			'last_modified' => $now,
		);

		if (intval($ttl) > 0){
			$row['expires'] = $now + $ttl;
		}

		$insert = array();

		foreach ($row as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('OAuth2AccessTokens', $insert);

		if ($rsp['ok']){
			$rsp['token'] = $row;
		}

		return $rsp;
	}

	#################################################################

	function api_oauth2_access_tokens_update(&$token, $update){

		$update['last_modified'] = time();

		$insert = array();

		foreach ($update as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($token['id']);
		$where = "id='{$enc_id}'";

		$rsp = db_update('OAuth2AccessTokens', $update, $where);

		if ($rsp['ok']){

			api_oauth2_access_tokens_purge_cache($token);

			$token = array_merge($token, $update);
			$rsp['token'] = $token;
		}

		return $rsp;
	}

	#################################################################

	function api_oauth2_access_tokens_disable(&$token){
		$update = array('disabled' => time());
		return api_oauth2_access_tokens_update($token, $update);
	}

	#################################################################

	function api_oauth2_access_tokens_enable(&$token){
		$update = array('disabled' => 0);
		return api_oauth2_access_tokens_update($token, $update);
	}

	#################################################################

	# THERE IS NO UNDO...

	function api_oauth2_access_tokens_delete(&$token){

		$enc_id = AddSlashes($token['id']);
		$sql = "DELETE FROM OAuth2AccessTokens WHERE id='{$enc_id}'";

		$rsp = db_write($sql);

		if ($rsp['ok']){
			api_oauth2_access_tokens_purge_cache($token);
		}

		return $rsp;
	}

	#################################################################

	function api_oauth2_access_tokens_delete_for_key(&$key){

		$enc_key = AddSlashes($key['id']);
		$sql = "DELETE FROM OAuth2AccessTokens WHERE api_key_id='{$enc_key}'";

		# TO DO: purge caches - iterate over all the things?
		# (20121103/straup)

		$rsp = db_write($sql);
		return $rsp;
	}

	#################################################################

	function api_oauth2_access_tokens_purge_cache(&$token){

		$cache_keys = array(
			"oauth2_access_token_{$token['access_token']}",
			"oauth2_access_token_id_{$token['id']}",
			"oauth2_access_token_uk_{$token['user_id']}_{$token['api_key_id']}",
		);

		foreach ($cache_keys as $key){
			cache_unset($key);
		}
	}

	#################################################################

	function api_oauth2_access_tokens_generate_token(){
		$token = md5(random_string(100) . time());
		return $token;
	}

	#################################################################

	function api_oauth2_access_tokens_fetch_site_token($user=null){

		$now = time();

		$site_token = api_oauth2_access_tokens_get_site_token($user);

		if ($site_token){

			$valid_key = 1;
			$valid_token = 1;

			$key = api_keys_get_by_id($site_token['api_key_id']);

			if (! $key){
				$valid_key = 0;
			}

			else if ($key['deleted']){
				$valid_key = 0;
			}

			else if (($key['expires']) && ($key['expires'] <= $now)){
				$valid_key = 0;
			}

			else if ($site_token['expires'] <= $now){
				$valid_token = 0;
			}

			# Now we check to see if either the key or the token will
			# expire in <some unknown amount of time that a user will
			# stay on the page...> and just create new ones if so.

			else {

				$ttl_key = $key['expires'] - $now;
				$ttl_token = $site_token['expires'] - $now;

				if ($ttl_key < 300){
					$valid_key = 0;
				}  

				if ($ttl_token < 300){
					$valid_token = 0;
				}  
			}

			if ((! $valid_key) || (! $valid_token)){

				$rsp = api_oauth2_access_tokens_delete($site_token);

				$user_id = ($user) ? $user['id'] : 0;
				$cache_key = "oauth2_access_token_site_{$user_id}";
				cache_unset($cache_key);

				$site_token = null;
			}
		}

		# TO DO: error handling / reporting

		if (! $site_token){

			$rsp = api_oauth2_access_tokens_create_site_token($user);
			$site_token = $rsp['token'];
		}

		return $site_token;
	}

	#################################################################

	function api_oauth2_access_tokens_get_site_token($user=null){

		$user_id = ($user) ? $user['id'] : 0;
		
		$cache_key = "oauth2_access_token_site_{$user_id}";
		$cache = cache_get($cache_key);

		if (($cache['ok']) && ($cache['data'])){
			return $cache['data'];
		}

		$site_key = api_keys_fetch_site_key();

		$enc_user = AddSlashes($user_id);
		$enc_key = AddSlashes($site_key['id']);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE user_id='{$enc_user}' AND api_key_id='{$enc_key}'  AND (expires=0 OR expires > UNIX_TIMESTAMP(NOW()))";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_oauth2_access_tokens_create_site_token($user=null){

		$site_key = api_keys_fetch_site_key();

		$id = dbtickets_create(64);

		$user_id = ($user) ? $user['id'] : 0;

		$token = api_oauth2_access_tokens_generate_token();

		$ttl = ($user) ? $GLOBALS['cfg']['api_site_tokens_user_ttl'] : $GLOBALS['cfg']['api_site_tokens_ttl'];
		$now = time();

		$expires = $now + $ttl;

		$perms_map = api_oauth2_access_tokens_permissions_map('string keys');
		$perms = ($user_id) ? $perms_map['write'] : $perms_map['login'];

		$row = array(
			'id' => $id,
			'perms' => $perms,
			'api_key_id' => $site_key['id'],
			'api_key_role_id' => $site_key['role_id'],
			'user_id' => $user_id,
			'access_token' => $token,
			'created' => $now,
			'last_modified' => $now,
			'expires' => $expires,
		);

		$insert = array();

		foreach ($row as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('OAuth2AccessTokens', $insert);

		if ($rsp['ok']){
			$rsp['token'] = $row;
		}

		return $rsp;
	}

	#################################################################

	# Infrastructure tokens

	function api_oauth2_access_tokens_create_infrastructure_token(&$api_key, $perms, $ttl=0){

		$id = dbtickets_create(64);

		$token = api_oauth2_access_tokens_generate_token();

		$expires = 0;

		if ($ttl){
			$now = time();
			$expires = $now + $ttl;
		}

		$row = array(
			'id' => $id,
			'perms' => $perms,
			'api_key_id' => $api_key['id'],
			'api_key_role_id' => $api_key['role_id'],
			'user_id' => 0,
			'access_token' => $token,
			'created' => $now,
			'last_modified' => $now,
			'expires' => $expires,
		);

		$insert = array();

		foreach ($row as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('OAuth2AccessTokens', $insert);

		if ($rsp['ok']){
			$rsp['token'] = $row;
		}

		return $rsp;
	}

	#################################################################

	# API explorer

	function api_oauth2_access_tokens_fetch_api_explorer_token($user=null){

		$now = time();

		$api_explorer_token = api_oauth2_access_tokens_get_api_explorer_token($user);

		if (($api_explorer_token) && ($api_explorer_token['expires'] <= $now)){

			$rsp = api_oauth2_access_tokens_delete($api_explorer_token);

			if ($rsp['ok']){

				$user_id = ($user) ? $user['id'] : 0;
				$cache_key = "oauth2_access_token_api_explorer_{$user_id}";
				cache_unset($cache_key);
			}

			$api_explorer_token = null;
		}

		# TO DO: error handling / reporting

		if (! $api_explorer_token){

			$rsp = api_oauth2_access_tokens_create_api_explorer_token($user);
			$api_explorer_token = $rsp['token'];
		}

		return $api_explorer_token;
	}

	#################################################################

	function api_oauth2_access_tokens_get_api_explorer_token($user=null){

		$user_id = ($user) ? $user['id'] : 0;
		
		$cache_key = "oauth2_access_token_api_explorer_{$user_id}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			# return $cache['data'];
		}

		$site_key = api_keys_fetch_api_explorer_key();

		$enc_user = AddSlashes($user_id);
		$enc_key = AddSlashes($site_key['id']);

		$sql = "SELECT * FROM OAuth2AccessTokens WHERE user_id='{$enc_user}' AND api_key_id='{$enc_key}'  AND (expires=0 OR expires > UNIX_TIMESTAMP(NOW())) LIMIT 1";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_oauth2_access_tokens_create_api_explorer_token($user=null){

		$site_key = api_keys_fetch_api_explorer_key();

		$id = dbtickets_create(64);

		$user_id = ($user) ? $user['id'] : 0;

		$token = api_oauth2_access_tokens_generate_token();

		$ttl = ($user) ? $GLOBALS['cfg']['api_explorer_tokens_user_ttl'] : $GLOBALS['cfg']['api_explorer_tokens_ttl'];
		$now = time();

		$expires = $now + $ttl;

		$perms_map = api_oauth2_access_tokens_permissions_map('string keys');
		$perms = ($user_id) ? $perms_map['read'] : $perms_map['login'];

		$row = array(
			'id' => $id,
			'perms' => $perms,
			'api_key_id' => $site_key['id'],
			'api_key_role_id' => $site_key['role_id'],
			'user_id' => $user_id,
			'access_token' => $token,
			'created' => $now,
			'last_modified' => $now,
			'expires' => $expires,
		);

		$insert = array();

		foreach ($row as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('OAuth2AccessTokens', $insert);

		if ($rsp['ok']){
			$rsp['token'] = $row;
		}

		return $rsp;
	}

	#################################################################

	function api_oauth2_access_tokens_is_expired(&$token){

		$now = time();
		return (($token['expires']) && ($token['expires'] <= $now)) ? 1 : 0;
	}

	#################################################################

	function api_oauth2_access_tokens_is_disabled(&$token){

		$now = time();
		return (($token['disabled']) && ($token['disabled'] <= $now)) ? 1 : 0;
	}

	#################################################################

	function api_oauth2_access_tokens_is_infrastructure_token(&$token){

		$map = api_keys_roles_map();
		$role = $token['api_key_role_id'];

		return ($map[$role] == "infrastructure") ? 1 : 0;
	}

	#################################################################

	# the end
