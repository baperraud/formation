<?php
namespace OCFram;

class HTTPRequest extends ApplicationComponent {
	public function cookieData($key) {
		return cookieExists($key) ? $_COOKIE[$key] : NULL;
	}
	public function cookieExists($key) {
		return isset($_COOKIE[$key]);
	}
	public function method() {
		return $_SERVER['REQUEST METHOD'];
	}
	public function getData($key) {
		return getExists($key) ? $_GET[$key] : NULL;	
	}
	public function getExists($key) {
		return isset($_GET[$key]);
	}
	public function postData($key) {
		return postExists($key) ? $_POST[$key] : NULL;	
	}
	public function postExists($key) {
		return isset($_POST[$key]);
	}
	public function requestURI() {
		return $_SERVER['REQUEST_URI'];
	}
}