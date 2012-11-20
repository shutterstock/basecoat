@messages>

<?php

if ( isset($this->msg_info) ) {
	echo '
<div class="alert alert-info">'.
	implode("<br />\n", $this->msg_info)
.'</div>';
}

if ( isset($this->msg_warn) ) {
	echo '
<div class="alert">'.
	implode("<br />\n", $this->msg_warn)
.'</div>';
}

if ( isset($this->msg_error) ) {
	echo '
<div class="alert alert-danger">'.
	implode("<br />\n", $this->msg_error)
.'</div>';
}