<?php

$content = new Content();

// Load template file
$tpl = file_get_contents($basecoat->view->templates_path . $basecoat->routing->current['template']);
// Parse section tags
$content->parseBlocks($tpl);

// Add route content to page
$content->addToView($basecoat->view);
unset($content);
