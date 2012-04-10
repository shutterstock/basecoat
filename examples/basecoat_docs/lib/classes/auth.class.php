<?php

class Auth {
	// Status of login
	public $is_logged_in	= false;

	/**
	* Initialize an Auth class instance
	*
	* @return Object	instance of the Auth class
	*/
	public function __construct() {}

	/**
	* Check if user is logged in, update core variable
	*
	* @return Boolean	whether the user is logged in or not
	**/
	public function checkLogin() {
		if ( isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] ) {
			$this->is_logged_in	= true;
		}
		return $this->is_logged_in;
	}
	
	/**
	* Check if login is required for the current route and redirect if not logged in
	*
	**/
	public function requireLogin() {
		if ( $this->is_logged_in ) {
			return;
		}
		Core::setRoute('login', true);
	}
	

}
