<?php
// Set template for displaying messages
\Basecoat\Content::$messages->setTemplate(BC_TEMPLATES . 'common/messages.tpl.php');


// Configure what type of URL format is in use
/*
Links in templates must be updated to match which
URL format is being used
If using Apache, this may be a good .htaccess to start with

RewriteEngine On
RewriteRule ^(css|img|js|files)($|/) - [L]
RewriteRule ^(.*)$ index.php [QSA,L]

*/
\Basecoat\Config::$use_pretty_urls	= true;

// If not using pretty URLs, what URL param contains which page to load
\Basecoat\Config::$route_param		= 'page';
\Basecoat\Config::$settings->url_root	= substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));

// Add content config to page
\Basecoat\Content::$page->add('charset', \Basecoat\Config::$content['charset'], false);
\Basecoat\Content::$page->add('lang', \Basecoat\Config::$content['lang'], false);

// Set page title prefix
\Basecoat\Content::$page->add('sitename', 'Basecoat Quick Start');
\Basecoat\Content::$page->add('title', \Basecoat\Content::$page->sitename);

/*
 -- Configure default template includes --
 These will be automatically used for each page
 unless the route overrides/clears them 
 within the route itself.
 These are run after all routes are run and are
 typically common page elements like header & footer
*/

\Basecoat\Config::$include_after	= array(
	'page_header'	=> BC_TEMPLATES . 'common/header.php',
	'page_footer'	=> BC_TEMPLATES . 'common/footer.php',
);

/******************************************************************************
 * Layouts - Containers for the templates
 *****************************************************************************/
\Basecoat\Config::$layouts = array(
	'basic'		=> BC_LAYOUTS . 'basic.php',
);
// layout aliases
\Basecoat\Config::$layouts['default'] = &\Basecoat\Config::$layouts['basic'];

/******************************************************************************
 * Routes
 *****************************************************************************/
// Fluid layout adjustments are made in the bootstrap_web.php
\Basecoat\Config::$routes = array(
	'home'	=> array(
		'file'		=> BC_ROUTES . 'index.php',
		'template'	=> BC_TEMPLATES . 'list.tpl.php',
	),
	'task'	=> array(
		'file'		=> BC_ROUTES . 'task_edit.php',
		'template'	=> BC_TEMPLATES . 'task.tpl.php',
	),
	'bulk'	=> array(
		'file'		=> BC_ROUTES . 'bulk.php',
		'template'	=> BC_TEMPLATES . 'bulk.tpl.php',
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
		'cacheable'	=> array(
			'expires'=>'20 minutes',
			)
	),

);
// Declare route aliases
\Basecoat\Config::$routes['default']	= &\Basecoat\Config::$routes['home'];
\Basecoat\Config::$routes['index']	= &\Basecoat\Config::$routes['home'];

//
// Initialize the session
session_start();

