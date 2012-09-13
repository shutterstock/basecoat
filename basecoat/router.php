<?php
// To prevent context switching, use request time (seconds since 1970)
// instead of calling time() whenever the current time is needed
if ( isset($_SERVER['REQUEST_TIME']) ) {
	define('TIME_NOW', $_SERVER['REQUEST_TIME']);
} else {
	define('TIME_NOW', time());
}

if ( defined('BC_CONFIGS') && file_exists(BC_CONFIGS . 'config.web.php') ) {
	require_once(BC_CONFIGS . 'config.web.php');
}

include_once(BASECOATDIR . 'url_parser.php');

if (Core::$profiling_enabled) {
	Core::$profiling['start']	= $route_start_time = round(microtime(true),3);
	Core::$profiling['page']	= Core::$current_route;
}


//
// Run any pre processing files
if ( count(Config::$include_before)>0 ) {
	foreach (Config::$include_before as $inc) {
		include($inc);
	}
	if (Core::$profiling_enabled) {
		Core::$profiling['start'] = $route_end_time	= round(microtime(true),3);
		$route_loop_cntr++;
		Core::$profiling['routes'][]	= array(
			'route'=>'pre-includes',
			'file'=>count(Config::$include_before),
			'time'=>$route_end_time-$route_start_time, 
			'start'=>$route_start_time,
			'end'=>$route_end_time,
			'seq'=>$route_loop_cntr
			);
		$route_start_time	= $route_end_time;
	}
}

// Set default headers
foreach(Config::$headers as $header=>$val) {
	header($header.': '.$val);
}

// Check if initial route is cacheable
if ( isset(Config::$routes[Core::$current_route]['cacheable']) ) {
	$is_cacheable	= Config::$routes[Core::$current_route]['cacheable'];
	// Check for expires
	if ( isset($is_cacheable['expires']) ) {
		// Add expires header Fri, 30 Oct 1998 14:19:41 GMT
		header("Pragma: cache", true);
		header('Expires: '. gmdate('D, d M Y H:i:s', strtotime('+'.$is_cacheable['expires'])).' GMT', true);
		header('Cache-Control: public, max-age='.(strtotime('+'.$is_cacheable['expires'])-time()), true);
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	}
	// Check if a server side cache should be created/returned
	if ( isset($is_cacheable['server']) ) {
		//TO DO
	}
}

do {
	// Save current route so we can check if it changes later
	Core::$last_run_route		= Core::$current_route;
	// Make sure it's a valid route
	if ( array_key_exists( Core::$current_route, Config::$routes ) ) {
		// Check if login is required
		if ( isset( Config::$routes[Core::$current_route]['require_login'] ) ) {
			Core::$auth->requireLogin();
			//Core::$last_run_route		= Core::$current_route;
		}
		// Check if http(s) is required
		if ( isset( Config::$routes[Core::$current_route]['require_secure'] ) && Config::$routes[Core::$current_route]['require_secure']!=2 ) {
			// Determine current scheme
			$scheme	= parse_url(Config::$url, PHP_URL_SCHEME);
			if ( $scheme=='http' && Config::$routes[Core::$current_route]['require_secure']==1 ) {
				// Redirect to https
				$new_url	= 'https'.substr(Config::$url, 4);
				header('Location: '.$new_url);
				exit();
			} else if ( $scheme=='https' && Config::$routes[Core::$current_route]['require_secure']==0 ) {
				$new_url	= 'http'.substr(Config::$url, 5);
				header('Location: '.$new_url);
				exit();
			}
		}
		// Run route file
		if ( isset(Config::$routes[Core::$current_route]) && file_exists(Config::$routes[Core::$current_route]['file']) ) {
			include(Config::$routes[Core::$current_route]['file']);
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
			'file'=>Config::$routes[Core::$last_run_route]['file'],
			'time'=>$route_end_time-$route_start_time, 
			'start'=>$route_start_time,
			'end'=>$route_end_time,
			'seq'=>$route_loop_cntr
			);
		$route_start_time	= $route_end_time;
	}
	$route_loop_cntr++;
} while (Core::$last_run_route != Core::$current_route && $route_loop_cntr<=Config::$max_routes);
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
if ( count(Config::$include_after)>0 ) {
	$content_inc	= new Content();
	foreach( Config::$include_after as $content_file ) {
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
if ( count(Config::$include_after_output)>0 ) {
	foreach (Core::$include_after_output as $inc) {
		include($inc);
	}
}
