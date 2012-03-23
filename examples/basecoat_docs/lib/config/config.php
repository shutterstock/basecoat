<?php
/**
This is a basic custom init file that can be used as an 
example/template for creating your own custom setup.
**/

date_default_timezone_set('UTC');

//
// Check what mode we are in, load config overrides
if ( Config::$run_env=='dev' ) {
	if ( file_exists(PATH_CONFIG . 'config.dev.php') ) {
		require_once(PATH_CONFIG . 'config.dev.php');
	}
}


/******************************************************************************
 * database setup
 *****************************************************************************/
$_DB_SERVERS_ = array(
	0 => array('host' => 'localhost', 'username' => 'app_user', 'db' => 'default_db', 'password' => 'db_password'),
	1 => array('host' => 'localhost', 'username' => 'app_user', 'db' => 'default_db', 'password' => 'db_password'),
	2 => array('host' => 'localhost', 'username' => 'app_user', 'db' => 'default_db', 'password' => 'db_password'),
);
// Specify which config is the master and slave db server
$_DB_SERVERS_MASTER = 0;
$_DB_SERVERS_SLAVE	= mt_rand(1,4);

require_once(PATH_CLASS . 'common/db.pdo.php');

DB::setServerConfig($_DB_SERVERS_, $_DB_SERVERS_MASTER);
Core::$db 	= DB::getServerInstance($_DB_SERVERS_SLAVE);
