<?php
namespace OCFram;

class PDOFactory {
	public static function getMysqlConnexion() {
		$Db = new \PDO('mysql:host=localhost;dbname=news', 'root', '');
		$Db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		return $Db;
	}
}