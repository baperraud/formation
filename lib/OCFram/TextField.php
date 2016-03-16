<?php

namespace OCFram;

class TextField extends Field {
    protected $cols;
    protected $rows;

    public function buildWidget() {
        $widget = '';

        if (!empty($this->error_message)) {
            $widget .= $this->error_message . '<br />';
        }

        $widget .= '<label ' . ($this->isRequired() ? 'class="required"' : '') . ' for="' . $this->name . '">' . $this->label . '</label><textarea name="' . $this->name . '" id="' . $this->name . $this->id . '"';

        if (!empty($this->cols)) {
            $widget .= ' cols="' . $this->cols . '"';
        }

        if (!empty($this->rows)) {
            $widget .= ' rows="' . $this->rows . '"';
        }

        if ($this->isRequired()) {
            $widget .= ' required';
        }

        $widget .= '>';

        if (!empty($this->value)) {
            $widget .= htmlspecialchars($this->value);
        }

        $widget .= '</textarea>';
        return $widget;
    }

    public function setCols($cols) {
        $cols = (int)$cols;
        if ($cols > 0) {
            $this->cols = $cols;
        }
    }
    public function setRows($rows) {
        $rows = (int)$rows;
        if ($rows > 0) {
            $this->$rows = $rows;
        }
    }
}