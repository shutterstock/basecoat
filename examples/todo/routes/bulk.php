<?php

$bulk_insert = array();

for ($i=3; $i<6; $i++) {
	$bulk_insert[]	= array(
		'task'			=> 'Carpe Diem: ' . date('l', strtotime('-'.$i.' days')),
		'description'	=> 'Seize the day!  ...or is it Carp is the Fish of the Day?',
		'due_date'		=> date('Y-m-d', strtotime('-'.$i.' days')),
		'status_id'		=> Core::$bc->tasks->default_status_id,
		'category_id'	=> Core::$bc->tasks->default_category_id,
		'created_on'	=> date('Y-m-d')
	);
}

for ($i=3; $i<10; $i++) {
	$bulk_insert[]	= array(
		'task'			=> 'Carpe Diem: ' . date('l', strtotime('+'.$i.' days')),
		'description'	=> 'Seize the day!  ...or is it Carp is the Fish of the Day?',
		'due_date'		=> date('Y-m-d', strtotime('+'.$i.' days')),
		'status_id'		=> Core::$bc->tasks->default_status_id,
		'category_id'	=> Core::$bc->tasks->default_category_id,
		'created_on'	=> date('Y-m-d')
	);
}

Core::$bc->tasks->save($bulk_insert);

$content	= new Content();

$content->add('bulk_insert', $bulk_insert);


// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);
$content->addToPage();
unset($content);
