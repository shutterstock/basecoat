<?php
// Turn on error reporting for development
ini_set('display_errors', true);
error_reporting(E_ALL);

// Define the site root directory
define('SITE_DIR', realpath('../').'/');

define('BC_CONFIGS', SITE_DIR . 'config/');
define('BC_ROUTES', SITE_DIR . 'routes/');
define('BC_TEMPLATES', SITE_DIR . 'templates/');
define('BC_LAYOUTS', BC_TEMPLATES . 'layouts/');

// Change path to point to central basecoat framework directory
include_once( '../../../basecoat/init.php');

