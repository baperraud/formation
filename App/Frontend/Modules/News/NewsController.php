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

// TODO: Factoriser le code pour les redirections en cas de non connexion (dans HTTPResponse)

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

	/**
	 * Action permettant d'afficher une news et les commentaires associés
	 * @param $Request HTTPRequest La requête de l'utilisateur
	 */
	public function executeShow(HTTPRequest $Request) {

		// On récupère la news de la requête
		/** @var NewsManager $NewsManager */
		$NewsManager = $this->Managers->getManagerOf('News');
		/** @var News $News */
		$News = $NewsManager->getNewscUsingId($Request->getGetData('id'));

		if (empty($News)) {
			$this->App->getHttpResponse()->redirect404();
		}

		// On envoie la news à la vue
		$this->Page->addVar('title', $News->getTitre());
		$this->Page->addVar('News', $News);

		// On récupère l'id de l'auteur de la news
		$user_id = $NewsManager->getUsercIdUsingNewscId($News->getId());
		if (!empty($user_id)) $this->Page->addVar('user_id', $user_id);
		else throw new \RuntimeException('Erreur de récupération de l\'auteur de la news');
		$news_user_url = Application::getRoute('Frontend', 'User', 'show', array($user_id));
		$this->Page->addVar('news_user_url', $news_user_url);

		// On récupère les commentaires associés également
		/** @var CommentsManager $CommentsManager */
		$CommentsManager = $this->Managers->getManagerOf('Comments');
		/** @var Comment[] $Comment_a */
		$Comment_a = $CommentsManager->getCommentcUsingNewscIdSortByDateDesc_a($News->getId());

		$this->Page->addVar('Comment_a', $Comment_a);

		// On récupère les routes de modification/suppression de commentaires
		// ainsi que les id des auteurs des commentaires
		// puis on les envoie à la vue
		$comment_update_url_a = [];
		$comment_delete_url_a = [];
		$comment_user_url_a = [];

		foreach ($Comment_a as $Comment) {
			$comment_update_url_a[$Comment->getId()] = Application::getRoute('Frontend', $this->getModule(), 'updateComment', array($Comment['id']));
			$comment_delete_url_a[$Comment->getId()] = Application::getRoute('Frontend', $this->getModule(), 'deleteComment', array($Comment['id']));
			$user_id = $CommentsManager->getUsercIdUsingCommentcId($Comment->getId());
			if (!empty($user_id))
				$comment_user_url_a[$Comment->getId()] = Application::getRoute('Frontend', 'User', 'show', array($user_id));
			else
				$comment_user_url_a[$Comment->getId()] = NULL;
		}

		$this->Page->addVar('comment_update_url_a', $comment_update_url_a);
		$this->Page->addVar('comment_delete_url_a', $comment_delete_url_a);
		$this->Page->addVar('comment_user_url_a', $comment_user_url_a);

		// On envoie le lien pour commenter la news
		$comment_news_url = Application::getRoute($this->App->getName(), $this->getModule(), 'insertComment', array($News['id']));
		$this->Page->addVar('comment_news_url', $comment_news_url);
	}

	public function executeInsertComment(HTTPRequest $Request) {
		/** @var CommentsManager $CommentsManager */
		$CommentsManager = $this->Managers->getManagerOf('Comments');

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
		$Form_handler = new FormHandler($Form, $CommentsManager, $Request);

		if ($Form_handler->process()) {
			Session::setFlash('Le commentaire a bien été ajouté, merci !');

			// On envoie un mail à tous ceux qui ont déjà commenté la news

			$mail = new \PHPMailer();

			$mail->isSMTP();
			$mail->SMTPDebug = 3;
			$mail->Debugoutput = 'html';
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'dreamcenturyfaformation@gmail.com';
			$mail->Password = 'UJ691vWtcdrm';
			$mail->SMTPSecure = 'ssl';
			$mail->Port = 465;

			$mail->setFrom('notifier@dreamcentury.com', 'Notifier');

			// Récupération de tous les mails et pseudos de ceux qui ont commenté la news
			$email_and_pseudo_a = $CommentsManager->getEmailAndPseudoUsingNewscId_a($Comment->getNews());

			foreach ($email_and_pseudo_a as $email_and_pseudo) {
				// On exclue le mail du commentaire en train d'être inséré
				if ($email_and_pseudo['email'] !== $Comment->getEmail())
					// On ajoute un destinataire
					$mail->addAddress($email_and_pseudo['email'], $email_and_pseudo['pseudo']);
			}

			$mail->Subject = 'Notification : New Comment Inserted';

			$comment_news_url = $_SERVER['HTTP_ORIGIN'] . Application::getRoute($this->App->getName(), 'News', 'show', array($Comment['news']));
			$comment_news_url .= '#commentaire-' . $Comment['id'];

			$mail->Body = '<h1>New posted comment</h1>
<b>Alert:</b> A new comment has been posted on a news you previously commented!<br /><br /><a href="' . $comment_news_url . '">Check it now</a>';
			$mail->AltBody = 'A new comment has been posted on a news you previously commented! Check it now here: ' . $comment_news_url;

			// Envoi du mail
			$mail->send();

			$news_url = Application::getRoute('Frontend', 'News', 'show', array($Request->getGetData('news')));
			$this->App->getHttpResponse()->redirect($news_url);
		}

		$this->Page->addVar('Comment', $Comment);
		// On passe le formulaire généré à la vue
		$this->Page->addVar('form', $Form->createView());
		$this->Page->addVar('title', 'Ajout d\'un commentaire');
	}

	public function executeInsert(HTTPRequest $Request) {
		// Redirection si l'utilisateur n'est pas connecté
		$this->App->getGenericComponentHandler()->checkAndredirectToLogin();

		$this->Page->addVar('title', 'Ajout d\'une news');
		$this->processForm($Request, 'insert');
	}

	public function executeUpdate(HTTPRequest $Request) {
		// Redirection si l'utilisateur n'est pas connecté
		$this->App->getGenericComponentHandler()->checkAndredirectToLogin();

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
		// Redirection si l'utilisateur n'est pas connecté
		$this->App->getGenericComponentHandler()->checkAndredirectToLogin();

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
			$news_url = Application::getRoute('Frontend', 'News', 'show', array($News->getId()));
			$this->App->getHttpResponse()->redirect($news_url);
		}

		$this->Page->addVar('form', $Form->createView());
	}

	/**
	 * Action permettant de modifier un commentaire
	 * @param $Request HTTPRequest La requête de l'utilisateur
	 */
	public function executeUpdateComment(HTTPRequest $Request) {
		// Redirection si l'utilisateur n'est pas connecté
		$this->App->getGenericComponentHandler()->checkAndredirectToLogin();

		/**
		 * @var CommentsManager $CommentsManager
		 * @var Comment $Comment
		 */

		// On récupère le commentaire en question
		$CommentsManager = $this->Managers->getManagerOf('Comments');
		$Comment = $CommentsManager->getCommentcUsingCommentcId($Request->getGetData('id'));

		/*
		* Si l'utilisateur n'est pas un admin
		 * * */
		if (!Session::isAdmin()) {
			// Si l'utilisateur tente de modifier un commentaire qui ne lui appartient pas
			if ($Comment['owner_type'] == 2 ||
				$Comment['pseudonym'] !== Session::getAttribute('pseudo')
			) {
				Session::setFlash('Vous ne pouvez modifier que vos propres commentaires !');
				$this->App->getHttpResponse()->redirect('.');
			}
		}

		$this->Page->addVar('title', 'Modification d\'un commentaire');

		if ($Request->getMethod() == 'POST') {
			$Comment = new Comment([
				'id' => $Request->getGetData('id'),
				'pseudonym' => $Request->getPostData('pseudonym'),
				'contenu' => $Request->getPostData('contenu')
			]);
			$Comment->setNews($CommentsManager->getNewsIdUsingCommentcId($Comment->getId()));
		} else {
			$Comment = $CommentsManager->getCommentcUsingCommentcId($Request->getGetData('id'));
		}

		$Form_builder = new CommentFormBuilder($Comment);
		$Form_builder->build();

		$Form = $Form_builder->getForm();

		// On récupère le gestionnaire de formulaire
		$Form_handler = new FormHandler($Form, $CommentsManager, $Request);

		if ($Form_handler->process()) {
			Session::setFlash('Le commentaire a bien été modifié');

			$news_url = Application::getRoute('Frontend', 'News', 'show', array($Comment->getNews()));
			$this->App->getHttpResponse()->redirect($news_url);
		}

		$this->Page->addVar('form', $Form->createView());
	}

	public function executeDeleteComment(HTTPRequest $Request) {
		// Redirection si l'utilisateur n'est pas connecté
		$this->App->getGenericComponentHandler()->checkAndredirectToLogin();

		/**
		 * @var CommentsManager $CommentsManager
		 * @var Comment $Comment
		 */

		// On récupère le commentaire en question
		$CommentsManager = $this->Managers->getManagerOf('Comments');
		$Comment = $CommentsManager->getCommentcUsingCommentcId($Request->getGetData('id'));

		/*
		* Si l'utilisateur n'est pas un admin
		* */
		if (!Session::isAdmin()) {
			// Si l'utilisateur tente de modifier un commentaire qui ne lui appartient pas
			if ($Comment['pseudonym'] !== Session::getAttribute('pseudo')) {
				Session::setFlash('Vous ne pouvez supprimer que vos propres commentaires !');
				$this->App->getHttpResponse()->redirect('.');
			}
		}

		// On supprime le commentaire
		$CommentsManager->deleteCommentcUsingId($Request->getGetData('id'));

		Session::setFlash('Le commentaire a bien été supprimé !');

		$news_url = Application::getRoute('Frontend', 'News', 'show', array($Comment->getNews()));
		$this->App->getHttpResponse()->redirect($news_url);
	}
}