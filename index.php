<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/bootstrap.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\AnonymousComponent;
use App\ComponentManager;

// Répertoires pour les templates et le cache
$viewsPath = __DIR__ . '/src/views';
$cachePath = __DIR__ . '/cache';
$componentsPath = $viewsPath . '/components';
$modulesPath = __DIR__ . '/modules';


// Création du répertoire de cache s'il n'existe pas
if (!is_dir($cachePath)) {
    mkdir($cachePath, 0755, true);
}

// Nettoyer le répertoire de cache pour forcer une recompilation
$files = glob($cachePath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

// Initialisation du container
$container = Container::getInstance();

// Configuration des bindings nécessaires
$container->bind('Illuminate\Contracts\View\Factory', Factory::class);
$container->bind('Illuminate\Contracts\Foundation\Application', function () use ($container) {
    return $container;
});

$container->singleton('files', function () {
    return new Filesystem();
});

// Création du BladeCompiler
$filesystem = new Filesystem();
$compiler = new BladeCompiler($filesystem, $cachePath);

// Configuration des directives personnalisées
configureDirectives($compiler);

// Enregistrement des bindings pour le view engine
$container->singleton('blade.compiler', function () use ($compiler) {
    return $compiler;
});

// Configuration du resolver
$resolver = new EngineResolver();
$resolver->register('blade', function () use ($compiler) {
    return new CompilerEngine($compiler);
});

// Configuration du view finder
$finder = new FileViewFinder($filesystem, [$viewsPath]);
$finder->addNamespace('prettyblocks', $componentsPath);
$finder->addNamespace('mymodules', $modulesPath);

// Création de la factory de views
$dispatcher = new Dispatcher($container);
$factory = new Factory($resolver, $finder, $dispatcher);

// Partage de la factory
$container->instance(Factory::class, $factory);
$container->instance('view', $factory);
$container->instance('Illuminate\Contracts\View\Factory', $factory);

// Configurer le ComponentManager pour enregistrer nos composants
$componentManager = new ComponentManager($compiler, $componentsPath);

// Ajouter le chemin des modules au ComponentManager avant la configuration
$componentManager->addComponentPath($modulesPath, 'mymodules');

// Configurer les composants après avoir ajouté tous les chemins
configureComponents($componentManager);

// Démarrer la sortie tampon pour capturer les erreurs éventuelles
ob_start();

try {
    // Rendre notre template
    echo $factory->make('hello', ['name' => 'World'])->render();
} catch (Exception $e) {
    echo "<h1>Erreur</h1>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}

// Afficher la sortie
echo ob_get_clean(); 