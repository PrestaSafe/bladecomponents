<?php
/**
 * Exemple d'utilisation des composants Twig avec une syntaxe inspirée de Vue.js
 * 
 * Ce fichier montre comment initialiser Twig avec l'extension TagComponentsExtension
 * pour utiliser une syntaxe proche de Vue.js pour les composants.
 */

// Chemin d'accès au dossier des vues
$viewsPath = __DIR__ . '/src/views';

// Vérifier si Composer est utilisé
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Composer est requis. Exécutez "composer install" pour installer les dépendances.');
}

require_once __DIR__ . '/vendor/autoload.php';

// Configuration de Twig
$loader = new \Twig\Loader\FilesystemLoader($viewsPath);
$twig = new \Twig\Environment($loader, [
    'cache' => false, // Désactiver le cache pour le développement
    'debug' => true,
    'auto_reload' => true
]);

// Ajout de l'extension de debug
$twig->addExtension(new \Twig\Extension\DebugExtension());

// Ajout de l'extension pour la syntaxe de tag inspirée de Vue.js
$tagExtension = new \App\TwigExtension\TagComponentsExtension();

// Vous pouvez ajouter d'autres composants personnalisés ici
// $tagExtension->addComponent('modal', 'components/modal.html.twig');
// $tagExtension->addComponent('alert', 'components/alert.html.twig');

$twig->addExtension($tagExtension);

// Fonction pour traiter les composants avec leurs attributs Alpine
function processComponentsWithAlpine($content) {
    // Traiter les balises {% button %} pour préserver les attributs Alpine et générer directement le HTML
    $content = preg_replace_callback(
        '/{% button ([^%]*?)%}(.*?){% endbutton %}/s',
        function($matches) {
            $attrs = $matches[1];
            $content = $matches[2];
            
            // Extraire les attributs de style
            $variant = 'primary';
            $size = 'md';
            $type = 'button';
            
            if (preg_match('/:variant=["\'](.*?)["\']/i', $attrs, $match)) {
                $variant = $match[1];
            }
            
            if (preg_match('/:size=["\'](.*?)["\']/i', $attrs, $match)) {
                $size = $match[1];
            }
            
            if (preg_match('/:type=["\'](.*?)["\']/i', $attrs, $match)) {
                $type = $match[1];
            }
            
            // Extraire les attributs Alpine
            $alpineAttrs = '';
            
            // Attributs x-* avec ou sans valeur
            if (preg_match_all('/(x-[a-zA-Z0-9_:.:-]+)(?:=(["\'])(.*?)\2)?/i', $attrs, $xMatches, PREG_SET_ORDER)) {
                foreach ($xMatches as $match) {
                    $attrName = $match[1];
                    if (isset($match[3])) {
                        $alpineAttrs .= ' ' . $attrName . '="' . $match[3] . '"';
                    } else {
                        $alpineAttrs .= ' ' . $attrName;
                    }
                }
            }
            
            // Attributs @* (raccourcis pour x-on:*)
            if (preg_match_all('/(@[a-zA-Z0-9_:-]+)(?:=(["\'])(.*?)\2)?/i', $attrs, $atMatches, PREG_SET_ORDER)) {
                foreach ($atMatches as $match) {
                    $event = substr($match[1], 1);
                    if (isset($match[3])) {
                        $alpineAttrs .= ' x-on:' . $event . '="' . $match[3] . '"';
                    } else {
                        $alpineAttrs .= ' x-on:' . $event;
                    }
                }
            }
            
            // Générer les classes CSS
            $variantClasses = [
                'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
                'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
                'outline' => 'bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
                'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
                'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
                'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500'
            ];
            
            $sizeClasses = [
                'sm' => 'px-2.5 py-1.5 text-xs',
                'md' => 'px-4 py-2 text-sm',
                'lg' => 'px-6 py-3 text-base'
            ];
            
            $baseClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
            $variantClass = isset($variantClasses[$variant]) ? $variantClasses[$variant] : $variantClasses['primary'];
            $sizeClass = isset($sizeClasses[$size]) ? $sizeClasses[$size] : $sizeClasses['md'];
            
            $classes = "$baseClasses $variantClass $sizeClass";
            
            return "<button type=\"$type\" class=\"$classes\"$alpineAttrs>$content</button>";
        },
        $content
    );
    
    // Traiter les balises {% card %} 
    // On les laisse telles quelles pour être traitées par Twig, mais on pourrait
    // aussi implémenter un rendu direct similaire aux boutons si nécessaire
    
    return $content;
}

// Rendre le template avec la syntaxe inspirée de Vue.js
try {
    // Récupérer le contenu source du template
    $templateContent = file_get_contents($viewsPath . '/working-demo.html.twig');
    
    // Prétraiter le contenu pour gérer les attributs Alpine dans les composants
    $processedContent = processComponentsWithAlpine($templateContent);
    
    // Créer un nouveau template avec le contenu prétraité
    $template = $twig->createTemplate($processedContent);
    
    // Rendre le template
    $output = $template->render();
    
    // Ajouter manuellement l'attribut x-data à la première carte
    $output = preg_replace(
        '/<div class="overflow-hidden border border-gray-200 bg-white shadow rounded-md "\s*>/',
        '<div class="overflow-hidden border border-gray-200 bg-white shadow rounded-md" x-data="{count: 0, open: false}">',
        $output,
        1
    );
    
    echo $output;
} catch (\Exception $e) {
    // Afficher une erreur plus propre en cas de problème
    echo '<h1 style="color: red">Erreur Twig</h1>';
    echo '<pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ccc;">';
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
    
    // Afficher la trace complète en mode développement
    if (isset($twig) && $twig->isDebug()) {
        echo '<h2>Trace:</h2>';
        echo '<pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ccc;">';
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    }
} 