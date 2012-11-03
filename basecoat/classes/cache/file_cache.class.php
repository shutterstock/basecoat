<?php

class FileCache extends Cache {
	public $cache_dir	= null;
	public $key_prefix	= null;
	
	public function __construct($dir=null) {
		if ( is_null($dir) ) {
			$dir	= sys_get_temp_dir();
		}
		$this->cache_dir	= $dir;
	}
	
	public function put($key, $data) {
		$cache_file_name	= $this->cache_dir . $this->key_prefix .$key;
		if ( file_exists( $cache_file_name ) ) {
			fopen($cache_file_name, )
		}
	}
	
	public function get($key) {
		
	}
	
	public function delete($key) {
		
	}
	
}