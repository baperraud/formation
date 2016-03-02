<?php
namespace OCFram;

abstract class Manager {
	protected $Dao;

	public function __construct($Dao) {
		$this->Dao = $Dao;
	}
}