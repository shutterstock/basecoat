<?php

$content_blocks	= $this->getData();
$this->replaceDataTags($content_blocks);

exit( json_encode($content_blocks) );