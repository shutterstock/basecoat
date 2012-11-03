<?php
namespace Basecoat;

$basecoat_dir	= __DIR__ . '/';

require_once("{$basecoat_dir}classes/routing.class.php");
require_once("{$basecoat_dir}classes/content.class.php");

class Basecoat {
	public $config = null;
	
	// Class holders
	public $routing = null;

	public $view = null;
	
	/**
	* Default instance of Messages class
	*/
	public $messages = null;
	
	public $db	= null;
	
	public $layouts = array();
	
	public $templates_path	= null;
	
	public $hooks	= array(
		'beforeOut'	=> array(),
		'afterOut'	=> array()
	);
	
	public $use_pretty_urls	= true;
	
	public $route_param	= 'page';

	/**
	* Headers to include in the output
	*/
	public $headers		= array(
		'Content-type'=>'text/html; charset=UTF-8',
		'X-Powered-By'=>'Basecoat PHP framework'
		);
	
	/**
	* Content data
	*/
	public $content		= array(
		'charset'	=> 'UTF-8',
		'lang'		=> 'en'
		);
		
	public function __construct() {
		$this->routing	= new Routing($this);
		$this->routing->setUrl();
		$this->view	= new View();
		$this->messages	= new Messages($this->view);
		$this->messages->setTemplate(__DIR__ . '/templates/messages.tpl.php');
	}
	
	public function setTemplatesPath($path) {
		$this->templates_path	= $path;
	}

	public function addBeforeOut($func) {
		$this->hooks['beforeOut'][]	= $func;
	}
	
	public function clearBeforeOut() {
		$this->hooks['beforeOut']	= array();
	}
	
	public function addAfterOut($func) {
		$this->hooks['afterOut'][]	= $func;		
	}

	public function clearAfterOut() {
		$this->hooks['afterOut']	= array();
	}
	
	public function loadDb($settings, $master_id, $slave_id) {
		require_once(__DIR__ . '/classes/db.pdo.php');
		\DB::setServerConfig($settings, $master_id);
		$this->db 	= \DB::getServerInstance($slave_id);
	}

	public function processRequest($url=null) {
		$route_name	= $this->routing->parseUrl($url);
		$this->routing->run($route_name);
		$this->messages->display();
		$this->out();
	}
	
	public function setCacheHeaders($expires) {
		$hdrs	= array(
			"Pragma"	=> "cache",
			"Expires"	=> gmdate('D, d M Y H:i:s', strtotime('+'.$expires)).' GMT',
			"Cache-Control" => 'public, max-age='.(strtotime('+'.$expires)-time()),
			"Last-Modified"	=> gmdate("D, d M Y H:i:s") . " GMT"
		);
		$this->headers	= array_merge($this->headers, $hdrs);
	}

	public function sendHeaders($headers=array()) {
		$headers	= array_merge($this->headers, $headers);
		foreach($headers as $header=>$val) {
			header($header.': '.$val);
		}
		return count($headers);
	}
	
		
	public function out() {
		$this->processHooks($this->hooks['beforeOut']);
		$this->sendHeaders();
		echo $this->view->processTemplate($this->view->getLayout(), false);
		$this->processHooks($this->hooks['afterOut']);
	}
	
	public function processHooks($hook) {
		if ( count($hook)>0 ) {
			foreach($hook as $f) {
				if ( is_callable($f) ) {
					$f();
				}
			}
		}
		
	}

}