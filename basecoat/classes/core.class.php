<?php
/**
* Configuration information, static to make it globally easily accessible
*
* @author Brent Baisley <brent@bigstockphoto.com>
*/


// Setup base config object to hold extra settings
Config::$settings	= new stdClass;

// Setup base class object to hold instances of core classes
Core::$bc			= new stdClass;

class Config {
	/**
	* Turn debugging on/off
	*/
	public static $debug		= false;
	
	/**
	* What environment running under (web, cli)
	*
	* Framework load process changes based on this value
	*/
	public static $run_mode		= 'prod';
	
	/**
	* What mode to run in (dev, prod)
	*
	* Different configuration can be loaded based on this value
	*/
	public static $run_env		= 'web';
	
	/*
	* Is the request from a bot
	*
	* This can be checked to avoid logging bot traffic
	*/
	public static $bot_request	= false;
	
	/**
	* Which parameter to user to determine page requested.
	* Set to 'rewrite' if using pretty urls (i.e. mod_rewrite)
	*/
	public static $use_pretty_urls	= false;
	
	/**
	* URL parameter to check for to determine route to run
	*/
	public static $route_param	= 'page';
	
	/**
	* Headers to include in the output
	*/
	public static $headers		= array(
		'Content-type'=>'text/html; charset=UTF-8',
		'X-Powered-By'=>'Basecoat PHP framework'
		);
	
	/**
	* Content data
	*/
	public static $content		= array(
		'charset'	=> 'UTF-8',
		'lang'		=> 'en'
		);
	
	/**
	* URL to evaluate for routing
	*/
	public static $url			= null;
	
	/**
	* Files to include prior to running any routes
	*/
	public static $include_before		= array();
	
	/**
	* Files to include after running routes, but before page output
	*/
	public static $include_after		= array();
	
	/**
	* Files to include after page output (i.e. logging)
	*/
	public static $include_after_output	= array();
	
	/**
	* Array to contain route definitions
	*/
	public static $routes	= array();
	
	/**
	* Maximum number of routes to run
	* Used to prevent circular routing when chaining routes
	*/
	public static $max_routes	= 5;
	
	/**
	* Array to contain list of available layouts
	*/
	public static $layouts	= array();
	
	/**
	* Any other settings that need be stored
	*/
	public static $settings = null;
	
}

class Core {
	/**
	* List of routes requested to be run
	* Determined by parsing the URL
	*/
	static public $run_routes	= array();
	
	/**
	* Which route to run
	*/
	static public $current_route	= 'default';
	
	/**
	* Originally requested route to be run
	*/
	static public $requested_route	= null;
	
	/**
	* Last route that was run
	*/
	static public $last_run_route	= null;
	
	/*
	* Which file(s) to run after the page has been outputted
	*/
	static public $page_wrapup	= array();
		
	/**
	* stdClass object to hold instances of base classes
	*/
	static public $bc			= null;
	
	/**
	* Variable to hold auth class instance
	*/
	static public $auth			= null;

	/**
	* Contains profiling information for code that is run
	*
	* This will contain routes run, timings for each route,
	* number of route loops executed, etc.
	*/
	static public $profiling	= array();
	
	/**
	* Whether to enable profiling
	*/
	static public $profiling_enabled	= false;
	
	/**
	* Set the next route to run
	*
	* @param String $name name of route to run, must be in route index
	* @param Boolean $change_layout change layout to the one defined by the new route if present
	*/
	static public function setRoute($name, $change_layout=false) {
		self::$current_route 		= $name;
		if ( $change_layout ) {
			// Check if the route specified a layout
			if ( isset(Config::$routes[self::$current_route]['layout']) ) {
				Content::$layout	= Config::$routes[self::$current_route]['layout'];
			}
		}
	}
		
	/**
	* Check if user is currently logged in
	*
	* @return Boolean login status
	*/
	static public function checkLogin() {
		if ( isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] ) {
			self::$is_logged_in	= true;
		}
		return self::$is_logged_in;
	}
	
	/**
	* Check if login is required for the current route and redirect if not logged in
	**/
	static public function requireLogin() {
		if ( self::$is_logged_in ) {
			return;
		}
		self::setRoute('login', true);
	}
	
}
