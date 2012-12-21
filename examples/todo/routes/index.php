<?php
global $tasks;

// Check if there is a status change on a task
if ( isset($_POST['task_status']) ) {
	$tasks->update($_POST['task_id'], array('status_id'=>$_POST['task_status']) );
	$basecoat->messages->info('Congrats on completing the task!');
}

$content = new \Basecoat\View();
//$content->enable_data_tags = false;

// Get todo list
$todo_list		= array(
	'todo'	=> $tasks->getTodo(),
	'pastdue'	=> $tasks->getpastDue()
);
$todo_list['todo_count']	= count($todo_list['todo']);
$todo_list['pastdue_count']	= count($todo_list['pastdue']);

$content->multiadd($todo_list);

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);

$basecoat->routing->runNext();