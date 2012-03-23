<?php
/*
Online documentation available:
	http://help.bigstockphoto.com/entries/20843622-api-overview

*/

class BspApiException extends Exception {

}

class BspApi {
	const VERSION_MAJOR		= 2;
	const VERSION_MINOR		= 0;
	protected $account_id	= '';
	protected $secret_key	= '';
	protected $mode			= 'test';
	private $base_url		= '';
	public $response_format	= 'json';
	
	public static $domains	= array(
		'test'	=> 'testapi.bigstockphoto.com',
		'prod'	=> 'api.bigstockphoto.com'
	);

	public static $curl_opts = array(
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT        => 60,
		CURLOPT_USERAGENT      => 'bspapi-php-',
	);
	
	/**
	 * Create a new API object
	 * Must contain at least:
	 * - account_id 
	 * - secret_key
	 *
	 * @param array $config	name/value pair of configuration options.
	 *
	**/
	public function __construct($config) {
		$this->account_id	= $config['account_id'];
		$this->secret_key	= $config['secret_key'];
		if ( isset($config['mode']) && $config['mode']=='prod' ) {
			$this->setProdMode();
		} else {
			$this->setTestMode();
		}
		self::$curl_opts[CURLOPT_USERAGENT]	.= self::VERSION_MAJOR . '.' . self::VERSION_MINOR;
	}
	
	/**
	 * Set API object into Test Mode
	 * Will not be charged for purchases,
	 * no downloads of real, unwatermarked images possible
	 *
	**/
	public function setTestMode() {
		$this->base_url		= self::$domains['test'] . '/' . self::VERSION_MAJOR . '/' . $this->account_id . '/';
	}

	/**
	 * Set API object into Production Mode
	 *
	**/
	public function setProdMode() {
		$this->base_url		= self::$domains['prod'] . '/' . self::VERSION_MAJOR . '/' . $this->account_id . '/';
	}
	
	/**
	 * Perform a search for images
	 * See documentation for options
	 * http://help.bigstockphoto.com/entries/20843622-api-overview
	 * 
	 * @param array $params	Name/value pair of search fields/values
	 * @return array Result of search
	 * @throws BspApiException
	 *
	**/
	public function search($params) {
		if ( count($params) == 0 ) {
			throw new BspApiException('No Search Parameters passed: '.implode(', ',$invalid_params) );
		}
		// Check of a response format was specified
		if ( !isset($params['format']) ) {
			$params['format']		= $this->response_format;
		}
		// Build search URL
		$search_url			= 'http://' . $this->base_url.'search/?' . http_build_query($params);
		return $this->sendRequest($search_url);
		
	}
	
	/**
	 * Retrieve information about a single image
	 *
	 * @param int $image_id	ID of image to retrieve
	 * @param string $fields Comma separated string of fields to return
	 * @return array Result of image request
	 *
	**/
	public function image($image_id, $fields=null) {
		$params			= array('image_id'=>$image_id);
		if ( !is_null($fields) ) {
			$params['fields']	= $fields;
		}
		$image_url				= 'http://' . $this->base_url.'image/' . $image_id;
		return $this->sendRequest($image_url);
	}
	
	/**
	 * Purchase an image. If successful, a download ID will be returned
	 *
	 * @param int $image_id ID of image to purchase
	 * @param int $product_id Product ID of image to buy
	 * @return array Contains the download ID required for downloading the image
	 *
	*/
	public function purchase($image_id, $product_id) {
		$auth_key		= sha1($this->secret_key . $this->account_id . $image_id);
		$params			= array(
			'image_id'	=> $image_id,
			'product_id'	=> $product_id,
			'auth_key'	=> $auth_key
		);
		$image_url		= 'https://' . $this->base_url.'purchase/?' . http_build_query($params);
		return $this->sendRequest($image_url);
	}
	
	/**
	 * Create the URL required for downloading the image
	 *
	 * @param int $download_id Download ID return by the purchase method
	 * @return string URL that can be used to download the image file
	 *
	**/
	public function getDownloadUrl($download_id) {
		$auth_key		= sha1($this->secret_key . $this->account_id . $download_id);
		$params			= array(
			'image_id'	=> $image_id,
			'format_id'	=> $format_id,
			'auth_key'	=> $auth_key
		);
		$download_url		= 'http://' . $this->base_url.'purchase/?' . http_build_query($params);
		return $download_url;
	}
	
	/**
	 * Send the API request to the Bigstock server
	 *
	 * @param string $url API URL to retrieve data from
	 *
	**/
	protected function sendRequest($url) {
		$ch					= curl_init();
		$opts				= self::$curl_opts;
		$opts[CURLOPT_URL]	= $url;
		
		curl_setopt_array($ch, $opts);
		$result				= curl_exec($ch);
		if ($result === false) {
			$err_no		= curl_errno($ch);
			$err_msg	= 'SendRequest Exception: '.$url.' '.curl_error($ch);
			curl_close($ch);
			throw new BspApiException($err_msg, $err_no);
		}
		curl_close($ch);
		return $result;
	}

}