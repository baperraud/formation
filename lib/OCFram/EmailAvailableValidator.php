<?php

namespace OCFram;

use Model\UsersManager;

class EmailAvailableValidator extends Validator {
	public function isValid($value) {
		if (!empty($value)) {
			$Managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
			/** @var UsersManager $UsersManager */
			$UsersManager = $Managers->getManagerOf('Users');
			return !$UsersManager->existsUsercUsingEmail($value);
		}

		return true;
	}
}