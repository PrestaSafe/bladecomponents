<?php

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

/**
 * Extension Twig pour faciliter l'utilisation des composants
 */
class ComponentExtension extends AbstractExtension
{
    /**
     * @var string Chemin vers les composants
     */
    private $componentsPath;
    
    /**
     * @var Environment Instance de l'environnement Twig
     */
    private $twig;

    /**
     * Constructeur
     * 
     * @param Environment $twig Instance de l'environnement Twig
     * @param string $componentsPath Chemin vers les composants
     */
    public function __construct(Environment $twig, string $componentsPath = 'components')
    {
        $this->twig = $twig;
        $this->componentsPath = $componentsPath;
    }

    /**
     * Retourne les fonctions Twig ajoutées par cette extension
     * 
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('card', [$this, 'renderCard'], ['is_safe' => ['html']]),
            new TwigFunction('button', [$this, 'renderButton'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Rend un composant card
     * 
     * @param array $props Propriétés du composant
     * @param string|null $content Contenu du composant
     * @param array $slots Slots supplémentaires (header, actions, footer, etc.)
     * @return string HTML rendu
     */
    public function renderCard(array $props = [], ?string $content = null, array $slots = [])
    {
        $templatePath = $this->componentsPath . '/card.html.twig';
        
        $context = $props;
        
        if ($content !== null) {
            $context['content'] = $content;
        }
        
        // Ajouter les slots (header, actions, footer)
        foreach ($slots as $slotName => $slotContent) {
            $context[$slotName] = $slotContent;
        }
        
        return $this->twig->render($templatePath, $context);
    }

    /**
     * Rend un composant button
     * 
     * @param array $props Propriétés du composant
     * @param string|null $content Contenu du composant
     * @return string HTML rendu
     */
    public function renderButton(array $props = [], ?string $content = null)
    {
        $templatePath = $this->componentsPath . '/button.html.twig';
        
        $context = $props;
        
        if ($content !== null) {
            $context['content'] = $content;
        }
        
        return $this->twig->render($templatePath, $context);
    }
} 