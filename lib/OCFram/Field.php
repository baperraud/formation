<?php

namespace OCFram;

abstract class Field {
    // On utilise le trait Hydrator afin que nos objets Field puissent être hydratés
    use Hydrator;

    protected $error_message;
    protected $label;
    protected $id;
    protected $name;
    protected $value;
    protected $required;
    /** @var Validator[] $Validator_a */
    protected $Validator_a = [];

    public function __construct(array $option_a = []) {
        if (!empty($option_a)) {
            $this->hydrate($option_a);
        }
    }

    abstract public function buildWidget();

    /**
     * Méthode permettant de vérifier que le champ est valide
     */
    public function isValid() {
        foreach ($this->Validator_a as $Validator) {
            if (!$Validator->isValid($this->value)) {
                $this->error_message = $Validator->getError_message();
                return false;
            }
        }

        return true;
    }

    public function getId() { return $this->id; }
    public function getError_message() { return $this->error_message; }
    public function getLabel() { return $this->label; }
    public function getName() { return $this->name; }
    public function getValue() { return $this->value; }
    public function getRequired() { return $this->required; }
    public function getValidator_a() { return $this->Validator_a; }

    public function isRequired() {
        return empty($this->required) ? false : $this->required;
    }

    public function setId($id) {
        if (is_string($id)) {
            $this->id = $id;
        }
    }
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
    public function setRequired($required = true) {
        if (is_bool($required)) {
            $this->required = $required;
        } else throw new \InvalidArgumentException('Require doit être de type boolean');
    }
    public function setValidator_a(array $Validator_a) {
        foreach ($Validator_a as $Validator) {
            if ($Validator instanceof Validator && !in_array($Validator, $this->Validator_a)) {
                $this->Validator_a[] = $Validator;
            }
        }
    }
}