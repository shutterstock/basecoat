<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE HTML">
<html lang="<?php echo $this->lang; ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
<style type="text/css">
<?php echo $this->css; ?>

</style>

<script type="text/javascript">
<?php echo $this->script; ?>

</script>

</head>
<body class="container">
<div id="fb-root"></div>
<?php echo $this->page_header; ?>
<?php echo $this->body_top; ?>

<div class="content_main">

<ul class="nav nav-tabs">
<li class="<?php echo Core::$requested_route == "default" ? "active" : ""; ?>"><a href="./">Home</a></li>
<li class="<?php echo Core::$requested_route == "configuration" ? "active" : ""; ?>"><a href="?page=configuration">Configuration</a></li>
<li class="<?php echo Core::$requested_route == "routes" ? "active" : ""; ?>"><a href="?page=routes">Routes</a></li>
<li class="<?php echo Core::$requested_route == "content" ? "active" : ""; ?>"><a href="?page=content">Content/Templates</a></li>
<li class="<?php echo Core::$requested_route == "messages" ? "active" : ""; ?>"><a href="?page=messages">Messaging</a></li>
<li class="<?php echo Core::$requested_route == "database" ? "active" : ""; ?>"><a href="?page=database">Database</a></li>
</ul>
<?php echo $this->messages; ?>

<?php echo $this->body; ?>
</div>

<?php echo $this->body_btm; ?>
<?php echo $this->page_footer; ?>

</body>
</html>
