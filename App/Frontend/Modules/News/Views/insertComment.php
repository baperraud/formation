<h2>Écrire un commentaire</h2>

<form action="" method="post">
	<p>
		<?= isset($erreur_a) && in_array(\Entity\Comment::AUTEUR_INVALIDE, $erreur_a) ? 'L\'auteur est invalide.<br />' : '' ?>
		<label>Pseudo</label>
		<input type="text" name="pseudo" value="<?= isset($Comment) ? htmlspecialchars($Comment['auteur']) : '' ?>" /><br />

		<?= isset($erreur_a) && in_array(\Entity\Comment::CONTENU_INVALIDE, $erreur_a) ? 'Le contenu est invalide.<br />' : '' ?>
		<label>Contenu</label>
		<textarea name="contenu" rows="7" cols="50"><?= isset($Comment) ? htmlspecialchars($Comment['contenu']) : '' ?></textarea><br />

		<input type="submit" value="Envoyer" />
	</p>
</form>