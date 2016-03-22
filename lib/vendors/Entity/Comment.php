<?php
namespace Entity;

use OCFram\Entity;
use OCFram\Session;

class Comment extends Entity {
    public static $_prefix_ = 'NCC_';

    protected $news;
    protected $pseudonym;
    protected $email;
    protected $contenu;
    /** @var  \DateTime $Date */
    protected $Date;
    protected $owner_type;

    /** @var  News $News */
    protected $News;
    /** @var  User $User */
    protected $User;

    const PSEUDO_INVALIDE = 1, CONTENU_INVALIDE = 2, EMAIL_INVALIDE = 3;
    const MEMBER = 1, VISITOR = 2;

    public function isValid() {
        // Si l'utilisateur n'est pas connecté
        if (!Session::isAuthenticated()) {
            return !(empty($this->pseudonym) || empty($this->contenu));
        } else {
            return !empty($this->contenu);
        }
    }

    //public function setUser(User $User) { $this->User = $User; }


    public function setNews($news) { $this->news = (int)$news; }
    public function setPseudonym($pseudonym) {
        if (!is_string($pseudonym) || empty($pseudonym)) {
            $this->erreur_a[] = self::PSEUDO_INVALIDE;
        }
        $this->pseudonym = $pseudonym;
    }
    public function setContenu($contenu) {
        if (!is_string($contenu) || empty($contenu)) {
            $this->erreur_a[] = self::CONTENU_INVALIDE;
        }
        $this->contenu = $contenu;
    }
    public function setEmail($email) {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->erreur_a[] = self::EMAIL_INVALIDE;
        }
        $this->email = $email;
    }
    public function setDate(\DateTime $Date) { $this->Date = $Date; }
    public function setOwner_type($owner_type) { $this->owner_type = (int)$owner_type; }

    public function getNews() { return $this->news; }
    public function getPseudonym() { return $this->pseudonym; }
    public function getContenu() { return $this->contenu; }
    public function getDate() { return $this->Date; }
    public function getEmail() { return $this->email; }
    public function getOwner_type() { return $this->owner_type; }
}