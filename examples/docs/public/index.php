<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
date_default_timezone_set('UTC');

// Define the site root directory
$site_dir		= realpath('../').'/';
$path_routes	= $site_dir . 'routes/';
$path_templates	= $site_dir . 'templates/';
$path_layouts	= $path_templates . 'layouts/';

include_once( '../../../basecoat/basecoat.php');
$bc		= new \Basecoat\Basecoat();

//
// Don't use pretty urls/mod_rewrite
$bc->routing->set('use_pretty_urls', false);
$bc->routing->set('route_param', 'page');

/*
// Optionally load database class
// Connection settings for master/slave setup
// Can specify many slaves
$db_settings = array(
	'master' => array(
		'host' => 'masterhost', 'db' => 'test', 
		'username' => 'username', 'password' => 'password', 
		'label'=>'master'),
	'slave' => array(
		'host' => 'slavehost', 'db' => 'test', 
		'username' => 'username', 'password' => 'password', 
		'label'=>'slave'),
);
// Load database and specify which setting is the master and which is the slave
// db instance is available in $bc->db
$bc->loadDb($db_settings, 'master', 'slave');
// Example on how to get a new db instance
$newDdb	= DB::getServerInstance('slave');
*/

//
// Register layout so they can referenced by name
$bc->view->setLayouts(
	array(
		'basic'	=> $path_layouts . 'basic.php',
		'json'	=> $path_layouts . 'json.tpl.php',
		'dialog'=> $path_layouts . 'dialog.php',
	)
);
$bc->view->setLayout('basic');
//
// Register global values with view
$bc->view->add('sitename', 'Basecoat');

//
// Set path to template files
$bc->view->setTemplatesPath($path_templates);

//
// Add a hook before output to process common page elements
$bc->addBeforeRender( function() use ($bc) {
		$content	= new \Basecoat\View();
		$content->processTemplate($bc->view->templates_path . 'common/header.php');
		$content->processTemplate($bc->view->templates_path . 'common/footer.php');
		$content->addToView($bc->view);
		$bc->view->add('requested_route', $bc->routing->requested_route);
});

//
// Define routes
$routes = array(
	'static'	=> array(
		'file'		=> $path_routes . 'static.php',
		'cacheable'	=> array(
			'expires'=>'20 minutes',
			)
	),
	'/'	=> array(
		'file'		=> $path_routes . 'index.php',
		'template'	=> 'index.tpl.php',
	),
	'login'	=> array(
		'file'		=> $path_routes . 'login.php',
		'template'	=> 'login.php',
		'require_secure'	=> 1
	),
	'logout'	=> array(
		'file'		=> $path_routes . 'logout.php',
	),	
	'data'		=> array(
		'file'		=> $path_routes . 'data.php',
		'data_only'	=> 1
	),
	'log'	=> array(
		'file' 		=> $path_routes . 'log_action.php',
		'data_only'	=> 1
	),
	// Site specific routes
	'configuration'	=> array(
		'file' 		=> $path_routes . 'configuration.php',
		'template'	=> 'configuration.tpl.php',
	),
	'routes'	=> array(
		'file' 		=> $path_routes . 'routing.php',
		'template'	=> 'routing.tpl.php',
	),
	'content'	=> array(
		'file' 		=> $path_routes . 'content.php',
		'template'	=> 'content.tpl.php',
	),
	'database'	=> array(
		'file' 		=> $path_routes . 'database.php',
		'template'	=> 'database.tpl.php',
		'require_login'	=> false,
	),
	'messages'	=> array(
		'file' 		=> $path_routes . 'messages.php',
		'template'	=> 'messaging.tpl.php',
	),
	'examples'	=> array(
		'file' 		=> $path_routes . 'examples.php',
		'template'	=> 'examples.tpl.php',
	),
	'json'	=> array(
		'file' 		=> $path_routes . 'json.php',
		'layout'	=> 'json',
	),
	'login'	=> array(
		'file' 		=> $path_routes . 'login.php',
		'template'	=> 'login.tpl.php',
	),
	'hello'	=> array(
		'function'	=> function() {
			   exit('Hello World!');
			}
	),
	'undefined' => array(
		'file'		=> $path_routes . '404.php',
		'template'	=> '404.tpl.php',
		'layout'	=> 'basic',
		'data_only'	=> 1
	),

);
$bc->routing->setRoutes($routes);

//
// Process the request
echo $bc->processRequest();
