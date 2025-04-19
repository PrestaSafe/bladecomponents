<?php

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Node\Node;

/**
 * Extension Twig pour déboguer et fournir une méthode alternative pour les composants
 */
class DebugComponentsExtension extends AbstractExtension
{
    /**
     * @var Environment L'environnement Twig
     */
    private $twig;
    
    /**
     * Constructeur
     * 
     * @param Environment $twig L'environnement Twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * Retourne les fonctions Twig ajoutées par cette extension
     * 
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('debugNode', [$this, 'debugNode'], ['is_safe' => ['html']]),
            new TwigFunction('component', [$this, 'renderComponent'], ['is_safe' => ['html']]),
        ];
    }
    
    /**
     * Affiche des informations de débogage sur un noeud Twig
     */
    public function debugNode($node)
    {
        $info = [
            'class' => get_class($node),
            'line' => $node->getTemplateLine(),
            'subnodes' => []
        ];
        
        // Récupérer les sous-noeuds si disponibles
        if (method_exists($node, 'getNode')) {
            foreach ($node->getNodes() as $name => $subnode) {
                $info['subnodes'][$name] = get_class($subnode);
            }
        }
        
        // Formater le résultat
        $output = '<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">';
        $output .= '<h3 style="margin-top: 0;">Informations sur le noeud</h3>';
        $output .= '<pre>' . htmlspecialchars(json_encode($info, JSON_PRETTY_PRINT)) . '</pre>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Rend un composant de manière simplifiée
     * 
     * @param string $name Nom du composant (ex: 'card', 'button')
     * @param array $props Propriétés du composant
     * @param string $content Contenu principal du composant
     * @param array $slots Slots nommés (header, footer, etc.)
     * @return string HTML rendu
     */
    public function renderComponent($name, array $props = [], $content = '', array $slots = [])
    {
        // Chemin vers le template du composant
        $componentPath = '@' . $name . '.html.twig';
        
        try {
            // Préparer les variables pour le template
            $variables = $props;
            $variables['slot'] = $content;
            
            // Ajouter les slots nommés
            foreach ($slots as $slotName => $slotContent) {
                $variables[$slotName . '_slot'] = $slotContent;
            }
            
            // Afficher des informations de débogage si demandé
            if (isset($props['debug']) && $props['debug']) {
                return $this->debugComponentContext($name, $variables);
            }
            
            // Rendre le template
            return $this->twig->render($componentPath, $variables);
            
        } catch (\Exception $e) {
            // En cas d'erreur, afficher un message d'erreur formaté
            return $this->renderErrorMessage($name, $e, $props);
        }
    }
    
    /**
     * Affiche des informations de débogage sur le contexte d'un composant
     */
    private function debugComponentContext($name, array $variables)
    {
        $output = '<div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0;">';
        $output .= '<h3 style="margin-top: 0;">Débogage du composant: ' . htmlspecialchars($name) . '</h3>';
        $output .= '<h4>Variables:</h4>';
        $output .= '<pre>' . htmlspecialchars(json_encode($variables, JSON_PRETTY_PRINT)) . '</pre>';
        $output .= '<h4>Chemin du template:</h4>';
        $output .= '<pre>@' . htmlspecialchars($name) . '.html.twig</pre>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Génère un message d'erreur formaté
     */
    private function renderErrorMessage($name, \Exception $e, array $props)
    {
        $output = '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0;">';
        $output .= '<h3 style="margin-top: 0; color: #721c24;">Erreur lors du rendu du composant: ' . htmlspecialchars($name) . '</h3>';
        $output .= '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        $output .= '<p><strong>Propriétés:</strong></p>';
        $output .= '<pre>' . htmlspecialchars(json_encode($props, JSON_PRETTY_PRINT)) . '</pre>';
        $output .= '</div>';
        
        return $output;
    }
} 