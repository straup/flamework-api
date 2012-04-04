<?php

	# THIS IS NOT DONE YET
	# (20120404/straup)

 	#################################################################

	# See also:
	# http://blog.linode.com/2012/04/04/api_spec/

	function api_spec_spec(){

		$export_keys = array(
			'method',
			'description',
			'requires_auth',
			'paginated',
			'requires_auth',
			'parameters',
			'notes',
			'example',
		);

		$methods = array();

		# TO DO: figure out what parts of the web-based API documentation
		# can be re-used here.

		foreach ($GLOBALS['cfg']['api']['methods'] as $name =>$details){

			if (! $details['enabled']){
				continue;
			}

			if (! $details['documented']){
				continue;
			}

			$method = array(
				'name' => $name,
			);

			foreach ($export_keys as $k){
				$v = (isset($details[$k])) ? $details[$k] : $v;
			}

			$methods[] = $method;
		}

		api_output_ok(array(
			'methods' => $methods
		));

	}

 	#################################################################
?>
