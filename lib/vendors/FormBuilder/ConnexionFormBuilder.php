<?php

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\NotNullValidator;
use OCFram\StringField;

class ConnexionFormBuilder extends FormBuilder {
    public function build($id = null) {
        $this->Form->add(new StringField([
            'label' => 'Pseudo',
            'name' => 'pseudonym',
            'required' => true,
            'max_length' => 50,
            'Validator_a' => [
                new NotNullValidator('Merci de spécifier votre pseudo')
            ]
        ]))->add(new StringField([
            'label' => 'Mot de passe',
            'name' => 'password',
            'required' => true,
            'max_length' => 100,
            'type' => 'password',
            'Validator_a' => [
                new NotNullValidator('Merci de spécifier votre mot de passe')
            ]
        ]));
    }
}