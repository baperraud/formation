<?php

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\IsEmailValidator;
use OCFram\MaxLengthValidator;
use OCFram\NotNullValidator;
use OCFram\StringField;
use OCFram\TextField;

class UserFormBuilder extends FormBuilder {
	public function build() {
		$this->Form->add(new StringField([
			'label' => 'Pseudo',
			'name' => 'pseudonym',
			'max_length' => 50,
			'Validator_a' => [
				new MaxLengthValidator('Le pseudo spécifié est trop long (50 caractères maximum)', 50),
				new NotNullValidator('Merci de spécifier votre pseudo')
			]
		]))->add(new StringField([
			'label' => 'E-mail',
			'name' => 'email',
			'max_length' => 50,
			'type' => 'email',
			'Validator_a' => [
				new IsEmailValidator('Merci de renseigner un email valide'),
				new NotNullValidator('Merci de spécifier votre email')
			]
		]))->add(new StringField([
			'label' => 'Mot de passe',
			'name' => 'password',
			'max_length' => 100,
			'type' => 'password',
			'Validator_a' => [
				new MaxLengthValidator('Le mot de passe spécifié est trop long (100 caractères maximum)', 50),
				new NotNullValidator('Merci de spécifier votre mot de passe')
			]
		]));
	}
}