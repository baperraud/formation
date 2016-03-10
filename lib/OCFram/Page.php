<?php
namespace OCFram;

class Page extends ApplicationComponent {
	protected $contentFile;
	protected $vars_a = [];

	public function addVar($var, $value) {
		if (!is_string($var) || is_numeric($var) || empty($var)) {
			throw new \InvalidArgumentException('Le nom de la variable doit être une chaîne de caractères non nulle');
		}
		$this->vars_a[$var] = $value;
	}

	public function getGeneratedPage() {
		if (!file_exists($this->contentFile)) {
			throw new \RuntimeException('La vue spécifiée n\'existe pas');
		}

		/** @var Session $Session */
		/** @noinspection PhpUnusedLocalVariableInspection */
		$Session = $this->App->getSession();

		extract($this->vars_a);

		ob_start();
		/** @noinspection PhpIncludeInspection */
		require $this->contentFile;
		/** @noinspection PhpUnusedLocalVariableInspection */
		$content = ob_get_clean();

		// On renseigne les routes génériques du layout
		$layout_route_a = [];
		$layout_route_a['accueil'] = Application::getRoute('Frontend', 'News', 'index');
		$layout_route_a['login'] = Application::getRoute('Frontend', 'User', 'index');
		$layout_route_a['logout'] = Application::getRoute('Frontend', 'User', 'logout');
		$layout_route_a['admin'] = Application::getRoute('Backend', 'News', 'index');
		$layout_route_a['insert'] = Application::getRoute('Frontend', 'News', 'insert');
		$layout_route_a['profil'] = Application::getRoute('Frontend', 'User', 'show', array(Session::getAttribute('pseudo')));
		$layout_route_a['signup'] = Application::getRoute('Frontend', 'User', 'signup');

		ob_start();
		/** @noinspection PhpIncludeInspection */
		require __DIR__ . '/../../App/' . $this->App->getName() . '/Templates/layout.php';
		return ob_get_clean();
	}

	public function setContentFile($contentFile) {
		if (!is_string($contentFile) || empty($contentFile)) {
			throw new \InvalidArgumentException('La vue spécifiée est invalide');
		}
		$this->contentFile = $contentFile;
	}
}