<?php
/**
 * @var string $content
 * @var array $layout_route_a
 */

use \OCFram\Session;

?>

<!DOCTYPE html>
<html>
<head>
	<title>
		<?= isset($title) ? $title : 'Mon super site' ?>
	</title>

	<meta charset="utf-8"/>

	<link rel="stylesheet" href="/css/Envision.css" type="text/css"/>
</head>

<body>
<div id="wrap">
	<header>
		<h1><a href=<?= $layout_route_a['accueil'] ?>>Mon super site</a></h1>
		<p>Comment ça, il n'y a presque rien ?<br/>
			<?= Session::isAuthenticated() ? ('Bienvenue ' . Session::getAttribute('pseudo') . ' !') : 'Pas de session en cours' ?>
		</p>
	</header>

	<nav>
		<ul>
			<li><a href=<?= $layout_route_a['accueil'] ?>>Accueil</a></li>
			<?php
			if (Session::isAuthenticated()):
				if (Session::getAttribute('admin') == 1): ?>
					<li><a href=<?= $layout_route_a['admin'] ?>>Admin</a></li>
					<li><a href=<?= $layout_route_a['admin_insert'] ?>>Ajouter une news</a></li>
					<?php
				endif; ?>
				<li><a href=<?= $layout_route_a['profil'] ?>>Mon profil</a></li>
				<li><a href=<?= $layout_route_a['logout'] ?>>Se déconnecter</a></li>
				<?php
			else: ?>
				<li><a href=<?= $layout_route_a['login'] ?>>Se connecter</a></li>
				<li><a href=<?= $layout_route_a['signup'] ?>>S'inscrire</a></li>
				<?php
			endif; ?>
		</ul>
	</nav>

	<div id="content-wrap">
		<section id="main">
			<?php if (Session::hasFlash()) echo '<p style="text-align: center;">', Session::getFlash(), '</p>'; ?>

			<?= $content ?>
		</section>
	</div>

	<footer></footer>
</div>
</body>
</html>