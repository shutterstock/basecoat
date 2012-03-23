<?php
Content::$page->add('title','Routing');

$content	= new Content();

// Get List of Configured Routes
$display_route_params	= array(
	'require_secure',
	'require_login',
	'data_only'
	);

ksort($_ROUTES_);
$configured_routes		= array();
foreach($_ROUTES_ as $route=>$settings) {
	$tmp				= array('name'=>$route);
	foreach($display_route_params as $param) {
		$tmp[$param]	= (isset($settings[$param]) ? $settings[$param] : '-' );
	}
	$configured_routes[]	= $tmp;
}

$content->add('routes', $configured_routes );

// Add route content to page
$content->processTemplate($_ROUTES_[Core::$current_route]['template']);
$content->addToPage();
unset($content);
