<?php
namespace App\Backend;

use OCFram\Application;
use OCFram\Session;

trait GenericActionHandler {
    protected $title;
    protected $connection_required;
    protected $admin_required;
    protected $ajax_required;
    protected $menu_a = [];

    public function runActionHandler() {
        if (isset($this->title)) $this->addTitle();

        /*---------------------------------------*/
        /* Gestion des redirections de connexion */
        /*---------------------------------------*/
        if ($this->connection_required || $this->admin_required)
            $this->checkAndRedirectToLogin();
        if ($this->ajax_required)
            $this->checkIfAjaxElse404();

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
            /** @noinspection PhpUndefinedFieldInspection */
            $this->App->getHttpResponse()->redirect(Application::getRoute('Frontend', 'User', 'index'));
        } // S'il n'a pas les droits admin qui sont requis
        elseif ($this->admin_required && !Session::isAdmin()) {
            Session::setFlash('Vous devez être connecté en tant qu\'admin pour accéder à cette page');
            /** @noinspection PhpUndefinedFieldInspection */
            $this->App->getHttpResponse()->redirect(Application::getRoute('Frontend', 'News', 'index'));
        }
    }

    /**
     * Méthode permettant d'ajouter un titre à la page
     * @return void
     */
    protected function addTitle() {
        if (!empty($this->title) && is_string($this->title))
            /** @noinspection PhpUndefinedMethodInspection */
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

        $this->menu_a['Zone Admin'] = Application::getRoute('Backend', 'News', 'index');
        $this->menu_a['Ajouter une news'] = Application::getRoute('Frontend', 'News', 'insert');
        $this->menu_a['Mon profil'] = Application::getRoute('Frontend', 'User', 'show', array(Session::getAttribute('id')));
        $this->menu_a['Se déconnecter'] = Application::getRoute('Frontend', 'User', 'logout');

        $this->menu_a = array_merge($this->getMainRoutes(), $this->menu_a);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->getPage()->addVar('menu_a', $this->menu_a);
    }

    /**
     * Méthode permettant de renvoyer une erreur 404 en cas de requête AJAX
     * passée via url
     * @return void
     */
    protected function checkIfAjaxElse404() {
        // Si ce n'est pas une requête AJAX valide
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) !== 'xmlhttprequest') {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->App->getHttpResponse()->redirect404();
        }
    }
}