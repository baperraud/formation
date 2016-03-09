<?php
namespace App\Frontend\Modules\News;

use \Entity\Comment;
use \Entity\News;
use \FormBuilder\CommentFormBuilder;
use \Model\CommentsManager;
use \Model\NewsManager;
use \OCFram\Application;
use \OCFram\BackController;
use \OCFram\FormHandler;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex() {
		$nombre_news = $this->App->getConfig()->get('nombre_news');
		$nombre_caracteres = $this->App->getConfig()->get('nombre_caracteres');

		// On ajoute une définition pour le titre
		$this->Page->addVar('title', 'Liste des ' . $nombre_news . ' dernières news');

		// On récupère le manager des news
		$Manager = $this->Managers->getManagerOf('News');

		// Récupération des 5 dernières news
		/** @var News[] $News_a
		 *  @var NewsManager $Manager
		 */
		$News_a = $Manager->getNewscSortByIdDesc_a(0, $nombre_news);
		$news_url_a = [];

		// On assigne aux news 200 caractères max
		foreach ($News_a as $News) {
			if (strlen($News->getContenu()) > $nombre_caracteres) {
				$debut = substr($News->getContenu(), 0, $nombre_caracteres);
				if (strrpos($debut, ' ') === false) {
					$debut .= '...';
				} else {
					$debut = substr($debut, 0, strrpos($debut, ' ')) . '...';
				}
				$News->setContenu($debut);
			}

			// On récupère l'url de la news (show)
			$news_url_a[$News->getId()] = Application::getRoute($this->App->getName(), $this->getModule(), 'show', array($News['id']));
		}

		// On envoie la liste des news à la vue ainsi que leur url
		$this->Page->addVar('News_a', $News_a);
		$this->Page->addVar('news_url_a', $news_url_a);

	}

	public function executeShow(HTTPRequest $Request) {
		/** @var NewsManager $Manager */
		// On récupère le manager des news
		$Manager = $this->Managers->getManagerOf('News');

		// On récupère la news de la requête
		/** @var News $News */
		$News = $Manager->getNewscUsingId($Request->getGetData('id'));

		if (empty($News)) { $this->App->getHttpResponse()->redirect404(); }

		// On envoie la news à la vue
		$this->Page->addVar('title', $News->getTitre());
		$this->Page->addVar('News', $News);

		// On envoie les commentaires associés également
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');
		$this->Page->addVar('Comment_a', $Manager->getCommentcUsingNewscIdSortByDateDesc_a($News->getId()));

		// On envoie le lien pour commenter la news
		$comment_news_url = Application::getRoute($this->App->getName(), $this->getModule(), 'insertComment', array($News['id']));
		$this->Page->addVar('comment_news_url', $comment_news_url);
	}

	public function executeInsertComment(HTTPRequest $Request) {
		/** @var CommentsManager $Manager */
		// On récupère le manager des commentaires
		$Manager = $this->Managers->getManagerOf('Comments');

		// Si le formulaire a été envoyé
		if ($Request->getMethod() == 'POST') {
			$Comment = new Comment([
				'news' => $Request->getGetData('news'),
				'pseudonym' => $Request->getPostData('pseudonym'),
				'email' => $Request->getPostData('email'),
				'contenu' => $Request->getPostData('contenu')
			]);
		} else {
			$Comment = new Comment;
		}

		$Form_builder = new CommentFormBuilder($Comment);
		$Form_builder->build();

		$Form = $Form_builder->getForm();

		// On récupère le gestionnaire de formulaire
		$Form_handler = new FormHandler($Form, $Manager, $Request);

		if ($Form_handler->process()) {
			$this->App->getSession()->setFlash('Le commentaire a bien été ajouté, merci !');
			$this->App->getHttpResponse()->redirect('news-' . $Request->getGetData('news') . '.html');
		}

		$this->Page->addVar('Comment', $Comment);
		// On passe le formulaire généré à la vue
		$this->Page->addVar('form', $Form->createView());
		$this->Page->addVar('title', 'Ajout d\'un commentaire');
	}
}