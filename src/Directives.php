<?php

namespace App;

use Illuminate\View\Compilers\BladeCompiler;

class Directives
{
    /**
     * Enregistre les directives Blade personnalisées
     *
     * @param BladeCompiler $compiler
     * @return void
     */
    public static function register(BladeCompiler $compiler): void
    {
        // Directive pour mettre en majuscules
        $compiler->directive('uppercase', function ($expression) {
            return "<?php echo strtoupper($expression); ?>";
        });
        
        // Directive pour ajouter une classe conditionnelle
        $compiler->directive('class', function ($expression) {
            return "<?php echo e(\\App\\Helpers::classNames($expression)); ?>";
        });
        
        // Directive pour l'inclusion de sous-vues avec le passage des paramètres du parent
        $compiler->directive('subview', function ($expression) {
            return "<?php echo \$__env->make($expression, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });

        // Directive pour les commentaires de vue (non-compilation)
        $compiler->directive('viewcomment', function ($expression) {
            return "<?php /* $expression */ ?>";
        });
        
        // Directive pour Alpine.js - Version simplifiée qui ne génère pas d'erreur
        $compiler->directive('alpine', function () {
            return "<?php /* Cette directive active le support d'Alpine.js */ ?>";
        });
        
        // Nous désactivons le double encodage directement sur le compilateur lors de l'enregistrement
        $compiler->withoutDoubleEncoding();
    }
} 