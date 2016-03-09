<?php

namespace OCFram;

class IsEmailValidator extends Validator {
	public function isValid($value) {
		if (!empty($value)) {
			return filter_var($value, FILTER_VALIDATE_EMAIL);
		}

		return true;
	}
}