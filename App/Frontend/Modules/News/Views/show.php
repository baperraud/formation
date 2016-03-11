<?php
/**
 * @var \Entity\News $News
 * @var \Entity\Comment $Comment
 * @var array $comment_news_url
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 * @var array $comment_user_url_a
 * @var string $news_user_url
 */

use \OCFram\Application;
use \OCFram\Session;
?>

	<p>Par <em><a href="<?= $news_user_url ?>"><?= htmlspecialchars($News['auteur']) ?></a></em>, le <?= $News['Date_ajout']->format('d/m/Y à H\hi') ?></p>

	<h2 class="overflow_hidden"><?= htmlspecialchars($News['titre']) ?></h2>

	<p class="overflow_hidden"><?= htmlspecialchars($News['contenu']) ?></p>

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
		<fieldset id="<?= 'commentaire-'.$Comment['id'] ?>">
			<legend>
				Posté par <strong>
					<?php
					// Si l'auteur du commentaire est un membre
					if (!empty($comment_user_url_a[$Comment['id']])) {
						echo '<a href=', $comment_user_url_a[$Comment['id']] ,'>', htmlspecialchars($Comment['pseudonym']), '</a>';
					} else {
						echo htmlspecialchars($Comment['pseudonym']), ' (visiteur)';
					}
					?></strong>
				le <?= $Comment['Date']->format('d/m/Y à H\hi') ?>
				<?php if (Session::isAdmin()
					|| $Comment['pseudonym'] === Session::getAttribute('pseudo')
				): ?> -
					<a href=<?= $comment_update_url_a[$Comment['id']] ?>>Modifier</a> |
					<a href=<?= $comment_delete_url_a[$Comment['id']] ?>>Supprimer</a>
				<?php endif; ?>
			</legend>
			<p class="overflow_hidden"><?= htmlspecialchars($Comment['contenu']) ?></p>
		</fieldset>
		<?php
	endforeach;
endif; ?>

	<p><a href=<?= $comment_news_url ?>>Ajouter un commentaire</a></p>

<?php