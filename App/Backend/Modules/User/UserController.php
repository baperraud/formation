<?php

namespace App\Backend\Modules\User;


use Entity\User;
use Model\UsersManager;
use OCFram\BackController;


class UserController extends BackController {
	public function executeIndex() {
		$this->Page->addVar('title', 'Gestion des membres');

		/** @var UsersManager $UsersManager */
		$UsersManager = $this->Managers->getManagerOf('Users');

		/** @var User[] $User_a */
		$User_a = $UsersManager->getUsercSortByIdDesc_a();

		// On envoie la liste des membres et leur nombre à la vue
		$this->Page->addVar('User_a', $User_a);
		$this->Page->addVar('nombre_membres', $UsersManager->countUserc());

//		// On récupère les routes de modification/suppression de news
//		// puis on les envoie à la vue
//		$news_update_url_a = [];
//		$news_delete_url_a = [];

//		foreach ($User_a as $User) {
//			$news_update_url_a[$User->getId()] = Application::getRoute($this->App->getName(), $this->getModule(), 'update', array($User['id']));
//			$news_delete_url_a[$User->getId()] = Application::getRoute($this->App->getName(), $this->getModule(), 'delete', array($User['id']));
//		}

//		$this->Page->addVar('news_update_url_a', $news_update_url_a);
//		$this->Page->addVar('news_delete_url_a', $news_delete_url_a);
	}
}