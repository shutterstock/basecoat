<?php
Content::$page->add('title', 'Introduction');

$content	= new Content();

// Add route content to page
$content->processTemplate($_ROUTES_[Core::$current_route]['template']);
$content->addToPage();
unset($content);
