<?php
namespace OCFram;

abstract class Entity implements \ArrayAccess {
	protected $erreurs_a = [], $id;

	public function __construct(array $donnees_a = []) {
		if (!empty($donnees_a)) {
			$this->hydrate($donnees_a);
		}
	}

	public function isNew() { return empty($this->id); }

	public function getErreurs_a() { return $this->erreurs_a; }
	public function id() { return $this->id; }

	public function setId($id) { $this->id = (int)$id; }

	public function hydrate(array $donnees_a) {
		foreach ($donnees_a as $attribut => $valeur) {
			$methode = 'set' . ucfirst($attribut);
			if (is_callable([$this, $methode])) {
				$this->$methode($valeur);
			}
		}
	}

	public function offsetGet($var) {
		if (isset($this->$var) && is_callable([$this, $var])) {
			return $this->$var();
		}
		return NULL;
	}

	public function offsetSet($var, $value) {
		$method = 'set' . ucfirst($var);
		if (isset($this->$var) && is_callable([$this, $method])) {
			$this->$method($value);
		}
	}

	public function offsetExists($var) {
		return isset($this->$var) && is_callable([$this, $var]);
	}

	public function offsetUnset($var) {
		throw new \Exception('Impossible de supprimer une quelconque valeur');
	}
}