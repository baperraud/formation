<?php

namespace OCFram;

class SimilarCheckValidator extends Validator {
    /** @var  Field $Field_origin */
    protected $Field_origin;

    public function __construct($error_message, Field $Field_origin) {
        parent::__construct($error_message);
        $this->setField_origin($Field_origin);
    }

    public function getField_origin() { return $this->Field_origin; }
    public function setField_origin(Field $Field_origin) {
        if (!empty($Field_origin)) {
            $this->Field_origin = $Field_origin;
        } else throw new \InvalidArgumentException('Le field passé doit être valide');
    }

    public function isValid($value) {
        return $value === $this->Field_origin->getValue();
    }
}