<!DOCTYPE html">
<html lang="<?php echo $this->lang; ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="content-language" content="<?php echo $this->lang; ?>">

<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Config::$settings->url_root;?>bootstrap/css/bootstrap.min.css" />
<script type="text/javascript" src="<?php echo Config::$settings->url_root;?>bootstrap/js/bootstrap.js"></script>

<style type="text/css">
body {
	padding: 0px 20px;
}
.header {
	background-color: #f0f0f0;
	border-bottom: 2px solid #606060;
	padding: 2px 2px 2px 5px;
}
.header h1, .header h4 {
	margin: 0;
}
.footer {
	background-color: #f0f0f0;
	border-top: 2px solid #606060;
	font-size: smaller;
	padding: 10px;
}
pre {
	margin: 10px;
	font-size: smaller;
}

<?php echo $this->css; ?>
</style>

<script type="text/javascript">
<?php echo $this->script; ?>

</script>

</head>
<body>

<?php echo $this->page_header; ?>

<div class="content">
<?php echo $this->body_top; ?>

<?php echo $this->messages; ?>

<?php echo $this->body; ?>

<?php echo $this->body_btm; ?>
</div>
<?php echo $this->page_footer; ?>

</body>
</html>
