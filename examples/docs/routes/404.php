<?php
$basecoat->view->add('title','404: Not Found');
header('HTTP/1.0 404 Not Found');

$content	= $basecoat->view->newView();
$content->add('requested_page', $basecoat->routing->requested_route);

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);
unset($content);

