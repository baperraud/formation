<?php
namespace OCFram;

class Route {
	protected $action;
	protected $module;
	protected $url;
	protected $vars_names_a;
	protected $vars_a = [];

	public function __construct($url, $module, $action, array $vars_names_a) {
		$this->setUrl($url);
		$this->setModule($module);
		$this->setAction($action);
		$this->setVarsNames($vars_names_a);
	}

	public function hasVars() { return !empty($this->vars_names_a); }

	public function match($url) {
		if (preg_match('`^' . $this->url . '$`', $url, $matches)) {
			return $matches;
		} else {
			return false;
		}
	}

	public function setAction($action) {
		if (is_string($action)) {
			$this->action = $action;
		}
	}

	public function setModule($module) {
		if (is_string($module)) {
			$this->module = $module;
		}
	}

	public function setUrl($url) {
		if (is_string($url)) {
			$this->url = $url;
		}
	}

	public function setVarsNames(array $vars_names_a) { $this->vars_names_a = $vars_names_a; }

	public function setVars(array $vars_a) { $this->vars_a = $vars_a; }

	public function getAction() { return $this->action; }
	public function getModule() { return $this->module; }
	public function getVars_a() { return $this->vars_a; }
	public function getVarsNames_a() { return $this->vars_names_a; }
}