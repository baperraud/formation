<?php
namespace Model;

use \Entity\News;
use \OCFram\Manager;

abstract class NewsManager extends Manager {
	/**
	 * Méthode retournant une liste de news
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste des news.
	 */
	abstract public function getNewscSortByIdDesc_a($debut = -1, $limite = -1);
	/**
	 * Méthode retournant une news précise
	 * @param $id int L'id de la news à récupérer
	 * @return News La news demandée
	 */
	abstract public function getNewscUsingId($id);
}