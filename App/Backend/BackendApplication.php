<?php
namespace App\Backend;

use \OCFram\Application;
use \OCFram\BackController;
use \OCFram\Session;

class BackendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Backend';
	}

	public function run() {
		// Si l'utilisateur n'est pas connecté ou n'a pas les droits
		$this->GenericComponentHandler->checkAndredirectToLogin('Vous devez vous connecter en tant qu\'administrateur pour accéder à cette page.');
		$this->GenericComponentHandler->checkAndredirectToLogin('Vous devez vous reconnecter en tant qu\'administrateur pour accéder à cette page.', true);

		// Obtention du contrôleur
		/** @var BackController $controller */
		$controller = $this->getController();

		// Exécution du contrôleur
		$controller->execute();

		// Assignation de la page créée par le contrôleur à la réponse
		$this->Http_response->setPage($controller->getPage());

		// Envoi de la réponse
		$this->Http_response->send();
	}
}