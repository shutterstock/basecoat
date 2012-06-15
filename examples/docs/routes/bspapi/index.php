<?php

Config::$page_includes	= array();

include(PATH_CLASS . 'bspapi.class.php');
$settings	= array(
	'account_id'	=> 34169,
	'secret_key'	=> '7ad8eec0a901b74ff8846cdfd923510991f86225',
	'mode'			=> 'test'
);
$bsp				= new BspApi($settings);

$api_routes	= array(
	'search'	=> array(
		'file' 		=> PATH_ROUTES . 'bspapi/search.php',
		'template'	=> PATH_TEMPLATES . 'bspapi/search.tpl.php',
	),
	'image'	=> array(
		'file' 		=> PATH_ROUTES . 'bspapi/image.php',
		'template'	=> PATH_TEMPLATES . 'bspapi/image.tpl.php',
	),
);
$api_default_route	= 'search';

if ( count(Core::$run_routes)>0 ) {
	Core::$current_route	= trim( array_shift(Core::$run_routes), '.');
	if ( !isset($api_routes[Core::$current_route]) ) {
		Core::$current_route	= $api_default_route;
	}
} else {
	Core::$current_route	= $api_default_route;
}

$_ROUTES_	= array_merge($_ROUTES_, $api_routes);

// Control is now returned to the Front Controller (index.php)
// which will notice there is a new route to run