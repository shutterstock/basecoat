<?php
// Set template for displaying messages
//Content::$messages->setTemplate(BC_TEMPLATES . 'common/messages.tpl.php');


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
 These will be automatically used for each page
 unless the route overrides/clears them 
 within the route itself.
 These are run after all routes are run and are
 typically common page elements like header & footer
*/

Config::$include_after	= array(
	'page_header'	=> BC_TEMPLATES . 'common/header.php',
	'page_footer'	=> BC_TEMPLATES . 'common/footer.php',
);

/******************************************************************************
 * Layouts - Containers for the templates
 *****************************************************************************/
Config::$layouts = array(
	'basic'		=> BC_LAYOUTS . 'basic.php',
	'dialog'	=> BC_LAYOUTS . 'dialog.php',
);
// layout aliases
Config::$layouts['default'] = &Config::$layouts['basic'];

/******************************************************************************
 * Routes
 *****************************************************************************/
// Fluid layout adjustments are made in the bootstrap_web.php
Config::$routes = array(
	'html'	=> array(
		'file'		=> BC_ROUTES . 'static.php',
	),
	'home'	=> array(
		'file'		=> BC_ROUTES . 'index.php',
		'template'	=> BC_TEMPLATES . 'index.tpl.php',
	),
	'login'	=> array(
		'file'		=> BC_ROUTES . 'login.php',
		'template'	=> BC_TEMPLATES . 'login.php',
		'require_secure'	=> 1
	),
	'logout'	=> array(
		'file'		=> BC_ROUTES . 'logout.php',
	),	
	'data'		=> array(
		'file'		=> BC_ROUTES . 'data.php',
		'data_only'	=> 1
	),
	'not_found' => array(
		'file'		=> BC_ROUTES . '404.php',
		'template'	=> BC_TEMPLATES . '404.tpl.php',
		'layout'	=> BC_LAYOUTS . 'basic.php',
		'data_only'	=> 1
	),
	'log'	=> array(
		'file' 		=> BC_ROUTES . 'log_action.php',
		'data_only'	=> 1
	),
	// Site specific routes
	'configuration'	=> array(
		'file' 		=> BC_ROUTES . 'configuration.php',
		'template'	=> BC_TEMPLATES . 'configuration.tpl.php',
	),
	'routes'	=> array(
		'file' 		=> BC_ROUTES . 'routing.php',
		'template'	=> BC_TEMPLATES . 'routing.tpl.php',
	),
	'content'	=> array(
		'file' 		=> BC_ROUTES . 'content.php',
		'template'	=> BC_TEMPLATES . 'content.tpl.php',
	),
	'database'	=> array(
		'file' 		=> BC_ROUTES . 'database.php',
		'template'	=> BC_TEMPLATES . 'database.tpl.php',
		'require_login'	=> true,
	),
	'messages'	=> array(
		'file' 		=> BC_ROUTES . 'messages.php',
		'template'	=> BC_TEMPLATES . 'messaging.tpl.php',
	),
	'json'	=> array(
		'file' 		=> BC_ROUTES . 'json.php',
		'layout'	=> BC_LAYOUTS . 'json.tpl.php',
	),
	'login'	=> array(
		'file' 		=> BC_ROUTES . 'login.php',
		'template'	=> BC_TEMPLATES . 'login.tpl.php',
	),
	'hello'	=> array(
		'file' 		=> BC_ROUTES . 'helloworld.php',
	),
	
	
	// Bigstock API example
	'bspapi'	=> array(
		'file' 		=> BC_ROUTES . 'bspapi/index.php',
		'layout'	=> BC_LAYOUTS . 'bspapi.php',
	),

);
// Declare route aliases
Config::$routes['dbfree']= Config::$routes['database'];
unset(Config::$routes['dbfree']['require_login']);

Config::$routes['default']= &Config::$routes['home'];
Config::$routes['index']	= &Config::$routes['home'];

Config::$routes['conf'] 	= &Config::$routes['configuration'];
Config::$routes['ini']	= &Config::$routes['configuration'];


//
// Load authentication class
require_once(SITE_DIR . 'classes/auth.class.php');
Core::$auth		= new Auth();

//
// Initialize the session
session_start();

//
// Check if they are logged in
Core::$auth->checkLogin();
