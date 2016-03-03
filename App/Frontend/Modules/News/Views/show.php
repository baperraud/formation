<?php /** @var \Entity\News $News */ ?>
<p>Par <em><?= $News['auteur'] ?></em>, le <?= $News['Date_ajout']->format('d/m/Y à H\hi') ?></p>

<h2><?= $News['titre'] ?></h2>

<p><?= nl2br($News['contenu']) ?></p>