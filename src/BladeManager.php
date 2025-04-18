<?php

namespace App;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class BladeManager
{
    /**
     * Le chemin vers le répertoire des vues
     *
     * @var string
     */
    protected $viewsPath;

    /**
     * Le chemin vers le répertoire de cache
     *
     * @var string
     */
    protected $cachePath;

    /**
     * Le container DI
     *
     * @var Container
     */
    protected $container;

    /**
     * Le compilateur Blade
     *
     * @var BladeCompiler
     */
    protected $compiler;

    /**
     * Le finder pour les vues
     *
     * @var FileViewFinder
     */
    protected $finder;

    /**
     * La factory de vues
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Le gestionnaire de composants
     *
     * @var ComponentManager
     */
    protected $componentManager;

    /**
     * Le système de fichiers
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Les directives personnalisées
     *
     * @var array
     */
    protected $directives = [];

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * Construit une nouvelle instance du BladeManager
     *
     * @param string $viewsPath Le chemin vers le répertoire des vues
     * @param string $cachePath Le chemin vers le répertoire de cache
     * @param Container|null $container Un container DI optionnel
     * @param array $config Configuration supplémentaire
     */
    public function __construct(string $viewsPath, string $cachePath, ?Container $container = null, array $config = [])
    {
        $this->viewsPath = $viewsPath;
        $this->cachePath = $cachePath;
        $this->container = $container ?? Container::getInstance();
        $this->filesystem = new Filesystem();
        $this->config = $config;
        
        $this->bootContainer();
        $this->bootCompiler();
        $this->bootViewFinder();
        $this->bootViewFactory();
        $this->bootComponentManager();
    }

    /**
     * Configure le container
     *
     * @return void
     */
    protected function bootContainer(): void
    {
        $container = $this->container;

        $container->bind('Illuminate\Contracts\View\Factory', Factory::class);
        $container->bind('Illuminate\Contracts\Foundation\Application', function () use ($container) {
            return $container;
        });

        $container->singleton('files', function () {
            return $this->filesystem;
        });
    }

    /**
     * Configure le compilateur Blade
     *
     * @return void
     */
    protected function bootCompiler(): void
    {
        // Création du répertoire de cache s'il n'existe pas
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }

        $this->compiler = $this->createCompiler($this->cachePath);
        $this->container->singleton('blade.compiler', function () {
            return $this->compiler;
        });
    }

    /**
     * Crée une nouvelle instance de BladeCompiler
     *
     * @param string $cachePath
     * @return BladeCompiler
     */
    protected function createCompiler(string $cachePath)
    {
        $compiler = new BladeCompiler(new Filesystem(), $cachePath);
        
        // Mode développement - ne pas mettre en cache les vues
        if ($this->config['dev_mode'] ?? false) {
            $compiler->setPath('/__none_existent_path__');
        }
        
        // Configuration pour s'assurer que les attributs Alpine.js sont correctement gérés
        $compiler->componentNamespace('App\\View\\Components', 'app');
        
        // Désactiver l'échappement pour les attributs qui commencent par x-
        $compiler->withoutDoubleEncoding();
        
        return $compiler;
    }

    /**
     * Configure le finder de vues
     *
     * @return void
     */
    protected function bootViewFinder(): void
    {
        $this->finder = new FileViewFinder($this->filesystem, [$this->viewsPath]);
    }

    /**
     * Configure la factory de vues
     *
     * @return void
     */
    protected function bootViewFactory(): void
    {
        $resolver = new EngineResolver();
        $resolver->register('blade', function () {
            return new CompilerEngine($this->compiler);
        });

        $dispatcher = new Dispatcher($this->container);
        $this->factory = new Factory($resolver, $this->finder, $dispatcher);

        $this->container->instance(Factory::class, $this->factory);
        $this->container->instance('view', $this->factory);
        $this->container->instance('Illuminate\Contracts\View\Factory', $this->factory);
    }

    /**
     * Configure le gestionnaire de composants
     *
     * @return void
     */
    protected function bootComponentManager(): void
    {
        $this->componentManager = new ComponentManager($this->compiler, $this->viewsPath . '/components');
    }

    /**
     * Ajoute un répertoire de vues
     *
     * @param string $path Le chemin vers le répertoire
     * @return $this
     */
    public function addViewPath(string $path): self
    {
        $this->finder->addLocation($path);
        return $this;
    }

    /**
     * Ajoute un namespace
     *
     * @param string $namespace Le namespace
     * @param string $path Le chemin vers le répertoire
     * @return $this
     */
    public function addNamespace(string $namespace, string $path): self
    {
        $this->finder->addNamespace($namespace, $path);
        return $this;
    }

    /**
     * Ajoute un chemin de composants
     *
     * @param string $path Le chemin vers le répertoire de composants
     * @param string $namespace Le namespace
     * @return $this
     */
    public function addComponentPath(string $path, string $namespace = 'components'): self
    {
        $this->componentManager->addComponentPath($path, $namespace);
        return $this;
    }

    /**
     * Enregistre un composant
     *
     * @param string $name Le nom du composant
     * @param string|null $alias L'alias du composant
     * @param string|null $namespace Le namespace du composant
     * @return $this
     */
    public function registerComponent(string $name, ?string $alias = null, ?string $namespace = 'components'): self
    {
        $this->componentManager->registerComponent($name, $alias, $namespace);
        return $this;
    }

    /**
     * Enregistre un composant de classe
     *
     * @param string $class La classe du composant
     * @param string|null $alias L'alias du composant
     * @return $this
     */
    public function registerClassComponent(string $class, ?string $alias = null): self
    {
        $this->componentManager->registerClassComponent($class, $alias);
        return $this;
    }

    /**
     * Enregistre tous les composants disponibles
     *
     * @return $this
     */
    public function registerComponents(): self
    {
        $this->componentManager->registerComponents();
        return $this;
    }

    /**
     * Ajoute une directive Blade
     *
     * @param string $name Le nom de la directive
     * @param callable $handler Le gestionnaire de la directive
     * @return $this
     */
    public function directive(string $name, callable $handler): self
    {
        $this->compiler->directive($name, $handler);
        return $this;
    }

    /**
     * Nettoie le cache
     *
     * @return $this
     */
    public function clearCache(): self
    {
        $files = $this->filesystem->glob($this->cachePath . '/*');
        foreach ($files as $file) {
            if ($this->filesystem->isFile($file)) {
                $this->filesystem->delete($file);
            }
        }
        return $this;
    }

    /**
     * Rend une vue
     *
     * @param string $view Le nom de la vue
     * @param array $data Les données à passer à la vue
     * @param array $mergeData Les données supplémentaires à fusionner
     * @return string
     */
    public function render(string $view, array $data = [], array $mergeData = []): string
    {
        return $this->factory->make($view, $data, $mergeData)->render();
    }

    /**
     * Récupère la factory de vues
     *
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * Récupère le compilateur Blade
     *
     * @return BladeCompiler
     */
    public function getCompiler(): BladeCompiler
    {
        return $this->compiler;
    }

    /**
     * Récupère le gestionnaire de composants
     *
     * @return ComponentManager
     */
    public function getComponentManager(): ComponentManager
    {
        return $this->componentManager;
    }

    /**
     * Récupère le container
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Enregistre plusieurs composants en une seule fois
     *
     * @param array $components Tableau avec la structure [
     *      ['name' => 'nom-composant', 'alias' => 'alias', 'namespace' => 'namespace'],
     *      ['name' => 'autre', 'alias' => 'autre-alias'],
     *      ...
     * ]
     * @return $this
     */
    public function addComponents(array $components): self
    {
        foreach ($components as $component) {
            $name = $component['name'];
            $alias = $component['alias'] ?? null;
            $namespace = $component['namespace'] ?? 'components';
            
            $this->registerComponent($name, $alias, $namespace);
        }
        
        return $this;
    }
} 