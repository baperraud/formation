<?php
namespace Model;

use Entity\Comment;
use OCFram\Manager;

abstract class CommentsManager extends Manager {
    /**
     * Méthode permettant de sauvegarder un commentaire
     * @param $Comment Comment Le commentaire à sauvegarder
     * @return void
     */
    public function save(Comment $Comment) {
        if ($Comment->isValid()) {
            $Comment->isNew() ? $this->insertCommentc($Comment) : $this->updateCommentc($Comment);
        } else {
            throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
        }
    }

    /**
     * Méthode permettant d'ajouter un commentaire en BDD
     * @param $Comment Comment Le commentaire à ajouter
     * @return void
     */
    abstract protected function insertCommentc(Comment $Comment);

    /**
     * Méthode permettant de modifier un commentaire
     * @param $Comment Comment Le commentaire à modifier
     * @return void
     */
    abstract protected function updateCommentc(Comment $Comment);

    /**
     * Méthode permettant de récupérer la liste des commentaires d'une news spécifique
     * @param $news_id int L'id de la news dont on veut récupérer les commentaires
     * @param $debut int Le premier commentaire à récupérer
     * @param $limite int Le nombre de commentaires à récupérer
     * @return array
     */
    abstract public function getCommentcUsingNewscIdSortByIdDesc_a($news_id, $debut = -1, $limite = -1);

    /**
     * Méthode permettant de récupérer la liste des mails et pseudos
     * de commentaires rattachés à une news spécifique
     * @param $news_id int L'id de la news considérée
     * @return array
     */
    abstract public function getEmailAndPseudoUsingNewscId_a($news_id);

    /**
     * Méthode permettant de récupérer un commentaire spécifique
     * @param $comment_id int L'id du commentaire à récupérer
     * @return Comment
     */
    abstract public function getCommentcUsingCommentcId($comment_id);

    /**
     * Méthode permettant de récupérer la liste des commentaires d'un membre
     * @param $user_id int L'id du membre dont on veut récupérer les commentaires
     * @return array
     */
    abstract public function getCommentcUsingUsercIdSortByDateDesc_a($user_id);

    /**
     * Méthode retournant les commentaires plus récents qu'un autre d'une news
     * @param $comment_id int Le commentaire à partir duquel chercher
     * @param $news_id int La news dans laquelle chercher
     * @return array La liste des commentaires
     */
    abstract public function getCommentcAfterOtherSortByIdDesc_a($comment_id, $news_id);

    /**
     * Méthode retournant les commentaires plus anciens qu'un autre d'une news
     * @param $comment_id int Le commentaire à partir duquel chercher
     * @param $news_id int La news dans laquelle chercher
     * @param $limite int Le nombre de commentaires à récupérer
     * @return array La liste des commentaires
     */
    abstract public function getCommentcBeforeOtherSortByIdDesc_a($comment_id, $news_id, $limite = -1);

    /**
     * Méthode récupérant une liste d'ids de commentaires s'ils existent
     * @param $comment_a array Les ids des commentaires
     * @param $news_id int La news dans laquelle chercher
     * @return array La liste des ids des commentaires trouvés
     */
    abstract public function getCommentcIdUsingId_a($comment_a, $news_id);

    /**
     * Méthode permettant de supprimer un commentaire
     * @param $comment_id int L'id du commentaire à supprimer
     * @return void
     */
    abstract public function deleteCommentcUsingId($comment_id);

    /**
     * Méthode permettant de supprimer tous les commentaires liés à une news
     * @param $news_id int L'id de la news à laquelle sont rattachés les commentaires
     * @return void
     */
    abstract public function deleteCommentcUsingNewcId($news_id);

    /**
     * Méthode permettant de récupérer l'id de la news d'un commentaire
     * @param $comment_id int L'id du commentaire
     * @return int
     */
    abstract public function getNewsIdUsingCommentcId($comment_id);

    /**
     * Méthode permettant de récupérer l'id de l'auteur d'un commentaire
     * @param $comment_id int L'id du commentaire
     * @return int Renvoie 0 si l'auteur n'est pas inscrit
     */
    abstract public function getUsercIdUsingCommentcId($comment_id);
}