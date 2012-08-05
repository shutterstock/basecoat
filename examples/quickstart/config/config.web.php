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

// Add content config to page
Content::$page->add('charset', Config::$content['charset'], false);
Content::$page->add('lang', Config::$content['lang'], false);

// Set page title prefix
Content::$page->add('sitename', 'Basecoat Quick Start');
Content::$page->add('title', Content::$page->sitename);

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
);
// layout aliases
Config::$layouts['default'] = &Config::$layouts['basic'];

/******************************************************************************
 * Routes
 *****************************************************************************/
// Fluid layout adjustments are made in the bootstrap_web.php
Config::$routes = array(
	'home'	=> array(
		'file'		=> BC_ROUTES . 'index.php',
		'template'	=> BC_TEMPLATES . 'index.tpl.php',
		'cacheable'	=> array(
			'expires'=>'20 minutes',
			)
	),
	'html'	=> array(
		'file'		=> BC_ROUTES . 'static.php',
		'cacheable'	=> array(
			'expires'=>'1 day',
			)
	),
	'not_found' => array(
		'file'		=> BC_ROUTES . '404.php',
		'template'	=> BC_TEMPLATES . '404.tpl.php',
		'layout'	=> BC_LAYOUTS . 'basic.php',
		'data_only'	=> 1
	),

);
// Declare route aliases
Config::$routes['default']	= &Config::$routes['home'];
Config::$routes['index']	= &Config::$routes['home'];

//
// Initialize the session
session_start();

