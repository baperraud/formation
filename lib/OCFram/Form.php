<?php

namespace OCFram;

class Form {
    protected $Entity;
    /** @var Field[] $Field_a */
    protected $Field_a = [];

    public function __construct(Entity $Entity) {
        $this->setEntity($Entity);
    }

    public function add(Field $Field) {
        $attr = 'get' . ucfirst($Field->getName()); // On récupère le nom du champ
        $Field->setValue($this->Entity->$attr()); // On assigne la valeur correspondante au champ

        $this->Field_a[] = $Field;
        return $this;
    }

    public function createView() {
        $view = '';

        // On génère un par un les champs du formulaire
        foreach ($this->Field_a as $Field) {
            $view .= $Field->buildWidget() . '<br />';
        }

        return $view;
    }

    public function isValid() {
        $valid = true;

        // On vérifie que tous les champs sont valides
        foreach ($this->Field_a as $Field) {
            if (!$Field->isValid()) {
                $valid = false;
            }
        }

        return $valid;
    }

    public function getEntity() { return $this->Entity; }
    public function getField_a() { return $this->Field_a; }

    public function setEntity(Entity $Entity) { $this->Entity = $Entity; }
}