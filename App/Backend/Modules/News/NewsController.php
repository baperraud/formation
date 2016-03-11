<?php
namespace App\Backend\Modules\News;

use \Entity\News;
use \FormBuilder\NewsFormBuilder;
use \Model\NewsManager;
use \Model\CommentsManager;
use \OCFram\Application;
use \OCFram\BackController;
use \OCFram\FormHandler;
use \OCFram\HTTPRequest;
use \OCFram\Session;

class NewsController extends BackController {
	public function executeIndex() {
		// TODO: Ajouter les liens vers les auteurs et les news
		// TODO: Ajouter le tableau de la liste des membres

		// On ajoute une définition pour le titre
		$this->Page->addVar('title', 'Gestion des news');

		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		/** @var News[] $News_a */
		$News_a = $Manager->getNewscSortByIdDesc_a();

		// On envoie la liste des news et leur nombre à la vue
		$this->Page->addVar('News_a', $News_a);
		$this->Page->addVar('nombre_news', $Manager->countNewsc());

		// On récupère les routes de modification/suppression de news
		// puis on les envoie à la vue
		$news_update_url_a = [];
		$news_delete_url_a = [];

		foreach ($News_a as $News) {
			$news_update_url_a[$News->getId()] = Application::getRoute($this->App->getName(), $this->getModule(), 'update', array($News['id']));
			$news_delete_url_a[$News->getId()] = Application::getRoute($this->App->getName(), $this->getModule(), 'delete', array($News['id']));
		}

		$this->Page->addVar('news_update_url_a', $news_update_url_a);
		$this->Page->addVar('news_delete_url_a', $news_delete_url_a);
	}

	public function executeDelete(HTTPRequest $Request) {
		$news_id = $Request->getGetData('id');

		// On supprime la news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');
		$Manager->deleteNewscUsingId($news_id);
		// On supprime les commentaires associés
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');
		$Manager->deleteCommentcUsingNewcId($news_id);

		Session::setFlash('La news a bien été supprimée !');

		$this->App->getHttpResponse()->redirect('.');
	}

	public function executeUpdate(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Modification d\'une news');
		$this->processForm($Request);
	}

	public function processForm(HTTPRequest $Request) {
		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		if ($Request->getMethod() == 'POST') {
			$News = new News([
				'auteur' => $Request->getPostData('auteur'),
				'titre' => $Request->getPostData('titre'),
				'contenu' => $Request->getPostData('contenu')
			]);

			if ($Request->getExists('id')) {
				$News->setId($Request->getGetData('id'));
			}
		} else {
				// L'identifiant de la news est transmis si on veut la modifier
				if ($Request->getExists('id')) {
					$News = $Manager->getNewscUsingId($Request->getGetData('id'));
				} else {
					$News = new News;
				}
			}

			$Form_builder = new NewsFormBuilder($News);
			$Form_builder->build();

			$Form = $Form_builder->getForm();

			// On récupère le gestionnaire de formulaire
			$Form_handler = new FormHandler($Form, $Manager, $Request);

			if ($Form_handler->process()) {
				Session::setFlash('La news a bien été modifiée !');
				$news_url = Application::getRoute('Frontend', 'News', 'show', array($News->getId()));
				$this->App->getHttpResponse()->redirect($news_url);
			}

			$this->Page->addVar('form', $Form->createView());
		}

	public function executeDeleteComment(HTTPRequest $Request) {
		/** @var CommentsManager $CommentsManager */
		$CommentsManager = $this->Managers->getManagerOf('Comments');

		$news_id = $CommentsManager->getNewsIdUsingCommentcId($Request->getGetData('id'));

		$CommentsManager->deleteCommentcUsingId($Request->getGetData('id'));

		Session::setFlash('Le commentaire a bien été supprimé !');

		$news_url = Application::getRoute('Frontend', 'News', 'show', array($news_id));
		$this->App->getHttpResponse()->redirect($news_url);
	}
}