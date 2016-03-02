<?php
namespace OCFram;

class Router {
	/** @var Route[] $routes */
	protected $routes = [];

	const NO_ROUTE = 1;

	public function addRoute(Route $route) {
		if (!in_array($route, $this->routes)) {
			$this->routes[] = $route;
		}
	}

	public function getRoute($url) {
		foreach ($this->routes as $route) {
			// Si la route correspond à l'URL
			if (($varsValues = $route->match($url)) !== false) {
				// Si elle a des variables
				if ($route->hasVars()) {
					$varsNames = $route->varsNames();
					$listVars = [];

					// Nouveau tableau clé/valeur
					foreach ($varsValues as $key => $value) {
						// La première valeur contient entièrement la chaîne capturée
						if ($key !== 0) {
							$listVars[$varsNames[$key - 1]] = $value;
						}
					}

					// On assigne ce tableau de variables à la route
					$route->setVars($listVars);
				}

				return $route;
			}
		}

		throw new \RuntimeException('Aucune route ne correspond à l\'URL', self::NO_ROUTE);
	}
}