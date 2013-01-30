<?php
namespace Basecoat;

$basecoat_dir	= __DIR__ . '/';

require_once("{$basecoat_dir}classes/routing.class.php");
require_once("{$basecoat_dir}classes/content.class.php");

class Basecoat {
	
	// Class holders
	public $routing = null;

	public $view = null;
	
	/**
	* Default instance of Messages class
	*/
	public $messages = null;
	
	/**
	* Default instance of Database class
	*/
	public $db	= null;
	
	public $content	= null;
	
	public $hooks	= array(
		'beforeRender'	=> array(),
		'afterRender'	=> array()
	);
	
	/**
	* Headers to include in the output
	*/
	public $headers		= array(
		'Content-type'=>'text/html; charset=UTF-8',
		'X-Powered-By'=>'Basecoat PHP framework'
		);
		
	public function __construct() {
		$this->routing	= new Routing($this);
		$this->routing->setUrl();
		$this->view	= new View();
		$this->messages	= new Messages();
		$this->messages->setTemplate(__DIR__ . '/templates/messages.tpl.php');
	}

	public function addBeforeRender($func) {
		$this->hooks['beforeRender'][]	= $func;
	}
	
	public function clearBeforeRender() {
		$this->hooks['beforeRender']	= array();
	}
	
	public function addAfterRender($func) {
		$this->hooks['afterRender'][]	= $func;		
	}

	public function clearAfterRender() {
		$this->hooks['afterRender']	= array();
	}
	
	public function loadDb($settings, $master_id, $slave_id) {
		require_once(__DIR__ . '/classes/db.pdo.php');
		\Basecoat\DB::setServerConfig($settings, $master_id);
		$this->db 	= \Basecoat\DB::getServerInstance($slave_id);
	}

	public function processRequest($url=null) {
		$route_name	= $this->routing->parseUrl($url);
		$this->routing->run($route_name);
		$this->messages->display($this->view);
		return $this->render();
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
	
		
	public function render() {
		$this->processHooks($this->hooks['beforeRender']);
		$this->sendHeaders();
		$this->content	= $this->view->processTemplate($this->view->getLayout(), false);
		$this->processHooks($this->hooks['afterRender']);
		return $this->content;
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