<?php
namespace Model;

use \OCFram\Manager;

abstract class NewsManager extends Manager {
	/**
	 * Méthode retournant une liste de news
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste des news.
	 */
	abstract public function getList($debut = -1, $limite = -1);
}