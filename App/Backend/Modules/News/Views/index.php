<?php
/**
 * @var int $nombre_news
 * @var array $news_update_url_a
 * @var array $news_delete_url_a
 */
?>
<p style="text-align: center">Il y a actuellement <?= $nombre_news ?> news. En voici la liste :</p>

<table>
	<tr>
		<th>Auteur</th><th>Titre</th><th>Date d'ajout</th><th>Dernière modification</th><th>Action</th>

		<?php /** @var \Entity\News[] $News_a */
		foreach ($News_a as $News) {
			echo '
				<tr>
					<td>', htmlspecialchars($News['auteur']), '</td>
					<td>', htmlspecialchars($News['titre']), '</td>
					<td>', $News['Date_ajout']->format('d/m/Y à H\hi'), '</td>
					<td>', ($News['Date_ajout'] == $News['Date_modif'] ? '-' : 'le ' . $News['Date_modif']->format('d/m/Y à H\hi')), '</td>
					<td><a href=', $news_update_url_a[$News['id']], '><img src="/images/update.png" alt="Modifier" /></a> <a href=', $news_delete_url_a[$News['id']], '><img src="/images/delete.png" alt="Supprimer" /></a></td>
				</tr>', "\n";
		}
		?>
	</tr>
</table>