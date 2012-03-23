<?php
//
// Set constant for template and layout directories
define('PATH_TEMPLATES',	PATH_LIB . 'templates/');
define('PATH_LAYOUTS',		PATH_TEMPLATES . 'layouts/');

//
// Create main instance of View Controller/Content class
require_once(PATH_CLASS . 'content.class.php');
Content::$page		= new Content();
// Create instance of message display class
Content::$messages	= new Messages();
Content::$messages->setTemplate(PATH_TEMPLATES . 'common/messages.tpl.php');


// Configure what type of URL format is in use
/*
Links in templates must be updated to match which
URL format is being used
If using Apache, this may be a good .htaccess to start with

RewriteEngine On
RewriteRule ^(css|img|js|files)($|/) - [L]
RewriteRule ^(.*)$ index.php [QSA,L]

*/
Config::$use_pretty_urls	= false;

// If not using pretty URLs, what URL param contains which page to load
Config::$route_param		= 'page';

// Set page title prefix
Content::$page->add('sitename', 'Basecoat');
Content::$page->add('title', Content::$page->sitename . ': ');


/*
 -- Configure default template includes --
 These will be automatically used for each route
 unless the route overrides/clears them 
 within the route itself.
 These are run after all routes are run and are
 typically common page elements like header & footer
 
 $pre_includes and $post_includes are also available
*/

Config::$page_includes	= array(
	'page_header'	=> PATH_TEMPLATES . 'common/example.header.php',
	'page_footer'	=> PATH_TEMPLATES . 'common/example.footer.php',
);

/*
 Configure files to load before running any routes
 Typically this will be an additional config/load file
 that will load files that may take advantage of what
 route was requested
*/

/******************************************************************************
 * Layouts - Containers for the templates
 *****************************************************************************/
$_LAYOUTS_ = array(
	'example'		=> PATH_LAYOUTS . 'example.php',
);
// layout aliases
$_LAYOUTS_['default'] = &$_LAYOUTS_['example'];

/******************************************************************************
 * Routes
 *****************************************************************************/
// Fluid layout adjustments are made in the bootstrap_web.php
$_ROUTES_ = array(
	'html'	=> array(
		'file'		=> PATH_ROUTES . 'static.php',
	),
	'home'	=> array(
		'file'		=> PATH_ROUTES . 'index.php',
		'template'	=> PATH_TEMPLATES . 'index.tpl.php',
	),
	'not_found' => array(
		'file'		=> PATH_ROUTES . '404.php',
		'template'	=> PATH_TEMPLATES . '404.tpl.php',
		'layout'	=> $_LAYOUTS_['default'],
		'data_only'	=> 1
	),
	'messages'	=> array(
		'file' 		=> PATH_ROUTES . 'messages.php',
		'template'	=> PATH_TEMPLATES . 'messaging.tpl.php',
	),

);

$_ROUTES_['default']= &$_ROUTES_['home'];
$_ROUTES_['index']	= &$_ROUTES_['home'];
