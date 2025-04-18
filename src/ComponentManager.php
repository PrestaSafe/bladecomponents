<?php

namespace App;

use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;

class ComponentManager
{
    /**
     * Le répertoire des composants
     *
     * @var string
     */
    protected $componentsPath;

    /**
     * Le compilateur Blade
     *
     * @var BladeCompiler
     */
    protected $compiler;

    /**
     * Les chemins additionnels pour les composants
     *
     * @var array
     */
    protected $additionalPaths = [];

    /**
     * Crée une nouvelle instance du gestionnaire de composants
     *
     * @param BladeCompiler $compiler
     * @param string $componentsPath
     * @return void
     */
    public function __construct(BladeCompiler $compiler, string $componentsPath)
    {
        $this->compiler = $compiler;
        $this->componentsPath = $componentsPath;
    }

    /**
     * Ajoute un chemin additionnel pour les composants
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    public function addComponentPath(string $path, string $namespace = 'components')
    {
        $this->additionalPaths[$namespace] = $path;
    }

    /**
     * Enregistre tous les composants disponibles dans le répertoire des composants
     *
     * @return void
     */
    public function registerComponents()
    {
        $files = new Filesystem();
        
        // Récupérer tous les fichiers .blade.php dans le répertoire des composants principal
        $componentFiles = $files->glob($this->componentsPath . '/*.blade.php');
        
        foreach ($componentFiles as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->registerComponent($name);
        }

        // Parcourir les chemins additionnels
        foreach ($this->additionalPaths as $namespace => $path) {
            $componentFiles = $files->glob($path . '/*.blade.php');
            
            foreach ($componentFiles as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                $this->registerComponent($name, null, $namespace);
            }
        }
    }

    /**
     * Enregistre un composant spécifique
     *
     * @param string $name
     * @param string|null $alias
     * @param string|null $namespace
     * @return void
     */
    public function registerComponent(string $name, ?string $alias = null, ?string $namespace = 'components')
    {
        $alias = $alias ?: $name;
        
        // Si un namespace est fourni, préfixer correctement pour blade
        if ($namespace !== 'components') {
            $view = $namespace . '::' . $name;
        } else {
            $view = $namespace . '.' . $name;
        }
        
        $this->compiler->component($view, $alias);
    }

    /**
     * Enregistre un composant avec une classe
     *
     * @param string $class
     * @param string|null $alias
     * @return void
     */
    public function registerClassComponent(string $class, ?string $alias = null)
    {
        $alias = $alias ?: class_basename($class);
        $this->compiler->component($class, $alias);
    }
} 