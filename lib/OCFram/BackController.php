<?php
namespace OCFram;

abstract class BackController extends ApplicationComponent {
    protected $action = '';
    protected $module = '';
    protected $Page = NULL;
    protected $view = '';
    protected $Managers = NULL;

    public function __construct(Application $app, $module, $action, $format) {
        parent::__construct($app);

        $this->Managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        $this->Page = new Page($app, $format);

        $this->setModule($module);
        $this->setAction($action);
        $this->setView($action);
    }

    public function setModule($module) {
        if (!is_string($module) || empty($module)) {
            throw new \InvalidArgumentexception('Le module doit être une chaîne de caractères valide');
        }
        $this->module = $module;
    }

    public function setAction($action) {
        if (!is_string($action) || empty($action)) {
            throw new \InvalidArgumentexception('L\'action doit être une chaîne de caractères valide');
        }
        $this->action = $action;
    }

    public function setView($view) {
        if (!is_string($view) || empty($view)) {
            throw new \InvalidArgumentexception('La vue doit être une chaîne de caractères valide');
        }
        $this->view = $view;
        $this->Page->setContentFile(__DIR__ . '/../../App/' . $this->App->getName() . '/Modules/' . $this->module . '/Views/' . $this->view . '.php');
    }

    public function getAction() { return $this->action; }
    public function getModule() { return $this->module; }
    public function getPage() { return $this->Page; }

    public function execute(array $data_a = null) {
        // On vide le tableau de vars
        $this->Page->clearVars();

        $method = 'execute' . ucfirst($this->action);

        if (!is_callable([$this, $method])) {
            throw new \RuntimeException('L\'action "' . $this->action . '" n\'est pas définie sur ce module');
        }

        if ($data_a === NULL)
            $this->$method($this->App->getHttpRequest());
        else
            $this->$method($this->App->getHttpRequest(), $data_a);
    }

    public function redirectToAction($action, array $data_a = []) {
        $this->setAction($action);
        $this->setView($action);
        $this->execute($data_a);
    }
}