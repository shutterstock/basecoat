<?php

$routes_post = array(
	'post'	=> array(
		'file' => DIR_ROUTES . 'index.php',
		'template' => 'index.tpl.php',
	),
);

$routes = array_merge($routes, $routes_post);