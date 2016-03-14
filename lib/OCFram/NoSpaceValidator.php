<?php

namespace OCFram;

class NoSpaceValidator extends Validator {
    public function isValid($value) {
        return preg_match('/\s/', $value) !== 1;
    }
}