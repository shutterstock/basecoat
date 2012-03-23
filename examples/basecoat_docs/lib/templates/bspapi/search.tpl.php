@css>
#image_container {
	border: 5px solid #3f3f3f;
}

#image_container div {
	float: left;
	text-align:center;
	height: 150px;
	width: 150px;
	margin: 5px;
	padding: 5px;
	border: 2px solid #3f3f3f;
	overflow: hidden;
}
#image_container a {
	color: #e0e0e0;
	font-weight: normal;
	text-decoration: none;
	font-size: smaller;
}
#image_container div img {
	border: 0px;
	margin-top: 10px;
}

@body>

<div style="float:right;font-weight:bold;">
Page <?php echo $this->page_no;?> of <?php echo $this->page_count;?>
</div>
<div style="font-weight:bold;margin-bottom: 5px;">
<?php echo $this->total_images;?> image found
</div>

<div id="image_container">
<?php
foreach($this->images as $image) {
?>
	<div>
		<a href="?page=image&id=<?php echo $image->id;?>">
		<img src="<?php echo $image->small_thumb->url;?>" height="<?php echo $image->small_thumb->height;?>" width="<?php echo $image->small_thumb->width;?>" />
		</a>
		<br />
		<a href="?page=image&id=<?php echo $image->id;?>"><?php echo $image->title;?></a>
	</div>
<?php
}

?>
<br />
<p style="clear:both"></p>
</div>