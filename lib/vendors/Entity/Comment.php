<?php
namespace Entity;

use OCFram\Entity;

class Comment extends Entity {
	protected $news, $auteur, $contenu, $Date;
	const AUTEUR_INVALIDE = 1, CONTENU_INVALIDE = 2;

	public function isValid() {
		return !(empty($this->auteur) || empty($this->contenu));
	}

	public function setNews($news) { $this->news = (int)$news; }
	public function setAuteur($auteur) {
		if (!is_string($auteur) || empty($auteur)) {
			$this->erreurs_a[] = self::AUTEUR_INVALIDE;
		}
		$this->auteur = $auteur;
	}
	public function setContenu($contenu) {
		if (!is_string($contenu) || empty($contenu)) {
			$this->erreurs_a[] = self::CONTENU_INVALIDE;
		}
		$this->contenu = $contenu;
	}
	public function setDate(\DateTime $Date) { $this->Date = $Date; }

	public function getNews() { return $this->news; }
	public function getAuteur() { return $this->auteur; }
	public function getContenu() { return $this->contenu; }
	public function getDate() { return $this->Date; }
}