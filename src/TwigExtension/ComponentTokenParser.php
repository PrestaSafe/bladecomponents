<?php

namespace App\TwigExtension;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Node\PrintNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;

/**
 * Parser de tokens pour les composants
 * 
 * Cette classe permet d'utiliser une syntaxe simplifiée comme:
 * {% button :variant="primary" :size="lg" %}Contenu{% endbutton %}
 * 
 * Et permet également d'utiliser des slots comme dans Vue.js:
 * {% card :variant="primary" %}
 *   Contenu par défaut
 *   <div slot="header">Titre</div>
 *   <div slot="footer">Pied de page</div>
 * {% endcard %}
 */
class ComponentTokenParser extends AbstractTokenParser
{
    /**
     * @var string Le nom du composant
     */
    private $componentName;
    
    /**
     * @var string Le chemin vers le template du composant
     */
    private $componentPath;

    /**
     * Constructeur
     * 
     * @param string $componentName Le nom du composant
     * @param string $componentPath Le chemin vers le template du composant
     */
    public function __construct(string $componentName, string $componentPath)
    {
        $this->componentName = $componentName;
        $this->componentPath = $componentPath;
    }

    /**
     * Parse le jeton Twig
     * 
     * @param Token $token Le jeton à parser
     * @return Node Le nœud AST généré
     */
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        
        // Initialiser les propriétés
        $props = [];
        $htmlAttrs = [];
        
        // Parser les attributs de style `:prop="value"`
        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test(Token::NAME_TYPE, '', true) && $stream->getCurrent()->getValue()[0] === ':') {
                // Obtenir le nom de la propriété (sans le ':')
                $propName = substr($stream->getCurrent()->getValue(), 1);
                $stream->next();
                
                // Consommer le '='
                $stream->expect(Token::OPERATOR_TYPE, '=');
                
                // Parser la valeur (expression)
                $propValue = $this->parser->getExpressionParser()->parseExpression();
                
                // Ajouter la propriété
                $props[$propName] = $propValue;
                
                // Pour les attributs spéciaux comme x-data, les ajouter également aux attributs HTML
                if (strpos($propName, 'x-') === 0 || strpos($propName, '@') === 0) {
                    $valueString = '';
                    if ($propValue instanceof \Twig\Node\Expression\ConstantExpression) {
                        $valueString = $propValue->getAttribute('value');
                    }
                    
                    // Si c'est un attribut @, le convertir en x-on:
                    if (strpos($propName, '@') === 0) {
                        $propName = 'x-on:' . substr($propName, 1);
                    }
                    
                    $htmlAttrs[$propName] = $valueString;
                }
            } else if ($stream->test(Token::NAME_TYPE, '', true) && 
                      (strpos($stream->getCurrent()->getValue(), 'x-') === 0 || 
                       strpos($stream->getCurrent()->getValue(), '@') === 0)) {
                // Traitement des attributs Alpine.js sans valeur (comme x-show, x-cloak, etc.)
                $attrName = $stream->getCurrent()->getValue();
                
                // Si c'est un attribut @, le convertir en x-on:
                if (strpos($attrName, '@') === 0) {
                    $attrName = 'x-on:' . substr($attrName, 1);
                }
                
                $htmlAttrs[$attrName] = true;
                $stream->next();
            } else {
                // Avancer pour éviter les boucles infinies
                $stream->next();
            }
        }
        
        // Consommer le marqueur de fin de bloc
        $stream->expect(Token::BLOCK_END_TYPE);
        
        // Stocker le contenu brut pour le bouton
        $rawContent = null;
        $cardBlocks = [];
        
        if ($this->componentName === 'button') {
            // Récupérer le code source
            $sourceContext = $stream->getSourceContext();
            $sourceCode = $sourceContext->getCode();
            
            // Obtenir la ligne actuelle pour trouver le bloc du bouton
            $currentLine = $token->getLine();
            
            // Trouver ce bouton spécifique dans le code source
            if (preg_match_all('/{% button[^%]*%}(.*?){% endbutton %}/s', $sourceCode, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $index => $match) {
                    $buttonCode = $match[0];
                    $buttonPosition = $match[1];
                    
                    // Compter les lignes pour voir si ce bouton correspond à notre ligne actuelle
                    $codeBeforeButton = substr($sourceCode, 0, $buttonPosition);
                    $lineCount = substr_count($codeBeforeButton, "\n") + 1;
                    
                    if ($lineCount <= $currentLine && $lineCount + substr_count($buttonCode, "\n") >= $currentLine) {
                        // C'est notre bouton!
                        $rawContent = $matches[1][$index][0];
                        break;
                    }
                }
            }
        } else if ($this->componentName === 'card') {
            // Récupérer le code source pour les cartes
            $sourceContext = $stream->getSourceContext();
            $sourceCode = $sourceContext->getCode();
            
            // Obtenir la ligne actuelle pour trouver le bloc de la carte
            $currentLine = $token->getLine();
            
            // Trouver cette carte spécifique dans le code source
            $cardPattern = '/{% card[^%]*%}(.*?){% endcard %}/s';
            if (preg_match_all($cardPattern, $sourceCode, $cardMatches, PREG_OFFSET_CAPTURE)) {
                foreach ($cardMatches[0] as $index => $match) {
                    $cardCode = $match[0];
                    $cardPosition = $match[1];
                    
                    // Extraire les attributs Alpine.js
                    $alpineAttrs = [];
                    if (preg_match_all('/:x-([^=]+)=["\'](.*?)["\']/s', $cardCode, $alpineMatches, PREG_SET_ORDER)) {
                        foreach ($alpineMatches as $alpineMatch) {
                            $attrName = 'x-' . $alpineMatch[1];
                            $attrValue = $alpineMatch[2];
                            $alpineAttrs[$attrName] = $attrValue;
                        }
                    }
                    
                    // Si on a des attributs Alpine.js, les ajouter au tableau withParams
                    if (!empty($alpineAttrs)) {
                        $attrsArray = [];
                        foreach ($alpineAttrs as $attrName => $attrValue) {
                            $attrsArray[] = new \Twig\Node\Expression\ConstantExpression($attrName, $lineno);
                            $attrsArray[] = new \Twig\Node\Expression\ConstantExpression($attrValue, $lineno);
                        }
                        $withParams['attrs'] = new \Twig\Node\Expression\ArrayExpression($attrsArray, $lineno);
                    }
                    
                    // Compter les lignes pour voir si cette carte correspond à notre ligne actuelle
                    $codeBeforeCard = substr($sourceCode, 0, $cardPosition);
                    $lineCount = substr_count($codeBeforeCard, "\n") + 1;
                    
                    if ($lineCount <= $currentLine && $lineCount + substr_count($cardCode, "\n") >= $currentLine) {
                        // C'est notre carte!
                        $cardContent = $cardMatches[1][$index][0];
                        
                        // Extraire les blocs
                        if (preg_match_all('/{% block\s+([a-zA-Z0-9_]+)\s+%}(.*?){% endblock %}/s', $cardContent, $blockMatches, PREG_SET_ORDER)) {
                            foreach ($blockMatches as $blockMatch) {
                                $blockName = $blockMatch[1];
                                $blockContent = $blockMatch[2];
                                
                                // Traiter les sous-composants dans le bloc
                                $blockContent = $this->processSubComponents($blockContent);
                                
                                $cardBlocks[$blockName] = trim($blockContent);
                            }
                        }
                        
                        // Extraire les slots div
                        if (preg_match_all('/<div\s+slot=["\'](.*?)["\']>(.*?)<\/div>/s', $cardContent, $slotMatches, PREG_SET_ORDER)) {
                            foreach ($slotMatches as $slotMatch) {
                                $slotName = $slotMatch[1];
                                $slotContent = $slotMatch[2];
                                
                                // Traiter les sous-composants dans le slot
                                $slotContent = $this->processSubComponents($slotContent);
                                
                                $cardBlocks[$slotName] = trim($slotContent);
                            }
                        }
                        
                        // Extraire le contenu principal (sans les slots div et les blocs)
                        $mainContent = $cardContent;
                        $mainContent = preg_replace('/{% block\s+([a-zA-Z0-9_]+)\s+%}(.*?){% endblock %}/s', '', $mainContent);
                        $mainContent = preg_replace('/<div\s+slot=["\'](.*?)["\']>(.*?)<\/div>/s', '', $mainContent);
                        $mainContent = trim($mainContent);
                        
                        // Traiter les sous-composants dans le contenu principal
                        $mainContent = $this->processSubComponents($mainContent);
                        
                        if (!empty($mainContent) && !isset($cardBlocks['content'])) {
                            $cardBlocks['content'] = $mainContent;
                        }
                        
                        break;
                    }
                }
            }
        }
        
        // Parser le contenu entre les tags (si présent)
        $body = $this->parser->subparse([$this, 'decideComponentEnd'], true);
        
        // Consommer le tag de fin ({% endbutton %}, {% endcard %}, etc.)
        $stream->expect(Token::BLOCK_END_TYPE);
        
        // Créer un tableau d'arguments pour le include
        $arguments = [];
        
        // Ajouter le template path
        $arguments[] = new \Twig\Node\Expression\ConstantExpression($this->componentPath, $lineno);
        
        // Ajouter les propriétés
        $withParams = [];
        foreach ($props as $name => $value) {
            $withParams[$name] = $value;
        }
        
        // Ajouter les attributs HTML s'il y en a
        if (!empty($htmlAttrs)) {
            $attrsArray = [];
            foreach ($htmlAttrs as $attrName => $attrValue) {
                $attrsArray[] = new \Twig\Node\Expression\ConstantExpression($attrName, $lineno);
                if ($attrValue === true) {
                    // Pour les attributs booléens comme x-show, x-cloak, etc.
                    $attrsArray[] = new \Twig\Node\Expression\ConstantExpression(true, $lineno);
                } else {
                    $attrsArray[] = new \Twig\Node\Expression\ConstantExpression($attrValue, $lineno);
                }
            }
            $withParams['attrs'] = new \Twig\Node\Expression\ArrayExpression($attrsArray, $lineno);
        }
        
        // Ajouter le contenu comme "content" s'il n'y a pas de slots
        if ($this->componentName === 'button' && $rawContent !== null) {
            $withParams['content'] = new \Twig\Node\Expression\ConstantExpression($rawContent, $lineno);
        } else if ($this->componentName === 'card' && !empty($cardBlocks)) {
            // Pour les cartes, utiliser les blocs extraits directement
            foreach ($cardBlocks as $blockName => $blockContent) {
                $withParams[$blockName] = new \Twig\Node\Expression\ConstantExpression($blockContent, $lineno);
            }
            
            // Traitement spécial pour x-data
            if (strpos($token->getValue(), 'x-data') !== false) {
                // Chercher le x-data dans la chaîne source
                $sourceCode = $stream->getSourceContext()->getCode();
                if (preg_match('/:x-data=["\'](.*?)["\']/s', $sourceCode, $matches)) {
                    $xDataValue = $matches[1];
                    
                    // Créer un tableau d'attributs ou utiliser celui existant
                    if (!isset($withParams['attrs'])) {
                        $attrsArray = [];
                    } else {
                        $attrsArray = $withParams['attrs']->getKeyValuePairs();
                        $attrsArray = array_map(function($pair) {
                            return [$pair['key']->getAttribute('value'), $pair['value']->getAttribute('value')];
                        }, $attrsArray);
                        $attrsArray = array_reduce($attrsArray, function($carry, $item) {
                            $carry[$item[0]] = $item[1];
                            return $carry;
                        }, []);
                    }
                    
                    // Ajouter x-data à attrs
                    $attrsArray['x-data'] = $xDataValue;
                    
                    // Créer un nouveau tableau d'attributs pour l'ArrayExpression
                    $newAttrsArray = [];
                    foreach ($attrsArray as $key => $value) {
                        $newAttrsArray[] = new \Twig\Node\Expression\ConstantExpression($key, $lineno);
                        $newAttrsArray[] = new \Twig\Node\Expression\ConstantExpression($value, $lineno);
                    }
                    
                    $withParams['attrs'] = new \Twig\Node\Expression\ArrayExpression($newAttrsArray, $lineno);
                }
            }
        } else {
            $withParams['content'] = new \Twig\Node\Expression\ConstantExpression($this->getNodeContent($body), $lineno);
        }
        
        // Ajouter les slots
        $slots = $this->extractSlots($body);
        foreach ($slots as $slotName => $slotContent) {
            $withParams[$slotName] = new \Twig\Node\Expression\ConstantExpression($slotContent, $lineno);
        }
        
        // Créer un tableau de paires clé-valeur pour l'ArrayExpression
        $withParamsNodes = [];
        foreach ($withParams as $key => $value) {
            $withParamsNodes[] = new \Twig\Node\Expression\ConstantExpression($key, $lineno);
            $withParamsNodes[] = $value;
        }
        
        // Créer un node Include
        return new \Twig\Node\IncludeNode(
            new \Twig\Node\Expression\ConstantExpression($this->componentPath, $lineno),
            new \Twig\Node\Expression\ArrayExpression($withParamsNodes, $lineno),
            false,
            false,
            $lineno,
            null
        );
    }
    
    /**
     * Extrait les slots du contenu
     * 
     * @param Node $body Le nœud du corps du composant
     * @return array Un tableau associatif [nom_slot => contenu_html]
     */
    private function extractSlots(Node $body): array
    {
        $slots = [];
        $content = $this->getNodeAsString($body);
        
        // Rechercher les divs avec attribut slot
        preg_match_all('/<div\s+slot=["\'](.*?)["\']>(.*?)<\/div>/s', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $slotName = $match[1];
            $slotContent = $match[2];
            $slots[$slotName] = $slotContent;
        }
        
        // Ajouter les blocs twig comme slots
        foreach ($body as $name => $subNode) {
            if ($subNode instanceof \Twig\Node\BlockNode) {
                $blockName = $subNode->getAttribute('name');
                $slots[$blockName] = $this->getBlockContent($subNode);
            }
        }
        
        return $slots;
    }
    
    /**
     * Extrait le contenu d'un bloc
     */
    private function getBlockContent(\Twig\Node\BlockNode $node): string
    {
        if ($node->hasNode('body')) {
            $content = $this->getNodeAsString($node->getNode('body'));
            
            // Traiter les sous-composants à l'intérieur des blocs
            $content = $this->processSubComponents($content);
            
            return $content;
        }
        
        return '';
    }
    
    /**
     * Traite les sous-composants dans un morceau de contenu
     */
    private function processSubComponents(string $content): string
    {
        // Traiter les boutons
        $content = preg_replace_callback(
            '/{% button ([^%]*?)%}(.*?){% endbutton %}/s',
            function($matches) {
                $attributes = $matches[1];
                $buttonContent = $matches[2];
                
                // Extraire les attributs
                $buttonAttrs = [];
                $variant = 'primary';
                $size = 'md';
                
                // Extraire les attributs spécifiques
                if (preg_match('/:variant=["\'](.*?)["\']/i', $attributes, $match)) {
                    $variant = $match[1];
                }
                
                if (preg_match('/:size=["\'](.*?)["\']/i', $attributes, $match)) {
                    $size = $match[1];
                }
                
                // Générer les classes CSS en fonction des attributs
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

                // Nous allons simplement passer le traitement des attributs Alpine.js au template Twig
                // Pour éviter les problèmes de duplication
                return "{% include 'components/button.html.twig' with {
                    'variant': '$variant',
                    'size': '$size',
                    'content': '$buttonContent',
                    'attrs': {
                        " . $this->extractAlpineAttributes($attributes) . "
                    }
                } %}";
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Extrait les attributs Alpine.js en tant que chaîne pour une inclusion dans le template
     */
    private function extractAlpineAttributes(string $attributes): string
    {
        $result = [];
        
        // Cas 1: x-* avec valeur
        if (preg_match_all('/\s+(x-[a-zA-Z0-9_:.:-]+)=["\'](.*?)["\']/i', $attributes, $alpineMatches, PREG_SET_ORDER)) {
            foreach ($alpineMatches as $match) {
                $attrName = $match[1];
                $attrValue = $match[2];
                $result[] = "'$attrName': '$attrValue'";
            }
        }
        
        // Cas 2: @* avec valeur (convertir en x-on:*)
        if (preg_match_all('/\s+@([a-zA-Z0-9_:-]+)=["\'](.*?)["\']/i', $attributes, $atMatches, PREG_SET_ORDER)) {
            foreach ($atMatches as $match) {
                $event = $match[1];
                $value = $match[2];
                $result[] = "'x-on:$event': '$value'";
            }
        }
        
        // Cas 3: x-* sans valeur
        if (preg_match_all('/\s+(x-[a-zA-Z0-9_:.:-]+)(?!\s*=)/i', $attributes, $booleanMatches)) {
            foreach ($booleanMatches[1] as $attr) {
                $attr = trim($attr);
                $result[] = "'$attr': true";
            }
        }
        
        // Cas 4: @* sans valeur
        if (preg_match_all('/\s+(@[a-zA-Z0-9_:-]+)(?!\s*=)/i', $attributes, $booleanAtMatches)) {
            foreach ($booleanAtMatches[1] as $attr) {
                $event = substr(trim($attr), 1);
                $result[] = "'x-on:$event': true";
            }
        }
        
        return implode(",\n                        ", $result);
    }
    
    /**
     * Obtient le contenu HTML du nœud
     * 
     * @param Node $node Le nœud dont on veut le contenu
     * @return string Le contenu textuel du nœud
     */
    private function getNodeContent(Node $node): string
    {
        // Vérifier si le nœud a des sous-nœuds
        if (!$node->count()) {
            return '';
        }
        
        // Pour les blocs, utiliser directement le contenu des blocs
        foreach ($node as $subNode) {
            if ($subNode instanceof \Twig\Node\BlockNode && $subNode->getAttribute('name') === 'content') {
                return $this->getBlockContent($subNode);
            }
        }
        
        $content = $this->getNodeAsString($node);
        
        // Analyser les divs avec attribut slot
        $contentWithoutSlots = $this->extractAndRemoveSlots($content);
        
        // Traiter les sous-composants
        $contentWithoutSlots = $this->processSubComponents($contentWithoutSlots);
        
        return $contentWithoutSlots;
    }
    
    /**
     * Extrait et supprime les divs avec attributs slot du contenu
     */
    private function extractAndRemoveSlots(string $content): string
    {
        // Supprimer tous les divs avec attribut slot
        $contentWithoutSlots = preg_replace('/<div\s+slot=["\'](.*?)["\']>(.*?)<\/div>/s', '', $content);
        
        // Traiter les sous-composants
        $contentWithoutSlots = $this->processSubComponents($contentWithoutSlots);
        
        return $contentWithoutSlots;
    }
    
    /**
     * Convertit un nœud en chaîne de caractères
     */
    private function getNodeAsString(Node $node): string
    {
        $content = '';
        
        foreach ($node as $subNode) {
            if ($subNode instanceof \Twig\Node\TextNode) {
                $content .= $subNode->getAttribute('data');
            } elseif ($subNode instanceof \Twig\Node\PrintNode) {
                $content .= '{{ ' . $this->getExpressionAsString($subNode->getNode('expr')) . ' }}';
            } elseif ($subNode instanceof \Twig\Node\Node) {
                $content .= $this->getNodeAsString($subNode);
            }
        }
        
        // Nettoyage du contenu pour enlever les espaces inutiles
        return trim($content);
    }
    
    /**
     * Convertit une expression en chaîne de caractères
     */
    private function getExpressionAsString(\Twig\Node\Expression\AbstractExpression $expr): string
    {
        if ($expr instanceof \Twig\Node\Expression\ConstantExpression) {
            return (string) $expr->getAttribute('value');
        } elseif ($expr instanceof \Twig\Node\Expression\NameExpression) {
            return $expr->getAttribute('name');
        } else {
            // Pour les autres types d'expressions, on retourne un placeholder
            return 'expression';
        }
    }

    /**
     * Décide quand terminer le parsing du contenu du composant
     * 
     * @param Token $token Le jeton à vérifier
     * @return bool True si c'est la fin du composant
     */
    public function decideComponentEnd(Token $token): bool
    {
        return $token->test('end' . $this->componentName);
    }

    /**
     * Retourne le tag géré par ce parser
     * 
     * @return string Le nom du tag
     */
    public function getTag(): string
    {
        return $this->componentName;
    }
} 