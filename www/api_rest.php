<?php

	include("include/init.php");
	loadlib("api");

	$method = request_str("method");
	$method = filter_strict($method);

	api_dispatch($method);
	exit();

?>
