<?php

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension Twig qui ajoute la syntaxe simplifiée pour les composants
 * 
 * Cette extension permet d'utiliser une syntaxe comme:
 * {% button :variant="primary" :size="lg" %}Contenu{% endbutton %}
 * {% card :variant="primary" :shadow="lg" %}Contenu{% endcard %}
 */
class TagComponentsExtension extends AbstractExtension
{
    /**
     * @var array Liste des composants à enregistrer
     */
    private $components = [];

    /**
     * Constructeur
     * 
     * @param array $components Liste des composants sous forme [nom => chemin]
     */
    public function __construct(array $components = [])
    {
        // Composants par défaut
        $defaultComponents = [
            'button' => 'components/button.html.twig',
            'card' => 'components/card.html.twig',
        ];
        
        // Fusionner avec les composants personnalisés
        $this->components = array_merge($defaultComponents, $components);
    }

    /**
     * Retourne les parsers de tokens Twig ajoutés par cette extension
     * 
     * @return array
     */
    public function getTokenParsers()
    {
        $parsers = [];
        
        foreach ($this->components as $name => $path) {
            $parsers[] = new ComponentTokenParser($name, $path);
        }
        
        return $parsers;
    }

    /**
     * Ajoute un composant
     * 
     * @param string $name Nom du composant (utilisé pour le tag)
     * @param string $path Chemin vers le template du composant
     * @return $this
     */
    public function addComponent(string $name, string $path)
    {
        $this->components[$name] = $path;
        return $this;
    }
} 