<?php
namespace OCFram;

abstract class Application {
    protected $Http_request;
    protected $Http_response;
    protected $name;
    protected $Session;
    protected $Config;

    public function __construct() {
        $this->Http_request = new HTTPRequest($this);
        $this->Http_response = new HTTPResponse($this);
        $this->name = '';
        $this->Session = new Session($this);
        $this->Config = new Config($this);
    }

    abstract public function run();

    public function getName() { return $this->name; }
    public function getHttpRequest() { return $this->Http_request; }
    public function getHttpResponse() { return $this->Http_response; }
    public function getSession() { return $this->Session; }
    public function getConfig() { return $this->Config; }

    public function getController() {
        $Router = new Router;

        $Xml = new \DOMDocument;
        $Xml->load(__DIR__ . '/../../App/' . $this->name . '/Config/routes.xml');

        /** @var \DOMElement[] $Route_a */
        $Route_a = $Xml->getElementsByTagName('route');

        // Pour chaque route dans routes.xml
        foreach ($Route_a as $Route) {
            $vars_a = [];

            // Si des variables sont présentes dans l'URL
            if ($Route->hasAttribute('vars')) {
                $vars_a = explode(',', $Route->getAttribute('vars'));
            }

            // On ajoute la route au routeur
            $Router->addRoute(new Route(
                $Route->getAttribute('url'),
                $Route->getAttribute('module'),
                $Route->getAttribute('action'),
                $vars_a));
        }

        try {
            // On récupère la route correspondante à l'URL
            $matchedRoute = $Router->getRoute($this->Http_request->getRequestURI());
        } catch (\RuntimeException $e) {
            if ($e->getCode() == Router::NO_ROUTE) {
                // Si aucune route ne correspond, alors la page demandée n'existe pas
                $this->Http_response->redirect404();
            }
        }

        // On ajoute les variables de l'URL au tableau $_GET
        /** @var Route $matchedRoute */
        $_GET = array_merge($_GET, $matchedRoute->getVars_a());

        // On instancie le contrôleur
        $controllerClass = 'App\\' . $this->name . '\\Modules\\' . $matchedRoute->getModule() .
            '\\' . $matchedRoute->getModule() . 'Controller';
        return new $controllerClass($this, $matchedRoute->getModule(), $matchedRoute->getAction());
    }

    /**
     * Méthode statique permettant de récupérer une route spécifique
     * @param $application string L'application de la route à récupérer
     * @param $module string Le module de la route à récupérer
     * @param $action string L'action de la route à récupérer
     * @param $var_a array La liste des variables qui devront "hydrater" la route
     * @return string
     */
    public static function getRoute($application, $module, $action, array $var_a = []) {

        $Xml = new \DOMDocument;
        $Xml->load(__DIR__ . '/../../App/' . $application . '/Config/routes.xml');

        /** @var \DOMElement[] $Route_a */
        $Route_a = $Xml->getElementsByTagName('route');

        // Pour chaque route dans routes.xml
        foreach ($Route_a as $Route) {

            // Si l'on trouve l'url cherchée
            if ($Route->getAttribute('module') == $module &&
                $Route->getAttribute('action') == $action
            ) {
                // On récupère l'url correspondante
                $url = $Route->getAttribute('url');

                // Si l'url a des variables
                if (!empty($var_a)) {
                    // On remplace chaque paire de parenthèses
                    foreach ($var_a as $var) {
                        $url = preg_replace('/\([^)]+\)/', $var, $url, 1);
                    }
                }

                return preg_replace('/\\\./', '.', $url);
            }
        }

        throw new \RuntimeException('Aucune route ne correspond à l\'URL !');
    }
}