<?php

// Check if there is a status change on a task
if ( isset($_POST['task_status']) ) {
	Core::$bc->tasks->update($_POST['task_id'], array('status_id'=>$_POST['task_status']) );
}

$content	= new Content();

// Get todo list
$tasks		= array(
	'todo'	=> Core::$bc->tasks->getTodo(),
	'pastdue'	=> Core::$bc->tasks->getpastDue()
);

$content->multiadd($tasks);


// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);
$content->addToPage();
unset($content);
