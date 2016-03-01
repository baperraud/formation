<?php
namespace OCFram;

abstract class Application {
	protected $name;
	protected $httpRequest;
	protected $httpResponse;

	public function __construct() {
		$this->httpRequest = new HTTPRequest($this);
		$this->httpResponse = new HTTPResponse($this);
		$this->name = '';
	}
	
	abstract public function run();
	
	public function name() { return $this->name; }
	public function httpRequest() { return $this->httpRequest; }
	public function httpResponse() { return $this->httpResponse; }
}