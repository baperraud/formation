<?php
namespace Model;

use \Entity\Comment;
use \OCFram\Manager;

abstract class CommentsManager extends Manager {
	/**
	 * Méthode permettant de sauvegarder un commentaire
	 * @param $Comment Comment Le commentaire à sauvegarder
	 * @return void
	 */
	public function save(Comment $Comment) {
		if ($Comment->isValid()) {
			$Comment->isNew() ? $this->addCommentc($Comment) : $this->updateCommentc($Comment);
		} else {
			throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
		}
	}

	/**
	 * Méthode permettant d'ajouter un commentaire en BDD
	 * @param $Comment Comment Le commentaire à ajouter
	 * @return void
	 */
	abstract protected function addCommentc(Comment $Comment);

	/**
	 * Méthode permettant de modifier un commentaire
	 * @param $Comment Comment Le commentaire à modifier
	 * @return void
	 */
	abstract protected function updateCommentc(Comment $Comment);

	/**
	 * Méthode permettant de récupérer la liste des commentaires d'une news spécifique
	 * @param $news_id int L'id de la news dont on veut récupérer les commentaires
	 * @return array
	 */
	abstract public function getCommentcUsingNewscIdSortByDateDesc_a($news_id);

	/**
	 * Méthode permettant de récupérer un commentaire spécifique
	 * @param $comment_id int L'id du commentaire à récupérer
	 * @return Comment
	 */
	abstract public function getCommentcUsingCommentcId($comment_id);

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
}