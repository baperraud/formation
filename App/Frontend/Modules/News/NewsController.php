<?php
namespace App\Frontend\Modules\News;

use App\Frontend\GenericActionHandler;
use Entity\Comment;
use Entity\News;
use FormBuilder\CommentFormBuilder;
use FormBuilder\NewsFormBuilder;
use Model\CommentsManager;
use Model\NewsManager;
use OCFram\Application;
use OCFram\BackController;
use OCFram\Field;
use OCFram\FormHandler;
use OCFram\HTTPRequest;
use OCFram\Page;
use OCFram\Session;

class NewsController extends BackController {

    use GenericActionHandler;

    /**
     * Action permettant d'afficher les dernières news
     */
    public function executeIndex() {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $nombre_news = $this->App->getConfig()->get('nombre_news');
        $this->title = 'Liste des ' . $nombre_news . ' dernières news';
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/
        $nombre_caracteres = (int)$this->App->getConfig()->get('nombre_caracteres');

        // Récupération des 5 dernières news
        /**
         * @var NewsManager $NewsManager
         * @var News[] $News_a
         */
        $NewsManager = $this->Managers->getManagerOf('News');
        $News_a = $NewsManager->getNewscSortByIdDesc_a(0, $nombre_news);

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
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/

        // On récupère la news de la requête
        /** @var NewsManager $NewsManager */
        $NewsManager = $this->Managers->getManagerOf('News');

        $News = $NewsManager->getNewscUsingId($Request->getGetData('id'));
        if ($News === null) $this->App->getHttpResponse()->redirect404();
        $this->Page->addVar('News', $News);

        $this->title = $News->getTitre();
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/

        // On récupère l'id de l'auteur de la news
        $user_id = $NewsManager->getUsercIdUsingNewscId($News->getId());
        if (!empty($user_id)) $this->Page->addVar('user_id', $user_id);
        else throw new \RuntimeException('Erreur de récupération de l\'auteur de la news');
        $news_user_url = Application::getRoute('Frontend', 'User', 'show', array($user_id));
        $this->Page->addVar('news_user_url', $news_user_url);

        // On récupère les 15 commentaires les plus récents
        /**
         * @var CommentsManager $CommentsManager
         * @var Comment[] $Comment_a
         */
        $nombre_commentaires = $this->App->getConfig()->get('nombre_commentaires');
        $CommentsManager = $this->Managers->getManagerOf('Comments');
        $Comment_a = $CommentsManager->getCommentcUsingNewscIdSortByIdDesc_a($News->getId(), 0, $nombre_commentaires);
        $this->Page->addVar('nombre_commentaires', $nombre_commentaires);

        $this->Page->addVar('Comment_a', $Comment_a);

        /* On récupère les routes de modification/suppression de commentaires
        ainsi que les id des auteurs des commentaires
        puis on les envoie à la vue */
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
                $comment_user_url_a[$Comment->getId()] = null;
        }

        $this->Page->addVar('comment_update_url_a', $comment_update_url_a);
        $this->Page->addVar('comment_delete_url_a', $comment_delete_url_a);
        $this->Page->addVar('comment_user_url_a', $comment_user_url_a);

        // On envoie les liens pour commenter la news
        $comment_news_url_a = [];
        $comment_news_url_a['html'] = Application::getRoute($this->App->getName(), $this->getModule(), 'insertComment', array($News['id']));
        $comment_news_url_a['json'] = Application::getRoute($this->App->getName(), $this->getModule(), 'insertCommentJson', array($News['id']));
        $this->Page->addVar('comment_news_url_a', $comment_news_url_a);

        /* On envoie le lien pour charger les anciens/nouveaux commentaires
        ainsi que pour obtenir les commentaires supprimés à la volée */
        $json_comments_url_a = [];
        $json_comments_url_a['load'] = Application::getRoute($this->App->getName(), $this->getModule(), 'loadCommentsJson', array($News['id']));
        $json_comments_url_a['deleted'] = Application::getRoute($this->App->getName(), $this->getModule(), 'getDeletedCommentsJson', array($News['id']));
        $this->Page->addVar('json_comments_url_a', $json_comments_url_a);

        // On génère et envoie le formulaire
        $Form_builder = new CommentFormBuilder(new Comment());
        $Form_builder->build();
        $Form = $Form_builder->getForm();
        $this->Page->addVar('Form', $Form);
    }

    /**
     * Action permettant d'insérer un commentaire
     * @param $Request HTTPRequest La requête de l'utilisateur
     */
    public function executeInsertComment(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->title = 'Ajout d\'un commentaire';
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/

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

        $Form_handler = new FormHandler($Form, $CommentsManager, $Request);

        if ($Form_handler->process()) {
            Session::setFlash('Le commentaire a bien été ajouté, merci !');

//            /*------------------------------------------------------*/
//            /* Envoi d'un mail à ceux qui ont déjà commenté la news */
//            /*------------------------------------------------------*/
//
//            $mail = new \PHPMailer();
//
//            $mail->isSMTP();
//            $mail->SMTPDebug = 3;
//            $mail->Debugoutput = 'html';
//            $mail->Host = 'smtp.gmail.com';
//            $mail->SMTPAuth = true;
//            $mail->Username = 'dreamcenturyfaformation@gmail.com';
//            $mail->Password = 'UJ691vWtcdrm';
//            $mail->SMTPSecure = 'ssl';
//            $mail->Port = 465;
//
//            $mail->setFrom('notifier@dreamcentury.com', 'Notifier');
//
//            // Récupération de tous les mails et pseudos de ceux qui ont commenté la news
//            $email_and_pseudo_a = $CommentsManager->getEmailAndPseudoUsingNewscId_a($Comment->getNews());
//
//            foreach ($email_and_pseudo_a as $email_and_pseudo) {
//                // On exclue le mail du commentaire en train d'être inséré
//                if ($email_and_pseudo['email'] !== $Comment->getEmail())
//                    // On ajoute un destinataire
//                    $mail->addAddress($email_and_pseudo['email'], $email_and_pseudo['pseudo']);
//            }
//
//            $mail->Subject = 'Notification : New Comment Inserted';
//
//            $comment_news_url = $_SERVER['HTTP_ORIGIN'] . Application::getRoute($this->App->getName(), 'News', 'show', array($Comment['news']));
//            $comment_news_url .= '#commentaire-' . $Comment['id'];
//
//            $mail->Body = '<h1>New posted comment</h1>
//<b>Alert:</b> A new comment has been posted on a news you previously commented!<br /><br /><a href="' . $comment_news_url . '">Check it now</a>';
//            $mail->AltBody = 'A new comment has been posted on a news you previously commented! Check it now here: ' . $comment_news_url;
//
//            // Envoi du mail
//            $mail->send();

            $news_url = Application::getRoute('Frontend', 'News', 'show', array($Request->getGetData('news')));
            $this->App->getHttpResponse()->redirect($news_url);
        }

        $this->Page->addVar('Comment', $Comment);
        // On passe le formulaire généré à la vue
        $this->Page->addVar('form', $Form->createView());
    }

    /**
     * Action permettant d'insérer un commentaire via JSON
     * @param $Request HTTPRequest La requête AJAX
     */
    public function executeInsertCommentJson(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->ajax_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/
        /** @var CommentsManager $CommentsManager */
        $CommentsManager = $this->Managers->getManagerOf('Comments');

        $Comment = new Comment([
            'is_new' => true,
            'news' => $Request->getGetData('news'),
            'pseudonym' => $Request->getPostData('pseudonym'),
            'email' => $Request->getPostData('email'),
            'contenu' => $Request->getPostData('contenu')
        ]);


        $Form_builder = new CommentFormBuilder($Comment);
        $Form_builder->build();

        $Form = $Form_builder->getForm();

        $Form_handler = new FormHandler($Form, $CommentsManager, $Request);

        if ($Form_handler->process()) {

//            /*------------------------------------------------------*/
//            /* Envoi d'un mail à ceux qui ont déjà commenté la news */
//            /*------------------------------------------------------*/
//
//            $mail = new \PHPMailer();
//
//            $mail->isSMTP();
//            $mail->SMTPDebug = 0;
//            $mail->Debugoutput = 'html';
//            $mail->Host = 'smtp.gmail.com';
//            $mail->SMTPAuth = true;
//            $mail->Username = 'dreamcenturyfaformation@gmail.com';
//            $mail->Password = 'UJ691vWtcdrm';
//            $mail->SMTPSecure = 'ssl';
//            $mail->Port = 465;
//
//            $mail->setFrom('notifier@dreamcentury.com', 'Notifier');
//
//            // Récupération de tous les mails et pseudos de ceux qui ont commenté la news
//            $email_and_pseudo_a = $CommentsManager->getEmailAndPseudoUsingNewscId_a($Comment->getNews());
//
//            foreach ($email_and_pseudo_a as $email_and_pseudo) {
//                // On exclue le mail du commentaire en train d'être inséré
//                if ($email_and_pseudo['email'] !== $Comment->getEmail())
//                    // On ajoute un destinataire
//                    $mail->addAddress($email_and_pseudo['email'], $email_and_pseudo['pseudo']);
//            }
//
//            $mail->Subject = 'Notification : New Comment Inserted';
//
//            $comment_news_url = $_SERVER['HTTP_ORIGIN'] . Application::getRoute($this->App->getName(), 'News', 'show', array($Comment['news']));
//            $comment_news_url .= '#commentaire-' . $Comment['id'];
//
//            $mail->Body = '<h1>New posted comment</h1>
//<b>Alert:</b> A new comment has been posted on a news you previously commented!<br /><br /><a href="' . $comment_news_url . '">Check it now</a>';
//            $mail->AltBody = 'A new comment has been posted on a news you previously commented! Check it now here: ' . $comment_news_url;
//
//            // Envoi du mail
//            $mail->send();
        }

        $error_message_a = [];
        /** @var Field $Field */
        foreach ($Form->getField_a() as $Field) {
            $error_message = $Field->getError_message();
            if (!empty($error_message))
                $error_message_a[] = $Field->getError_message();
        }

        /* Récupération de tous les commentaires récents */
        /** @var Comment[] $Comment_a */
        $Comment_a = $CommentsManager->getCommentcAfterOtherSortByIdDesc_a($Request->getPostData('last_comment'), $Comment['news']);

        /* On récupère les routes de modification/suppression de commentaires
        ainsi que les id des auteurs des commentaires et si il y a droit de
        modification ou suppression */
        $comment_update_url_a =
        $comment_delete_url_a =
        $comment_user_url_a =
        $comment_write_access_a = [];

        foreach ($Comment_a as $Comment) {
            $comment_update_url_a[$Comment['id']] = Application::getRoute('Frontend', $this->getModule(), 'updateComment', array($Comment['id']));
            $comment_delete_url_a[$Comment['id']] = Application::getRoute('Frontend', $this->getModule(), 'deleteComment', array($Comment['id']));
            $user_id = $CommentsManager->getUsercIdUsingCommentcId($Comment->getId());
            $comment_user_url_a[$Comment['id']] = empty($user_id) ? null : Application::getRoute('Frontend', 'User', 'show', array($user_id));
            $comment_write_access_a[$Comment['id']] = (Session::isAdmin()
                || $Comment['pseudonym'] === Session::getAttribute('pseudo'));
        }


        $this->Page->addVar('error_message_a', $error_message_a);


        /* Construction des fieldset des commentaires */
        $Comments_page = new Page($this->App);
        $Comments_page->setContentFile(__DIR__ . '/Views/buildComments.php');
        $Comments_page->addVar('Comment_a', $Comment_a);

        $Comments_page->addVar('comment_update_url_a', $comment_update_url_a);
        $Comments_page->addVar('comment_delete_url_a', $comment_delete_url_a);
        $Comments_page->addVar('comment_user_url_a', $comment_user_url_a);
        $Comments_page->addVar('comment_write_access_a', $comment_write_access_a);


        $comments_html = $Comments_page->getGeneratedSubView();
        $this->Page->addVar('comments_html', $comments_html);
    }

    /**
     * Action permettant de récupérer un jeu de commentaires d'une news via JSON
     * @param $Request HTTPRequest La requête AJAX
     */
    public function executeLoadCommentsJson(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->ajax_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/

        /**
         * @var CommentsManager $CommentsManager
         * @var Comment[] $Comment_a
         */
        $CommentsManager = $this->Managers->getManagerOf('Comments');
        $nombre_commentaires = $this->App->getConfig()->get('nombre_commentaires');
        $news_id = $Request->getGetData('news');

        $type = $Request->getPostData('type');
        $first_comment = $Request->getPostData('first_comment');
        $last_comment = $Request->getPostData('last_comment');

        // On récupère 15 commentaires plus anciens
        if ($type == 'old')
            $Comment_a = $CommentsManager->getCommentcBeforeOtherSortByIdDesc_a($last_comment, $news_id, $nombre_commentaires);
        // On récupère les commentaires entre deux bornes
        elseif ($type == 'range')
            $Comment_a = $CommentsManager->getCommentcWithinRangeSortByIdDesc_a($first_comment, $last_comment, $news_id);
        // On récupère les nouveaux commentaires
        else
            $Comment_a = $CommentsManager->getCommentcAfterOtherSortByIdDesc_a($last_comment, $news_id);

        $this->Page->addVar('Comment_a', $Comment_a);

        /* On récupère les routes de modification/suppression de commentaires
        ainsi que les id des auteurs des commentaires et si il y a droit de
        modification ou suppression */
        $comment_update_url_a = [];
        $comment_delete_url_a = [];
        $comment_user_url_a = [];
        $comment_write_access_a = [];

        foreach ($Comment_a as $Comment) {
            $comment_update_url_a[$Comment['id']] = Application::getRoute('Frontend', $this->getModule(), 'updateComment', array($Comment['id']));
            $comment_delete_url_a[$Comment['id']] = Application::getRoute('Frontend', $this->getModule(), 'deleteComment', array($Comment['id']));
            $user_id = $CommentsManager->getUsercIdUsingCommentcId($Comment->getId());
            $comment_user_url_a[$Comment['id']] = empty($user_id) ? null : Application::getRoute('Frontend', 'User', 'show', array($user_id));
            $comment_write_access_a[$Comment['id']] = (Session::isAdmin()
                || $Comment['pseudonym'] === Session::getAttribute('pseudo'));
        }

        /* Construction des fieldset des commentaires */
        $Comments_page = new Page($this->App);
        $Comments_page->setContentFile(__DIR__ . '/Views/buildComments.php');
        $Comments_page->addVar('Comment_a', $Comment_a);

        $Comments_page->addVar('comment_update_url_a', $comment_update_url_a);
        $Comments_page->addVar('comment_delete_url_a', $comment_delete_url_a);
        $Comments_page->addVar('comment_user_url_a', $comment_user_url_a);
        $Comments_page->addVar('comment_write_access_a', $comment_write_access_a);


        $comments_html = $Comments_page->getGeneratedSubView();
        $comments_count = count($Comment_a);
        $this->Page->addVar('comments_html', $comments_html);
        $this->Page->addVar('comments_count', $comments_count);
    }

    /**
     * Action permettant de récupérer les commentaires ayant été supprimé
     * depuis le chargement de la page via JSON
     * @param $Request HTTPRequest La requête AJAX
     */
    public function executeGetDeletedCommentsJson(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->ajax_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/

        /** @var CommentsManager $CommentsManager */
        $CommentsManager = $this->Managers->getManagerOf('Comments');
        $news_id = $Request->getGetData('news');
        $comment_a = $Request->getPostData('comments');

        // On récupère les commentaires encore existants
        $comment_exists_a = $CommentsManager->getCommentcIdUsingId_a($comment_a, $news_id);

        // On ne garde que ceux qui ont été supprimé
        $comment_delete_a = array_diff($comment_a, $comment_exists_a);
        $this->Page->addVar('comment_a', $comment_delete_a);
    }

    /**
     * Action permettant d'ajouter une news
     * @param $Request HTTPRequest La requête de l'utilisateur
     */
    public function executeInsert(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->title = 'Ajout d\'une news';
        $this->connection_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/
        $this->processForm($Request, 'insert');
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
        $this->connection_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/

        // On récupère l'id de l'owner de la news
        /**
         * @var NewsManager $NewsManager
         * @var News $News
         */
        $NewsManager = $this->Managers->getManagerOf('News');
        $News = $NewsManager->getNewscUsingId($Request->getGetData('id'));

        // Si l'utilisateur tente de modifier une news qui ne lui appartient pas
        if ($News['auteur'] !== Session::getAttribute('pseudo')) {
            Session::setFlash('Vous ne pouvez modifier que vos propres news !');
            $this->App->getHttpResponse()->redirect('.');
        }

        $this->processForm($Request, 'update');
    }

    /**
     * Action permettant de supprimer une news
     * @param $Request HTTPRequest La requête de l'utilisateur
     */
    public function executeDelete(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->connection_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/

        // On récupère l'id de l'owner de la news
        /**
         * @var NewsManager $NewsManager
         * @var News $News
         */
        $NewsManager = $this->Managers->getManagerOf('News');
        $News = $NewsManager->getNewscUsingId($Request->getGetData('id'));

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

    protected function processForm(HTTPRequest $Request, $type) {
        /** @var NewsManager $NewsManager */
        $NewsManager = $this->Managers->getManagerOf('News');

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
                $News = $NewsManager->getNewscUsingId($Request->getGetData('id'));
            } else {
                $News = new News;
            }
        }

        $Form_builder = new NewsFormBuilder($News);
        $Form_builder->build();

        $Form = $Form_builder->getForm();

        // On récupère le gestionnaire de formulaire
        $Form_handler = new FormHandler($Form, $NewsManager, $Request);

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
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->title = 'Modification d\'un commentaire';
        $this->connection_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/
        /**
         * @var CommentsManager $CommentsManager
         * @var Comment $Comment
         */

        // On récupère le commentaire en question
        $CommentsManager = $this->Managers->getManagerOf('Comments');
        $Comment = $CommentsManager->getCommentcUsingCommentcId($Request->getGetData('id'));

        // Si non admin
        if (!Session::isAdmin()) {
            // Si l'utilisateur tente de modifier un commentaire qui ne lui appartient pas
            if ($Comment['owner_type'] == 2 ||
                $Comment['pseudonym'] !== Session::getAttribute('pseudo')
            ) {
                Session::setFlash('Vous ne pouvez modifier que vos propres commentaires !');
                $this->App->getHttpResponse()->redirect('.');
            }
        }

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

        $Form_handler = new FormHandler($Form, $CommentsManager, $Request);

        if ($Form_handler->process()) {
            Session::setFlash('Le commentaire a bien été modifié');

            $news_url = Application::getRoute('Frontend', 'News', 'show', array($Comment->getNews()));
            $this->App->getHttpResponse()->redirect($news_url . '#commentaire-' . $Comment['id']);
        }

        $this->Page->addVar('form', $Form->createView());
    }

    /**
     * Action permettant de supprimer un commentaire
     * @param $Request HTTPRequest La requête de l'utilisateur
     */
    public function executeDeleteComment(HTTPRequest $Request) {
        /*------------------------*/
        /* Traitements génériques */
        /*------------------------*/
        $this->title = 'Suppression d\'un commentaire';
        $this->connection_required = true;
        $this->runActionHandler();


        /*-------------------------*/
        /* Traitements spécifiques */
        /*-------------------------*/
        /**
         * @var CommentsManager $CommentsManager
         * @var Comment $Comment
         */

        // On récupère le commentaire en question
        $CommentsManager = $this->Managers->getManagerOf('Comments');
        $Comment = $CommentsManager->getCommentcUsingCommentcId($Request->getGetData('id'));

        // Si non admin
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