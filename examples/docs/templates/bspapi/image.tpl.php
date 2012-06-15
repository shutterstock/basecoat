@body>
<div style="text-align: center;color: #e0e0e0;">
<?php
echo $this->image->title;
?>
<br />

<img src="<?php echo $this->image->preview->url;?>" height="<?php echo $this->image->preview->height;?>" width="<?php echo $this->image->preview->width;?>" />
<br />
<div>
<strong>Sizes available:</strong><br />
<?php
foreach($this->image->formats as $size) {
	echo $size->label . ' ('.$size->height.' x '.$size->width.') <br />';
}
?>
</div>
</div>
