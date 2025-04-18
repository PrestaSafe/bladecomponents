<?php

/**
 * Bootstrap file pour configurer l'application et les composants
 */

use App\ComponentManager;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Configure les composants et leurs alias
 * 
 * @param ComponentManager $manager
 * @return void
 */
function configureComponents(ComponentManager $manager)
{
    // Enregistrer les composants avec leur nom de fichier
    $manager->registerComponents();
    
    // Ajouter des alias personnalisés pour les composants
    $manager->registerComponent('button-simple', 'button');
    $manager->registerComponent('button-simple', 'btn');
    $manager->registerComponent('header-simple', 'header');
    $manager->registerComponent('footer', 'footer');
    
    // Enregistrer le composant footer du module
    // Utiliser le chemin complet avec le préfixe du namespace
    $manager->registerComponent('footer', 'module-footer', 'mymodules');
    
    // Vous pouvez enregistrer d'autres composants ici
    // $manager->registerComponent('alert', 'notification');
}

/**
 * Configure les directives Blade personnalisées
 * 
 * @param BladeCompiler $compiler
 * @return void
 */
function configureDirectives(BladeCompiler $compiler)
{
    // Exemple de directive personnalisée
    $compiler->directive('uppercase', function ($expression) {
        return "<?php echo strtoupper($expression); ?>";
    });
    
    // Exemple de directive pour ajouter une classe conditionnelle
    $compiler->directive('class', function ($expression) {
        return "<?php echo e(\\App\\Helpers::classNames($expression)); ?>";
    });
} 