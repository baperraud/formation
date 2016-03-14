<?php
namespace App\Backend;

use OCFram\Application;
use OCFram\BackController;

class BackendApplication extends Application {

    public function __construct() {
        parent::__construct();
        $this->name = 'Backend';
    }

    public function run() {
        // Obtention du contrôleur
        /** @var BackController $controller */
        $controller = $this->getController();

        // Exécution du contrôleur
        $controller->execute();

        // Assignation de la page créée par le contrôleur à la réponse
        $this->Http_response->setPage($controller->getPage());

        // Envoi de la réponse
        $this->Http_response->send();
    }
}