<?php
namespace Model;

use \OCFram\Manager;

abstract class UsersManager extends Manager {
	/**
	 * Méthode permettant de vérifier si un utilisateur existe avec les
	 * identifiants fournis
	 * @param $pseudo Le pseudonyme de l'utilisateur
	 * @param $password Le mot de passe de l'utilisateur
	 * @return bool
	 */
	abstract public function existsUsercUsingPseudoAndPassword($pseudo, $password);
}