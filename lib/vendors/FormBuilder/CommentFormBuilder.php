<?php

namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class CommentFormBuilder extends FormBuilder {
	public function build() {
		$this->Form->add(new StringField([
			'label' => 'Auteur',
			'name' => 'auteur',
			'max_length' => 50,
			'Validator_a' => [
				new MaxLengthValidator('L\'auteur spécifié est trop long (50 caractères maximum)', 50),
				new NotNullValidator('Merci de spécifier l\'auteur du commentaire'),
			]
		]))->add(new TextField([
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