<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE HTML">
<html lang="<?php echo $this->lang; ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<style type="text/css">
html,body {
font-family: Arial, Helvetica, sans-serif;
}
.content {
border: 2px solid #f0f0f0;padding:20px 10px;
}
.footer, .header {
clear:both;background-color:#f0f0f0;padding:5px;
}
.footer {
font-size:11px;text-align:center;
}
<?php echo $this->css; ?>

</style>

<script type="text/javascript">
<?php echo $this->script; ?>

</script>

</head>
<body class="container">
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
