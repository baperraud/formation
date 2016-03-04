<?php
namespace OCFram;

abstract class Application {
	protected $Http_request;
	protected $Http_response;
	protected $name;
	protected $User;
	protected $Config;

	public function __construct() {
		$this->Http_request = new HTTPRequest($this);
		$this->Http_response = new HTTPResponse($this);
		$this->name = '';
		$this->User = new User($this);
		$this->Config = new Config($this);
	}

	abstract public function run();

	public function getName() { return $this->name; }
	public function getHttpRequest() { return $this->Http_request; }
	public function getHttpResponse() { return $this->Http_response; }
	public function getUser() { return $this->User; }
	public function getConfig() { return $this->Config; }

	public function getController() {
		$Router = new Router;

		$Xml = new \DOMDocument;
		$Xml->load(__DIR__ . '/../../App/' . $this->name . '/Config/routes.xml');

		/** @var \DOMElement[] $Route_a */
		$Route_a = $Xml->getElementsByTagName('route');

		// Pour chaque route dans routes.xml
		foreach ($Route_a as $Route) {
			$vars_a = [];

			// Si des variables sont présentes dans l'URL
			if ($Route->hasAttribute('vars')) {
				$vars_a = explode(',', $Route->getAttribute('vars'));
			}

			// On ajoute la route au routeur
			$Router->addRoute(new Route(
				$Route->getAttribute('url'),
				$Route->getAttribute('module'),
				$Route->getAttribute('action'),
				$vars_a));
		}

		try {
			// On récupère la route correspondante à l'URL
			$matchedRoute = $Router->getRoute($this->Http_request->getRequestURI());
		} catch (\RuntimeException $e) {
			if ($e->getCode() == Router::NO_ROUTE) {
				// Si aucune route ne correspond, alors la page demandée n'existe pas
				$this->Http_response->redirect404();
			}
		}

		// On ajoute les variables de l'URL au tableau $_GET
		/** @var Route $matchedRoute */
		$_GET = array_merge($_GET, $matchedRoute->getVars_a());

		// On instancie le contrôleur
		$controllerClass = 'App\\' . $this->name . '\\Modules\\' . $matchedRoute->getModule() .
			'\\' . $matchedRoute->getModule() . 'Controller';
		return new $controllerClass($this, $matchedRoute->getModule(), $matchedRoute->getAction());
	}
}