<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

if ( get_magic_quotes_gpc() ) {
	function cleanup_post(&$val) {
        return is_array($val) ? array_map('cleanup_post', $val) : stripslashes($val);
	}
	$_POST	= array_map('cleanup_post',$_POST);
}

// Execute actions until no new actions are specified.
// Count the numbers of loops in case we get stuck in an infinite loop
$route_loop_cntr		= 0;
$route_inc_cntr			= 0;
$max_loops				= 5;

// Load core initialization file to setup
include_once( dirname(dirname(__FILE__)) . '/lib/bootstrap.inc.php');

if (Core::$profiling_enabled) {
	Core::$profiling['start']	= $route_start_time = round(microtime(true),3);
	Core::$profiling['page']	= Core::$current_route;
}

// Run any pre processing files
if ( count(Config::$pre_includes)>0 ) {
	foreach (Config::$pre_includes as $inc) {
		include($inc);
	}
	if (Core::$profiling_enabled) {
		Core::$profiling['start'] = $route_end_time	= round(microtime(true),3);
		$route_loop_cntr++;
		Core::$profiling['routes'][]	= array(
			'route'=>'pre-includes',
			'file'=>count(Config::$pre_includes),
			'time'=>$route_end_time-$route_start_time, 
			'start'=>$route_start_time,
			'end'=>$route_end_time,
			'seq'=>$route_loop_cntr
			);
		$route_start_time	= $route_end_time;
	}
}

do {
	// Save current route so we can check if it changes later
	Core::$last_run_route		= Core::$current_route;
	// Make sure it's a valid route
	if ( array_key_exists( Core::$current_route, $_ROUTES_ ) ) {
		// Check if login is required
		if ( isset( $_ROUTES_[Core::$current_route]['require_login'] ) ) {
			Core::$auth->requireLogin();
			//Core::$last_run_route		= Core::$current_route;
		}
		// Check if https is required
		if ( isset( $_ROUTES_[Core::$current_route]['require_secure'] ) && $_ROUTES_[Core::$current_route]['require_secure']==1 ) {
			
		}
		// Run route file
		if ( isset($_ROUTES_[Core::$current_route]) && file_exists($_ROUTES_[Core::$current_route]['file']) ) {
			include($_ROUTES_[Core::$current_route]['file']);
			$route_inc_cntr++;
		} else {
			echo 'NO ROUTE OR ROUTE FILE: '.Core::$current_route;
		}
	} else {
		error_log("Sorry, but I'm afraid I can't do that. " . Core::$current_route . ": [".Core::$last_run_route."]");
	}
	if (Core::$profiling_enabled) {
		$route_end_time	= round(microtime(true),3);
		// Log profiling information
		Core::$profiling['routes'][]	= array(
			'route'=>Core::$last_run_route,
			'file'=>$_ROUTES_[Core::$last_run_route]['file'],
			'time'=>$route_end_time-$route_start_time, 
			'start'=>$route_start_time,
			'end'=>$route_end_time,
			'seq'=>$route_loop_cntr
			);
		$route_start_time	= $route_end_time;
	}
	$route_loop_cntr++;
} while (Core::$last_run_route != Core::$current_route && $route_loop_cntr<=$max_loops);
if (Core::$profiling_enabled) {
	Core::$profiling['end']		= round(microtime(true),3);
	Core::$profiling['files']	= $route_inc_cntr;
	Core::$profiling['loops']	= $route_loop_cntr;
	Core::$profiling['time']	= Core::$profiling['end']-Core::$profiling['start'];
}

// Check if we've gone loopy
if ( $route_loop_cntr>5 ) {
	echo "Sorry, I've seem to have gone a bit loopy.";
	exit();
}

// Add page includes
if ( count(Config::$page_includes)>0 ) {
	$content_inc	= new Content();
	foreach( Config::$page_includes as $content_file ) {
		if ( file_exists($content_file) ) {
			$content_inc->processTemplate($content_file);
			$content_inc->addToPage();
			$content_inc->clear();
		}
	}
	unset($content_inc);
}

// Display an pending messages
Content::$messages->display();

echo Content::$page->processTemplate(Content::$layout, false);

// Run any post processing files
if ( count(Config::$post_includes)>0 ) {
	foreach (Core::$post_includes as $inc) {
		include($inc);
	}
}
