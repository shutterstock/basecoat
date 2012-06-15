<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<link rel="stylesheet" type="text/css" href="<?php echo BASEDIR_WEB;?>/css/base.css" />
<style type="text/css">
body {
	background-color: #303030;
	color: #fffff0;
}
<?php echo $this->css; ?>

</style>

<script type="text/javascript">
<?php echo $this->script; ?>

</script>

</head>
<body>
<div id="fb-root"></div>

<div style="float:right">
<form method="GET" action="">
<input type="hidden" name="page" value="search">
<input type="text" name="words" size="50" value="<?php echo $_GET['words'];?>">
<input type="submit" label="search" value="search">
</form>
</div>
<h1>BiggerStock</h1>
<?php echo $this->page_header; ?>
<?php echo $this->body_top; ?>

<div class="content_main">
<?php echo $this->messages; ?>

<?php echo $this->body; ?>
</div>

<?php echo $this->body_btm; ?>
<?php echo $this->page_footer; ?>

</body>
</html>