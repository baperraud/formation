<?php
namespace OCFram;

abstract class Entity implements \ArrayAccess {
	use Hydrator;

	protected $id;
	protected $erreur_a = [];

	public function __construct(array $donnee_a = []) {
		if (!empty($donnee_a)) {
			$this->hydrate($donnee_a);
		}
	}

	public function isNew() { return empty($this->id); }

	public function getErreur_a() { return $this->erreur_a; }
	public function getId() { return $this->id; }

	public function setId($id) { $this->id = (int)$id; }

	public function offsetGet($var) {
		if (isset($this->$var) && is_callable([$this, $method = 'get' . ucfirst($var)])) {
			return $this->$method();
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