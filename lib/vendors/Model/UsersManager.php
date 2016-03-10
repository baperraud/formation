<?php
namespace Model;

use \Entity\User;
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

	/**
	 * Méthode permettant de créer un compte.
	 * @param $User User Le membre à ajouter
	 * @return void
	 */
	abstract protected function insertUserc(User $User);

	/**
	 * Méthode permettant de modifier un membre
	 * @param $User User Le membre à modifier
	 * @return void
	 */
	abstract protected function updateUserc(User $User);

	/**
	 * Méthode permettant d'enregistrer un membre
	 * @param $User User Le membre à enregistrer
	 * @see self::insertUserc(User $User)
	 * @see self::updateUserc(User $User)
	 * @return void
	 */
	public function save(User $User) {
		if ($User->isValid()) {
			$User->isNew() ? $this->insertUserc($User) : $this->updateUserc($User);
		} else {
			throw new \RuntimeException('L\'utilisateur doit être validé pour être enregistré');
		}
	}
}