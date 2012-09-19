<?php
if ( count($this->pastdue)>0 ) {
	echo '<h3 style="color:red">Past Due ' . ' ('.count($this->pastdue).')</h3>'; 

	$this->tasklist_title	= 'Past Due';
	$this->tasklist			= $this->pastdue;
	include( BC_TEMPLATES . 'common/tasklist.tpl.php');
}

echo '<h3>To Do ' . ' ('.count($this->todo).')</h3>'; 
$this->tasklist	= $this->todo;

include( BC_TEMPLATES . 'common/tasklist.tpl.php');


