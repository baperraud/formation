<?php
namespace Model;

use \OCFram\Manager;

abstract class UsersManager extends Manager {
	const COMPTE_ACTIF = 1;
	const COMPTE_INACTIF = 2;
	const ROLE_ADMIN = 1;
	const ROLE_USER = 2;

	/**
	 * Méthode permettant de récupérer l'id, le rôle et le statut
	 * d'un utilisateur s'il existe avec les identifiants fournis
	 * @param $pseudo string Le pseudonyme de l'utilisateur
	 * @return array | bool
	 */
	abstract public function getUsercUsingPseudo($pseudo);
}