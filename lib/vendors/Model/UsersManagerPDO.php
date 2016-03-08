<?php
namespace Model;

class UsersManagerPDO extends UsersManager {
	/**
	 * Méthode permettant de récupérer l'id, le rôle et le statut
	 * d'un utilisateur s'il existe avec les identifiants fournis
	 * @param $pseudo string Le pseudonyme de l'utilisateur
	 * @return array | bool
	 */
	public function getUsercUsingPseudo($pseudo) {
		$select_query = '
			SELECT NUC_id id, NUC_fk_NUY role, NUC_fk_NUE etat, NUC_password password, NUC_salt salt
			FROM T_NEW_userc
			WHERE NUC_pseudonym = :pseudonym';

		/** @var \PDOStatement $select_query_result */
		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':pseudonym', $pseudo);
		$select_query_result->execute();

		return $select_query_result->fetch(\PDO::FETCH_ASSOC);
	}
}