<?php
namespace OCFram;

trait Hydrator {
    public function hydrate(array $data_a) {
        foreach ($data_a as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (is_callable([$this, $method])) {
                $this->$method($value);
            }
        }
    }
}