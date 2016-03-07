<?php
namespace App\Backend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {
	public function executeIndex(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Connexion');

		// Si le formulaire a été envoyé
		if ($Request->postExists('login')) {
			$login = $Request->getPostData('login');
			$password = $Request->getPostData('password');

			// Si les informations d'identification sont correctes
			if ($login == $this->App->getConfig()->get('login') && $password == $this->App->getConfig()->get('pass')) {
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
			$this->App->getUser()->setAuthenticated(false);
			$this->App->getUser()->setFlash('Vous avez bien été déconnecté !');

			$this->App->getHttpResponse()->redirect('/');
		}
	}
}