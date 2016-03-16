<?php
namespace App\Frontend;

use OCFram\Application;
use OCFram\Session;

trait GenericActionHandler {
    protected $title;
    protected $connection_required;
    protected $deconnection_required;
    protected $admin_required;
    protected $menu_a = [];

    public function runActionHandler() {
        if (isset($this->title)) $this->addTitle();

        /*---------------------------------------*/
        /* Gestion des redirections de connexion */
        /*---------------------------------------*/
        if ($this->connection_required || $this->admin_required)
            $this->checkAndRedirectToLogin();
        if ($this->deconnection_required)
            $this->checkIfAlreadyConnected();

        /*-----------------*/
        /* Gestion du menu */
        /*-----------------*/
        $this->buildMenu();
    }


    /**
     * Méthode permettant de rediriger l'utilisateur vers la page de connexion
     * @return void
     */
    protected function checkAndRedirectToLogin() {

        // Si l'utilisateur n'est pas identifié
        if (!Session::isAuthenticated()) {
            Session::setFlash('Vous devez être connecté pour accéder à cette page');
            $this->App->getHttpResponse()->redirect(Application::getRoute('Frontend', 'User', 'index'));
        } // S'il n'a pas les droits admin qui sont requis
        elseif ($this->admin_required && !Session::isAdmin()) {
            Session::setFlash('Vous devez être connecté en tant qu\'admin pour accéder à cette page');
            $this->App->getHttpResponse()->redirect(Application::getRoute('Frontend', 'News', 'index'));
        }
    }

    /**
     * Méthode permettant de vérifier que l'utilisateur n'est pas connecté
     * @return void
     */
    protected function checkIfAlreadyConnected() {
        // Si l'utilisateur est déjà connecté
        if (Session::isAuthenticated()) {
            Session::setFlash('Vous êtes déjà connecté !');
            $this->App->getHttpResponse()->redirect('.');
        }
    }

    /**
     * Méthode permettant d'ajouter un titre à la page
     * @return void
     */
    protected function addTitle() {
        if (!empty($this->title) && is_string($this->title))
            $this->getPage()->addVar('title', $this->title);
    }

    /**
     * Méthode permettant de récupérer les routes génériques
     * @return array
     */
    protected function getMainRoutes() {
        $main_route_a = [];
        $main_route_a['Accueil'] = Application::getRoute('Frontend', 'News', 'index');

        return $main_route_a;
    }

    /**
     * Méthode permettant de constuire le menu dynamiquement
     * @return void
     */
    protected function buildMenu() {
        // Si l'utilisateur est connecté en tant qu'admin
        if (Session::isAdmin()) {
            $this->menu_a['Zone Admin'] = Application::getRoute('Backend', 'News', 'index');
        }

        // Si l'utilisateur est connecté
        if (Session::isAuthenticated()) {
            $this->menu_a['Ajouter une news'] = Application::getRoute('Frontend', 'News', 'insert');
            $this->menu_a['Mon profil'] = Application::getRoute('Frontend', 'User', 'show', array(Session::getAttribute('id')));
            $this->menu_a['Se déconnecter'] = Application::getRoute('Frontend', 'User', 'logout');
        } // Sinon
        else {
            $this->menu_a['Se connecter'] = Application::getRoute('Frontend', 'User', 'index');
            $this->menu_a['S\'inscrire'] = Application::getRoute('Frontend', 'User', 'signup');
        }

        $this->menu_a = array_merge($this->getMainRoutes(), $this->menu_a);

        $this->getPage()->addVar('menu_a', $this->menu_a);
    }
}