<?php
// To prevent context switch, use request time (seconds since 1970)
// instead of calling time() whenever the current time is needed
define('TIME_NOW', $_SERVER['REQUEST_TIME']);
define('BASEDIR_WEB', trim(dirname($_SERVER['PHP_SELF']),'/') );

require_once(PATH_LIB . 'config/config.web.php');

/****************************************************
 * Parse the request into its route and sub-routes
 * The URL is parse and a list of routes is
 * created that needs to be run.
 * The next route to run is shifted off the stack
 ****************************************************/

// Check what URL format is in use
if ( Config::$use_pretty_urls ) {
	$cleaned_url	= trim( str_replace(BASEDIR_WEB, '', $_SERVER['REQUEST_URI']), '/');
	if ( $cleaned_url=='' ) {
		//No route specified
		Core::$run_routes	= array('default');

	} else {
		// prepend a dummy domain so parse_url works right under all circumstances
		$url_path				= trim( parse_url('http://localhost'. $cleaned_url, PHP_URL_PATH), '/');
		Core::$run_routes		= explode('/',$url_path);

	}

} else {
	/*
	 The route is determined by parsing the "page" url parameter
	 Multiple routes are delimited by a period (.)
	*/
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
if ( !isset($_ROUTES_[Core::$current_route]) ) {
	// No route by that name
	// Check if there is a static template file matching request
	if ( file_exists(PATH_TEMPLATES . 'static/'.Core::$current_route.'.html') ) {
		// Set route to HTML route
		$_ROUTES_[Core::$current_route]	= $_ROUTES_['html'];

	} else if ( file_exists(PATH_TEMPLATES . 'static/'.Core::$current_route) ) {
		// Set route to HTML route
		$_ROUTES_[Core::$current_route]	= $_ROUTES_['html'];
	
	} else {
		Core::$current_route	= 'not_found';

	}
}

// Check if the route specified a layout
if ( isset($_ROUTES_[Core::$current_route]['layout']) ) {
	Content::$layout	= $_ROUTES_[Core::$current_route]['layout'];

} else {
	Content::$layout	= $_LAYOUTS_['default'];

}


