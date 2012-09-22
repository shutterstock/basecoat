@messages>

<div style="margin:3px">
<?php

if ( isset($this->msg_info) ) {
	echo '
<div class="alert alert-block  alert-success">'.
'<span class="close" data-dismiss="alert">×</span>'.
	implode("<br />\n", $this->msg_info)
.'</div>';
}

if ( isset($this->msg_warn) ) {
	echo '
<div class="alert alert-block">'.
'<span class="close" data-dismiss="alert">×</span>'.
	implode("<br />\n", $this->msg_warn)
.'</div>';
}

if ( isset($this->msg_error) ) {
	echo '
<div class="alert alert-error">'.
'<span class="close" data-dismiss="alert">×</span>'.
	implode("<br />\n", $this->msg_error)
.'</div>';
}
?>
</div>