@page_footer>
<?php
if ($this->use_pretty_urls) {
	$bulk_url	= 'bulk/';
} else {
	$bulk_url	= '?page=bulk';
}
?>

<hr>
<div class="footer">

<div style="float: right;">
<a href="<?php echo $bulk_url;?>" class="btn btn-mini">Add Test Data</a>

<form method="POST" action="<?php echo $bulk_url;?>" style="display:inline">
<input type="hidden" name="delete" value="1" />
<input type="submit" class="btn btn-mini btn-inverse" value="Clear Test Data">
</form>
</div>

To Do list example by Brent Baisley

<div style="clear:both"></div>
</div>