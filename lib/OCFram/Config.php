<?php
namespace OCFram;

class Config extends ApplicationComponent {
	protected $vars = [];

	public function get($var) {
		if (!$this->vars) {
			$xml = new \DOMDocument;
			$xml->load(__DIR__ . '/../../App/' . $this->app->name() . '/Config/app.xml');

			/** @var \DOMElement[] $elements */
			$elements = $xml->getElementsByTagName('define');

			foreach ($elements as $element) {
				$this->vars[$element->getAttribute('var')] = $element->getAttribute('value');
			}
		}

		return (isset($this->vars[$var])) ? $this->vars[$var] : NULL;
	}
}