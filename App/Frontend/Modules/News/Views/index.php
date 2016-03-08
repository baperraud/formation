<?php
/** @var \Entity\News[] $News_a */
foreach ($News_a as $News) { ?>
	<h2 class="overflow_hidden"><a href="news-<?= $News['id'] ?>.html"><?= htmlspecialchars($News['titre']) ?></a></h2>

	<p class="overflow_hidden"><?= nl2br(htmlspecialchars($News['contenu'])) ?></p>
<?php }