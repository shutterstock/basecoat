<?php

class Auth {
	public $is_logged_in	= false;

	public function __construct() {}

	/**
	* Check if user is logged in, update core variable
	**/
	public function checkLogin() {
		if ( isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] ) {
			$this->is_logged_in	= true;
		}
	}
	
	/**
	* Check if login is required for the current route and redirect if not logged in
	**/
	public function requireLogin() {
		if ( $this->is_logged_in ) {
			return;
		}
		Core::setRoute('login', true);
	}
	

}
