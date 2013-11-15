<?php
// Only leave error reporting on in dev
ini_set('display_errors', true);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('UTC');

// Configure paths for convenience
define('DIR_LIB', realpath('../lib').'/');
define('DIR_TPL', realpath('../templates').'/');
define('DIR_ROUTES', realpath('../routes').'/');

//
// This should be customized to where the Basecoat framework is located
include_once( '../../../basecoat/basecoat.php');

// Create instance of framework
$basecoat		= new \Basecoat\Basecoat();

/**
OPTIONAL DATABASE SETUP
// Connection settings for master/slave setup
$db_settings = array(
    'master' => array(
        'host' => 'localhost',
        'db' => 'todo',
        'username' => 'root',
        'password' => '',
        'label'=>'master'),
);
$db_settings['slave'] = $db_settings['master'];
// Load database and specify which setting is the master and which is the slave
// db instance is available in $bc->db
$basecoat->loadDb($db_settings, 'master', 'slave');
*/

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
        'template'	=> 'index.tpl.php',
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
//$basecoat->routing->set('use_pretty_urls', false);
//$basecoat->routing->set('route_param', 'page');

//
// Add a hook before output to process common page elements
$basecoat->addBeforeRender( function () use ($basecoat) {
        $content	= new \Basecoat\View();
        $content->processTemplate($basecoat->view->templates_path . 'common/header.php');
        $content->processTemplate($basecoat->view->templates_path . 'common/footer.php');
        $content->addToView($basecoat->view);
});

//
// Process the request
echo $basecoat->processRequest();
