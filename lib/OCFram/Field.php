<?php

namespace OCFram;

abstract class Field {
	// On utilise le trait Hydrator afin que nos objets Field puissent être hydratés
	use Hydrator;

	protected $error_message;
	protected $label;
	protected $name;
	protected $value;

	public function __construct(array $option_a = []) {
		if (!empty($option_a)) {
			$this->hydrate($option_a);
		}
	}

	abstract  public function buildWidget();

	public function isValid() {
		// On écrira cette méthode plus tard.
	}

	public function getLabel() { return $this->label; }
	public function getName() { return $this->name; }
	public function getValue() { return $this->value; }

	public function setLabel($label) {
		if (is_string($label)) {
			$this->label = $label;
		}
	}
	public function setName($name) {
		if (is_string($name)) {
			$this->name = $name;
		}
	}
	public function setValue($value) {
		if (is_string($value)) {
			$this->value = $value;
		}
	}
}