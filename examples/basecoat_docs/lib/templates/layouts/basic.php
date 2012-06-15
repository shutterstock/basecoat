<!DOCTYPE HTML">
<html lang="<?php echo $this->lang; ?>" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
<title><?php echo $this->title; ?></title>
<?php echo $this->head; ?>

<link rel="stylesheet" type="text/css" href="css/basenn.css" />
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />

<style type="text/css">
<?php echo $this->css; ?>

ul#tabnav { /* general settings */
text-align: left; /* set to left, right or center */
margin: 1em 0 1em 0; /* set margins as desired */
font: bold 11px verdana, arial, sans-serif; /* set font as desired */
border-bottom: 1px solid #999; /* set border COLOR as desired */
list-style-type: none;
padding: 3px 10px 3px 10px; /* THIRD number must change with respect to padding-top (X) below */
}

ul#tabnav li { /* do not change */
display: inline;
}

body#tab1 li.tab1, body#tab2 li.tab2, body#tab3 li.tab3, body#tab4 li.tab4 { /* settings for selected tab */
border-bottom: 1px solid #fff; /* set border color to page background color */
background-color: #fff; /* set background color to match above border color */
}

body#tab1 li.tab1 a, body#tab2 li.tab2 a, body#tab3 li.tab3 a, body#tab4 li.tab4 a { /* settings for selected tab link */
background-color: #fff; /* set selected tab background color as desired */
color: #000; /* set selected tab link color as desired */
position: relative;
top: 1px;
padding-top: 4px; /* must change with respect to padding (X) above and below */
}

ul#tabnav li a { /* settings for all tab links */
padding: 3px 4px; /* set padding (tab size) as desired; FIRST number must change with respect to padding-top (X) above */
border: 1px solid #999; /* set border COLOR as desired; usually matches border color specified in #tabnav */
background-color: #f0f0f0; /* set unselected tab background color as desired */
color: #666; /* set unselected tab link color as desired */
margin-right: -2px; /* set additional spacing between tabs as desired */
text-decoration: none;
border-bottom: none;
}

ul#tabnav a:hover { /* settings for hover effect */
background: #fff; /* set desired hover color */
}

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

<div class="nav_list">
<ul id="tabnav">
<li class="tab1"><a href="./">Home</a></li>
<li class="tab2"><a href="?page=configuration">Configuration</a></li>
<li class="tab3"><a href="?page=routes">Routes</a></li>
<li class="tab4"><a href="?page=content">Content/Templates</a></li>
<li class="tab5"><a href="?page=messages">Messaging</a></li>
<li class="tab6"><a href="?page=database">Database</a></li>
</ul>
</div>
<?php echo $this->messages; ?>

<?php echo $this->body; ?>
</div>

<?php echo $this->body_btm; ?>
<?php echo $this->page_footer; ?>

</body>
</html>