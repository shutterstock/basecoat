<?php

$content	= new Content();

// Get list of current users
$users		= array();
$user_count	= Core::$bc->db->select('SELECT * FROM users ORDER BY created_on DESC');
if ( $user_count>0 ) {
	Core::$bc->fetchAll($users);
}

$content->add('user_count', $user_count);
$content->add('users', $users);

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);

$content->addToPage();
unset($content);
