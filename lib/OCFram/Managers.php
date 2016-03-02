<?php
namespace OCFram;

class Managers {
	protected $api = NULL;
	protected $Dao = NULL;
	protected $managers = [];

	public function __construct($api, $Dao) {
		$this->api = $api;
		$this->Dao = $Dao;
	}

	public function getManagerOf($module) {
		if (!is_string($module) || empty($module)) {
			throw new \InvalidArgumentException('Le module spécifié est invalide');
		}

		if (!isset($this->managers[$module])) {
			$manager = '\\Model' . $module . 'Manager' . $this->api;

			$this->managers[$module] = new $manager($this->Dao);
		}

		return $this->managers[$module];
	}
}