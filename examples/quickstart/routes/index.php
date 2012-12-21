<?php

$content = new \Basecoat\View();
$content->add('hello_world','Hello World');

$content->processTemplate($basecoat->view->templates_path . $basecoat->routing->current['template']);
$content->addToView($basecoat->view);

unset($content);

$basecoat->routing->runNext();