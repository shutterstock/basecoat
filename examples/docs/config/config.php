<?php
/**
This is a basic custom init file that can be used as an 
example/template for creating your own custom setup.
**/

// Set default timezone
date_default_timezone_set('UTC');

// Configure database connections
Config::$settings->db = array(
	0 => array(
		'host' => 'localhost', 'db' => 'default_db', 
		'username' => 'db_user', 'password' => 'db_password', 
		'label'=>'master'),
	1 => array(
		'host' => 'localhost', 'db' => 'default_db', 
		'username' => 'db_user', 'password' => 'db_password', 
		'label'=>'slave1'),
	2 => array(
		'host' => 'localhost', 'db' => 'default_db', 
		'username' => 'db_user', 'password' => 'db_password', 
		'label'=>'slave2'),
);
// Specify which config is the master and which slave db server to use.
// This example show how to use a random slave when more than one is available
Config::$settings->dbmaster_id	= 0;
Config::$settings->dbslave_id	= mt_rand(1,2);

//
// Once all standard configurations are set, load any overrides based on environment
//

//
// Check what mode we are in, load config overrides
if ( Config::$run_env=='dev' ) {
	Core::$profiling_enabled	= true;
	if ( file_exists(BC_CONFIGS . 'config.dev.php') ) {
		require_once(BC_CONFIGS . 'config.dev.php');
	}
}


require_once(BASECOATDIR . 'classes/db.pdo.php');

DB::setServerConfig(Config::$settings->db, Config::$settings->dbmaster_id);
Core::$bc->db 	= DB::getServerInstance(Config::$settings->dbslave_id);
