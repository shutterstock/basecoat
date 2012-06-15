<?php

// Check if they are trying to login or out
if ( isset($_POST['loginout']) ) {
	if ($_POST['loginout']=='in') {
		$_SESSION['is_logged_in']	= true;
		Core::$auth->is_logged_in	= true;
		$route_swap					= Core::$current_route;
		Core::$current_route		= Core::$last_run_route;
		Core::$last_run_route		= $route_swap;
		return;
	} else {
		$_SESSION['is_logged_in']	= false;
		Core::$auth->is_logged_in	= false;
		header('Location: ./');
		exit();
	}
}
Core::$last_run_route		= Core::$current_route;

Content::$page->add('title','Login');

$content	= new Content();

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);

$content->addToPage();

unset($content);
