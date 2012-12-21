<?php
global $tasks;

$content = new \Basecoat\View();

// Check if we are adding or deleting
if ( isset($_POST['delete']) ) {
	$dresult	= $basecoat->db->delete('tasks', 'task LIKE :task_filter', array('task_filter'=>'Carpe Diem:%'));
	$bulk_message	= $dresult . ' tasks deleted.';
	$basecoat->messages->error($bulk_message);

} else {

	$bulk_insert = array();
	
	for ($i=3; $i<6; $i++) {
		$bulk_insert[]	= array(
			'task'			=> 'Carpe Diem: ' . date('l', strtotime('-'.$i.' days')),
			'description'	=> 'Seize the day!  ...or is it Carp is the Fish of the Day?',
			'due_date'		=> date('Y-m-d', strtotime('-'.$i.' days')),
			'status_id'		=> $tasks->default_status_id,
			'category_id'	=> $tasks->default_category_id,
			'created_on'	=> date('Y-m-d')
		);
	}
	
	for ($i=3; $i<10; $i++) {
		$bulk_insert[]	= array(
			'task'			=> 'Carpe Diem: ' . date('l', strtotime('+'.$i.' days')),
			'description'	=> 'Seize the day!  ...or is it Carp is the Fish of the Day?',
			'due_date'		=> date('Y-m-d', strtotime('+'.$i.' days')),
			'status_id'		=> $tasks->default_status_id,
			'category_id'	=> $tasks->default_category_id,
			'created_on'	=> date('Y-m-d')
		);
	}
	
	$iresult		= $tasks->save($bulk_insert);
	$bulk_message	= $iresult.' tasks added.';
	$content->add('bulk_insert', $bulk_insert);
	$basecoat->messages->info($bulk_message);

}



// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);
unset($content);
