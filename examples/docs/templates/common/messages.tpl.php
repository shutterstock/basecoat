@messages>

<?php

if ( isset($this->msg_info) ) {
	echo '
<div class="message_info">'.
	implode("<br />\n", $this->msg_info)
.'</div>';
}

if ( isset($this->msg_warn) ) {
	echo '
<div class="message_warn">'.
	implode("<br />\n", $this->msg_warn)
.'</div>';
}

if ( isset($this->msg_error) ) {
	echo '
<div class="message_error">'.
	implode("<br />\n", $this->msg_error)
.'</div>';
}