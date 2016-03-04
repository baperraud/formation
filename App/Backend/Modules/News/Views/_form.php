<form action="" method="post">

	<p>
		<?= isset($erreur_a) && in_array(\Entity\News::AUTEUR_INVALIDE, $erreur_a) ? 'L\'auteur est invalide.<br />' : '' ?>
		<label>Auteur</label>
		<input type="text" name="auteur" value="<?= isset($News) ? $News['auteur'] : '' ?>" /><br />

		<?= isset($erreur_a) && in_array(\Entity\News::TITRE_INVALIDE, $erreur_a) ? 'Le titre est invalide.<br />' : '' ?>
		<label>Titre</label>
		<input type="text" name="titre" value="<?= isset($News) ? $News['titre'] : '' ?>" /><br />

		<?= isset($erreur_a) && in_array(\Entity\News::CONTENU_INVALIDE, $erreur_a) ? 'Le contenu est invalide.<br />' : '' ?>
		<label>Contenu</label>
		<textarea name="contenu" rows="8" cols="60"><?= isset($News) ? $News['contenu'] : '' ?></textarea><br />

<?php
	if (isset($News) && !$News->isNew()) { ?>
		<input type="hidden" name="id" value="<?= $News['id'] ?>" />
		<input type="submit" value="Modifier" name="modifier" />
<?php } else { ?>
		<input type="submit" value="Ajouter" />
<?php } ?>
	</p>
</form>