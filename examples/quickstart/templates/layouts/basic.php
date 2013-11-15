<!DOCTYPE HTML">
<html lang="<?php echo $this->lang; ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<style type="text/css">
html,body {
font-family: Arial, Helvetica, sans-serif;
}
body {
    border: 1px solid #c0c0c0;
}
legend {
    background-color: #606060;
    color: #f0f0f0;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 5px;
}
.section {
    border: 1px solid #c0c0c0;
    margin:2px;
}
.content {
    padding: 5px;
}
<?php echo $this->css; ?>

</style>

<script type="text/javascript">
<?php echo $this->script; ?>

</script>

</head>
<body class="container">
<legend><?php echo basename(__FILE__);?> layout</legend>
<div class="content">

<?php echo $this->page_header; ?>

<div class="content">
<?php echo $this->body_top; ?>

<?php echo $this->messages; ?>

<?php echo $this->body; ?>

<?php echo $this->body_btm; ?>
</div>
<?php echo $this->page_footer; ?>

</div>

</body>
</html>
