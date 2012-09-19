<?php

// Check if we adding a new task, editing or saving
if ( isset($_POST['task_id']) ) {
	// Save task
	if ($_POST['task_id']=='new') {
		$task_data['created_on']	= date('Y-m-d');
		$result	= Core::$bc->tasks->save($_POST['task_data']);
	} else {
		$result	= Core::$bc->tasks->update($_POST['task_id'], $_POST['task_data']);
	}
	if ( $result<=0 ) {
		Content::$messages->error('Error saving task: '.Core::$db->errorMsg);
	} else {
		Content::$messages->info('The task has been saved, no do it!');
		header('Location: '.Config::$settings->url_root);
	}
}

$content	= new Content();

$content->add('status_opts', Core::$bc->tasks->getStatusOpts());
$content->add('category_opts', Core::$bc->tasks->getCategoryOpts());


if ( $_GET['id']!='new' ) {
	// Retrieve task
	$task_data	= Core::$bc->tasks->get($_GET['id']);
	if ( !is_array($task_data) ) {
		Content::$messages->error('Invalid Task ID specified, entering a new Task');
		$_GET['task_id']	= 'new';
	}
}

if ( $_GET['id']=='new' ) {
	$task_data	= array(
		'task'	=> '',
		'description'	=> '',
		'category_id'	=> Core::$bc->tasks->default_category_id,
		'status_id'		=> Core::$bc->tasks->default_status_id,
	);
}
$task_data['task_id']	= $_GET['id'];

$content->multiadd($task_data);

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);
$content->addToPage();
unset($content);
