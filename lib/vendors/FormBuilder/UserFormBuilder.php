<?php

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\IsEmailValidator;
use OCFram\MaxLengthValidator;
use OCFram\NotNullValidator;
use OCFram\StringField;
use OCFram\PseudoAvailableValidator;
use OCFram\EmailAvailableValidator;
use OCFram\NoSpaceValidator;
use OCFram\SimilarCheckValidator;

// TODO: Factoriser en un param par défaut les messages d'erreur des validateurs

class UserFormBuilder extends FormBuilder {
	public function build() {
		$this->Form->add(new StringField([
			'label' => 'Pseudo',
			'name' => 'pseudonym',
			'required' => true,
			'max_length' => 50,
			'Validator_a' => [
				new MaxLengthValidator('Le pseudo spécifié est trop long (50 caractères maximum)', 50),
				new NotNullValidator('Merci de spécifier votre pseudo'),
				new PseudoAvailableValidator('Erreur : le pseudo est déjà pris'),
				new NoSpaceValidator('Veuillez ne pas utiliser le caractère d\'espacement')
			]
		]))->add(new StringField([
			'label' => 'E-mail',
			'name' => 'email',
			'required' => true,
			'max_length' => 50,
			'type' => 'email',
			'Validator_a' => [
				new IsEmailValidator('Merci de renseigner un email valide'),
				new EmailAvailableValidator('Erreur : l\'email est déjà pris'),
				new NotNullValidator('Merci de spécifier votre email')
			]
		]))->add($password_origin = new StringField([
			'label' => 'Mot de passe',
			'name' => 'password',
			'required' => true,
			'max_length' => 100,
			'type' => 'password',
			'Validator_a' => [
				new MaxLengthValidator('Le mot de passe spécifié est trop long (100 caractères maximum)', 50),
				new NotNullValidator('Merci de spécifier votre mot de passe'),
				new NoSpaceValidator('Veuillez ne pas utiliser le caractère d\'espacement')
			]
		]))->add(new StringField([
			'label' => 'Confirmation du mot de passe',
			'name' => 'password_confirmation',
			'required' => true,
			'max_length' => 100,
			'type' => 'password',
			'Validator_a' => [
				new SimilarCheckValidator('Les mots de passe doivent être identiquement saisis', $password_origin)
			]
		]));
	}
}