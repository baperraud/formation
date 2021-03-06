<?php
namespace Model;

use Entity\User;

class UsersManagerPDO extends UsersManager {
    /**
     * Méthode retournant le nombre de membres existants
     * @return int Le nombre de membres
     */
    public function countUserc() {
        return $this->Dao->query('SELECT COUNT(*) FROM T_NEW_userc')->fetchColumn();
    }

    /**
     * Méthode retournant une liste de membres
     * @param $debut int Le premier membre à sélectionner
     * @param $limite int Le nombre de membres à sélectionner
     * @return array La liste des membres
     */
    public function getUsercSortByIdDesc_a($debut = -1, $limite = -1) {
        $select_query = '
			SELECT NUC_id id, NUC_date Date, NUC_pseudonym pseudonym, NUC_password password, NUC_salt salt, NUC_email email, NUC_fk_NUY role, NUC_fk_NUE etat
			FROM T_NEW_userc
			ORDER BY NUC_id DESC';

        if ($debut != -1 || $limite != -1) {
            $select_query .= ' LIMIT ' . (int)$limite . ' OFFSET ' . (int)$debut;
        }

        $select_query_result = $this->Dao->query($select_query);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

        $User_a = $select_query_result->fetchAll();

        /** @var User[] $User_a */
        foreach ($User_a as $User) {
            $User->setDate(new \DateTime($User->getDate()));
        }

        $select_query_result->closeCursor();

        return $User_a;
    }

    /**
     * Méthode permettant de récupérer un membre en BDD
     * @param $id int L'id du membre
     * @return User
     */
    public function getUsercUsingId($id) {
        $select_query = '
			SELECT NUC_id id, NUC_fk_NUY role, NUC_fk_NUE etat, NUC_pseudonym pseudonym, NUC_password password, NUC_salt salt, NUC_email email
			FROM T_NEW_userc
			WHERE NUC_id = :id';

        /** @var \PDOStatement $select_query_result */
        $select_query_result = $this->Dao->prepare($select_query);
        $select_query_result->bindValue(':id', $id);
        $select_query_result->execute();

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $select_query_result->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

        if ($User = $select_query_result->fetch()) {
            return $User;
        }

        return NULL;
    }

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
     * Méthode renvoyant un boolean selon l'existance d'un membre en BDD
     * @param $pseudo string Le pseudonyme du membre cherché
     * @return boolean
     */
    public function existsUsercUsingPseudonym($pseudo) {
        $select_query = '
			SELECT NUC_id
			FROM T_NEW_userc
			WHERE NUC_pseudonym = :pseudonym';

        /** @var \PDOStatement $select_query_result */
        $select_query_result = $this->Dao->prepare($select_query);
        $select_query_result->bindValue(':pseudonym', $pseudo);
        $select_query_result->execute();

        return $select_query_result->fetch() ? true : false;
    }

    /**
     * Méthode renvoyant un boolean selon l'existence d'un membre en BDD
     * @param $email string L'email du membre cherché
     * @return boolean
     */
    public function existsUsercUsingEmail($email) {
        $select_query = '
			SELECT NUC_id
			FROM T_NEW_userc
			WHERE NUC_email = :email';

        /** @var \PDOStatement $select_query_result */
        $select_query_result = $this->Dao->prepare($select_query);
        $select_query_result->bindValue(':email', $email);
        $select_query_result->execute();

        return $select_query_result->fetch() ? true : false;
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

        $User->setId($this->Dao->lastInsertId());
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