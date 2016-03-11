<?php

namespace OCFram;

use Model\UsersManager;

class PseudoAvailableValidator extends Validator {
	public function isValid($value) {
		if (!empty($value)) {
			$Managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
			/** @var UsersManager $UsersManager */
			$UsersManager = $Managers->getManagerOf('Users');
			return !$UsersManager->existsUsercUsingPseudonym($value);
		}

		return true;
	}
}