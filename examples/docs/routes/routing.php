<?php
Content::$page->add('title','Routing');

$content	= new Content();

// Get List of Configured Routes
$display_route_params	= array(
	'require_secure',
	'require_login',
	'data_only'
	);

ksort(Config::$routes);
$configured_routes		= array();
foreach(Config::$routes as $route=>$settings) {
	$tmp				= array('name'=>$route);
	foreach($display_route_params as $param) {
		$tmp[$param]	= (isset($settings[$param]) ? $settings[$param] : '-' );
	}
	$configured_routes[]	= $tmp;
}

$content->add('routes', $configured_routes );

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);
$content->addToPage();
unset($content);
