<?php

namespace OCFram;

class StringField extends Field {
	protected $max_length;

	public function buildWidget() {
		$widget = '';

		if (!empty($this->error_message)) { $widget .= $this->error_message . '<br />'; }

		$widget .= '<label>' . $this->label . '</label><input type="text" name="' . $this->name . '"';

		if (!empty($this->value)) { $widget .= ' value="' . htmlspecialchars($this->value) . '"'; }

		if (!empty($this->max_length)) { $widget .= ' max_length="' . $this->max_length . '"'; }

		$widget .= ' />';
		return $widget;
	}

	public function setMax_length($max_length) {
		$max_length = (int)$max_length;
		if ($max_length > 0) {
			$this->max_length = $max_length;
		} else {
			throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
		}
	}
}