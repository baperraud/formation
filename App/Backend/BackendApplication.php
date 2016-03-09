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
		// Si l'utilisateur est authentifié en tant qu'admin
		if (Session::isAdmin()) {
			// Obtention du contrôleur
			/** @var BackController $controller */
			$controller = $this->getController();

			// On exécute le contrôleur
			$controller->execute();

			// Assignation de la page créée par le contrôleur à la réponse
			$this->Http_response->setPage($controller->getPage());

			// Envoi de la réponse
			$this->Http_response->send();

		} // Si l'utilisateur n'a pas les droits
		elseif (Session::isAuthenticated()) {
			Session::setFlash('Vous devez vous reconnecter en tant qu\'administrateur pour accéder à cette page.');
			$this->getHttpResponse()->redirect('/');
		} // Sinon, redirection vers la page de connexion
		else {
			Session::setFlash('Vous devez vous connecter en tant qu\'administrateur pour accéder à cette page.');
			$this->getHttpResponse()->redirect('/login.html');
		}
	}
}