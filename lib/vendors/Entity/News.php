<?php
namespace Entity;

use OCFram\Entity;

class News extends Entity {
	protected $id, $auteur, $titre, $contenu, $date_ajout, $date_modif;
	const AUTEUR_INVALIDE = 1, TITRE_INVALIDE = 2, CONTENU_INVALIDE = 3;

	public function isValid() {
		return !(empty($this->auteur) || empty($this->titre) || empty($this->contenu));
	}

	public function setAuteur($auteur) {
		if (!is_string($auteur) || empty($auteur)) {
			$this->erreurs_a[] = self::AUTEUR_INVALIDE;
		}
		$this->auteur = $auteur;
	}
	public function setTitre($titre) {
		if (!is_string($titre) || empty($titre)) {
			$this->erreurs_a[] = self::TITRE_INVALIDE;
		}
		$this->titre = $titre;
	}
	public function setContenu($contenu) {
		if (!is_string($contenu) || empty($contenu)) {
			$this->erreurs_a[] = self::CONTENU_INVALIDE;
		}
		$this->contenu = $contenu;
	}
	public function setDateAjout(\DateTime $date_ajout) { $this->date_ajout = $date_ajout; }
	public function setDateModif(\DateTime $date_modif) { $this->date_modif = $date_modif; }

	public function getAuteur() { return $this->auteur; }
	public function getTitre() { return $this->titre; }
	public function getContenu() { return $this->contenu; }
	public function getAjout() { return $this->date_ajout; }
	public function getModif() { return $this->date_modif; }
}