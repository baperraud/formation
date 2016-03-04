<?php

namespace OCFram;

class MaxLengthValidator extends Validator {
	protected $max_length;

	public function __construct($error_message, $max_length) {
		parent::__construct($error_message);

		$this->setMax_length($max_length);
	}

	public function isValid($value) {
		return strlen($value) <= $this->max_length;
	}

	public function setMax_length($max_length) {
		$max_length = (int)$max_length;

		if ($max_length > 0) {
			$this->max_length = $max_length;
		} else {
			throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
		}
	}
}