<?php

	include("include/init.php");
	loadlib("api_output");

	$method = get_str("method");
	$method = filter_strict($method);

	if ((! $method) || (! isset($GLOBALS['cfg']['api']['methods'][$method]))){
		error_404();
	}

	$method_row = $GLOBALS['cfg']['api']['methods'][$method];

	if ((! $method_row['enabled']) || (! $method_row['documented'])){
		error_404();
	}

	$GLOBALS['smarty']->assign_by_ref("method", $method);
	$GLOBALS['smarty']->assign_by_ref("method_row", $method_row);

	$GLOBALS['smarty']->display("page_api_method.txt");
	exit();

?>
