<br />

<form method="post" action="">
<input type="hidden" name="task_id" value="<?php echo $this->task_id; ?>" />

<label for="f_task">Task</label>
<input type="text" name="task_data[task]" id="f_task" value="<?php echo htmlentities($this->task); ?>" class="span4" />
<select name="task_data[status_id]" class="span2">
<?php
foreach($this->status_opts as $opt) {
	if ( $opt['sid']==$this->status_id ) {
		$selected	= ' selected';
	} else {
		$selected	= '';
	}
	echo "<option label='{$opt['name']}' value='{$opt['sid']}'{$selected}>{$opt['name']}</option>";
}
?>
</select>
<br />

<label for="f_duedate">Due Date</label>
<input type="date" name="task_data[due_date]" id="f_duedate" value="<?php echo htmlentities($this->due_date); ?>"  class="span2" />
<br />

<label for="f_category">Category</label>
<select name="task_data[category_id]" id="f_category">
<?php
foreach($this->category_opts as $opt) {
	if ( $opt['sid']==$this->category_id ) {
		$selected	= ' selected';
	} else {
		$selected	= '';
	}
	echo "<option label='{$opt['category']}' value='{$opt['sid']}'{$selected}>{$opt['category']}</option>";
}
?>
</select>
<br />

<label for="f_notes">Notes</label>
<textarea name="task_data[description]" id="f_notes" cols="80" rows="4"  class="span5">
<?php echo $this->description; ?>
</textarea>
<br />

<input type="submit" class="btn btn-primary" value="Save">
<a href="../" class="btn">Cancel</a>


