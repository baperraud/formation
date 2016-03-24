<?php
namespace OCFram;

class Page extends ApplicationComponent {
    protected $contentFile;
    protected $vars_a = [];
    protected $format;

    public function __construct(Application $App, $format = null) {
        parent::__construct($App);
        $this->format = $format;
    }

    public function addVar($var, $value) {
        if (!is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('Le nom de la variable doit être une chaîne de caractères non nulle');
        }
        $this->vars_a[$var] = $value;
    }

    public function clearVars() {
        $this->vars_a = [];
    }

    public function getGeneratedPage() {
        if (!file_exists($this->contentFile)) {
            throw new \RuntimeException('La vue ' . $this->contentFile . ' n\'existe pas');
        }

        extract($this->vars_a);

        /* Traitement standard -> HTML */
        if ($this->format != 'json') {

            ob_start();
            /** @noinspection PhpIncludeInspection */
            require $this->contentFile;
            /** @noinspection PhpUnusedLocalVariableInspection */
            $content = ob_get_clean();

            ob_start();
            /** @noinspection PhpIncludeInspection */
            require __DIR__ . '/../../App/' . $this->App->getName() . '/Templates/layout_html.php';
            return ob_get_clean();
        }

        /* Traitement alternatif -> JSON */
        /** @noinspection PhpIncludeInspection */
        require $this->contentFile;
        /** @noinspection PhpIncludeInspection */
        require __DIR__ . '/../../App/' . $this->App->getName() . '/Templates/layout_json.php';
        /** @var string $json */
        return $json;
    }

    public function getGeneratedSubView() {
        if (!file_exists($this->contentFile)) {
            throw new \RuntimeException('La sous-vue ' . $this->contentFile . ' n\'existe pas');
        }

        extract($this->vars_a);

        ob_start();
        /** @noinspection PhpIncludeInspection */
        require $this->contentFile;
        return ob_get_clean();

    }

    public function setContentFile($contentFile) {
        if (!is_string($contentFile) || empty($contentFile)) {
            throw new \InvalidArgumentException('La vue spécifiée est invalide');
        }
        $this->contentFile = $contentFile;
    }
}