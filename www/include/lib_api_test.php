<?php

	#################################################################

	function test_echo($args){
		api_output_ok($args);
	}

	#################################################################

	function test_error(){
		api_output_error(500, 'This is the network of our disconnect');
	}

	#################################################################
?>
