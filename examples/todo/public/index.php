<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
date_default_timezone_set('UTC');

define('DIR_LIB', realpath('../lib').'/');
define('DIR_TPL', realpath('../templates').'/');
define('DIR_ROUTES', realpath('../routes').'/');

include_once( '../../../basecoat/basecoat.php');
$basecoat		= new \Basecoat\Basecoat();

// Connection settings for master/slave setup
$db_settings = array(
	'master' => array(
		'host' => 'localhost', 
		'db' => 'todo', 
		'username' => 'root', 
		'password' => 'freeDB', 
		'label'=>'master'),
);
$db_settings['slave'] = $db_settings['master'];
// Load database and specify which setting is the master and which is the slave
// db instance is available in $bc->db
$basecoat->loadDb($db_settings, 'master', 'slave');


//
// Set path to template files
$basecoat->view->setTemplatesPath(DIR_TPL);

// Set layouts
$basecoat->view->setLayouts(
	array(
		'basic'	=> DIR_TPL . 'layouts/basic.php',
	)
);
$basecoat->view->setLayout('basic');

// Set routes
$routes = array(
	'/'	=> array(
		'file'		=> DIR_ROUTES . 'index.php',
		'template'	=> 'list.tpl.php',
	),
	'task'	=> array(
		'file'		=> DIR_ROUTES . 'task_edit.php',
		'template'	=> 'task.tpl.php',
	),
	'bulk'	=> array(
		'file'		=> DIR_ROUTES . 'bulk.php',
		'template'	=> 'bulk.tpl.php',
	),
	'static'	=> array(
		'file'		=> DIR_ROUTES . 'static.php',
		'cacheable'	=> array(
			'expires'=>'1 day',
			)
	),
	'undefined' => array(
		'file'		=> DIR_ROUTES . '404.php',
		'template'	=> '404.tpl.php',
		'cacheable'	=> array(
			'expires'=>'20 minutes',
			)
	),

);
$basecoat->routing->setRoutes($routes);
$basecoat->routing->set('use_pretty_urls', false);
$basecoat->routing->set('route_param', 'page');

require_once(DIR_LIB . 'classes/Tasks.class.php');
$tasks	= new Tasks();

//
// Add a hook before output to process common page elements
$basecoat->addBeforeRender( function() use ($basecoat) {
		$content	= new \Basecoat\View();
		$content->processTemplate($basecoat->view->templates_path . 'common/header.php');
		$content->processTemplate($basecoat->view->templates_path . 'common/footer.php');
		$content->addToView($basecoat->view);
});

//
// Process the request
echo $basecoat->processRequest();

echo '<pre>
Information retrieved from the DB class profiling function.
Following is what database activity just occurred:

'.print_r($basecoat->db->getProfiling(),true).'</pre>';

