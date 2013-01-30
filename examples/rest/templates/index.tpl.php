@body_top>
<div class="section">
<legend><?php echo basename(__FILE__);?> template</legend>

<div class="content">
<br/>body_top<br/>
</div>

</div>

@body>
<div class="section">
<legend><?php echo basename(__FILE__);?> template</legend>

<div class="content">
<br/>body
<?php echo $this->hello_world; ?>
<br/>

</div>
</div>

@body_btm>
<div class="section">
<legend><?php echo basename(__FILE__);?> template</legend>

<div class="content">
<br/>body_btm<br/>
</div>

</div>