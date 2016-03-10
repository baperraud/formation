<?php

namespace App\Frontend\Modules\User;

use \Entity\User;
use \FormBuilder\UserFormBuilder;
use \Model\UsersManager;
use \OCFram\Application;
use \OCFram\BackController;
use \OCFram\FormHandler;
use \OCFram\HTTPRequest;
use \OCFram\Session;

class UserController extends BackController {

	public function executeIndex(HTTPRequest $Request) {
		// Si l'utilisateur est connecté
		if (Session::isAuthenticated()) {
			Session::setFlash('Vous êtes déjà connecté !');
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
					// On initialise les variables de session
					Session::setAuthenticated(true);
					Session::setAttribute('admin', (int)$user_a['role']);
					Session::setAttribute('pseudo', $login);
					Session::setAttribute('id', (int)$user_a['id']);

					Session::setFlash('Connexion réussie !');

					// On redirige l'utilisateur en fonction de ses droits
					if ($user_a['role'] == UsersManager::ROLE_ADMIN) {
						$this->App->getHttpResponse()->redirect('/admin/');
					} elseif ($user_a['role'] == UsersManager::ROLE_USER) {
						$this->App->getHttpResponse()->redirect('/');
					} else {
						throw new \RuntimeException('Role utilisateur non valide');
					}
				} elseif ($user_a['etat'] == UsersManager::COMPTE_INACTIF) {
					Session::setFlash('Ce compte est inactif.');
				}
			} else {
				Session::setFlash('Le pseudo ou le mot de passe est incorrect.');
			}
		}
	}

	public function executeLogout() {
		$this->Page->addVar('title', 'Déconnexion');

		// Si l'utilisateur est connecté
		if (Session::isAuthenticated()) {

			// On détruit les variables de notre session
			session_unset();
			// On détruit notre session
			session_destroy();

			// Puis on relance une session vierge
			session_start();
			Session::setFlash('Vous avez bien été déconnecté !');

			$this->App->getHttpResponse()->redirect('/');
		}

		Session::setFlash('Vous n\'êtes pas connecté !');
		$this->App->getHttpResponse()->redirect('.');
	}

	public function executeShow() {
		$pseudo = $this->App->getHttpRequest()->getGetData('pseudo');

		$this->Page->addVar('title', 'Profil de ' . $pseudo);
		$this->Page->addVar('pseudo', $pseudo);

	}

	public function executeSignup(HTTPRequest $Request) {
		// Si l'utilisateur est connecté
		if (Session::isAuthenticated()) {
			Session::setFlash('Vous êtes déjà connecté !');
			$this->App->getHttpResponse()->redirect('.');
		}

		/** @var UsersManager $Manager */
		// On récupère le manager des utilisateurs
		$Manager = $this->Managers->getManagerOf('Users');

		// Si le formulaire a été envoyé
		if ($Request->getMethod() == 'POST') {
			$User = new User([
				'pseudonym' => $Request->getPostData('pseudonym'),
				'password' => $Request->getPostData('password'),
				'email' => $Request->getPostData('email')
			]);
		} else {
			$User = new User;
		}

		$Form_builder = new UserFormBuilder($User);
		$Form_builder->build();

		$Form = $Form_builder->getForm();

		// On récupère le gestionnaire de formulaire
		$Form_handler = new FormHandler($Form, $Manager, $Request);

		if ($Form_handler->process()) {
			Session::setFlash('Votre compte a bien été créé !');
			$login_route = Application::getRoute('Frontend', 'User', 'index');
			$this->App->getHttpResponse()->redirect($login_route);
		}

		$this->Page->addVar('User', $User);
		// On passe le formulaire généré à la vue
		$this->Page->addVar('form', $Form->createView());
		$this->Page->addVar('title', 'Création d\'un compte');
	}

}