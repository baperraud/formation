<?php
namespace OCFram;

class Route {
    protected $action;
    protected $module;
    protected $url;
    protected $vars_names_a;
    protected $vars_a = [];
    protected $format;

    public function __construct($url, $module, $action, array $vars_names_a, $format = '') {
        $this->setUrl($url);
        $this->setModule($module);
        $this->setAction($action);
        $this->setVarsNames($vars_names_a);
        $this->setFormat($format);
    }

    public function hasVars() { return !empty($this->vars_names_a); }

    /**
     * Méthode permettant de déterminer si la route correspond à l'url fournie
     * Renvoie la liste des variables si c'est le cas, false sinon
     * @param $url string L'url fournie
     * @return string[] | boolean
     */
    public function match($url) {
        if (preg_match('`^' . $this->url . '$`', $url, $matches)) {
            return $matches;
        } else {
            return false;
        }
    }

    public function setAction($action) {
        if (is_string($action)) {
            $this->action = $action;
        }
    }

    public function setModule($module) {
        if (is_string($module)) {
            $this->module = $module;
        }
    }

    public function setUrl($url) {
        if (is_string($url)) {
            $this->url = $url;
        }
    }

    public function setFormat($format) {
        if (is_string($format) && !(empty($format))) {
            $this->format = $format;
        }
    }

    public function setVarsNames(array $vars_names_a) { $this->vars_names_a = $vars_names_a; }

    public function setVars(array $vars_a) { $this->vars_a = $vars_a; }

    public function getAction() { return $this->action; }
    public function getModule() { return $this->module; }
    public function getVars_a() { return $this->vars_a; }
    public function getVarsNames_a() { return $this->vars_names_a; }
    public function getFormat() { return $this->format; }
}