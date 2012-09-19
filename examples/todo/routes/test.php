<?php

$content	= new Content();
$content->add('hello_test','Hello Test Route');

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);

$content->addToPage();

unset($content);
