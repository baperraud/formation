<?php
namespace Entity;

use OCFram\Entity;

class News extends Entity {
    protected $auteur;
    protected $titre;
    protected $contenu;
    /**
     * @var \DateTime $Date_ajout
     * @var \DateTime $Date_modif
     */
    protected $Date_ajout;
    protected $Date_modif;

    const AUTEUR_INVALIDE = 1, TITRE_INVALIDE = 2, CONTENU_INVALIDE = 3;

    public function isValid() {
        return !(empty($this->titre) || empty($this->contenu));
    }

    public function setAuteur($auteur) {
        if (!is_string($auteur) || empty($auteur)) {
            $this->erreur_a[] = self::AUTEUR_INVALIDE;
        }
        $this->auteur = $auteur;
    }
    public function setTitre($titre) {
        if (!is_string($titre) || empty($titre)) {
            $this->erreur_a[] = self::TITRE_INVALIDE;
        }
        $this->titre = $titre;
    }
    public function setContenu($contenu) {
        if (!is_string($contenu) || empty($contenu)) {
            $this->erreur_a[] = self::CONTENU_INVALIDE;
        }
        $this->contenu = $contenu;
    }
    public function setDateAjout(\DateTime $Date_ajout) { $this->Date_ajout = $Date_ajout; }
    public function setDateModif(\DateTime $Date_modif) { $this->Date_modif = $Date_modif; }

    public function getAuteur() { return $this->auteur; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getDate_ajout() { return $this->Date_ajout; }
    public function getDate_modif() { return $this->Date_modif; }
}