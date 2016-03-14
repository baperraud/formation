<?php
namespace OCFram;

use App\Backend\BackendApplication;
use App\Frontend\FrontendApplication;

abstract class ApplicationComponent {
	/** @var BackendApplication |FrontendApplication $App */
	protected $App;

	public function __construct(Application $App) {
		$this->App = $App;
	}

	public function getApp() { return $this->App; }
}