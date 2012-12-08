<?php

	#################################################################

	function api_oauth1_5_sign_arguments($args, $app_keys, $user_keys){

		$parts = array();
		$raw = implode("&", $parts);

		$key = rawurlencode($app_key['app_secret']);
		$key .= "&";

		if (isset($user_keys['access_token_secret'])){
			$key .= rawurlencode($user_keys['access_token_secret']);
		}

		# TO DO: check for native hashing function

		$signed = api_oauth1_5_hmac_sha1($raw, $key, TRUE);
		$signed = base64_encode($signed);

		return $signed
	}

	#################################################################

	# copied from lib_oauth.php

	function api_oauth1_5_hmac_sha1($data, $key, $raw=TRUE){

		if (strlen($key) > 64){
			$key =  pack('H40', sha1($key));
		}

		if (strlen($key) < 64){
			$key = str_pad($key, 64, chr(0));
		}

		$_ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
		$_opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

		$hex = sha1($_opad . pack('H40', sha1($_ipad . $data)));

		if ($raw){
			$bin = '';
			while (strlen($hex)){
				$bin .= chr(hexdec(substr($hex, 0, 2)));
				$hex = substr($hex, 2);
			}
			return $bin;
		}

		return $hex;
	}

	#################################################################
?>
