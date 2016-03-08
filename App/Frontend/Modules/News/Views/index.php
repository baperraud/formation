<?php
/** @var \Entity\News[] $Liste_news_a */
foreach ($Liste_news_a as $News) { ?>
	<h2 class="overflow_hidden"><a href="news-<?= $News['id'] ?>.html"><?= htmlspecialchars($News['titre']) ?></a></h2>

	<p class="overflow_hidden"><?= nl2br(htmlspecialchars($News['contenu'])) ?></p>
<?php }