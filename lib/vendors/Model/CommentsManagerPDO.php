<?php
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	public function deleteCommentcUsingId($comment_id) {
		$this->Dao->exec('DELETE FROM T_NEW_commentc WHERE NCC_id = ' . (int)$comment_id);
	}

	public function deleteCommentcUsingNewcId($news_id) {
		$this->Dao->exec('DELETE FROM T_NEW_commentc WHERE NCC_fk_NNC = ' . (int)$news_id);
	}

	protected function addCommentc(Comment $Comment) {
		$insert_query = 'INSERT INTO T_NEW_commentc (NCC_fk_NNC, NCC_author, NCC_content, NCC_date) VALUES (:news, :auteur, :content, NOW())';

		$insert_query_result = $this->Dao->prepare($insert_query);
		$insert_query_result->bindValue(':news', (int)$Comment->getNews(), \PDO::PARAM_INT);
		$insert_query_result->bindValue(':auteur', $Comment->getAuteur());
		$insert_query_result->bindValue(':content', $Comment->getContenu());

		$insert_query_result->execute();

		$Comment->setId($this->Dao->lastInsertId());
	}

	protected function updateCommentc(Comment $Comment) {
		$update_query = 'UPDATE T_NEW_commentc SET NCC_author = :auteur, NCC_content = :contenu WHERE NCC_id = :id';

		$update_query_result = $this->Dao->prepare($update_query);
		$update_query_result->bindValue(':auteur', $Comment->getAuteur());
		$update_query_result->bindValue(':contenu', $Comment->getContenu());
		$update_query_result->bindValue(':id', $Comment->getId(), \PDO::PARAM_INT);

		$update_query_result->execute();
	}

	public function getCommentcUsingCommentcId($comment_id) {
		$select_query = 'SELECT NCC_id id, NCC_fk_NNC news, NCC_author auteur, NCC_content contenu FROM T_NEW_commentc WHERE NCC_id = :id';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':id', (int)$comment_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

		return $select_query_result->fetch();
	}

	public function getCommentcUsingNewscIdSortByDateDesc_a($news_id) {
		if (!ctype_digit($news_id)) {
			throw new \InvalidArgumentException('L\'identifiant de la news passée doit être un entier valide');
		}

		$select_query = 'SELECT NCC_id id, NCC_author auteur, NCC_content contenu, NCC_date Date FROM T_NEW_commentc WHERE NCC_fk_NNC = :news ORDER BY NCC_date DESC';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':news', (int)$news_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

		$Comment_a = $select_query_result->fetchAll();

		/** @var Comment[] $Comment_a */
		foreach ($Comment_a as $Comment) {
			$Comment->setDate(new \DateTime($Comment->getDate()));
		}

		$select_query_result->closeCursor();

		return $Comment_a;
	}

	public function getNewsIdUsingCommentcId($comment_id) {
		$select_query = 'SELECT NCC_fk_NNC FROM T_NEW_commentc WHERE NCC_id = :id';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':id', (int)$comment_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		return $select_query_result->fetchColumn();
	}
}