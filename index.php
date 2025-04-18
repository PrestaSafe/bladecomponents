<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\BladeManager;
use App\Directives;

// Définir les chemins
$viewsPath = __DIR__ . '/src/views';
$cachePath = __DIR__ . '/cache';
$componentsPath = $viewsPath . '/components';
$modulesPath = __DIR__ . '/modules';

// Créer une instance de BladeManager avec la configuration pour le mode développement
$blade = new BladeManager($viewsPath, $cachePath, null, [
    'dev_mode' => true  // Activer le mode développement
]);

// Nettoyage du cache pour le développement
$blade->clearCache();

// Configuration des namespaces
$blade->addNamespace('prettyblocks', $componentsPath);
$blade->addNamespace('mymodules', $modulesPath);

// Configuration des chemins de composants
$blade->addComponentPath($componentsPath);
$blade->addComponentPath($modulesPath, 'mymodules');

// Enregistrer les directives personnalisées
Directives::register($blade->getCompiler());

// Enregistrer les composants
$blade->registerComponents();

// Enregistrement manuel des composants avec leurs alias
$blade->addComponents([
    // Composants dans src/views/components
    ['name' => 'button', 'alias' => 'button'],
    ['name' => 'button-simple', 'alias' => 'btn'],
    ['name' => 'header-simple', 'alias' => 'header'],
    
    // Composants dans /modules
    ['name' => 'footer', 'alias' => 'module-footer', 'namespace' => 'mymodules'],
]);

// Démarrer la sortie tampon pour capturer les erreurs éventuelles
ob_start();

try {
    // Rendre notre template avec les données
    echo $blade->render('hello', ['name' => 'World']);
} catch (Exception $e) {
    echo "<h1>Erreur</h1>";
    echo "<pre>" . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "</pre>";
}

// Afficher la sortie
echo ob_get_clean(); 