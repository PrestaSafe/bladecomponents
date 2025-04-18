<?php

namespace App;

abstract class Component
{
    /**
     * Les données pour le rendu du composant
     *
     * @var array
     */
    protected $data = [];

    /**
     * Crée une nouvelle instance de composant
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Obtient les données à passer à la vue
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Rend le composant
     *
     * @return string
     */
    public function render()
    {
        $class = get_class($this);
        $componentName = strtolower(class_basename($class));
        $view = 'components.' . $componentName;
        
        // Chemin vers la vue
        $viewPath = __DIR__ . '/views/' . str_replace('.', '/', $view) . '.blade.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("La vue du composant n'existe pas : {$viewPath}");
        }
        
        // Rendu simple avec inclusion et extraction des variables
        extract($this->data());
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    /**
     * Helper pour créer un nouveau composant
     *
     * @param array $data
     * @return static
     */
    public static function make(array $data = [])
    {
        return new static($data);
    }

    /**
     * Convertit un nom de classe en nom de base
     *
     * @param string $class
     * @return string
     */
    protected static function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
} 