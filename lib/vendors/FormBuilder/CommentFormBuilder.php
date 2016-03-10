<?php

namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\IsEmailValidator;
use \OCFram\Session;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class CommentFormBuilder extends FormBuilder {
	public function build() {
		// Si l'utilisateur n'est pas connecté, on affiche les champs pseudo et email
		if (!Session::isAuthenticated()) {
			$this->Form->add(new StringField([
				'label' => 'Pseudo',
				'name' => 'pseudonym',
				'max_length' => 50,
				'Validator_a' => [
					new MaxLengthValidator('Le pseudo spécifié est trop long (50 caractères maximum)', 50),
					new NotNullValidator('Merci de spécifier votre pseudo'),
				]
			]))->add(new StringField([
				'label' => 'E-mail',
				'name' => 'email',
				'max_length' => 50,
				'type' => 'email',
				'Validator_a' => [
					new IsEmailValidator('Merci de renseigner un email valide')
				]
			]));
		}

		$this->Form->add(new TextField([
			'label' => 'Contenu',
			'name' => 'contenu',
			'rows' => 7,
			'cols' => 50,
			'Validator_a' => [
				new NotNullValidator('Merci de spécifier votre commentaire')
			]
		]));
	}
}