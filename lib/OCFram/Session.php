<?php
namespace OCFram;

use \Model\UsersManager;

session_start();

class Session extends ApplicationComponent {

	public static function getAttribute($attr) {
		return isset($_SESSION[$attr]) ? $_SESSION[$attr] : NULL;
	}

	public static function getFlash() {
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);

		return $flash;
	}

	public static function hasFlash() { return isset($_SESSION['flash']); }

	public static function isAuthenticated() { return isset($_SESSION['auth']) && $_SESSION['auth'] === true; }

	public static function setAttribute($attr, $value) { $_SESSION[$attr] = $value; }

	public static function setAuthenticated($authenticated = true) {
		if (!is_bool($authenticated)) {
			throw new \InvalidArgumentException('La valeur spécifiée à la méthode Session::setAuthenticated() doit être un boolean');
		}
		$_SESSION['auth'] = $authenticated;
	}

	public static function setFlash($value) { $_SESSION['flash'] = $value; }

	public static function isAdmin() {
		if (!Session::isAuthenticated()) return false;
		return (Session::getAttribute('admin') == UsersManager::ROLE_ADMIN);
	}
}