<?php

/****************************************************
 * Parse the request into its route and sub-routes
 * The URL is parsed and a list of routes is
 * created that needs to be run.
 * The next route to run is shifted off the stack
 ****************************************************/
// Check if a URL to parse has been set
if ( is_null(Config::$url) ) {
	Config::$url	= $_SERVER['REQUEST_URI'];
}

// Check what URL format is in use
if ( Config::$use_pretty_urls ) {
	if ( Config::$url=='' ) {
		//No route specified
		Core::$run_routes		= array('default');

	} else {
		// prepend a dummy domain so parse_url works right under all circumstances
		$url_path				= trim( parse_url('http://localhost'. Config::$url, PHP_URL_PATH), '/');
		Core::$run_routes		= explode('/',$url_path);

	}

} else {
	/*
	 The route is determined by parsing the "page" url parameter
	 Multiple routes are delimited by a period (.)
	*/
	parse_str(parse_url(Config::$url, PHP_URL_QUERY), $tmp_get);
	if ( isset($_GET[Config::$route_param]) && $_GET[Config::$route_param]!='' ) {
		// Create a run routes list, to be used by subroutes
		Core::$run_routes		= explode('.', $_GET[Config::$route_param]);
		
	} else {
		// Use default route
		Core::$run_routes		= array('default');
	}

}
//
// Set the first route as the current run route
// trim out leading/trailing . for security
Core::$current_route	= Core::$requested_route	= trim( array_shift(Core::$run_routes), '.');

// check if valid route is specified
if ( !isset(Config::$routes[Core::$current_route]) ) {
	// No route by that name
	// Check if there is a static template file matching request
	if ( file_exists(BC_TEMPLATES . 'static/'.Core::$current_route.'.html') ) {
		// Set route to HTML route
		Config::$routes[Core::$current_route]	= Config::$routes['html'];

	} else if ( file_exists(BC_TEMPLATES . 'static/'.Core::$current_route) ) {
		// Set route to HTML route
		Config::$routes[Core::$current_route]	= Config::$routes['html'];
	
	} else {
		Core::$current_route	= 'not_found';

	}
}

// Check if the route specified a layout
if ( isset(Config::$routes[Core::$current_route]['layout']) ) {
	Content::$layout	= Config::$routes[Core::$current_route]['layout'];

} else {
	Content::$layout	= Config::$layouts['default'];

}
