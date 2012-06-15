<?php

Content::$page->add('title','Messaging');

Content::$messages->info('This is an example of an information message');
Content::$messages->warn('This is an example of an warning message');
Content::$messages->error('This is an example of an error message');
Content::$messages->info('Adding another informational message');

$content	= new Content();

// Add route content to page
$content->processTemplate(Config::$routes[Core::$current_route]['template']);
$content->addToPage();
unset($content);
