<?php
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	public function getList($debut = -1, $limite = -1) {
		$select_query = 'SELECT NNC_id id, NNC_author auteur, NNC_title titre, NNC_content contenu, NNC_dateadd date_ajout, NNC_dateupdate date_modif FROM T_NEW_newsc ORDER BY NNC_id DESC';

		if ($debut != -1 || $limite != -1) {
			$select_query .= ' LIMIT ' . (int)$limite . ' OFFSET ' . (int)$debut;
		}

		/** @var \PDOStatement $select_query_result */
		$select_query_result = $this->Dao->query($select_query);
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

		$Liste_news_a = $select_query_result->fetchAll();
		/** @var News[] $Liste_news_a */
		foreach ($Liste_news_a as $News) {
			$News->setDateAjout(new \DateTime($News->getDateAjout()));
			$News->setDateModif(new \DateTime($News->getDateModif()));
		}

		$select_query_result->closeCursor();

		return $Liste_news_a;
	}
}