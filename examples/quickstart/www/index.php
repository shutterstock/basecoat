<?php
// Turn on error reporting for development
ini_set('display_errors', true);
error_reporting(E_ALL);

date_default_timezone_set('UTC');

// Define the site root directory
$site_dir	= realpath('../').'/';
$path_routes		= $site_dir . 'routes/';
$path_templates		= $site_dir . 'templates/';
$path_layouts		= $path_templates . 'layouts/';

include_once( '../../../basecoat/basecoat.php');
$bc		= new \Basecoat\Basecoat();

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

$bc->view->setLayouts(
	array(
		'basic'	=> $path_layouts . 'basic.php'
	)
);
$bc->view->setLayout('basic');
$bc->view->setTemplatesPath($path_templates);

$bc->addBeforeOut( function() use ($bc) {
		$content	= $bc->view->newView();
		$content->processTemplate($bc->view->templates_path . 'common/header.php');
		$content->processTemplate($bc->view->templates_path . 'common/footer.php');
		$content->addToView($bc->view);
});

$routes = array(
	'/'	=> array(
		'file'		=> $path_routes . 'index.php',
		'template'	=> 'index.tpl.php',
		'cacheable'	=> array(
			'expires'=>'20 minutes',
			)
	),
	'static'	=> array(
		'function'	=> function() use ($bc) {
			$content	= $bc->view->newView();
			$tpl		= file_get_contents($bc->view->templates_path . $bc->routing->current['template']);
			$content->parseBlocks($tpl);
			// Add route content to page
			$content->addToView($bc->view);
		},
//		'file'		=> $path_routes . 'static.php',
		'cacheable'	=> array(
			'expires'=>'1 day',
			)
	),
	'not_found' => array(
		'file'		=> $path_routes . '404.php',
		'template'	=> '404.tpl.php',
		'layout'	=> 'basic',
		'cacheable'	=> array(
			'expires'=>'5 minutes',
			)
	),

);
$bc->routing->setRoutes($routes);

$bc->processRequest();

/*
// processRequest makes the following calls:
$route_name = $bc->routing->parseUrl();
$bc->routing->run($route_name);
$bc->messages->display();
$bc->out();
*/


