<?php
Config::$page_includes	= array();


include(PATH_CLASS . 'bspapi.class.php');
$settings	= array(
	'account_id'	=> 34169,
	'secret_key'	=> '7ad8eec0a901b74ff8846cdfd923510991f86225',
	'mode'			=> 'test'
);
$bsp				= new BspApi($settings);

$response	= $bsp->image($_GET['id']);

$response	= json_decode($response);
if ( $response->response_code!=200 ) {
	exit('ERROR: '.$response->response_code . ' ' . $response->data->error->message);
}

$image	= $response->data->image;


$content	= new Content();
Content::$page->add('title', 'BiggerStock Images - '.$image->title);

$content->add('image', $image );

// Add route content to page
$content->loadTemplate($_ROUTES_[Core::$current_route]['template']);
$content->addToPage();
unset($content);
