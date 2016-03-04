<?php
namespace OCFram;

class HTTPRequest extends ApplicationComponent {
	public function cookieExists($key) {
		return isset($_COOKIE[$key]);
	}
	public function getCookieData($key) {
		return $this->cookieExists($key) ? $_COOKIE[$key] : NULL;
	}

	public function getMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}

	public function getExists($key) {
		return isset($_GET[$key]);
	}
	public function getGetData($key) {
		return $this->getExists($key) ? $_GET[$key] : NULL;
	}

	public function postExists($key) {
		return isset($_POST[$key]);
	}
	public function getPostData($key) {
		return $this->postExists($key) ? $_POST[$key] : NULL;
	}

	public function getRequestURI() {
		return $_SERVER['REQUEST_URI'];
	}
}