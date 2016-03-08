<?php
namespace OCFram;

class HTTPResponse extends ApplicationComponent {
	/** @var  Page $Page */
	protected $Page;

	public function addHeader($header) {
		header($header);
	}

	public function redirect($location) {
		header('Location: ' . $location);
		exit;
	}

	public function redirect404() {
		$this->Page = new Page($this->getApp());
		$this->Page->setContentFile(__DIR__ . '/../../Errors/404.html');

		$this->addHeader('HTTP/1.0 404 Not Found');

		$this->send();
	}

	public function send() {
		exit($this->Page->getGeneratedPage());
	}

	public function setPage(Page $Page) { $this->Page = $Page; }

	public function setCookie($name, $value = '', $expire = 0, $path = NULL, $domain = NULL, $secure = false, $http_only = true) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $http_only);
	}
}