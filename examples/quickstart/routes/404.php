<?php

$basecoat->view->add('title','404: Not Found');
header('HTTP/1.0 404 Not Found');

$content = new \Basecoat\View();
$content->add('requested_page', $this->requested_route);

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $this->current['template']);
$content->addToView($basecoat->view);
unset($content);
