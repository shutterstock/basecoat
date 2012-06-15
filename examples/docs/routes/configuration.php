<?php
Content::$page->add('title','Configuration');

$content	= new Content();

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);
$content->addToPage();
unset($content);
