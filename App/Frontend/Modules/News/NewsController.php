<?php
namespace App\Frontend\Modules\News;

use \Entity\News;
use \Model\NewsManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex() {
		$nombre_news = $this->App->getConfig()->get('nombre_news');
		$nombre_caracteres = $this->App->getConfig()->get('nombre_caracteres');

		// On ajoute une définition pour le titre
		$this->Page->addVar('title', 'Liste des ' . $nombre_news . ' dernières news');

		// On récupère le manager des news
		$Manager = $this->Managers->getManagerOf('News');

		// Récupération des 5 dernières news
		/** @var News[] $Liste_news_a
		 *  @var NewsManager $Manager
		 */
		$Liste_news_a = $Manager->getList(0, $nombre_news);

		// On assigne aux news 200 caractères max
		foreach ($Liste_news_a as $News) {
			if (strlen($News->getContenu()) > $nombre_caracteres) {
				$debut = substr($News->getContenu(), 0, $nombre_caracteres);
				$debut = substr($debut, 0, strrpos($debut, ' ')) . '...';
				$News->setContenu($debut);
			}
		}

		// On envoie la liste des news à la vue
		$this->Page->addVar('Liste_news_a', $Liste_news_a);
	}

	public function executeShow(HTTPRequest $request) {
		// On récupère le manager des news
		$Manager = $this->Managers->getManagerOf('News');

		// On récupère la news de la requête
		/** @var News $News */
		$News = $Manager->getUnique($request->getGetData('id'));

		if (empty($News)) { $this->App->getHttpResponse()->redirect404(); }

		$this->Page->addVar('title', $News->getTitre());
		$this->Page->addVar('news', $News);
	}
}