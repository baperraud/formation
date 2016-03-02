<?php
namespace OCFram;

class Config extends ApplicationComponent {
	protected $vars_a = [];

	public function get($var) {
		if (!$this->vars_a) {
			$xml = new \DOMDocument;
			$xml->load(__DIR__ . '/../../App/' . $this->App->getName() . '/Config/app.xml');

			/** @var \DOMElement[] $Elements */
			$Elements = $xml->getElementsByTagName('define');

			foreach ($Elements as $Element) {
				$this->vars_a[$Element->getAttribute('var')] = $Element->getAttribute('value');
			}
		}

		return (isset($this->vars_a[$var])) ? $this->vars_a[$var] : NULL;
	}
}