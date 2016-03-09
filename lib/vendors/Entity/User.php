<?php
namespace Entity;

use OCFram\Entity;

class User extends Entity {
	protected $pseudonym;
	protected $password;
	protected $email;

	const PSEUDO_INVALIDE = 1, PASSWORD_INVALIDE = 2, EMAIL_INVALIDE = 3;

	public function isValid() {
		return !(empty($this->pseudonym) || empty($this->password) || empty($this->email));
	}

	public function setPseudonym($pseudonym) {
		if (!is_string($pseudonym) || empty($pseudonym)) {
			$this->pseudonym = self::PSEUDO_INVALIDE;
		}
		$this->pseudonym = $pseudonym;
	}
	public function setPassword($password) {
		if (!is_string($password) || empty($password)) {
			$this->password = self::PASSWORD_INVALIDE;
		}
		$this->password = $password;
	}
	public function setEmail($email) {
		if (!is_string($email) || empty($email)) {
			$this->email = self::EMAIL_INVALIDE;
		}
		$this->email = $email;
	}

	public function getPseudonym() { return $this->pseudonym; }
	public function getPassword() { return $this->password; }
	public function getEmail() { return $this->email; }
}