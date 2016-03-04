<?php
namespace Model;

use \Entity\News;
use \OCFram\Manager;

abstract class NewsManager extends Manager {
	/**
	 * Méthode permettant d'ajouter une news.
	 * @param $News News La news à ajouter
	 * @return void
	 */
	abstract protected function addNewsc(News $News);

	/**
	 * Méthode permettant de modifier une news.
	 * @param $News News La news à ajouter
	 * @return void
	 */
	abstract protected function updateNewsc(News $News);

	/**
	 * Méthode permettant d'enreigster une news.
	 * @param $News News La news à enregistrer
	 * @see self::add()
	 * @see self::modify()
	 * @return void
	 */
	public function save(News $News) {
		if ($News->isValid()) {
			$News->isNew() ? $this->addNewsc($News) : $this->updateNewsc($News);
		} else {
			throw new \RuntimeException('La news doit être validée pour être enregistrée');
		}
	}

	/**
	 * Méthode retournant le nombre de news existantes
	 * @return int Le nombre de news
	 */
	abstract public function countNewsc();

	/**
	 * Méthode retournant une liste de news
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste des news
	 */
	abstract public function getNewscSortByIdDesc_a($debut = -1, $limite = -1);

	/**
	 * Méthode retournant une news précise
	 * @param $id int L'id de la news à récupérer
	 * @return News La news demandée
	 */
	abstract public function getNewscUsingId($id);
}