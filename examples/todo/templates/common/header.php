@page_header>
<?php
if ($this->use_pretty_urls) {
	$task_url	= 'task/?id=new';
} else {
	$task_url	='?page=task&id=new';
}
?>

<div class="header">
<div style="position: fixed;top:15px;right:50px;">
<a href="<?php echo $task_url;?>" class="btn btn-primary">New To Do</a>
</div>

<a href="./">
<h1>Basecoat: To Do list example</h1>
</a>

<h4>Don't put off `til tomorrow what can be done today</h4>
</div>