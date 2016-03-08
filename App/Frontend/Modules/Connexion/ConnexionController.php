<?php

namespace App\Frontend\Modules\Connexion;

use \Model\UsersManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {

	public function executeIndex(HTTPRequest $Request) {
		// Si l'utilisateur est connecté
		if ($this->App->getUser()->isAuthenticated()) {
			$this->App->getUser()->setFlash('Vous êtes déjà connecté !');
			$this->App->getHttpResponse()->redirect('.');
		}

		// On récupère le manager des utilisateurs
		/** @var UsersManager $Manager */
		$Manager = $this->Managers->getManagerOf('Users');

		$this->Page->addVar('title', 'Connexion');

		// Si le formulaire a été envoyé
		if ($Request->postExists('login')) {
			$login = $Request->getPostData('login');
			$password = $Request->getPostData('password');

			$user_a = $Manager->getUsercUsingPseudo($login);

			$hashed_password = crypt($password, $user_a['salt']);

			// Si les informations d'identification sont correctes
			if ($hashed_password === $user_a['password']) {
				// Et que le compte est actif
				if ($user_a['etat'] == UsersManager::COMPTE_ACTIF) {
					$this->App->getUser()->setAuthenticated(true);
					$this->App->getUser()->setAttribute('admin', (int)$user_a['role']);
					$this->App->getUser()->setAttribute('pseudo', $login);

					$this->App->getUser()->setFlash('Connexion réussie !');

					// On redirige l'utilisateur en fonction de ses droits
					if ($user_a['role'] == UsersManager::ROLE_ADMIN) {
						$this->App->getHttpResponse()->redirect('/admin/');
					} elseif ($user_a['role'] == UsersManager::ROLE_USER) {
						$this->App->getHttpResponse()->redirect('/');
					} else {
						throw new \RuntimeException('Role utilisateur non valide');
					}
				} elseif ($user_a['etat'] == UsersManager::COMPTE_INACTIF) {
					$this->App->getUser()->setFlash('Ce compte est inactif.');
				}
			} else {
				$this->App->getUser()->setFlash('Le pseudo ou le mot de passe est incorrect.');
			}
		}
	}

	public function executeLogout() {
		$this->Page->addVar('title', 'Déconnexion');

		// Si l'utilisateur est connecté
		if ($this->App->getUser()->isAuthenticated()) {

			// On détruit les variables de notre session
			session_unset();
			// On détruit notre session
			session_destroy();

			// Puis on relance une session vierge
			session_start();
			$this->App->getUser()->setFlash('Vous avez bien été déconnecté !');

			$this->App->getHttpResponse()->redirect('/');
		}
	}
}