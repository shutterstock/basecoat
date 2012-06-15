@page_footer>
<script src="http://connect.facebook.net/en_US/all.js"></script>

<script type="text/javascript">
FbApp.init(<?php //echo Config::$settings['app_id']; ?>);

FbApp.getPerms('publish_stream', function() {
		alert('done');
	}
);

</script>

<div id="debug_box">
</div>