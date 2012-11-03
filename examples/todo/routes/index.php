<?php

// Check if there is a status change on a task
if ( isset($_POST['task_status']) ) {
	\Basecoat\Core::$bc->tasks->update($_POST['task_id'], array('status_id'=>$_POST['task_status']) );
	\Basecoat\Content::$messages->info('Congrats on completing the task!');
}

$content	= new \Basecoat\Content();

// Get todo list
$tasks		= array(
	'todo'	=> \Basecoat\Core::$bc->tasks->getTodo(),
	'pastdue'	=> \Basecoat\Core::$bc->tasks->getpastDue()
);
$tasks['todo_count']	= count($tasks['todo']);
$tasks['pastdue_count']	= count($tasks['pastdue']);

$content->multiadd($tasks);


// Add route content to page
$content->processTemplate(\Basecoat\Config::$routes[\Basecoat\Core::$current_route]['template']);
$content->addToPage();
unset($content);
