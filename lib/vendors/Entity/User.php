<?php
namespace Entity;

use OCFram\Entity;

class User extends Entity {
    protected $Date;
    protected $pseudonym;
    protected $password;
    protected $password_confirmation;
    protected $salt;
    protected $email;
    protected $role;
    protected $etat;

    const PSEUDO_INVALIDE = 1, PASSWORD_INVALIDE = 2, EMAIL_INVALIDE = 3, PASSWORD_CONFIRMATION_INVALIDE = 4;

    public function isValid() {
        return !(empty($this->pseudonym) || empty($this->password) || empty($this->email));
    }

    public function setDate(\DateTime $Date) { $this->Date = $Date; }
    public function setPseudonym($pseudonym) {
        if (!is_string($pseudonym) || empty($pseudonym)) {
            $this->pseudonym = self::PSEUDO_INVALIDE;
        }
        $this->pseudonym = $pseudonym;
    }
    public function setSalt($salt) {
        if (!is_string($salt) || empty($salt)) {
            throw new \InvalidArgumentException('Le salt renseigné est invalide');
        }
        $this->password = $salt;
    }
    public function setPassword($password) {
        if (!is_string($password) || empty($password)) {
            $this->password = self::PASSWORD_INVALIDE;
        }
        $this->password = $password;
    }
    public function setPassword_confirmation($password_confirmation) {
        if (!is_string($password_confirmation) || empty($password_confirmation)) {
            $this->password_confirmation = self::PASSWORD_CONFIRMATION_INVALIDE;
        }
        $this->password_confirmation = $password_confirmation;
    }
    public function setEmail($email) {
        if (!is_string($email) || empty($email)) {
            $this->email = self::EMAIL_INVALIDE;
        }
        $this->email = $email;
    }
    public function setRole($role) {
        if (!is_numeric($role) || empty($role)) {
            throw new \InvalidArgumentException('Le role renseigné est invalide');
        }
        $this->role = $role;
    }
    public function setEtat($etat) {
        if (!is_numeric($etat) || empty($etat)) {
            throw new \InvalidArgumentException('L\'état renseigné est invalide');
        }
        $this->etat = $etat;
    }

    public function getDate() { return $this->Date; }
    public function getPseudonym() { return $this->pseudonym; }
    public function getPassword() { return $this->password; }
    public function getPassword_confirmation() { return $this->password_confirmation; }
    public function getSalt() { return $this->salt; }
    public function getEmail() { return $this->email; }
    public function getRole() { return (int)$this->role; }
    public function getEtat() { return (int)$this->etat; }
}