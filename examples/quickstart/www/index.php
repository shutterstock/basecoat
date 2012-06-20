<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

define('SITE_DIR', realpath('../').'/');

define('BC_CONFIGS', SITE_DIR . 'config/');
define('BC_ROUTES', SITE_DIR . 'routes/');
define('BC_TEMPLATES', SITE_DIR . 'templates/');
define('BC_LAYOUTS', BC_TEMPLATES . 'layouts/');

// Load core initialization file to setup;
include_once( '../../../basecoat/init.php');

