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
	
	public function getController() {
		$router = new Router;
		
		$xml = new \DOMDocument;
		$xml->load(__DIR__.'/../../App/' . $this->name . 'Config/routes.xml');
		
		$routes = $xml->getElementsByTagName('route');
		
		// Pour chaque route dans routes.xml
		foreach ($routes as $route) {
			$vars = [];
			
			// Si des variables sont présentes dans l'URL
			if ($route->hasAttribute('vars')) {
				$vars = explode(',', $route->getAttribute('vars'));
			}
			
			// On ajoute la route au routeur
			$routeur->addRoute(new Route(
				$route->getAttribute('url'),
				$route->getAttribute('module'),
				$route->getAttribute('action'),
				$vars));
			
			try {
				// On récupère la route correspondante à l'URL
				$matchedRoute = $router->getRoute($this->httpRequest->requestURI());
			}
			catch (\RuntimeException $e) {
				if ($e->getCode() == Router::NO_ROUTE) {
					// Si aucune route ne correspond, alors la page demandée n'existe passthru
					$this->httpResponse->redirect404();
				}
			}
			
			// On ajoute les variables de l'URL au tableau $_GET
			$_GET = array_merge($_GET, $matchedRoute->vars());
			
			// On instancie le contrôleur
			$controllerClass = 'App\\' . $this->name . '\\Modules\\' . $matchedRoute->module() .
				'\\' . $matchedRoute->module() . 'Controller';
			return new $controllerClass ($this, $matchedRoute->module(), $matchedRoute->action());
		}
	
	}
}