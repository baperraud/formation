<?php

namespace OCFram;

abstract class FormBuilder {
    /** @var  Form $Form */
    protected $Form;

    public function __construct(Entity $Entity) {
        $this->setForm(new Form($Entity));
    }

    abstract public function build();

    public function getForm() { return $this->Form; }

    public function setForm(Form $Form) { $this->Form = $Form; }
}