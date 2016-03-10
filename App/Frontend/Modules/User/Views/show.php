<?php
use \OCFram\Session;

/** @var string $pseudo */
?>

	<h2><?= Session::getAttribute('pseudo') == $pseudo ?
			'Mon profil' : 'Profil de ' . $pseudo;
		?></h2>

	<p><?= Session::getAttribute('pseudo') == $pseudo ?
			'Bienvenue sur votre page de profil !' : 'Vous visitez actuellement le profil de ' . $pseudo . '.';
		?></p>

<?php

