<?php

$content_blocks	= Content::getData();

exit( json_encode($content_blocks) );