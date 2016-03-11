<?php
namespace App\Frontend\Modules\News;

use \Entity\Comment;
use \Entity\News;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;
use \Model\CommentsManager;
use \Model\NewsManager;
use \OCFram\Application;
use \OCFram\BackController;
use \OCFram\FormHandler;
use \OCFram\HTTPRequest;
use \OCFram\Session;

// TODO: Remplacer les url de redirection en utilisant Application::getRoute
// TODO: Factoriser le code pour les redirections en cas de non connexion (dans HTTPResponse)
// TODO: ancre vers commentaire directe

class NewsController extends BackController {
	public function executeIndex() {
		$nombre_news = $this->App->getConfig()->get('nombre_news');
		$nombre_caracteres = (int)$this->App->getConfig()->get('nombre_caracteres');

		// On ajoute une définition pour le titre
		$this->Page->addVar('title', 'Liste des ' . $nombre_news . ' dernières news');

		// On récupère le manager des news
		$Manager = $this->Managers->getManagerOf('News');

		// Récupération des 5 dernières news
		/** @var News[] $News_a
		 * @var NewsManager $Manager
		 */
		$News_a = $Manager->getNewscSortByIdDesc_a(0, $nombre_news);
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

		if (empty($News)) {
			$this->App->getHttpResponse()->redirect404();
		}

		// On envoie la news à la vue
		$this->Page->addVar('title', $News->getTitre());
		$this->Page->addVar('News', $News);

		// On envoie les commentaires associés également
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');
		/** @var Comment[] $Comment_a */
		$Comment_a = $Manager->getCommentcUsingNewscIdSortByDateDesc_a($News->getId());
		$this->Page->addVar('Comment_a', $Comment_a);

		// On récupère les routes de modification/suppression de commentaires
		// puis on les envoie à la vue
		$comment_update_url_a = [];
		$comment_delete_url_a = [];

		foreach ($Comment_a as $Comment) {
			$comment_update_url_a[$Comment->getId()] = Application::getRoute('Backend', $this->getModule(), 'updateComment', array($Comment['id']));
			$comment_delete_url_a[$Comment->getId()] = Application::getRoute('Backend', $this->getModule(), 'deleteComment', array($Comment['id']));
		}

		$this->Page->addVar('comment_update_url_a', $comment_update_url_a);
		$this->Page->addVar('comment_delete_url_a', $comment_delete_url_a);

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
				'is_new' => true,
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
			Session::setFlash('Le commentaire a bien été ajouté, merci !');

			// Récupération de tous les mails de ceux qui ont commenté la news
//			$Manager->getEmailUsingNewscId_a($Comment->getNews());

			// On envoie un mail à tous ceux qui ont déjà commenté la news






			$this->App->getHttpResponse()->redirect('news-' . $Request->getGetData('news') . '.html');
		}

		$this->Page->addVar('Comment', $Comment);
		// On passe le formulaire généré à la vue
		$this->Page->addVar('form', $Form->createView());
		$this->Page->addVar('title', 'Ajout d\'un commentaire');
	}

	public function executeInsert(HTTPRequest $Request) {
		// Si l'utilisateur n'est pas connecté
		if (!Session::isAuthenticated()) {
			Session::setFlash('Vous devez être connecté pour poster une news.');
			$this->App->getHttpResponse()->redirect('.');
		}

		$this->Page->addVar('title', 'Ajout d\'une news');
		$this->processForm($Request, 'insert');
	}

	public function executeUpdate(HTTPRequest $Request) {
		// Si l'utilisateur n'est pas connecté
		if (!Session::isAuthenticated()) {
			Session::setFlash('Vous devez être connecté pour modifier une news.');
			$this->App->getHttpResponse()->redirect('.');
		}

		// On récupère l'id de l'owner de la news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');
		/** @var News $News */
		$News = $Manager->getNewscUsingId($Request->getGetData('id'));

		// Si l'utilisateur tente de modifier une news qui ne lui appartient pas
		if ($News['auteur'] !== Session::getAttribute('pseudo')) {
			Session::setFlash('Vous ne pouvez modifier que vos propres news !');
			$this->App->getHttpResponse()->redirect('.');
		}

		$this->Page->addVar('title', 'Modification d\'une news');
		$this->processForm($Request, 'update');
	}

	public function executeDelete(HTTPRequest $Request) {
		// Si l'utilisateur n'est pas connecté
		if (!Session::isAuthenticated()) {
			Session::setFlash('Vous devez être connecté pour modifier une news.');
			$this->App->getHttpResponse()->redirect('.');
		}

		// On récupère l'id de l'owner de la news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');
		/** @var News $News */
		$News = $Manager->getNewscUsingId($Request->getGetData('id'));

		// Si l'utilisateur tente de supprimer une news qui ne lui appartient pas
		if ($News['auteur'] !== Session::getAttribute('pseudo')) {
			Session::setFlash('Vous ne pouvez supprimer que vos propres news !');
			$this->App->getHttpResponse()->redirect('.');
		}

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

	public function processForm(HTTPRequest $Request, $type) {
		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		if ($Request->getMethod() == 'POST') {
			$News = new News([
				'titre' => $Request->getPostData('titre'),
				'contenu' => $Request->getPostData('contenu')
			]);
			if ($type === 'insert') $News->setIs_new();

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
			Session::setFlash($News->isNew() ? 'La news a bien été ajoutée !' : 'La news a bien été modifiée !');
			$this->App->getHttpResponse()->redirect('/news-' . $News->getId() . '.html');
		}

		$this->Page->addVar('form', $Form->createView());
	}

	public function executeUpdateComment(HTTPRequest $Request) {
		// Si l'utilisateur n'est pas connecté
		if (!Session::isAuthenticated()) {
			Session::setFlash('Vous devez être connecté pour modifier un commentaire.');
			$this->App->getHttpResponse()->redirect('.');
		}

		// On récupère l'id de l'owner du commentaire
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');
		/** @var Comment $Comment */
		$Comment = $Manager->getCommentcUsingCommentcId($Request->getGetData('id'));

		// Si l'utilisateur tente de modifier un commentaire qui ne lui appartient pas
		if ($Comment['owner_type'] == 2 ||
			$Comment['pseudonym'] !== Session::getAttribute('pseudo')
		) {
			Session::setFlash('Vous ne pouvez modifier que vos propres commentaires !');
			$this->App->getHttpResponse()->redirect('.');
		}

		$this->Page->addVar('title', 'Modification d\'un commentaire');

		// On récupère le manager des commentaires
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');

		if ($Request->getMethod() == 'POST') {
			$Comment = new Comment([
				'id' => $Request->getGetData('id'),
				'pseudonym' => $Request->getPostData('pseudonym'),
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
			Session::setFlash('Le commentaire a bien été modifié');

			$Comment->setNews($Manager->getNewsIdUsingCommentcId($Comment->getId()));

			$this->App->getHttpResponse()->redirect('/news-' . $Comment->getNews() . '.html');
		}

		$this->Page->addVar('form', $Form->createView());
	}

	public function executeDeleteComment(HTTPRequest $Request) {
		// Si l'utilisateur n'est pas connecté
		if (!Session::isAuthenticated()) {
			Session::setFlash('Vous devez être connecté pour modifier un commentaire.');
			$this->App->getHttpResponse()->redirect('.');
		}

		// On récupère l'id de l'owner du commentaire
		/** @var CommentsManager $Manager */
		$Manager = $this->Managers->getManagerOf('Comments');
		/** @var Comment $Comment */
		$Comment = $Manager->getCommentcUsingCommentcId($Request->getGetData('id'));

		// Si l'utilisateur tente de modifier un commentaire qui ne lui appartient pas
		if ($Comment['pseudonym'] !== Session::getAttribute('pseudo')) {
			Session::setFlash('Vous ne pouvez supprimer que vos propres commentaires !');
			$this->App->getHttpResponse()->redirect('.');
		}

		// On supprime le commentaire
		$Manager->deleteCommentcUsingId($Request->getGetData('id'));

		Session::setFlash('Le commentaire a bien été supprimé !');

		$this->App->getHttpResponse()->redirect('/news-' . $Comment->getNews() . '.html');
	}
}