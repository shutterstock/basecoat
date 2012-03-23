<?php
Content::$page->add('title','Database');

$content		= new Content();

$content->processTemplate($_ROUTES_[Core::$current_route]['template']);

// Add route content to page
$content->addToPage();
unset($content);
