<?php
/**
This is a basic custom init file that can be used as an 
example/template for creating your own custom setup.
**/

// Set default timezone
date_default_timezone_set('UTC');

// Configure database connections
\Basecoat\Config::$settings->db = array(
	0 => array(
		'host' => 'localhost', 
		'db' => 'todo', 
		'username' => 'root', 
		'password' => 'freeDB', 
		'label'=>'master'),
);
// Specify which config is the master and which slave db server to use.
// This example show how to use a random slave when more than one is available
\Basecoat\Config::$settings->dbmaster_id	= 0;
\Basecoat\Config::$settings->dbslave_id	= 0;

//
// Once all standard configurations are set, load any overrides based on environment
//

//
// Check what mode we are in, load config overrides
if ( \Basecoat\Config::$run_env=='dev' ) {
	\Basecoat\Core::$profiling_enabled	= true;
	if ( file_exists(BC_CONFIGS . 'config.dev.php') ) {
		require_once(BC_CONFIGS . 'config.dev.php');
	}
}


require_once(BASECOATDIR . 'classes/db.pdo.php');

DB::setServerConfig(\Basecoat\Config::$settings->db, \Basecoat\Config::$settings->dbmaster_id);
\Basecoat\Core::$db 		= DB::getServerInstance(\Basecoat\Config::$settings->dbslave_id);

require_once(BC_LIB . 'classes/Tasks.class.php');
\Basecoat\Core::$bc->tasks	= new Tasks();