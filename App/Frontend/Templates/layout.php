<?php
/**
 * @var string $content
 * @var array $layout_route_a
 * @var array $menu_a
 */

use OCFram\Session;

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
        <h1><a href=<?= \OCFram\Application::getRoute('Frontend', 'News', 'index') ?>>Mon super site</a></h1>
        <p>Comment ça, il n'y a presque rien ?<br/>
            <?= Session::isAuthenticated() ? ('Bienvenue ' . Session::getAttribute('pseudo') . ' !') : 'Pas de session en cours' ?>
        </p>
    </header>

    <nav>
        <ul>
            <?php
            foreach ($menu_a as $label => $route): ?>
                <li><a href=<?= $route ?>><?= $label ?></a></li>
                <?php
            endforeach;
            ?>

        </ul>
    </nav>

    <div id="content-wrap">
        <section id="main">
            <?php if (Session::hasFlash()) echo '<p id="flash_message" style="text-align: center;">', Session::getFlash(), '</p>'; ?>

            <?= $content ?>
        </section>
    </div>

    <footer></footer>
</div>

<!--suppress JSUnresolvedLibraryURL -->
<script src="//code.jquery.com/jquery-2.2.1.min.js"></script>
<script src="/js/user_functions.js"></script>
<script src="/js/notify.js"></script>
<?php if (preg_match('`^/news-[0-9]+\.html$`', $_SERVER['REQUEST_URI'])): ?>
    <script src="/js/news_show_websocket.js"></script>
    <script src="/js/news_show.js"></script>
<?php endif; ?>
<script src="/js/WebSocket.js"></script>
</body>
</html>