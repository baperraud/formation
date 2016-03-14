<?php

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\StringField;
use OCFram\TextField;
use OCFram\MaxLengthValidator;
use OCFram\NotNullValidator;

class NewsFormBuilder extends FormBuilder {
	public function build() {
		$this->Form->add(new StringField([
			'label' => 'Titre',
			'name' => 'titre',
			'required' => true,
			'max_length' => 100,
			'Validator_a' => [
				new MaxLengthValidator('Le titre spécifié est trop long (100 caractères maximum)', 100),
				new NotNullValidator('Merci de spécifier le titre de la news'),
			]
		]))->add(new TextField([
			'label' => 'Contenu',
			'name' => 'contenu',
			'required' => true,
			'rows' => 8,
			'cols' => 60,
			'Validator_a' => [
				new NotNullValidator('Merci de spécifier le contenu de la news')
			]
		]));
	}
}