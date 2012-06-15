<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<style type="text/css">
<?php echo $this->css; ?>
</style>

<script type="text/javascript">
<?php echo $this->script; ?>

</script>

</head>
<body>

<?php echo $this->page_header; ?>
<?php echo $this->body_top; ?>

<?php echo $this->messages; ?>

<div style="padding:20px 10px;">
<?php echo $this->body; ?>
</div>

<?php echo $this->body_btm; ?>
<?php echo $this->page_footer; ?>

</body>
</html>