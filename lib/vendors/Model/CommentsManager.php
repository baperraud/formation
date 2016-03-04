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
			$Comment->isNew() ? $this->add($Comment) : $this->modify($Comment);
		} else {
			throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
		}
	}

	/**
	 * Méthode permettant d'ajouter un commentaire en BDD
	 * @param $Comment Comment Le commentaire à ajouter
	 * @return void
	 */
	abstract protected function add(Comment $Comment);

	/**
	 * Méthode permettant de récupérer la liste des commentaires d'une news spécifique
	 * @param $news_id int L'id de la news dont on veut récupérer les commentaires
	 * @return array
	 */
	abstract public function getCommentcUsingNewscIdSortByDateDesc_a($news_id);
}