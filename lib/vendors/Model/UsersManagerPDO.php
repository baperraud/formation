<?php
namespace Model;

class UsersManagerPDO extends UsersManager {
	public function existsUsercUsingPseudoAndPassword($pseudo, $password) {
		$select_query = 'SELECT NUC_id id FROM T_NEW_userc WHERE NUC_pseudonym = :pseudonym AND NUC_password = :password';

		/** @var \PDOStatement $select_query_result */
		$select_query_result = $this->Dao->prepare($select_query);
		$select_query_result->bindValue(':pseudonym', $pseudo);
		$select_query_result->bindValue(':password', $password);
		$select_query_result->execute();

		return $select_query_result->fetch();
	}
}