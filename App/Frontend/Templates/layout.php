<!DOCTYPE html>
<html>
<head>
	<title>
		<?= isset($title) ? $title : 'Mon super site' ?>
	</title>

	<meta charset="utf-8" />

	<link rel="stylesheet" href="/css/Envision.css" type="text/css" />
</head>

<body>
<div id="wrap">
	<header>
		<h1><a href="/">Mon super site</a></h1>
		<p>Comment ça, il n'y a presque rien ?<br />
		<?= $User->isAuthenticated() ? 'Session ouverte' : 'Pas de session en cours' ?></p>
	</header>

	<nav>
		<ul>
			<li><a href="/">Accueil</a></li>
			<?php
			if ($User->isAuthenticated()):
				if ($User->getAttribute('admin') === true): ?>
					<li><a href="/admin/">Admin</a></li>
					<li><a href="/admin/news-insert.html">Ajouter une news</a></li>
					<li><a href="/admin/logout.html">Se déconnecter</a></li>
			<?php
				else: ?>
			<li><a href="/admin/logout.html">Se déconnecter</a></li>
			<?php
				endif;
			else: ?>
				<li><a href="/login">Se connecter</a></li>
				<li><a href="/admin/login">Se connecter (espace admin)</a></li>
			<?php
			endif; ?>
		</ul>
	</nav>

	<div id="content-wrap">
		<section id="main">
			<?php if ($User->hasFlash()) echo '<p style="text-align: center;">', $User->getFlash(), '</p>'; ?>

			<?= $content ?>
		</section>
	</div>

	<footer></footer>
</div>
</body>
</html>