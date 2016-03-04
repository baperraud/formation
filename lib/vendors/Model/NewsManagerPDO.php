<?php
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	public function countNewsc() {
		return $this->Dao->query('SELECT COUNT(*) FROM T_NEW_newsc')->fetchColumn();
	}

	public function getNewscSortByIdDesc_a($debut = -1, $limite = -1) {
		$select_query = 'SELECT NNC_id id, NNC_author auteur, NNC_title titre, NNC_content contenu, NNC_dateadd Date_ajout, NNC_dateupdate Date_modif FROM T_NEW_newsc ORDER BY NNC_id DESC';

		if ($debut != -1 || $limite != -1) {
			$select_query .= ' LIMIT ' . (int)$limite . ' OFFSET ' . (int)$debut;
		}

		$select_query_result = $this->Dao->query($select_query);
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

		$Liste_news_a = $select_query_result->fetchAll();
		/** @var News[] $Liste_news_a */
		foreach ($Liste_news_a as $News) {
			$News->setDateAjout(new \DateTime($News->getDate_ajout()));
			$News->setDateModif(new \DateTime($News->getDate_modif()));
		}

		$select_query_result->closeCursor();

		return $Liste_news_a;
	}

	public function getNewscUsingId($id) {
		$select_query = 'SELECT NNC_id id, NNC_author auteur, NNC_title titre, NNC_content contenu, NNC_dateadd Date_ajout, NNC_dateupdate Date_modif FROM T_NEW_newsc WHERE NNC_id = :id';

		/** @var \PDOStatement $select_query_result */
		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':id', (int)$id, \PDO::PARAM_INT);
		$select_query_result->execute();

		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

		if ($News = $select_query_result->fetch()) {
			$News->setDateAjout(new \DateTime($News->getDate_ajout()));
			$News->setDateModif(new \DateTime($News->getDate_modif()));

			return $News;
		}

		return NULL;
	}
}