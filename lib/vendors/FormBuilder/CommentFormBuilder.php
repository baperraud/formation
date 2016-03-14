<?php

namespace FormBuilder;

use OCFram\EmailAvailableValidator;
use OCFram\FormBuilder;
use OCFram\IsEmailValidator;
use OCFram\NoSpaceValidator;
use OCFram\PseudoAvailableValidator;
use OCFram\Session;
use OCFram\StringField;
use OCFram\TextField;
use OCFram\MaxLengthValidator;
use OCFram\NotNullValidator;

class CommentFormBuilder extends FormBuilder {
    public function build() {
        // Si l'utilisateur n'est pas connecté, on affiche les champs pseudo et email
        if (!Session::isAuthenticated()) {
            $this->Form->add(new StringField([
                'label' => 'Pseudo',
                'name' => 'pseudonym',
                'max_length' => 50,
                'required' => true,
                'Validator_a' => [
                    new MaxLengthValidator('Le pseudo spécifié est trop long (50 caractères maximum)', 50),
                    new NotNullValidator('Merci de spécifier votre pseudo'),
                    new PseudoAvailableValidator('Erreur : un membre utilise ce pseudo. Veuillez vous connecter s\'il s\'agit du vôtre'),
                    new NoSpaceValidator('Veuillez ne pas utiliser le caractère d\'espacement')
                ]
            ]))->add(new StringField([
                'label' => 'E-mail',
                'name' => 'email',
                'max_length' => 50,
                'type' => 'email',
                'Validator_a' => [
                    new IsEmailValidator('Merci de renseigner un email valide'),
                    new EmailAvailableValidator('Erreur : un membre utilise cet email. Veuillez vous connecter s\'il s\'agit du vôtre'),
                ]
            ]));
        }

        $this->Form->add(new TextField([
            'label' => 'Contenu',
            'name' => 'contenu',
            'required' => true,
            'rows' => 7,
            'cols' => 50,
            'Validator_a' => [
                new NotNullValidator('Merci de spécifier votre commentaire')
            ]
        ]));
    }
}