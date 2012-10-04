
<table class="table table-striped">
<tr>
<th>Category</th>
<th>Task</th>
<th>Due Date</th>
<th>Actions</th>
</tr>
<?php
foreach($this->tasklist as $item) {

echo <<<TEXT
	<tr>
	<td>{$item['category']}</td>
	<td>{$item['task']}</td>
	<td>{$item['due_date']}</td>
	<td>
		<form method="post" action="" style="display:inline">
		<input type="hidden" name="task_id" value="{$item['sid']}" />
		<input type="hidden" name="task_status" value="2" />
		<input type="submit" class="btn btn-mini btn-success" value="complete">
		</form>
		<a href="{$task_link}id={$item['sid']}" class="btn btn-mini">edit</a>
		&nbsp;
		<a href="#" class="btn btn-mini"><i class="icon-trash"></i></a>
	</td>
	</tr>
TEXT;

}
?>
</table>