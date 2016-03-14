<?php

namespace OCFram;

class StringField extends Field {
    protected $max_length;
    protected $type = 'text';

    public function buildWidget() {
        $widget = '';

        if (!empty($this->error_message)) {
            $widget .= $this->error_message . '<br />';
        }

        $widget .= '<label ' . ($this->isRequired() ? 'class="required"' : '') . ' for="' . $this->name . '">' . $this->label . '</label><input type="' . $this->type . '" name="' . $this->name . '" id="' . $this->name . '"';

        if (!empty($this->value)) {
            $widget .= ' value="' . htmlspecialchars($this->value) . '"';
        }

        if (!empty($this->max_length)) {
            $widget .= ' maxlength="' . $this->max_length . '"';
        }

        if ($this->isRequired()) {
            $widget .= ' required';
        }

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

    public function setType($type) {
        if (!empty($type)) {
            $this->type = $type;
        } else {
            throw new \RuntimeException('Le type doit être une string non vide');
        }
    }
}