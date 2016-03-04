<?php
namespace App\Backend\Modules\News;

use \Entity\Comment;
use \Entity\News;
use \Model\NewsManager;
use \Model\CommentsManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex() {
		// On ajoute une définition pour le titre
		$this->Page->addVar('title', 'Gestion des news');

		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		// On envoie la liste des news et leur nombre à la vue
		$this->Page->addVar('News_a', $Manager->getNewscSortByIdDesc_a());
		$this->Page->addVar('nombre_news', $Manager->countNewsc());
	}

	public function executeDelete(HTTPRequest $Request) {
		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		$Manager->deleteNewscUsingId($Request->getGetData('id'));

		$this->App->getUser()->setFlash('La news a bien été supprimée !');

		$this->App->getHttpResponse()->redirect('.');
	}

	public function executeInsert(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Ajout d\'une news');

		// Si le formulaire a été envoyé
		if ($Request->postExists('auteur')) {
			$this->processForm($Request);
		}
	}

	public function executeUpdate(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Modification d\'une news');

		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		// Si le formulaire a été envoyé
		if ($Request->postExists('auteur')) {
			$this->processForm($Request);
		} else {
			$this->Page->addVar('News', $Manager->getNewscUsingId($Request->getGetData('id')));
		}
	}

	public function processForm(HTTPRequest $Request) {
		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		$News = new News([
			'auteur' => $Request->getPostData('auteur'),
			'titre' => $Request->getPostData('titre'),
			'contenu' => $Request->getPostData('contenu')
		]);

		// L'identifiant de la news est transmis si l'on veut la modifier
		if ($Request->postExists('id')) {
			$News->setId($Request->getPostData('id'));
		}

		if ($News->isValid()) {
			$Manager->save($News);

			$this->App->getUser()->setFlash('La news a bien été ' . ($News->isNew() ? 'ajoutée' : 'modifiée') . ' :)');
		} else {
			$this->Page->addVar('erreur_a', $News->getErreur_a());
		}

		$this->Page->addVar('News', $News);
	}

	public function executeUpdateComment(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Modification d\'un commentaire');

		// On récupère le manager des commentaires
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');

		// Si le formulaire a été envoyé
		if ($Request->postExists('pseudo')) {
			$Comment = new Comment([
				'id' => $Request->getGetData('id'),
				'auteur' => $Request->getPostData('pseudo'),
				'contenu' => $Request->getPostData('contenu')
			]);

			if ($Comment->isValid()) {
				$Manager->save($Comment);
				$this->App->getUser()->setFlash('Le commentaire a bien été modifié');
				$this->App->getHttpResponse()->redirect('/news-' . $Request->getPostData('news') . '.html');
			} else {
				$this->Page->addVar('erreur_a', $Comment->getErreur_a());
			}

			$this->Page->addVar('Comment', $Comment);
		} else {
			$this->Page->addVar('Comment', $Manager->getCommentcUsingCommentcId($Request->getGetData('id')));
		}
	}

	public function executeDeleteComment(HTTPRequest $Request) {
		// On récupère le manager des commentaires
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');

		$Manager->deleteCommentcUsingId($Request->getGetData('id'));

		$this->App->getUser()->setFlash('Le commentaire a bien été supprimé !');

		$this->App->getHttpResponse()->redirect('.');
	}
}