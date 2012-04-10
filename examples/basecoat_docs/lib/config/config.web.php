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
Content::$page->add('title',Content::$page->sitename . ': ');
Content::$page->add('lang', 'en', false);


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
	'page_header'	=> PATH_TEMPLATES . 'common/header.php',
	'page_footer'	=> PATH_TEMPLATES . 'common/footer.php',
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
	'basic'		=> PATH_LAYOUTS . 'basic.php',
	'dialog'	=> PATH_LAYOUTS . 'dialog.php',
);
// layout aliases
$_LAYOUTS_['default'] = &$_LAYOUTS_['basic'];

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
	'login'	=> array(
		'file'		=> PATH_ROUTES . 'login.php',
		'template'	=> PATH_TEMPLATES . 'login.php',
		'require_secure'	=> 1
	),
	'logout'	=> array(
		'file'		=> PATH_ROUTES . 'logout.php',
	),	
	'data'		=> array(
		'file'		=> PATH_ROUTES . 'data.php',
		'data_only'	=> 1
	),
	'not_found' => array(
		'file'		=> PATH_ROUTES . '404.php',
		'template'	=> PATH_TEMPLATES . '404.tpl.php',
		'layout'	=> PATH_LAYOUTS . 'basic.php',
		'data_only'	=> 1
	),
	'log'	=> array(
		'file' 		=> PATH_ROUTES . 'log_action.php',
		'data_only'	=> 1
	),
	// Site specific routes
	'configuration'	=> array(
		'file' 		=> PATH_ROUTES . 'configuration.php',
		'template'	=> PATH_TEMPLATES . 'configuration.tpl.php',
	),
	'routes'	=> array(
		'file' 		=> PATH_ROUTES . 'routing.php',
		'template'	=> PATH_TEMPLATES . 'routing.tpl.php',
	),
	'content'	=> array(
		'file' 		=> PATH_ROUTES . 'content.php',
		'template'	=> PATH_TEMPLATES . 'content.tpl.php',
	),
	'database'	=> array(
		'file' 		=> PATH_ROUTES . 'database.php',
		'template'	=> PATH_TEMPLATES . 'database.tpl.php',
		'require_login'	=> true,
	),
	'messages'	=> array(
		'file' 		=> PATH_ROUTES . 'messages.php',
		'template'	=> PATH_TEMPLATES . 'messaging.tpl.php',
	),
	'json'	=> array(
		'file' 		=> PATH_ROUTES . 'json.php',
		'layout'	=> PATH_LAYOUTS . 'json.tpl.php',
	),
	'login'	=> array(
		'file' 		=> PATH_ROUTES . 'login.php',
		'template'	=> PATH_TEMPLATES . 'login.tpl.php',
	),
	
	
	// Bigstock API example
	'bspapi'	=> array(
		'file' 		=> PATH_ROUTES . 'bspapi/index.php',
		'layout'	=> PATH_LAYOUTS . 'bspapi.php',
	),

);
// Declare route aliases
$_ROUTES_['dbfree']= $_ROUTES_['database'];
unset($_ROUTES_['dbfree']['require_login']);

$_ROUTES_['default']= &$_ROUTES_['home'];
$_ROUTES_['index']	= &$_ROUTES_['home'];

$_ROUTES_['conf'] 	= &$_ROUTES_['configuration'];
$_ROUTES_['ini']	= &$_ROUTES_['configuration'];


//
// Load authentication class
require_once(PATH_CLASS . 'auth.class.php');
Core::$auth		= new Auth();

//
// Initialize the session
session_start();

//
// Check if they are logged in
Core::$auth->checkLogin();
