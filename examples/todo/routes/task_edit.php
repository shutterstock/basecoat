<?php
global $tasks;

// Check if we adding a new task, editing or saving
if ( isset($_POST['task_id']) ) {
	// Save task
	if ($_POST['task_id']=='new') {
		$task_data['created_on']	= date('Y-m-d');
		$result	= $tasks->save($_POST['task_data']);
	} else {
		$result	= $tasks->update($_POST['task_id'], $_POST['task_data']);
	}
	if ( $result<=0 ) {
		$basecoat->messages->error('Error saving task: '.Core::$db->errorMsg);
	} else {
		$basecoat->messages->info('The task has been saved, now do it!');
		header('Location: ./');
	}
}

$content = new \Basecoat\View();

$content->add('status_opts', $tasks->getStatusOpts());
$content->add('category_opts', $tasks->getCategoryOpts());


if ( $_GET['id']!='new' ) {
	// Retrieve task
	$task_data	= $tasks->get($_GET['id']);
	if ( !is_array($task_data) ) {
		$basecoat->messages->error('Invalid Task ID specified, entering a new Task');
		$_GET['task_id']	= 'new';
	}
}

if ( $_GET['id']=='new' ) {
	$task_data	= array(
		'task'	=> '',
		'description'	=> '',
		'category_id'	=> $tasks->default_category_id,
		'status_id'		=> $tasks->default_status_id,
	);
}
$task_data['task_id']	= $_GET['id'];

$content->multiadd($task_data);

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);
