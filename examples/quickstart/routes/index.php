<?php

$content	= $basecoat->view->newView();
$content->add('hello_world','Hello World');

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);

$content->addToView($basecoat->view);

unset($content);
