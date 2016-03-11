<?php
use \OCFram\Session;

/**
 * @var \Entity\User $User
 * @var \Entity\News[] $News_a
 * @var \Entity\Comment[] $Comment_a
 * @var array $news_url_a
 * @var array $news_update_url_a
 * @var array $news_delete_url_a
 * @var array $comment_news_url_a
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 */

?>

	<h2><?= Session::getAttribute('pseudo') == $User['pseudonym'] ?
			'Mon profil' : 'Profil de ' . $User['pseudonym'];
		?></h2>

	<p><?= Session::getAttribute('pseudo') == $User['pseudonym'] ?
			'Bienvenue sur votre page de profil !' : 'Vous visitez actuellement le profil de ' . $User['pseudonym'] . '.';
		?></p>

	<h3><?= Session::getAttribute('pseudo') == $User['pseudonym'] ?
			'Voici la liste des news que vous avez postées :' : 'Liste des news postées par ce membre :';
		?></h3>

	<table id="news_user_view">
		<tr>
			<th>Titre</th><th>Contenu</th><th>Date d'ajout</th><th>Dernière modification</th><?= Session::getAttribute('pseudo') == $User['pseudonym'] ? '<th>Action</th>' : '' ?>

			<?php /** @var \Entity\News[] $News_a */
			foreach ($News_a as $News) {
				echo '
				<tr>
					<td><a href=', $news_url_a[$News['id']] ,'>', htmlspecialchars($News['titre']), '</a></td>
					<td>', htmlspecialchars($News['contenu']), '</td>
					<td>', $News['Date_ajout']->format('d/m/Y à H\hi'), '</td>
					<td>', ($News['Date_ajout'] == $News['Date_modif'] ? '-' : 'le ' . $News['Date_modif']->format('d/m/Y à H\hi')), '</td>';

				if (Session::getAttribute('pseudo') == $User['pseudonym']) {
					echo '
					<td><a href=', $news_update_url_a[$News['id']], '><img src="/images/update.png" alt="Modifier" /></a> <a href=', $news_delete_url_a[$News['id']], '><img src="/images/delete.png" alt="Supprimer" /></a></td>
				</tr>', "\n";
				}
			}
			?>
		</tr>
	</table>

	<h3><?= Session::getAttribute('pseudo') == $User['pseudonym'] ?
			'Voici la liste des commentaires que vous avez postés :' : 'Liste des commentaires postés par ce membre :';
		?></h3>

	<table id="comments_user_view">
		<tr>
			<th>Contenu</th><th>Date</th><?= Session::getAttribute('pseudo') == $User['pseudonym'] ? '<th>Action</th>' : '' ?>

			<?php
			foreach ($Comment_a as $Comment) {
				echo '
				<tr>
					<td><a href=', $comment_news_url_a[$Comment['id']], '#' , 'commentaire-'.$Comment['id'] ,'>', htmlspecialchars($Comment['contenu']), '</a></td>
					<td>', $Comment['Date']->format('d/m/Y à H\hi'), '</td>';

				if (Session::getAttribute('pseudo') == $User['pseudonym']) {
					echo '
					<td><a href=', $comment_update_url_a[$Comment['id']], '><img src="/images/update.png" alt="Modifier" /></a> <a href=', $comment_delete_url_a[$Comment['id']], '><img src="/images/delete.png" alt="Supprimer" /></a></td>
				</tr>', "\n";
				}
			}
			?>
		</tr>
	</table>

<?php

