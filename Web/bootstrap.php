<?php
const DEFAULT_APP = 'Frontend';

// Si l'application n'est pas valide, on charge celle par défaut qui générera une erreur 404
if (!isset($_GET['app']) || !file_exists(__DIR__ . '/../App/' . $_GET['app']))
	$_GET['app'] = DEFAULT_APP;

// On commence par inclure la classe nous permettant d'enregistrer nos autoload
require __DIR__ . '/../lib/OCFram/SplClassLoader.php';

// On va ensuite enregistrer les autoloads correspondant à chaque vendor (OCFram, App, Model, etc.)
$OCFram_loader = new SplClassLoader('OCFram', __DIR__ . '/../lib');
$OCFram_loader->register();

$App_loader = new SplClassLoader('App', __DIR__ . '/..');
$App_loader->register();

$Model_loader = new SplClassLoader('Model', __DIR__ . '/../lib/vendors');
$Model_loader->register();

$Entity_loader = new SplClassLoader('Entity', __DIR__ . '/../lib/vendors');
$Entity_loader->register();

// On déduit enfin le nom de la classe avant de l'instancier
$app_class_name = 'App\\' . $_GET['app'] . '\\' . $_GET['app'] . 'Application';
/** @var \OCFram\Application $App */
$App = new $app_class_name;
$App->run();