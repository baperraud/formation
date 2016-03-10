<?php

// TODO: Implémenter un Validator qui vérifie que les 2 mdp sont identiques
// TODO: Implémenter un Validator qui vérifie que le login/le mail n'est pas déjà pris

namespace OCFram;

abstract class Validator {
	protected $error_message;

	public function __construct($error_message) {
		$this->setError_message($error_message);
	}

	abstract public function isValid($value);

	public function setError_message($error_message) {
		if (is_string($error_message)) {
			$this->error_message = $error_message;
		}
	}

	public function getError_message() { return $this->error_message; }
}