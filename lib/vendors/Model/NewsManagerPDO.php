<?php
namespace Model;

use \Entity\News;
use OCFram\Session;

class NewsManagerPDO extends NewsManager {
	/**
	 * Méthode permettant de supprimer une news
	 * @param $new_id int L'id de la news à supprimer
	 * @return void
	 */
	public function deleteNewscUsingId($new_id) {
		$delete_query = 'DELETE FROM T_NEW_newsc WHERE NNC_id = :id';
		$delete_query_result = $this->Dao->prepare($delete_query);
		$delete_query_result->bindValue(':id', $new_id, \PDO::PARAM_INT);

		$delete_query_result->execute();
	}

	/**
	 * Méthode permettant de modifier une news.
	 * @param $News News La news à modifier
	 * @return void
	 */
	protected function updateNewsc(News $News) {
		$update_query = '
			UPDATE T_NEW_newsc
			SET NNC_title = :titre, NNC_content = :contenu, NNC_dateupdate = NOW()
			WHERE NNC_id = :id';

		$update_query_result = $this->Dao->prepare($update_query);
		$update_query_result->bindValue(':titre', $News->getTitre());
		$update_query_result->bindValue(':contenu', $News->getContenu());
		$update_query_result->bindValue(':id', $News->getId(), \PDO::PARAM_INT);

		$update_query_result->execute();
	}

	/**
	 * Méthode permettant d'ajouter une news.
	 * @param $News News La news à ajouter
	 * @return void
	 */
	protected function insertNewsc(News $News) {
		$insert_query = '
			INSERT INTO T_NEW_newsc (NNC_fk_NUC, NNC_title, NNC_content, NNC_dateadd, NNC_dateupdate)
			VALUES (:auteur, :titre, :contenu, NOW(), NOW())';

		$auteur_id = Session::getAttribute('id');

		$insert_query_result = $this->Dao->prepare($insert_query);
		$insert_query_result->bindValue(':auteur', $auteur_id, \PDO::PARAM_INT);
		$insert_query_result->bindValue(':titre', $News->getTitre());
		$insert_query_result->bindValue(':contenu', $News->getContenu());

		$insert_query_result->execute();

		$News->setId($this->Dao->lastInsertId());
	}

	/**
	 * Méthode retournant le nombre de news existantes
	 * @return int Le nombre de news
	 */
	public function countNewsc() {
		return $this->Dao->query('SELECT COUNT(*) FROM T_NEW_newsc')->fetchColumn();
	}

	/**
	 * Méthode retournant une liste de news
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste des news
	 */
	public function getNewscSortByIdDesc_a($debut = -1, $limite = -1) {
		$select_query = '
			SELECT NNC_id id, NUC_pseudonym auteur, NNC_title titre, NNC_content contenu, NNC_dateadd Date_ajout, NNC_dateupdate Date_modif
			FROM T_NEW_newsc
			INNER JOIN T_NEW_userc ON NUC_id = NNC_fk_NUC
			ORDER BY NNC_id DESC';

		if ($debut != -1 || $limite != -1) {
			$select_query .= ' LIMIT ' . (int)$limite . ' OFFSET ' . (int)$debut;
		}

		$select_query_result = $this->Dao->query($select_query);
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

		$News_a = $select_query_result->fetchAll();

		/** @var News[] $News_a */
		foreach ($News_a as $News) {
			$News->setDateAjout(new \DateTime($News->getDate_ajout()));
			$News->setDateModif(new \DateTime($News->getDate_modif()));
		}

		$select_query_result->closeCursor();

		return $News_a;
	}

	/**
	 * Méthode retournant une news précise
	 * @param $id int L'id de la news à récupérer
	 * @return News La news demandée
	 */
	public function getNewscUsingId($id) {
		$select_query = '
			SELECT NNC_id id, NUC_pseudonym auteur, NNC_title titre, NNC_content contenu, NNC_dateadd Date_ajout, NNC_dateupdate Date_modif
			FROM T_NEW_newsc
			INNER JOIN T_NEW_userc ON NUC_id = NNC_fk_NUC
			WHERE NNC_id = :id';

		/** @var \PDOStatement $select_query_result */
		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':id', (int)$id, \PDO::PARAM_INT);
		$select_query_result->execute();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

		if ($News = $select_query_result->fetch()) {
			$News->setDateAjout(new \DateTime($News->getDate_ajout()));
			$News->setDateModif(new \DateTime($News->getDate_modif()));

			return $News;
		}

		return NULL;
	}

	/**
	 * Méthode permettant de récupérer la liste des news d'un membre
	 * @param $user_id int L'id du membre dont on veut récupérer les news
	 * @return array
	 */
	public function getNewscUsingUsercIdSortByDateDesc_a($user_id) {
		$select_query = '
			SELECT NNC_id id, NNC_title titre, NNC_content contenu, NNC_dateadd Date_ajout, NNC_dateupdate Date_modif
			FROM T_NEW_newsc
			WHERE NNC_fk_NUC = :user
			ORDER BY Date_ajout DESC';

		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':user', (int)$user_id, \PDO::PARAM_INT);
		$select_query_result->execute();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');

		$News_a = $select_query_result->fetchAll();

		/** @var News[] $News_a */
		foreach ($News_a as $News) {
			$News->setDateAjout(new \DateTime($News->getDate_ajout()));
			$News->setDateModif(new \DateTime($News->getDate_modif()));
		}

		$select_query_result->closeCursor();

		return $News_a;
	}
}