<?php
namespace OCFram;

class BackController extends ApplicationComponent {
	protected $action = '';
	protected $module = '';
	protected $page = NULL;
	protected $view = '';

	public function __construct(Application $app, $module, $action) {
		parent::__construct($app);
	
		$this->page = new Page($app);
		
		$this->setModule($module);
		$this->setAction($action);
		$this->setView($action);
	}
	
	public function setModule($module) {
		if (!is_string($module) || empty($module)) {
			throw new \InvalidArgumentexception('Le module doit être une chaîne de caractères valide');
		}
		$this->module = $module;
	}
	public function setAction($action) {
		if (!is_string($action) || empty($action)) {
			throw new \InvalidArgumentexception('L\'action doit être une chaîne de caractères valide');
		}
		$this->action = $action;
	}
	public function setView($view) {
		if (!is_string($view) || empty($view)) {
			throw new \InvalidArgumentexception('La vue doit être une chaîne de caractères valide');
		}
		$this->view = $view;
	}
	
	public function page() { return $this->page; }
	
	public function execute() {
		$method = 'execute' . ucfirst($this->action);
		
		if (!is_callable([$this, $method])) {
			throw new \RuntimeException('L\'action "' . $this->action . '" n\'est pas définie sur ce module');
		}
		
		$this->$method($this->app->httpRequest());
	}
}