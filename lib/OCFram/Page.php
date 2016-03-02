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

		$User = $this->App->getUser();

		extract($this->vars_a);

		ob_start();
		require $this->contentFile;
		$content = ob_get_clean();

		ob_start();
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