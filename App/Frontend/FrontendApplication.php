<?php
namespace App\Frontend;

use \OCFram\Application;
use OCFram\BackController;

class FrontendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Frontend';
	}

	public function run() {
		// Obtention et exécution du contrôleur
		/** @var BackController $controller */
		$controller = $this->getController();
		$controller->execute();

		// Assignation de la page créée par le contrôleur à la réponse
		$this->Http_response->setPage($controller->getPage());
		// Envoi de la réponse
		$this->Http_response->send();
	}
}