<?php

$content = new \Basecoat\View();

$tpl = file_get_contents($basecoat->view->templates_path . $basecoat->routing->current['template']);

$content->parseBlocks($tpl);

// Add route content to page
$content->addToView($basecoat->view);
unset($content);
