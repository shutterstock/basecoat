<?php
Content::$page->add('title','404: Not Found');
header('HTTP/1.0 404 Not Found');

$content	= new Content();
$content->add('requested_page', \Basecoat\Core::$requested_route);

// Add route content to page
$content->processTemplate(\Basecoat\Config::$routes[\Basecoat\Core::$current_route]['template']);
$content->addToPage();
unset($content);

