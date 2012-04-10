<?php

class Content {
	// Storage area for layout template to use
	public static $layout	= null;
	
	// Default instance of Content class for page
	public static $page		= null;
	
	// Default instance of Content class for messages
	public static $messages	= null;
	
	// Namespace to place content in if none is specified
	public $default_namespace	= 'body';
	
	// Name/value pairing of data tags and data for search and replace in templates
	public $data			= array();
	
	// Content "blocks" in template files
	public $blocks			= array();
	
	/**
	* Create an instance of the Content class
	*
	* @return Object instance of Content class
	*/
	public function __construct() {
	}
	
	/**
	* Getter method for returning a data item
	*
	* @return Mixed	value of the data item
	*/
	public function __get($name) {
		if ( !isset($this->data[$name]) ) {
			$this->data[$name]	= null;
		}
		return $this->data[$name];
	}
	
	/**
	* Return data values as string
	*
	* @return String values of data items delimited with line feeds
	*/
	public function __toString() {
		echo implode("\n", $this->data);
	}
	
	/**
	* Add content under the namespace
	* By default append to any existing data item with same namespace
	*
	* @param String $name namespace to add content under
	* @param Mixed $content content to any under the namespace, can be any data structure
	* @param Boolean $append whether or not to append the content to the namespace if namespace already exists
	*/
	public function add($name, $content, $append=true) {
		if ( isset($this->data[$name]) && $append ) {
			$this->data[$name]	.= $content;
		} else {
			$this->data[$name]	= $content;
		}
		$this->$name		= $this->data[$name];
	}
	
	/**
	* Add multiple content under multiple namespaces
	* Optionally provide a prefix to be prepended to each namespace name
	*
	* @param Array $name_vals array of name/value pairs to add
	* @param String $prefix prefix to prepend to each namespace name
	*/
	public function multiadd($name_vals, $prefix=null) {
		foreach($name_vals as $name=>$val ) {
			$this->add($prefix.$name, $val);
		}
	}
	
	/**
	* Add a content block under a specify namespace
	*
	* @param String $block_name namespace to add content block under
	* @param String $content content to add
	*/
	public function addBlock($block_name, $content) {
		if ( isset($this->blocks[$block_name]) ) {
			$this->blocks[$block_name]	.= $content;
		} else {
			$this->blocks[$block_name]	= $content;
		}
	}
	
	/**
	* Load and process a content template, 
	* optionally parse content blocks into namespaces
	*
	* @param String $tpl template file to include
	* @param Boolean $parse whether to parse the template blocks into namespaces (default true)
	* @return String the processed template or the number of content blocks parsed
	*/
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

	/**
	* Parse a template into content block namespaces
	* if content block identifiers are present
	*
	* @param String $tpl template to parse
	* @return Integer number of content blocks discovered
	*/
	public function parseBlocks($tpl) {
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
	
	/**
	* Clear all content block namespaces and data
	*/
	public function clear() {
		foreach($this->data as $namespace=>$data) {
			unset($this->$namespace);
		}
		$this->data		= array();
		$this->blocks	= array();
	}
	
	/**
	* Get all the data currently loaded
	*
	* @return Array associative array of loaded data
	*/
	public function getData() {
		return $this->data;
	}
	
	/**
	* Get all currently parsed content blocks
	*
	* @return Array associative array of content blocks
	*/
	public function getBlocks() {
		return $this->blocks;
	}
	
	/**
	* Merge content blocks for this instance with master instance
	*
	* @return Integer number of content blocks merged
	*/
	public function addToPage() {
		self::$page->multiadd($this->blocks);
		return count($this->blocks);
	}
	
}


class Messages {
	// Template file to use for message output
	private $tpl_file	= null;
	
	/**
	* Create an instance of the Message class
	*
	* @return Object instance of Message class
	*/
	public function __construct() {
	}
	
	/**
	* Set the template file to use for message output
	*
	* @param String $tpl_file path to the template file to load
	*/
	public function setTemplate($tpl_file) {
		$this->tpl_file	= $tpl_file;
	}
	
	/**
	* Add an information type message to output
	*/
	public function info($message) {
		$this->add('info', $message);
	}
	
	/**
	* Add an warning type message to output
	*/
	public function warn($message) {
		$this->add('warn', $message);
	}
	
	/**
	* Add an error type message to output
	*/
	public function error($message) {
		$this->add('error', $message);
	}
	
	/**
	* Get currently loaded messages
	* Optionally filter on message type
	*
	* @param String $msg_type message type to return (info, warn, error)
	* @return Array list of currently loaded messages
	*/
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
	
	/**
	* Add a message of the specified type
	*
	* @param String $type message type to add
	* @param String $message message to add
	* @return Integer current number of messages of the specified type
	*/
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
		return count($_SESSION['messages'][$type]);
	}
	
	/**
	* Add current messages to the page for output
	*
	* @clear Boolean $clear clear messages after added to output
	* @return Integer number of messages added to output
	*/
	public function display($clear=true) {
		if ( !isset($_SESSION['messages']) ) {
			return 0;
		}
		$msg_count	= 0;
		foreach($_SESSION['messages'] as $msgs) {
			$msg_count	+=count($msgs);
		}
		if ( $msg_count>0 ) {
			$content	= new Content();
			$content->multiadd($_SESSION['messages'], 'msg_');
			$msg_out	= $content->processTemplate($this->tpl_file);
			$content->addToPage();
			if ( $clear ) {
				$this->clear();
			}
			unset($content);
			return $msg_count;
		} else {
			return 0;
		}
	}
	
	/**
	* Clear all currently loaded messages
	*
	* @param String $msg_type optionally clear only messages of specified type (default is to clear all)
	* @return Integer number of messages cleared
	*/
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

