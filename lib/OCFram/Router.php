<?php
namespace OCFram;

class Router {
    /** @var Route[] $Routes_a */
    protected $Route_a = [];

    const NO_ROUTE = 1;

    public function addRoute(Route $Route) {
        if (!in_array($Route, $this->Route_a)) {
            $this->Route_a[] = $Route;
        }
    }

    public function getRoute($url) {
        foreach ($this->Route_a as $Route) {
            // Si la route correspond à l'URL

            /** Route[] $vars_values */
            if (($vars_values = $Route->match($url)) !== false) {
                // Si elle a des variables
                if ($Route->hasVars()) {
                    $varsNames = $Route->getVarsNames_a();
                    $list_vars_a = [];

                    // Nouveau tableau clé/valeur
                    foreach ($vars_values as $key => $value) {
                        // La première valeur contient entièrement la chaîne capturée
                        if ($key !== 0) {
                            $list_vars_a[$varsNames[$key - 1]] = $value;
                        }
                    }

                    // On assigne ce tableau de variables à la route
                    $Route->setVars($list_vars_a);
                }

                return $Route;
            }
        }

        throw new \RuntimeException('Aucune route ne correspond à l\'URL', self::NO_ROUTE);
    }
}