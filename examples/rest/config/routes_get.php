<?php

$routes_get = array(
	'get'	=> array(
		'file' => DIR_ROUTES . 'index.php',
		'template' => 'index.tpl.php',
	),
);

$routes = array_merge($routes, $routes_get);