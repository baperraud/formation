<?php
namespace App\Backend\Modules\News;

use \Model\NewsManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex(HTTPRequest $Request) {
		// On ajoute une définition pour le titre
		$this->Page->addVar('title', 'Gestion des news');

		// On récupère le manager des news
		/** @var NewsManager $Manager */
		$Manager = $this->Managers->getManagerOf('News');

		// On envoie la liste des news et leur nombre à la vue
		$this->Page->addVar('News_a', $Manager->getNewscSortByIdDesc_a());
		$this->Page->addVar('nombre_news', $Manager->countNewsc());
	}
}