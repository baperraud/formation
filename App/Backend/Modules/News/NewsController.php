<?php
namespace App\Backend\Modules\News;

use \Entity\Comment;
use \Entity\News;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;
use \Model\NewsManager;
use \Model\CommentsManager;
use \OCFram\Application;
use \OCFram\BackController;
use \OCFram\FormHandler;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex() {
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

		$this->App->getUser()->setFlash('La news a bien été supprimée !');

		$this->App->getHttpResponse()->redirect('.');
	}

	public function executeInsert(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Ajout d\'une news');
		$this->processForm($Request);
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
				$this->App->getUser()->setFlash($News->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');
				$this->App->getHttpResponse()->redirect($News->isNew() ? '/admin/' : '/news-' . $News->getId(). '.html');
			}

			$this->Page->addVar('form', $Form->createView());
		}

	public function executeUpdateComment(HTTPRequest $Request) {
		$this->Page->addVar('title', 'Modification d\'un commentaire');

		// On récupère le manager des commentaires
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');

		if ($Request->getMethod() == 'POST') {
			$Comment = new Comment([
				'id' => $Request->getGetData('id'),
				'auteur' => $Request->getPostData('auteur'),
				'contenu' => $Request->getPostData('contenu')
			]);
		} else {
			$Comment = $Manager->getCommentcUsingCommentcId($Request->getGetData('id'));
		}

		$Form_builder = new CommentFormBuilder($Comment);
		$Form_builder->build();

		$Form = $Form_builder->getForm();

		// On récupère le gestionnaire de formulaire
		$Form_handler = new FormHandler($Form, $Manager, $Request);

		if ($Form_handler->process()) {
			$this->App->getUser()->setFlash('Le commentaire a bien été modifié');

			$Comment->setNews($Manager->getNewsIdUsingCommentcId($Comment->getId()));

			$this->App->getHttpResponse()->redirect('/news-' . $Comment->getNews() . '.html');
		}

		$this->Page->addVar('form', $Form->createView());
	}

	public function executeDeleteComment(HTTPRequest $Request) {
		// On récupère le manager des commentaires
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');

		$news_id = $Manager->getNewsIdUsingCommentcId($Request->getGetData('id'));

		$Manager->deleteCommentcUsingId($Request->getGetData('id'));

		$this->App->getUser()->setFlash('Le commentaire a bien été supprimé !');

		$this->App->getHttpResponse()->redirect('/news-' . $news_id . '.html');
	}
}