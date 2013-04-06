<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	include("include/init.php");
	loadlib("api_config");
	loadlib("cli");

	echo "THIS IS NOT FINISHED YET... (20130405/straup)\n\n";

	$spec = array(
		"output" => array("flag" => "o", "required" => 1, "help" => "..."),
		"all" => array("flag" => "a", "required" => 0, "boolean" => 1, "help" => "..."),
		"exclude" => array("flag" => "e", "required" => 0, "help" => "..."),
	);

	$opts = cli_getopts($spec);

	#

	api_config_init();
	ksort($GLOBALS['cfg']['api']['methods']);

	#

	$fh = fopen($opts['output'], 'w');

	$GLOBALS['smarty']->assign("page_title", "{$GLOBALS['cfg']['site_name']} API documentation");
	fwrite($fh, $GLOBALS['smarty']->fetch("inc_head.txt"));

	foreach ($GLOBALS['cfg']['api']['methods'] as $method_name => $method_details){

		$include = 1;

		if (! $method_details['enabled']){
			$include = 0;
		}

		if (! $method_details['documented']){
			$include = 0;
		}

		if ($method_details['requires_blessing']){
			$include = 0;
		}

		if ($opts['include_all']){
			$include = 1;
		}

		if ($opts['exclude']){

			$exclude = explode($opts['exclude'], ',');

			if (in_array($method_name, $exclude)){
				$include = 0;
			}
		}

		if (! $include){
			continue;
		}

		echo "{$method_name}\n";

		$GLOBALS['smarty']->assign_by_ref("method", $method_name);
		$GLOBALS['smarty']->assign_by_ref("details", $method_details);

		fwrite($fh, $GLOBALS['smarty']->fetch("inc_api_method.txt"));
	}

	fwrite($fh, $GLOBALS['smarty']->fetch("inc_foot.txt"));

	echo "--\n";
	echo "done";

	fclose($fh);

	exit();
?>
