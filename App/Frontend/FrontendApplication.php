<?php
namespace App\Frontend;

use \App\Frontend\Modules\Connexion\ConnexionController;
use \OCFram\Application;
use OCFram\BackController;

class FrontendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Frontend';
	}

//	public function run() {
//		// Obtention et exécution du contrôleur
//		/** @var BackController $controller */
//		$controller = $this->getController();
//		$controller->execute();
//
//		// Assignation de la page créée par le contrôleur à la réponse
//		$this->Http_response->setPage($controller->getPage());
//		// Envoi de la réponse
//		$this->Http_response->send();
//	}

	public function run() {
		// Si l'utilisateur est authentifié
		if ($this->getUser()->isAuthenticated()) {
			// Obtention du contrôleur
			/** @var BackController $controller */
			$controller = $this->getController();
		} else {
			// Sinon, instanciation du contrôleur du module de connexion
			$controller = new ConnexionController($this, 'Connexion', 'index');
		}

		// On exécute le contrôleur
		$controller->execute();

		// Assignation de la page créée par le contrôleur à la réponse
		$this->Http_response->setPage($controller->getPage());

		// Envoi de la réponse
		$this->Http_response->send();
	}



}