<?php
$basecoat->view->add('title','Routing');

$content = new \Basecoat\View();
$content->enable_data_tags	= false;

// Get List of Configured Routes
$display_route_params	= array(
	'require_secure',
	'require_login',
	'data_only'
	);

ksort($basecoat->routing->routes);
$configured_routes		= array();
foreach($basecoat->routing->routes as $route=>$settings) {
	$tmp				= array('name'=>$route);
	foreach($display_route_params as $param) {
		$tmp[$param]	= (isset($settings[$param]) ? $settings[$param] : '-' );
	}
	$configured_routes[]	= $tmp;
}

$content->add('routes', $configured_routes );

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);

$basecoat->routing->runNext();