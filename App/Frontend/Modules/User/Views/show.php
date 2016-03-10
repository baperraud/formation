<?php
use \OCFram\Session;

/**
 * @var string $pseudo
 * @var \Entity\News[] $News_a
 * @var array $news_url_a
 */

?>

	<h2><?= Session::getAttribute('pseudo') == $pseudo ?
			'Mon profil' : 'Profil de ' . $pseudo;
		?></h2>

	<p><?= Session::getAttribute('pseudo') == $pseudo ?
			'Bienvenue sur votre page de profil !' : 'Vous visitez actuellement le profil de ' . $pseudo . '.';
		?></p>

	<h3><?= Session::getAttribute('pseudo') == $pseudo ?
			'Voici la liste des news que vous avez postées :' : 'Liste des news postées par ce membre :';
		?></h3>

	<table>
		<tr>
			<th>Titre</th><th>Contenu</th><th>Date d'ajout</th><th>Dernière modification</th><!--<th>Action</th>-->

			<?php /** @var \Entity\News[] $News_a */
			foreach ($News_a as $News) {
				echo '
				<tr>
					<td><a href=', $news_url_a[$News['id']] ,'>', htmlspecialchars($News['titre']), '</a></td>
					<td>', htmlspecialchars($News['contenu']), '</td>
					<td>', $News['Date_ajout']->format('d/m/Y à H\hi'), '</td>
					<td>', ($News['Date_ajout'] == $News['Date_modif'] ? '-' : 'le ' . $News['Date_modif']->format('d/m/Y à H\hi')), '</td>
					<!--<td><a href=', $news_update_url_a[$News['id']], '><img src="/images/update.png" alt="Modifier" /></a> <a href=', $news_delete_url_a[$News['id']], '><img src="/images/delete.png" alt="Supprimer" /></a></td>-->
				</tr>', "\n";
			}
			?>
		</tr>
	</table>

<?php

