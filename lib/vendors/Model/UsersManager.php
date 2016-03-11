<?php
namespace Model;

use \Entity\User;
use \OCFram\Manager;

abstract class UsersManager extends Manager {
	const COMPTE_ACTIF = 1;
	const COMPTE_INACTIF = 2;
	const ROLE_ADMIN = 1;
	const ROLE_USER = 2;
	const SALT_GENERATOR = 'return uniqid(mt_rand(), true);';

	/**
	 * Méthode permettant de récupérer un membre selon son pseudonyme
	 * @param $pseudo string Le pseudonyme du membre
	 * @return User
	 */
	abstract public function getUsercUsingPseudo($pseudo);

	/**
	 * Méthode permettant de récupérer un membre en BDD
	 * @param $id int L'id du membre
	 * @return User
	 */
	abstract public function getUsercUsingId($id);

	/**
	 * Méthode renvoyant un boolean selon l'existence d'un membre en BDD
	 * @param $pseudo string Le pseudonyme du membre cherché
	 * @return boolean
	 */
	abstract public function existsUsercUsingPseudonym($pseudo);

	/**
	 * Méthode renvoyant un boolean selon l'existence d'un membre en BDD
	 * @param $email string L'email du membre cherché
	 * @return boolean
	 */
	abstract public function existsUsercUsingEmail($email);

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
		var_dump($User);
		var_dump($User->isValid());
		var_dump($User->isNew());
		if ($User->isValid()) {
			$User->isNew() ? $this->insertUserc($User) : $this->updateUserc($User);
		} else {
			throw new \RuntimeException('L\'utilisateur doit être validé pour être enregistré');
		}
	}
}