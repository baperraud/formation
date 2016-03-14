<?php


namespace OCFram;

class GenericComponentHandler extends ApplicationComponent {

	/**
	 * Méthode permettant de rediriger l'utilisateur vers la page de connexion
	 * @param $message string Le message à afficher
	 * @param $to_admin boolean Indique s'il faut se reconnecter en tant qu'admin
	 * @return void
	 */
	public function checkAndredirectToLogin($message = 'Vous devez être connecté pour accéder à cette page', $to_admin = false) {
		Session::setFlash($message);

		// Si l'utilisateur n'est pas identifié
		if (!Session::isAuthenticated()) {
			$this->App->getHttpResponse()->redirect(Application::getRoute('Frontend', 'User', 'index'));
		} // S'il n'a pas les droits admin qui sont requis
		elseif ($to_admin && !Session::isAdmin()) {
			$this->App->getHttpResponse()->redirect(Application::getRoute('Frontend', 'News', 'index'));
		}
	}
}