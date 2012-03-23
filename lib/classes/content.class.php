<?php

class Content {
	public static $layout	= null;
	public static $page		= null;
	public static $messages	= null;
	public $default_namespace	= 'body';
	public $data			= array();
	public $blocks			= array();
	
	public function __construct() {
	}
	
	public function __get($name) {
		//echo 'Variable Name: '.$name;
		//$this->name		= null;
		if ( !isset($this->data[$name]) ) {
			$this->data[$name]	= null;
		}
		return $this->data[$name];
	}
	
	public function __toString() {
		echo implode("\n", $this->data);
	}
	
	public function add($name, $content, $append=true) {
		if ( isset($this->data[$name]) && $append ) {
			$this->data[$name]	.= $content;
		} else {
			$this->data[$name]	= $content;
		}
		$this->$name		= $this->data[$name];
	}
	
	public function multiadd($name_vals, $prefix=null) {
		foreach($name_vals as $name=>$val ) {
			$this->add($prefix.$name, $val);
		}
	}
	
	public function addBlock($block_name, $content) {
		if ( isset($this->blocks[$block_name]) ) {
			$this->blocks[$block_name]	.= $content;
		} else {
			$this->blocks[$block_name]	= $content;
		}
	}
	
	public function processTemplate($tpl, $parse=true) {
		if ( !file_exists($tpl) ) {
			return -1;
		}
		ob_start();
		include($tpl);
		$content	= ob_get_clean();
		if ( $parse ) {
			return $this->parseBlocks($content);
		} else {
			return $content;
		}
	}

	public function parseBlocks($tpl, $add_data=false) {
		$tpl_blocks	= preg_split('/^@(\\S+)>$/m', $tpl, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$blocks_parsed	= count($tpl_blocks);
		if ( 1 == $blocks_parsed ) {
			$this->addBlock($this->default_namespace, $tpl_blocks[0]);
		} else {
			$blocks_parsed	= $blocks_parsed/2;
			$namespace		= $this->default_namespace;
			foreach($tpl_blocks as $i=>$data) {
				if ( $i%2==0 ) {
					if ( strlen($data)>30 ) {
						$this->addBlock($namespace, $data);
					} else {
						$namespace	= $data;
					}
				} else {
					$this->addBlock($namespace, $data);
				}
			}
		}
		return $blocks_parsed;
	}
	
	public function clear() {
		foreach($this->data as $namespace=>$data) {
			unset($this->$namespace);
		}
		$this->data		= array();
		$this->blocks	= array();
	}
	
	public function getData() {
		return $this->data;
	}
		
	public function getBlocks() {
		return $this->blocks;
	}
	
	public function addToPage() {
		self::$page->multiadd($this->blocks);
	
	}
	
}


class Messages {
	private $tpl_file	= null;
	
	public function __construct() {
	}
	
	public function setTemplate($tpl_file) {
		$this->tpl_file	= $tpl_file;
	}
	
	public function info($message) {
		$this->add('info', $message);
	}
	
	public function warn($message) {
		$this->add('warn', $message);
	}
	
	public function error($message) {
		$this->add('error', $message);
	}
	
	public function get($msg_type=null) {
		if ( is_null($msg_type) ) {
			if ( isset($_SESSION['messages'][$msg_type]) ) {
				return $_SESSION['messages'][$msg_type];
			} else {
				return array();
			}
		} else {
			return $_SESSION['messages'];
		}
	}
	
	protected function add($type, $message) {
		// Store messages to display in the SESSION so that
		// they will be retained across page loads and until
		// cleared with displayMessages or clearMessages
		if ( !isset($_SESSION['messages']) ) {
			$_SESSION['messages']			= array();
		}
		$type	= strtolower($type);
		if ( isset($_SESSION['messages'][$type]) ) {
			$_SESSION['messages'][$type][]	= $message;
		} else {
			$_SESSION['messages'][$type]	= array($message);
		}
	}
	
	public function display() {
		if ( !isset($_SESSION['messages']) ) {
			return 0;
		}
		$msg_count	= count($_SESSION['messages']);
		if ( $msg_count>0 ) {
			$content	= new Content();
			$content->multiadd($_SESSION['messages'], 'msg_');
			$msg_out	= $content->processTemplate($this->tpl_file);
			$content->addToPage();
			$this->clear();
			unset($content);
			return $msg_count;
		} else {
			return 0;
		}
	}
	
	public function clear($msg_type=null) {
		if ( !isset($_SESSION['messages']) ) {
			return;
		}
		if ( is_null($msg_type) ) {
			unset($_SESSION['messages']);
		} else if ( isset($_SESSION['messages'][$msg_type]) ) {
			unset($_SESSION['messages'][$msg_type]);
		}
	}
	
}

