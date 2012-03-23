<?php
if (!defined('BASEDIR')) {
	define('BASEDIR', realpath(dirname(__FILE__) . '/../') . '/');
}

/******************************************************************************
 * Default path setup and some general variables
 *****************************************************************************/
define('PATH_LIB',			BASEDIR . 'lib/');
define('PATH_INCLUDES', 	PATH_LIB . 'includes/');
define('PATH_CLASS', 		PATH_LIB . 'classes/');
define('PATH_CLASS_COMMON', PATH_LIB . 'classes/common/');
define('PATH_ROUTES', 		PATH_LIB . 'routes/');
define('PATH_CONFIG', 		PATH_LIB . 'config/');

require_once(PATH_CLASS . 'core.class.php');
require_once(PATH_CONFIG . 'config.php');

//
// Check if we are running on the command line
if ( 'cli'==php_sapi_name() ) {
	Config::$run_mode		= 'cli';
} else {
	Config::$run_mode		= 'web';
}

//
// If we are not on the command line, we must be on the web
if ( Config::$run_mode!='cli' ) {
	require_once(PATH_LIB . 'bootstrap.web.php');
}

