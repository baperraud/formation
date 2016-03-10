<?php
/**
 * @var \Entity\News[] $News_a
 * @var array $news_url_a
 */

foreach ($News_a as $News): ?>
	<h2 class="overflow_hidden"><a href=<?= $news_url_a[$News['id']] ?>><?= htmlspecialchars($News['titre']) ?></a></h2>

	<p class="overflow_hidden"><?= htmlspecialchars($News['contenu']) ?></p>
	<?php
endforeach;