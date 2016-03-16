<?php
namespace OCFram;

class Page extends ApplicationComponent {
    protected $contentFile;
    protected $vars_a = [];
    protected $format;

    public function __construct(Application $App, $format) {
        parent::__construct($App);
        $this->format = $format;
    }

    public function addVar($var, $value) {
        if (!is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('Le nom de la variable doit être une chaîne de caractères non nulle');
        }
        $this->vars_a[$var] = $value;
    }

    public function getGeneratedPage() {
        if (!file_exists($this->contentFile)) {
            throw new \RuntimeException('La vue ' . $this->contentFile . ' n\'existe pas');
        }

        /** @var Session $Session */
        /** @noinspection PhpUnusedLocalVariableInspection */
        $Session = $this->App->getSession();

        extract($this->vars_a);

        //die('stop');

        /* Traitement standard -> HTML */
        if ($this->format != 'json') {
            //die('html');

            ob_start();
            /** @noinspection PhpIncludeInspection */
            require $this->contentFile;
            /** @noinspection PhpUnusedLocalVariableInspection */
            $content = ob_get_clean();

            ob_start();
            /** @noinspection PhpIncludeInspection */
            require __DIR__ . '/../../App/' . $this->App->getName() . '/Templates/layout.php';
            return ob_get_clean();
        }

        /* Traitement alternatif -> JSON */
        /** @noinspection PhpIncludeInspection */
        require $this->contentFile;
        /** @var string $json */
        return $json;
    }

    public function setContentFile($contentFile) {
        if (!is_string($contentFile) || empty($contentFile)) {
            throw new \InvalidArgumentException('La vue spécifiée est invalide');
        }
        $this->contentFile = $contentFile;
    }
}