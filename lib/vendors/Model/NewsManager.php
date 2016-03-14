<?php
namespace Model;

use Entity\News;
use OCFram\Manager;

abstract class NewsManager extends Manager {
    /**
     * Méthode permettant d'ajouter une news.
     * @param $News News La news à ajouter
     * @return void
     */
    abstract protected function insertNewsc(News $News);

    /**
     * Méthode permettant de modifier une news.
     * @param $News News La news à modifier
     * @return void
     */
    abstract protected function updateNewsc(News $News);

    /**
     * Méthode permettant d'enregistrer une news
     * @param $News News La news à enregistrer
     * @see self::insertNewsc()
     * @see self::updateNewsc()
     * @return void
     */
    public function save(News $News) {
        if ($News->isValid()) {
            $News->isNew() ? $this->insertNewsc($News) : $this->updateNewsc($News);
        } else {
            throw new \RuntimeException('La news doit être validée pour être enregistrée');
        }
    }

    /**
     * Méthode permettant de supprimer une news
     * @param $new_id int L'id de la news à supprimer
     * @return void
     */
    abstract public function deleteNewscUsingId($new_id);

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

    /**
     * Méthode permettant de récupérer la liste des news d'un membre
     * @param $user_id int L'id du membre dont on veut récupérer les news
     * @return array
     */
    abstract public function getNewscUsingUsercIdSortByDateDesc_a($user_id);

    /**
     * Méthode permettant de récupérer l'id de l'auteur d'une news
     * @param $news_id int L'id de la news considérée
     * @return int Renvoie 0 en cas d'erreur
     */
    abstract public function getUsercIdUsingNewscId($news_id);
}