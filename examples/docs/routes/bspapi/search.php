<?php

try {
$response	= $bsp->search(array(
	'q'	=> $_GET['words']
	));
} catch ( BspApiException $e) {
	echo $e->getMessage();
	exit();
}
$response	= json_decode($response);
if ( $response->response_code!=200 ) {
	exit('ERROR: '.$response->response_code . ' ' . $response->message);
}

$paging	= $response->data->paging;
$images	= $response->data->images;


$content	= new Content();
Content::$page->add('title', 'BiggerStock Images - ');

$content->add('total_images', number_format($paging->total_items) );
$content->add('page_no', number_format($paging->page) );
$content->add('page_count', number_format($paging->total_pages) );
$content->add('images', $images);

// Add route content to page
$content->loadTemplate($_ROUTES_[Core::$current_route]['template']);
$content->addToPage();
unset($content);
