<?php
/**
 * @var \Entity\News $News
 * @var \Entity\Comment $Comment
 * @var array $comment_news_url
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 */

use \OCFram\Application;
use \OCFram\Session;
?>

	<p>Par <em>
			<?php
			$user_profil_url = Application::getRoute('Frontend', 'User', 'show', array($News['auteur']));
			echo '<a href="', $user_profil_url ,'">', htmlspecialchars($News['auteur']) ,'</a>';
			?></em>, le <?= $News['Date_ajout']->format('d/m/Y à H\hi') ?></p>

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
		<fieldset>
			<legend>
				Posté par <strong>
					<?php
					// Si l'auteur du commentaire est un membre
					if ((int)$Comment['owner_type'] === 1) {
						$user_profil_url = Application::getRoute('Frontend', 'User', 'show', array($Comment['pseudonym']));
						echo '<a href=', $user_profil_url ,'>', htmlspecialchars($Comment['pseudonym']), '</a>';
					} else {
						echo htmlspecialchars($Comment['pseudonym']), ' (visiteur)';
					}
					?></strong>
				le <?= $Comment['Date']->format('d/m/Y à H\hi') ?>
				<?php if (Session::isAuthenticated() && Session::isAdmin()
				): ?> -
					<a href=<?= $comment_update_url_a[$Comment['id']] ?>>Modifier</a> |
					<a href=<?= $comment_delete_url_a[$Comment['id']] ?>>Supprimer</a>
				<?php endif; ?>
			</legend>
			<p class="overflow_hidden"><?= nl2br(htmlspecialchars($Comment['contenu'])) ?></p>
		</fieldset>
		<?php
	endforeach;
endif; ?>

	<p><a href=<?= $comment_news_url ?>>Ajouter un commentaire</a></p>

<?php