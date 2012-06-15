<?php

class Cache {
	protected $key_prefix	= null;

	public function __construct($key_prefix=null) {
		$this->key_prefix	= $key_prefix;
	}
	
	public function set($cache_key, $content, $duration, $compress=false) {
		
	}
	
	public function get($cache_key) {
	
	}

	public function delete($cache_key) {
	
	}
}