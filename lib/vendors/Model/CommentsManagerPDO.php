<?php
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	public function add(Comment $Comment) {
		$insert_query = 'INSERT INTO T_NEW_commentc (NCC_fk_NNC, NCC_author, NCC_content, NCC_date) VALUES (:news, :auteur, :content, NOW())';

		/** @var \PDOStatement $insert_query_result */
		$insert_query_result = $this->Dao->prepare($insert_query);
		$insert_query_result->bindValue(':news', (int)$Comment->getNews(), \PDO::PARAM_INT);
		$insert_query_result->bindValue(':auteur', (int)$Comment->getAuteur());
		$insert_query_result->bindValue(':content', (int)$Comment->getContenu());

		$insert_query_result->execute();

		$Comment->setId($this->Dao->lastInsertId());
	}
}