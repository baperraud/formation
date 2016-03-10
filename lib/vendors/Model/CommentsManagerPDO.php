<?php
namespace Model;

use \Entity\Comment;
use \OCFram\Session;

class CommentsManagerPDO extends CommentsManager {
	/**
	 * Méthode permettant de supprimer un commentaire
	 * @param $comment_id int L'id du commentaire à supprimer
	 * @return void
	 */
	public function deleteCommentcUsingId($comment_id) {
		$delete_query = 'DELETE FROM T_NEW_commentc WHERE NCC_id = :id';
		$delete_query_result = $this->Dao->prepare($delete_query);
		$delete_query_result->bindValue(':id', $comment_id, \PDO::PARAM_INT);

		$delete_query_result->execute();
	}

	/**
	 * Méthode permettant de supprimer tous les commentaires liés à une news
	 * @param $news_id int L'id de la news à laquelle sont rattachés les commentaires
	 * @return void
	 */
	public function deleteCommentcUsingNewcId($news_id) {
		$delete_query = 'DELETE FROM T_NEW_commentc WHERE NCC_fk_NNC = :id';
		$delete_query_result = $this->Dao->prepare($delete_query);
		$delete_query_result->bindValue(':id', $news_id, \PDO::PARAM_INT);

		$delete_query_result->execute();
	}

	/**
	 * Méthode permettant d'ajouter un commentaire en BDD
	 * @param $Comment Comment Le commentaire à ajouter
	 * @return void
	 */
	protected function addCommentc(Comment $Comment) {
		$insert_query = '
			INSERT INTO T_NEW_commentc (NCC_fk_NNC, NCC_author, NCC_content, NCC_date, NCC_email, NCC_fk_NUC)
			VALUES (:news, :auteur, :content, NOW(), :email, :user)';

		$insert_query_result = $this->Dao->prepare($insert_query);
		$insert_query_result->bindValue(':news', (int)$Comment->getNews(), \PDO::PARAM_INT);
		$insert_query_result->bindValue(':auteur', $Comment->getPseudonym());
		$insert_query_result->bindValue(':content', $Comment->getContenu());
		$insert_query_result->bindValue(':email', $Comment->getEmail());
		$insert_query_result->bindValue(':user', Session::getAttribute('id'));

		$insert_query_result->execute();

		$Comment->setId($this->Dao->lastInsertId());
	}

	/**
	 * Méthode permettant de modifier un commentaire
	 * @param $Comment Comment Le commentaire à modifier
	 * @return void
	 */
	protected function updateCommentc(Comment $Comment) {
		$update_query = '
			UPDATE T_NEW_commentc
			SET NCC_author = :auteur, NCC_content = :contenu
			WHERE NCC_id = :id';

		$update_query_result = $this->Dao->prepare($update_query);
		$update_query_result->bindValue(':auteur', $Comment->getPseudonym());
		$update_query_result->bindValue(':contenu', $Comment->getContenu());
		$update_query_result->bindValue(':id', $Comment->getId(), \PDO::PARAM_INT);

		$update_query_result->execute();
	}

	/**
	 * Méthode permettant de récupérer un commentaire spécifique
	 * @param $comment_id int L'id du commentaire à récupérer
	 * @return Comment
	 */
	public function getCommentcUsingCommentcId($comment_id) {
		$select_query = '
			SELECT NCC_id id, NCC_fk_NNC news, NCC_author pseudonym, NCC_email email, NCC_content contenu, NCC_date Date, 2 owner_type
			FROM T_NEW_commentc
			WHERE NCC_id = :id AND NCC_fk_NUC IS NULL
			UNION
			SELECT NCC_id id, NCC_fk_NNC news, NUC_pseudonym pseudonym, NULL email, NCC_content contenu, NCC_date Date, 1 owner_type
			FROM T_NEW_commentc
			INNER JOIN T_NEW_userc ON NUC_id = NCC_fk_NUC
			WHERE NCC_id = :id';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':id', (int)$comment_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

		return $select_query_result->fetch();
	}

	/**
	 * Méthode permettant de récupérer la liste des commentaires d'une news spécifique
	 * @param $news_id int L'id de la news dont on veut récupérer les commentaires
	 * @return array
	 */
	public function getCommentcUsingNewscIdSortByDateDesc_a($news_id) {
		if (!ctype_digit($news_id)) {
			throw new \InvalidArgumentException('L\'identifiant de la news passée doit être un entier valide');
		}

		$select_query = '
			SELECT NCC_id id, NCC_fk_NNC news, NCC_author pseudonym, NCC_email email, NCC_content contenu, NCC_date Date, 2 owner_type
			FROM T_NEW_commentc
			WHERE NCC_fk_NNC = :news AND NCC_fk_NUC IS NULL
			UNION
			SELECT NCC_id id, NCC_fk_NNC news, NUC_pseudonym pseudonym, NULL email, NCC_content contenu, NCC_date Date, 1 owner_type
			FROM T_NEW_commentc
			INNER JOIN T_NEW_userc ON NUC_id = NCC_fk_NUC
			WHERE NCC_fk_NNC = :news
			ORDER BY date DESC';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':news', (int)$news_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

		$Comment_a = $select_query_result->fetchAll();

		/** @var Comment[] $Comment_a */
		foreach ($Comment_a as $Comment) {
			$Comment->setDate(new \DateTime($Comment->getDate()));
		}

		$select_query_result->closeCursor();

		return $Comment_a;
	}

	/**
	 * Méthode permettant de récupérer la liste des commentaires d'un membre
	 * @param $user_id int L'id du membre dont on veut récupérer les commentaires
	 * @return array
	 */
	public function getCommentcUsingUsercIdSortByDateDesc_a($user_id) {
		$select_query = '
			SELECT NCC_id id, NCC_fk_NNC news, NCC_email email, NCC_content contenu, NCC_date Date
			FROM T_NEW_commentc
			WHERE NCC_fk_NUC = :user
			ORDER BY Date DESC';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':user', (int)$user_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

		$Comment_a = $select_query_result->fetchAll();

		/** @var Comment[] $Comment_a */
		foreach ($Comment_a as $Comment) {
			$Comment->setDate(new \DateTime($Comment->getDate()));
		}

		$select_query_result->closeCursor();

		return $Comment_a;
	}

	/**
	 * Méthode permettant de récupérer l'id de la news d'un commentaire
	 * @param $comment_id int L'id du commentaire
	 * @return int
	 */
	public function getNewsIdUsingCommentcId($comment_id) {
		$select_query = '
			SELECT NCC_fk_NNC
			FROM T_NEW_commentc
			WHERE NCC_id = :id';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':id', (int)$comment_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		return $select_query_result->fetchColumn();
	}

	/**
	 * Méthode permettant de récupérer la liste des mails de commentaires rattachés
	 * à une news spécifique
	 * @param $news_id int L'id de la news dont on veut récupérer les mails de commentaires
	 * @return array
	 */
	public function getEmailUsingNewscId_a($news_id) {
		$select_query = '
			SELECT NCC_email email
			FROM T_NEW_commentc
			WHERE NCC_fk_NNC = :news AND NCC_fk_NUC IS NULL
			UNION DISTINCT
			SELECT NUC_email email
			FROM T_NEW_commentc
			INNER JOIN T_NEW_userc ON NUC_id = NCC_fk_NUC
			WHERE NCC_fk_NNC = :news';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':news', (int)$news_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		$email_a = $select_query_result->fetchAll();

		$select_query_result->closeCursor();

		var_dump($email_a);die();

		return $email_a;
	}
}