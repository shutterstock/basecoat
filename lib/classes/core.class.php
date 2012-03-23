<?php

class Config {
	public static $debug		= false;
	
	// Authentication file to load
	public static $auth_inc		= '';
	
	// What mode to run in (dev, prod)
	public static $run_mode		= 'prod';
	
	// What environment running under (web, cli)
	public static $run_env		= 'web';
	
	// Is the request from a bot
	public static $bot_request	= false;
	
	// Which parameter to user to determine page requested.
	// Set to 'rewrite' if using pretty urls (i.e. mod_rewrite)
	public static $use_pretty_urls	= false;
	public static $route_param	= 'page';
	
	// Any other settings that should be stored
	public static $settings = array(
		);
	
	// Files to include prior to running any routes
	public static $pre_includes		= array();
	
	// Files to include after running routes, but before page output
	public static $page_includes	= array();
	
	// Files to include after page output (i.e. logging)
	public static $post_includes	= array();
	
}

class Core {
	// Holder for an instance of the database class
	static public $db			= NULL;
	// List of routes requested to be run
	static public $run_routes	= array();
	// Which route to run
	static public $current_route	= 'default';
	static public $requested_route	= null;
	static public $last_run_route	= null;
	// Which file(s) to run after the page has been outputted
	static public $page_wrapup	= array();
	
	// Initialize memcache variables
	static public $mc_session	= null;
	static public $mc_data		= null;
	
	// Varibles to hold class instances
	static public $messages		= null;
	static public $facebook		= null;
	static public $auth			= null;
	static public $is_logged_in	= false;
	
	// Configuration information
	static public $profiling;
	
	static public function runRoute() {
	}
	
	static public function setRoute($name, $change_layout=false) {
		self::$current_route 		= $name;
		if ( $change_layout ) {
			// Check if the route specified a layout
			if ( isset($_ROUTES_[self::$current_route]['layout']) ) {
				Content::$layout	= $_ROUTES_[self::$current_route]['layout'];
			}
		}
	}
	
	static public function setLayout( $layout ) {
		Content::$layout	= $layout;
	}
	
	static public function checkLogin() {
		if ( isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] ) {
			self::$is_logged_in	= true;
		}
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
