<?php
namespace OCFram;

class Config extends ApplicationComponent {
    protected $vars_a = [];

    public function get($var) {
        if (!$this->vars_a) {
            $Xml = new \DOMDocument;
            $Xml->load(__DIR__ . '/../../App/' . $this->App->getName() . '/Config/app.xml');

            /** @var \DOMElement[] $Element_a */
            $Element_a = $Xml->getElementsByTagName('define');

            foreach ($Element_a as $Element) {
                $this->vars_a[$Element->getAttribute('var')] = $Element->getAttribute('value');
            }
        }

        return (isset($this->vars_a[$var])) ? $this->vars_a[$var] : NULL;
    }
}