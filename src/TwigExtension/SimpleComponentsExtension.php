<?php

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

/**
 * Extension Twig simplifié pour les composants
 * 
 * Cette extension utilise des fonctions Twig pour simuler une syntaxe simplifiée:
 * {{ button({variant: 'primary', size: 'lg'}, 'Contenu') }}
 * {{ card({variant: 'primary', shadow: 'lg'}, 'Contenu', {header: 'Titre', footer: 'Pied'}) }}
 */
class SimpleComponentsExtension extends AbstractExtension
{
    /**
     * @var Environment Instance de l'environnement Twig
     */
    private $twig;
    
    /**
     * @var string Chemin de base vers les composants
     */
    private $basePath;

    /**
     * Constructeur
     * 
     * @param Environment $twig Instance de l'environnement Twig
     * @param string $basePath Chemin de base vers les composants
     */
    public function __construct(Environment $twig, string $basePath = 'components')
    {
        $this->twig = $twig;
        $this->basePath = $basePath;
    }

    /**
     * Retourne les fonctions Twig ajoutées par cette extension
     * 
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('button', [$this, 'renderButton'], ['is_safe' => ['html']]),
            new TwigFunction('card', [$this, 'renderCard'], ['is_safe' => ['html']]),
            new TwigFunction('verbatim', [$this, 'renderVerbatim'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Rend un composant button
     * 
     * @param array $props Propriétés du bouton
     * @param string|null $content Contenu du bouton
     * @return string HTML rendu
     */
    public function renderButton(array $props = [], ?string $content = null)
    {
        // Paramètres du composant
        $context = $props;
        
        // Ajouter le contenu s'il est fourni
        if ($content !== null) {
            $context['content'] = $content;
        }
        
        // Rendre le composant
        return $this->twig->render($this->basePath . '/button.html.twig', $context);
    }

    /**
     * Rend un composant card
     * 
     * @param array $props Propriétés de la carte
     * @param string|null $content Contenu principal de la carte
     * @param array $slots Slots supplémentaires (header, footer, actions)
     * @return string HTML rendu
     */
    public function renderCard(array $props = [], ?string $content = null, array $slots = [])
    {
        // Paramètres du composant
        $context = $props;
        
        // Ajouter le contenu s'il est fourni
        if ($content !== null) {
            $context['content'] = $content;
        }
        
        // Ajouter les slots
        foreach ($slots as $name => $value) {
            $context[$name] = $value;
        }
        
        // Rendre le composant
        return $this->twig->render($this->basePath . '/card.html.twig', $context);
    }

    /**
     * Rend du contenu brut non échappé (remplace raw)
     * 
     * @param string $content Contenu à rendre sans échappement
     * @return string Le contenu tel quel
     */
    public function renderVerbatim(string $content)
    {
        return $content;
    }
} 