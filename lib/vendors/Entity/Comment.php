<?php
namespace Entity;

use OCFram\Entity;

class Comment extends Entity {
	protected $news;
	protected $pseudonym;
	protected $email;
	protected $contenu;
	protected $Date;
	const PSEUDO_INVALIDE = 1, CONTENU_INVALIDE = 2, EMAIL_INVALIDE = 3;

	public function isValid() {
		return !(empty($this->pseudonym) || empty($this->contenu));
	}

	public function setNews($news) { $this->news = (int)$news; }
	public function setpseudonym($pseudonym) {
		if (!is_string($pseudonym) || empty($pseudonym)) {
			$this->erreur_a[] = self::PSEUDO_INVALIDE;
		}
		$this->pseudonym = $pseudonym;
	}
	public function setContenu($contenu) {
		if (!is_string($contenu) || empty($contenu)) {
			$this->erreur_a[] = self::CONTENU_INVALIDE;
		}
		$this->contenu = $contenu;
	}
	public function setEmail($email) {
		if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->erreur_a[] = self::EMAIL_INVALIDE;
		}
		$this->email = $email;
	}
	public function setDate(\DateTime $Date) { $this->Date = $Date; }

	public function getNews() { return $this->news; }
	public function getPseudonym() { return $this->pseudonym; }
	public function getContenu() { return $this->contenu; }
	public function getDate() { return $this->Date; }
	public function getEmail() { return $this->email; }
}