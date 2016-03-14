<?php
namespace App\Backend\Modules\News;

use App\Backend\GenericActionHandler;
use Entity\News;
use FormBuilder\NewsFormBuilder;
use Model\NewsManager;
use Model\CommentsManager;
use OCFram\Application;
use OCFram\BackController;
use OCFram\FormHandler;
use OCFram\HTTPRequest;
use OCFram\Session;

// TODO: Ajouter les liens vers les auteurs et les news
// TODO: Ajouter le tableau de la liste des membres

class NewsController extends BackController {

	use GenericActionHandler;

	/**
	 * Action permettant d'afficher la liste des news
	 */
	public function executeIndex() {
		/*------------------------*/
		/* Traitements génériques */
		/*------------------------*/
		$this->title = 'Gestion des news';
		$this->admin_required = true;
		$this->runActionHandler();


		/*-------------------------*/
		/* Traitements spécifiques */
		/*-------------------------*/
		/**
		 * @var NewsManager $NewsManager *
		 * @var News[] $News_a
		 */
		$NewsManager = $this->Managers->getManagerOf('News');
		$News_a = $NewsManager->getNewscSortByIdDesc_a();

		// On envoie la liste des news et leur nombre à la vue
		$this->Page->addVar('News_a', $News_a);
		$this->Page->addVar('nombre_news', $NewsManager->countNewsc());

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

	/**
	 * Action permettant de modifier une news
	 * @param $Request HTTPRequest La requête de l'utilisateur
	 */
	public function executeUpdate(HTTPRequest $Request) {
		/*------------------------*/
		/* Traitements génériques */
		/*------------------------*/
		$this->title = 'Modification d\'une news';
		$this->admin_required = true;
		$this->runActionHandler();


		/*-------------------------*/
		/* Traitements spécifiques */
		/*-------------------------*/
		$this->processForm($Request);
	}

	/**
	 * Action permettant de supprimer une news
	 * @param $Request HTTPRequest La requête de l'utilisateur
	 */
	public function executeDelete(HTTPRequest $Request) {
		/*------------------------*/
		/* Traitements génériques */
		/*------------------------*/
		$this->admin_required = true;
		$this->runActionHandler();


		/*-------------------------*/
		/* Traitements spécifiques */
		/*-------------------------*/
		$news_id = $Request->getGetData('id');

		// On supprime la news
		/** @var NewsManager $NewsManager */
		$NewsManager = $this->Managers->getManagerOf('News');
		$NewsManager->deleteNewscUsingId($news_id);

		// On supprime les commentaires associés
		/** @var CommentsManager $CommentsManager */
		$CommentsManager = $this->Managers->getManagerOf('Comments');
		$CommentsManager->deleteCommentcUsingNewcId($news_id);

		Session::setFlash('La news a bien été supprimée !');

		$this->App->getHttpResponse()->redirect('.');
	}

	protected function processForm(HTTPRequest $Request) {
		/** @var NewsManager $NewsManager */
		$NewsManager = $this->Managers->getManagerOf('News');

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
				$News = $NewsManager->getNewscUsingId($Request->getGetData('id'));
			} else {
				$News = new News;
			}
		}

		$Form_builder = new NewsFormBuilder($News);
		$Form_builder->build();

		$Form = $Form_builder->getForm();

		$Form_handler = new FormHandler($Form, $NewsManager, $Request);

		if ($Form_handler->process()) {
			Session::setFlash('La news a bien été modifiée !');
			$news_url = Application::getRoute('Frontend', 'News', 'show', array($News->getId()));
			$this->App->getHttpResponse()->redirect($news_url);
		}

		$this->Page->addVar('form', $Form->createView());
	}
}