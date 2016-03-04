<?php
namespace App\Frontend\Modules\News;

use \Entity\Comment;
use \Entity\News;
use \Model\NewsManager;
use \OCFram\BackController;
use OCFram\Form;
use \OCFram\HTTPRequest;
use OCFram\StringField;
use OCFram\TextField;

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
		$Liste_news_a = $Manager->getNewscSortByIdDesc_a(0, $nombre_news);

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

	public function executeShow(HTTPRequest $Request) {
		// On récupère le manager des news
		$Manager = $this->Managers->getManagerOf('News');

		// On récupère la news de la requête
		/** @var News $News
		 *  @var NewsManager $Manager
		 */
		$News = $Manager->getNewscUsingId($Request->getGetData('id'));

		if (empty($News)) { $this->App->getHttpResponse()->redirect404(); }

		// On envoie la news à la vue
		$this->Page->addVar('title', $News->getTitre());
		$this->Page->addVar('News', $News);
		// On envoie les commentaires associés également
		$this->Page->addVar('Comment_a', $this->Managers->getManagerOf('Comments')->getCommentcUsingNewscIdSortByDateDesc_a($News->getId()));
	}

	public function executeInsertComment(HTTPRequest $Request) {
		// Si le formulaire a été envoyé, on crée le commentaire avec les valeurs du formulaire
		if ($Request->getMethod() == 'POST') {
			$Comment = new Comment([
				'news' => $Request->getGetData('news'),
				'auteur' => $Request->getPostData('pseudo'),
				'contenu' => $Request->getPostData('contenu')
			]);
		} else {
			$Comment = new Comment;
		}

		$Form = new Form($Comment);

		$Form->add(new StringField([
			'label' => 'Auteur',
			'name' => 'auteur',
			'max_length' => 50
		]))->add(new TextField([
			'label' => 'Contenu',
			'name' => 'contenu',
			'rows' => 7,
			'cols' => 50
		]));

		if ($Form->isValid()) {
			// On enregistre le commentaire
		}

		$this->Page->addVar('Comment', $Comment);
		// On passe le formulaire généré à la vue
		$this->Page->addVar('Form', $Form->createView());
		$this->Page->addVar('title', 'Ajout d\'un commentaire');
	}
}