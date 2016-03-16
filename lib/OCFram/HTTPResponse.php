<?php
namespace OCFram;

// TODO: trouver une solution pour retirer la redondance (menu) qui provient du trait GenericActionHandler

class HTTPResponse extends ApplicationComponent {
    /** @var  Page $Page */
    protected $Page;

    public function addHeader($header) {
        header($header);
    }

    public function redirect($location) {
        header('Location: ' . $location);
        exit;
    }

    public function redirect404() {
        $this->Page = new Page($this->getApp(), '');
        $this->Page->setContentFile(__DIR__ . '/../../Errors/404.html');

        $this->addHeader('HTTP/1.0 404 Not Found');

        /* Construction du menu */

        $menu_a['Accueil'] = Application::getRoute('Frontend', 'News', 'index');

        // Si l'utilisateur est connecté en tant qu'admin
        if (Session::isAdmin()) {
            $menu_a['Zone Admin'] = Application::getRoute('Backend', 'News', 'index');
        }

        // Si l'utilisateur est connecté
        if (Session::isAuthenticated()) {
            $menu_a['Ajouter une news'] = Application::getRoute('Frontend', 'News', 'insert');
            $menu_a['Mon profil'] = Application::getRoute('Frontend', 'User', 'show', array(Session::getAttribute('id')));
            $menu_a['Se déconnecter'] = Application::getRoute('Frontend', 'User', 'logout');
        } // Sinon
        else {
            $menu_a['Se connecter'] = Application::getRoute('Frontend', 'User', 'index');
            $menu_a['S\'inscrire'] = Application::getRoute('Frontend', 'User', 'signup');
        }

        $this->Page->addVar('menu_a', $menu_a);

        $this->send();
    }

    public function send() {
        exit($this->Page->getGeneratedPage());
    }

    public function setPage(Page $Page) { $this->Page = $Page; }

    public function setCookie($name, $value = '', $expire = 0, $path = NULL, $domain = NULL, $secure = false, $http_only = true) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $http_only);
    }
}