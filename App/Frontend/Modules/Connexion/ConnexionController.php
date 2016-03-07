<?php

namespace App\Frontend\Modules\Connexion;

use \Model\UsersManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {
	public function executeIndex(HTTPRequest $Request) {

		// On récupère le manager des utilisateurs
		/** @var UsersManager $Manager */
		$Manager = $this->Managers->getManagerOf('Users');

		$this->Page->addVar('title', 'Connexion');

		// Si le formulaire a été envoyé
		if ($Request->postExists('login')) {
			$login = $Request->getPostData('login');
			$password = $Request->getPostData('password');

			// Si les informations d'identification sont correctes
			if ($Manager->existsUsercUsingPseudoAndPassword($login, $password)) {
				$this->App->getUser()->setAuthenticated(true);
				$this->App->getHttpResponse()->redirect('.');
			} else {
				$this->App->getUser()->setFlash('Le pseudo ou le mot de passe est incorrect.');
			}
		}
	}

	public function executeLogout(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Déconnexion');

		// Si l'utilisateur est connecté
		if ($this->App->getUser()->isAuthenticated()) {

			// On détruit les variables de notre session
			session_unset();
			// On détruit notre session
			session_destroy();

			// Puis on relance une session vierge
			session_start();
			$this->App->getUser()->setAuthenticated(false);
			$this->App->getUser()->setFlash('Vous avez bien été déconnecté !');

			$this->App->getHttpResponse()->redirect('/');
		}
	}
}