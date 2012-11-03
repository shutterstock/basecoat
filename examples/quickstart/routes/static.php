<?php

$content	= $basecoat->view->newView();

$tpl		= file_get_contents($basecoat->view->templates_path . $this->current['template']);

$content->parseBlocks($tpl);

// Add route content to page
$content->addToView($basecoat->view);
unset($content);
