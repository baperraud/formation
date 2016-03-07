<?php

namespace OCFram;

abstract class Field {
	// On utilise le trait Hydrator afin que nos objets Field puissent être hydratés
	use Hydrator;

	protected $error_message;
	protected $label;
	protected $name;
	protected $value;
	/** @var Validator[] $Validator_a*/
	protected $Validator_a = [];

	public function __construct(array $option_a = []) {
		if (!empty($option_a)) {
			$this->hydrate($option_a);
		}
	}

	abstract  public function buildWidget();

	public function isValid() {
		foreach ($this->Validator_a as $Validator) {
			if (!$Validator->isValid($this->value)) {
				$this->error_message = $Validator->getError_message();
				return false;
			}
		}

		return true;
	}

	public function getLabel() { return $this->label; }
	public function getName() { return $this->name; }
	public function getValue() { return $this->value; }
	public function getValidator_a() { return $this->Validator_a; }

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
	public function setValidator_a(array $Validator_a) {
		foreach ($Validator_a as $Validator) {
			if ($Validator instanceof Validator && !in_array($Validator, $this->Validator_a)) {
				$this->Validator_a[] = $Validator;
			}
		}
	}
}