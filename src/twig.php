<?php
/**
 * Exemple d'utilisation des composants Twig
 * 
 * Ce fichier montre comment initialiser Twig et rendre la page de démonstration
 * qui utilise les composants card et button.
 */

// Chemin d'accès au dossier des vues
$viewsPath = __DIR__ . '/views';

// Vérifier si Composer est utilisé
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    die('Composer est requis. Exécutez "composer install" pour installer les dépendances.');
}

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;
use Twig\Error\Error;
use App\TwigExtension\TagComponentsExtension;
use App\TwigExtension\SimpleComponentsExtension;

// Configuration de Twig
$loader = new FilesystemLoader($viewsPath);
$twig = new Environment($loader, [
    'cache' => false, // Désactiver le cache pour simplifier le développement
    'debug' => true,
    'auto_reload' => true
]);

// Ajout de l'extension de debug pour afficher d'éventuels problèmes
$twig->addExtension(new DebugExtension());

// Fonction pour aider à déboguer
$twig->addFunction(new TwigFunction('dump', function ($var) {
    var_dump($var);
}));

// Ajout de l'extension pour les tags de composants style Vue.js
$twig->addExtension(new TagComponentsExtension());

// Ajout de l'extension pour les fonctions de composants (en option)
$twig->addExtension(new SimpleComponentsExtension($twig));

// Rendre le template de démonstration
try {
    echo $twig->render('demo-twig.html.twig');
} catch (Error $e) {
    // Afficher une erreur plus propre en cas de problème
    echo '<h1>Erreur Twig</h1>';
    echo '<p><strong>' . get_class($e) . '</strong>: ' . $e->getMessage() . '</p>';
    echo '<p>Dans le fichier <strong>' . $e->getSourceContext()->getPath() . '</strong> à la ligne <strong>' . $e->getLine() . '</strong></p>';
    
    // Afficher la trace complète en mode développement
    echo '<h2>Trace</h2>';
    echo '<pre>';
    echo $e->getTraceAsString();
    echo '</pre>';
} 