<?php
function getRoute($module, $action, array $var_a = []) {

	$Xml = new \DOMDocument;
	$Xml->load(__DIR__ . '/../../../Config/routes.xml');

	/** @var \DOMElement[] $Route_a */
	$Route_a = $Xml->getElementsByTagName('route');

// Pour chaque route dans routes.xml
	foreach ($Route_a as $Route) {

// Si l'on trouve l'url cherchée
		if ($Route->getAttribute('module') == $module &&
			$Route->getAttribute('action') == $action
		) {

// On récupère l'url correspondante
			$url = $Route->getAttribute('url');

// Si l'url a des variables
			if (!empty($var_a)) {
// On remplace chaque paire de parenthèses
				foreach ($var_a as $var) {
					$url = preg_replace('/\([^)]+\)/', $var, $url, 1);
				}
			}

			return $url;
		}
	}

	throw new \RuntimeException('Aucune route ne correspond à l\'URL !');
}