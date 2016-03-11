<?php

namespace App\Frontend\Modules\User;

use \Entity\Comment;
use \Entity\News;
use \Entity\User;
use \FormBuilder\UserFormBuilder;
use \Model\CommentsManager;
use \Model\NewsManager;
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

			/** @var User $User */
			$User = $Manager->getUsercUsingPseudo($login);

			// Si le pseudonyme existe
			if ($User !== NULL) {
				$hashed_password = crypt($password, $User['salt']);

				// Si le mot de passe saisi est correct
				if ($hashed_password === $User['password']) {
					// Et que le compte est actif
					if ($User['etat'] == UsersManager::COMPTE_ACTIF) {
						// On initialise les variables de session
						Session::setAuthenticated(true);
						Session::setAttribute('admin', (int)$User['role']);
						Session::setAttribute('pseudo', $login);
						Session::setAttribute('id', (int)$User['id']);

						Session::setFlash('Connexion réussie !');

						// On redirige l'utilisateur en fonction de ses droits
						if ($User['role'] == UsersManager::ROLE_ADMIN) {
							$this->App->getHttpResponse()->redirect('/admin/');
						} elseif ($User['role'] == UsersManager::ROLE_USER) {
							$this->App->getHttpResponse()->redirect('/');
						} else {
							throw new \RuntimeException('Role utilisateur non valide');
						}
					} elseif ($User['etat'] == UsersManager::COMPTE_INACTIF) {
						Session::setFlash('Ce compte est inactif.');
					}
				} else {
					Session::setFlash('Le mot de passe est incorrect.');
				}
			} else {
				Session::setFlash('Il n\'existe pas de compte avec le pseudo renseigné.');
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

	/**
	 * Action permettant d'afficher le profil d'un membre
	 * avec les news et commentaires qu'il a postés
	 * @param $Request HTTPRequest La requête de l'utilisateur
	 */
	public function executeShow(HTTPRequest $Request) {

		// On vérifie que le membre existe bien
		/** @var UsersManager $UsersManager */
		$UsersManager = $this->Managers->getManagerOf('Users');
		/** @var User $User */
		$User = $UsersManager->getUsercUsingId($Request->getGetData('id'));
		if (empty($User)) {
			$this->App->getHttpResponse()->redirect404();
		}

		$nombre_caracteres = (int)$this->App->getConfig()->get('nombre_caracteres');


		// On passe le membre à la vue
		$this->Page->addVar('title', 'Profil de ' . $User->getPseudonym());
		$this->Page->addVar('User', $User);

		/*
		 * Récupération des news du membre
		 * */

		/** @var NewsManager $NewsManager */
		$NewsManager = $this->Managers->getManagerOf('News');

		/** @var News[] $News_a */
		$News_a = $NewsManager->getNewscUsingUsercIdSortByDateDesc_a($User['id']);
		$news_url_a = [];

		foreach ($News_a as $News) {
			// On assigne aux news le nombre de caractères max
			if (strlen($News->getContenu()) > $nombre_caracteres) {
				$debut = substr($News->getContenu(), 0, $nombre_caracteres);
				if (strrpos($debut, ' ') === false) {
					$debut .= '...';
				} else {
					$debut = substr($debut, 0, strrpos($debut, ' ')) . '...';
				}
				$News->setContenu($debut);
			}

			// On récupère l'url de la news
			$news_url_a[$News->getId()] = Application::getRoute($this->App->getName(), 'News', 'show', array($News['id']));
		}

		// On envoie la liste des news à la vue ainsi que leur url
		$this->Page->addVar('News_a', $News_a);
		$this->Page->addVar('news_url_a', $news_url_a);

		// On récupère les routes de modification/suppression de news
		// puis on les envoie à la vue
		$news_update_url_a = [];
		$news_delete_url_a = [];

		foreach ($News_a as $News) {
			$news_update_url_a[$News->getId()] = Application::getRoute($this->App->getName(), 'News', 'update', array($News['id']));
			$news_delete_url_a[$News->getId()] = Application::getRoute($this->App->getName(), 'News', 'delete', array($News['id']));
		}

		$this->Page->addVar('news_update_url_a', $news_update_url_a);
		$this->Page->addVar('news_delete_url_a', $news_delete_url_a);

		/*
		 * Récupération des commentaires du membre
		 * */

		/** @var CommentsManager $CommentsManager */
		$CommentsManager = $this->Managers->getManagerOf('Comments');

		/** @var Comment[] $Comment_a */
		$Comment_a = $CommentsManager->getCommentcUsingUsercIdSortByDateDesc_a($User['id']);
		$comment_news_url_a = [];

		foreach ($Comment_a as $Comment) {
			// On récupère l'url de la news du commentaire
			$comment_news_url_a[$Comment->getId()] = Application::getRoute($this->App->getName(), 'News', 'show', array($Comment['news']));
		}

		// On envoie la liste des commentaires à la vue ainsi que l'url de leur news
		$this->Page->addVar('Comment_a', $Comment_a);
		$this->Page->addVar('comment_news_url_a', $comment_news_url_a);

		// On récupère les routes de modification/suppression de commentaire
		// puis on les envoie à la vue
		$comment_update_url_a = [];
		$comment_delete_url_a = [];

		foreach ($Comment_a as $Comment) {
			$comment_update_url_a[$Comment->getId()] = Application::getRoute($this->App->getName(), 'News', 'updateComment', array($Comment['id']));
			$comment_delete_url_a[$Comment->getId()] = Application::getRoute($this->App->getName(), 'News', 'deleteComment', array($Comment['id']));
		}

		$this->Page->addVar('comment_update_url_a', $comment_update_url_a);
		$this->Page->addVar('comment_delete_url_a', $comment_delete_url_a);
	}

	/**
	 * Méthode permettant d'inscrire un nouveau membre
	 * @param $Request HTTPRequest La requête de l'utilisateur
	 */
	public function executeSignup(HTTPRequest $Request) {
		// Si l'utilisateur est connecté
		if (Session::isAuthenticated()) {
			Session::setFlash('Vous êtes déjà connecté !');
			$this->App->getHttpResponse()->redirect('.');
		}

		/** @var UsersManager $UsersManager */
		// On récupère le manager des utilisateurs
		$UsersManager = $this->Managers->getManagerOf('Users');

		// Si le formulaire a été envoyé
		if ($Request->getMethod() == 'POST') {
			$User = new User([
				'pseudonym' => $Request->getPostData('pseudonym'),
				'password' => $Request->getPostData('password'),
				'password_confirmation' => $Request->getPostData('password_confirmation'),
				'email' => $Request->getPostData('email'),
				'is_new' => true,
			]);
		} else {
			$User = new User;
		}

		$Form_builder = new UserFormBuilder($User);
		$Form_builder->build();

		$Form = $Form_builder->getForm();

		// On récupère le gestionnaire de formulaire
		$Form_handler = new FormHandler($Form, $UsersManager, $Request);

		if ($Form_handler->process()) {
			Session::setFlash('Votre compte a bien été créé !');

			// On connecte automatiquement le membre puis l'on redirige vers son profil
			Session::setAuthenticated(true);
			Session::setAttribute('pseudo', $User->getPseudonym());
			Session::setAttribute('id', (int)$User->getId());

			Session::setFlash('Inscription réussie !');


			//$login_route = Application::getRoute('Frontend', 'User', 'index');
			//$this->App->getHttpResponse()->redirect($login_route);
			$this->App->getHttpResponse()->redirect('/');
		}

		$this->Page->addVar('User', $User);
		// On passe le formulaire généré à la vue
		$this->Page->addVar('form', $Form->createView());
		$this->Page->addVar('title', 'Création d\'un compte');
	}

}