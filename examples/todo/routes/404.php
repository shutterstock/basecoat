<?php
$content = new \Basecoat\View();

$content->add('title','404: Not Found');
header('HTTP/1.0 404 Not Found');

$content->add('requested_page', $basecoat->routing->requested_url);

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);
unset($content);
