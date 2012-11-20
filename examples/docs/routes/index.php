<?php

$basecoat->view->add('title', 'Introduction');

$content	= $basecoat->view->newView();

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);

