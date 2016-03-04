<?php
namespace OCFram;

abstract class Manager {
	/** @var  \PDO $Dao */
	protected $Dao;

	public function __construct($Dao) {
		$this->Dao = $Dao;
	}
}