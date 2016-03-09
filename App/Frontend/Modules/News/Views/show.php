<?php
/**
 * @var \Entity\News $News
 * @var \Entity\Comment $Comment
 * @var \OCFram\User $User
 */
?>

<p>Par <em><?= htmlspecialchars($News['auteur']) ?></em>, le <?= $News['Date_ajout']->format('d/m/Y à H\hi') ?></p>

<h2 class="overflow_hidden"><?= htmlspecialchars($News['titre']) ?></h2>

<p class="overflow_hidden"><?= nl2br(htmlspecialchars($News['contenu'])) ?></p>

<?php if ($News['Date_ajout'] != $News['Date_modif']): ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $News['Date_modif']->format('d/m/Y à H\hi') ?></em></small>
	</p>
<?php endif; ?>

<p><a href=<?= $comment_news_url ?>>Ajouter un commentaire</a></p>

<?php
if (empty($Comment_a)): ?>
	<p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php else:
	foreach ($Comment_a as $Comment): ?>
		<fieldset>
			<legend>
				Posté par <strong><?= htmlspecialchars($Comment['auteur']) ?></strong>
				le <?= $Comment['Date']->format('d/m/Y à H\hi') ?>
				<?php if ($User->isAuthenticated() && $User->isAdmin()
				): ?> -
					<a href="admin/comment-update-<?= $Comment['id'] ?>.html">Modifier</a> |
					<a href="admin/comment-delete-<?= $Comment['id'] ?>.html">Supprimer</a>
				<?php endif; ?>
			</legend>
			<p class="overflow_hidden"><?= nl2br(htmlspecialchars($Comment['contenu'])) ?></p>
		</fieldset>
		<?php
	endforeach;
endif; ?>

<p><a href=<?= $comment_news_url ?>>Ajouter un commentaire</a></p>