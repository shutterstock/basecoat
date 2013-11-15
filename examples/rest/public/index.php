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
define('DIR_CONFIG', realpath('../config').'/');

//
// This should be customized to where the Basecoat framework is located
include_once( '../../../basecoat/basecoat.php');

// Create instance of framework
$basecoat		= new \Basecoat\Basecoat();

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
        'file' => DIR_ROUTES . 'index.php',
        'template' => 'index.tpl.php',
        'methods' => array('POST', 'GET')
    ),
    'undefined' => array(
        'file'		=> DIR_ROUTES . '404.php',
        'template'	=> '404.tpl.php',
        'cacheable'	=> array(
            'expires'=>'20 minutes',
            )
    ),

);
// Determine which routes to load
if ( in_array($_SERVER['REQUEST_METHOD'], array('GET','POST','PUT','DELETE') ) ) {
    include_once(DIR_CONFIG.'routes_'.strtolower($_SERVER['REQUEST_METHOD']).'.php');
}

$basecoat->routing->setRoutes($routes);
$basecoat->routing->addBeforeEach(function () use ($basecoat) {
    // Check if method matches
    if ( isset($basecoat->routing->current['methods']) ) {
        if ( !in_array($_SERVER['REQUEST_METHOD'], $basecoat->routing->current['methods']) ) {
            // Not a valid method for route, reset routes and run undefined
            $basecoat->routing->setRunRoutes( array('undefined'), true );
        }
    }
});
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
