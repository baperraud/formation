<?php

namespace OCFram;

use \Model\CommentsManager;
use \Model\NewsManager;

class FormHandler {
	/** @var  Form */
	protected $Form;
	/** @var  NewsManager | CommentsManager */
	protected $Manager;
	/** @var  HTTPRequest */
	protected $Request;

	public function __construct(Form $Form, Manager $Manager, HTTPRequest $Request) {
		$this->setForm($Form);
		$this->setManager($Manager);
		$this->setRequest($Request);
	}

	public function process() {
		if ($this->Request->getMethod() == 'POST' && $this->Form->isValid()) {
			$this->Manager->save($this->Form->getEntity());

			return true;
		}

		return false;
	}

	public function setForm(Form $Form) { $this->Form = $Form; }
	public function setManager(Manager $Manager) { $this->Manager = $Manager; }
	public function setRequest(HTTPRequest $Request) { $this->Request = $Request; }
}