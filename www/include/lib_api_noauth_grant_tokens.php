<?php

	# noauth - as in "not oauth"

	#################################################################

	function api_noauth_grant_tokens_get_by_code($code){

		$cache_key = "noauth_grant_token_{$code}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_code = AddSlashes($code);

		$sql = "SELECT * FROM NOAuthGrantTokens WHERE code='{$enc_code}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_noauth_grant_tokens_get_for_user_and_key(&$user, &$key){

		$cache_key = "noauth_grant_token_uk_{$user['id']}_{$key['id']}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$enc_user = AddSlashes($user['id']);
		$enc_key = AddSlashes($key['id']);

		$sql = "SELECT * FROM NOAuthGrantTokens WHERE user_id='{$enc_user}' AND api_key_id='{$enc_key}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if ($rsp['ok']){
			cache_set($cache_key, $row);
		}

		return $row;
	}

	#################################################################

	function api_noauth_grant_tokens_create(&$key, &$user, $perms, $ttl=0){

		$code = api_noauth_grant_tokens_generate_code();
		$now = time();

		$token = array(
			'code' => $code,
			'api_key_id' => $key['id'],
			'user_id' => $user['id'],
			'perms' => $perms,
			'created' => $now,
			'ttl' => $ttl,
		);

		$insert = array();

		foreach ($token as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('NOAuthGrantTokens', $insert);

		if ($rsp['ok']){
			$rsp['token'] = $token;
		}

		return $rsp;
	}

	#################################################################

	function api_noauth_grant_tokens_delete(&$token){

		$enc_code = AddSlashes($token['code']);

		$sql = "DELETE FROM NOAuthGrantTokens WHERE code='{$enc_code}'";
		$rsp = db_write($sql);

		if ($rsp['ok']){
			api_noauth_grant_tokens_purge_cache($token);
		}
	
		return $rsp;
	}

	#################################################################

	function api_noauth_grant_tokens_purge_cache(&$token){

		$cache_keys = array(
			"noauth_grant_token_{$token['code']}",
			"noauth_grant_token_uk_{$token['user_id']}_{$token['api_key_id']}",
		);

		foreach ($cache_keys as $key){
			cache_unset($key);
		}
	}

	#################################################################

	function api_noauth_grant_tokens_purge(){

		$then = api_noauth_grant_tokens_min_age();

		# TO DO: purge caches - iterate over all the keys?
		# (20121103/straup)

		$sql = "DELETE FROM NOAuthGrantTokens WHERE created <= {$then}";
		$rsp = db_write($sql);

		return $rsp;
	}

	#################################################################

	function api_noauth_grant_tokens_generate_code(){
		$key = md5(random_string(100) . time());
		return $key;		
	}

	#################################################################

	function api_noauth_grant_tokens_min_age(){

		$now = time();
		$then = $now - (60 * 5);

		return $then;
	}

	#################################################################

	function api_noauth_grant_tokens_is_timely(&$token){

		$min_age = api_noauth_grant_tokens_min_age();
		$ok = ($token['created'] > $min_age) ? 1 : 0;

		return $ok;
	}

	#################################################################
?>
