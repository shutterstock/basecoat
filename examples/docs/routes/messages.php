<?php

$basecoat->view->add('title','Messaging');

$basecoat->messages->info('This is an example of an information message');
$basecoat->messages->warn('This is an example of an warning message');
$basecoat->messages->error('This is an example of an error message');
$basecoat->messages->info('Adding another informational message');

$content = new \Basecoat\View();

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);
