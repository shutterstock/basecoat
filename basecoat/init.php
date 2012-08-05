<?php
/**
* basecoat init file
*
* @author Brent Baisley
* @version 0.2
*/

// Route counters for executing routes until no new routes are specified.
// Count the numbers of loops in case we get stuck in an infinite loop
$route_loop_cntr		= 0;
$route_inc_cntr			= 0;
$max_loops				= 5;

if (!defined('BASECOATDIR')) {
	define('BASECOATDIR', realpath(dirname(__FILE__)) . '/');
}

// Load Core class
require_once(BASECOATDIR . 'classes/core.class.php');
// Check for config file
if ( defined('BC_CONFIGS') && file_exists(BC_CONFIGS . 'config.php') ) {
	require_once(BC_CONFIGS . 'config.php');
}

// Load Content class and create main instance of View Controller/Content class
require_once(BASECOATDIR . 'classes/content.class.php');
Content::$page		= new Content();
// Create instance of message display class
Content::$messages	= new Messages();
Content::$messages->setTemplate(BASECOATDIR . 'templates/messages.tpl.php');

//
// Check if mode is already defined, if not check if we are running on the command line
if ( is_null(Config::$run_mode) ) {
	if ( 'cli'==php_sapi_name() ) {
		// If in command line mode, return control to calling script
		Config::$run_mode		= 'cli';
		// Check for a cli config file
		if ( defined('BC_CONFIGS') && file_exists(BC_CONFIGS . 'config.cli.php') ) {
			include_once(BC_CONFIGS . 'config.cli.php');
		}
		return;
	}
	Config::$run_mode	= 'web';

} else if ( Config::$run_mode=='cli' ) {
	return;
}

require_once(BASECOATDIR . 'router.php');
