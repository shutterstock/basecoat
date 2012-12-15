<?php

$basecoat->view->add('title','Content, Templates &amp; Layouts');

$content = new \Basecoat\View();

// Add route content to page
$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);

$basecoat->routing->runNext();