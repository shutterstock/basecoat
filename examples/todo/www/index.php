<?php
// Turn on error reporting for development
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

// Define the site root directory
define('SITE_DIR', realpath('../').'/');

define('BC_CONFIGS', SITE_DIR . 'config/');
define('BC_ROUTES', SITE_DIR . 'routes/');
define('BC_TEMPLATES', SITE_DIR . 'templates/');
define('BC_LAYOUTS', BC_TEMPLATES . 'layouts/');
define('BC_LIB', SITE_DIR . 'lib/');

// Change path to point to central basecoat framework directory
include_once( '../../../basecoat/init.php');


echo '<pre>
Information retrieved from the DB class profiling function.
Following is what database activitiy just occurred:

'.print_r(Core::$db->getProfiling(),true).'</pre>';

