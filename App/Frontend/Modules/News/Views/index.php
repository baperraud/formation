<?php
/** @var \Entity\News[] $Liste_news_a */
foreach ($Liste_news_a as $News) { ?>
	<h2><a href="news-<?= $News['id'] ?>.html"><?= $News['titre'] ?></a></h2>

	<p><?= nl2br($News['contenu']) ?></p>
<?php }