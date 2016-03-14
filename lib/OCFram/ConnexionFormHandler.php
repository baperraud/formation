<?php

namespace OCFram;


use Entity\User;
use Model\UsersManager;

class ConnexionFormHandler extends FormHandler {
    protected $error_type;
    /** @var  User $User */
    protected $User;

    const PSEUDO_INEXISTANT = 1, PASSWORD_INVALIDE = 2, COMPTE_INACTIF = 3;

    public function process() {

        if ($this->Request->getMethod() == 'POST') {

            $this->setUser($this->Manager->getUsercUsingPseudo($this->Request->getPostData('pseudonym')));

            // Si le pseudo n'existe pas
            if (empty($this->User)) {
                $this->setError_type(self::PSEUDO_INEXISTANT);
            } // Si le mot de passe est incorrect
            elseif (crypt($this->Request->getPostData('password'), $this->User['salt']) !== $this->User['password']) {
                $this->setError_type(self::PASSWORD_INVALIDE);
            } // Si le compte est inactif
            elseif ($this->User['etat'] == UsersManager::COMPTE_INACTIF) {
                $this->setError_type(self::COMPTE_INACTIF);
            }

            $error = $this->getError_type();
            return empty($error);
        }

        return false;
    }

    public function getError_type() { return $this->error_type; }
    public function setError_type($error_type) {
        if (is_numeric($error_type))
            $this->error_type = $error_type;
    }

    public function getUser() { return $this->User; }
    public function setUser($User) { $this->User = $User; }

}