<?php
namespace Model;

use \Entity\User;

class UsersManagerPDO extends UsersManager {
	/**
	 * Méthode permettant de récupérer un membre selon son pseudonyme
	 * @param $pseudo string Le pseudonyme du membre
	 * @return User
	 */
	public function getUsercUsingPseudo($pseudo) {
		$select_query = '
			SELECT NUC_id id, NUC_fk_NUY role, NUC_fk_NUE etat, :pseudonym pseudonym, NUC_password password, NUC_salt salt, NUC_email email
			FROM T_NEW_userc
			WHERE NUC_pseudonym = :pseudonym';

		/** @var \PDOStatement $select_query_result */
		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':pseudonym', $pseudo);
		$select_query_result->execute();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

		if ($User = $select_query_result->fetch()) {
			return $User;
		}

		return NULL;
	}

	/**
	 * Méthode permettant de créer un compte.
	 * @param $User User Le membre à ajouter
	 * @return void
	 */
	protected function insertUserc(User $User) {

		// Génération du salt puis du mot de passe crypté
		$salt = eval(self::SALT_GENERATOR);
		$hashed_password = crypt($User->getPassword(), $salt);

		$insert_query = '
			INSERT INTO T_NEW_userc (NUC_date, NUC_pseudonym, NUC_password, NUC_salt, NUC_email, NUC_fk_NUE, NUC_fk_NUY)
			VALUES (NOW(), :pseudo, :password, :salt, :email, :etat, :role)';

		$insert_query_result = $this->Dao->prepare($insert_query);
		$insert_query_result->bindValue(':pseudo', $User->getPseudonym());
		$insert_query_result->bindValue(':password', $hashed_password);
		$insert_query_result->bindValue(':salt', $salt);
		$insert_query_result->bindValue(':email', $User->getEmail());
		$insert_query_result->bindValue(':etat', self::COMPTE_ACTIF);
		$insert_query_result->bindValue(':role', self::ROLE_USER);
		$insert_query_result->execute();
	}

	/**
	 * Méthode permettant de modifier un membre
	 * @param $User User Le membre à modifier
	 * @return void
	 */
	protected function updateUserc(User $User) {
		// TODO: implement method
	}
}