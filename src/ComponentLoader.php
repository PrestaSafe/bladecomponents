<?php

namespace App;

class ComponentLoader
{
    /**
     * Retourne l'instance du composant à partir du nom et des attributs
     *
     * @param string $component Le nom du composant
     * @param array $attributes Les attributs à passer au composant
     * @return string Le contenu HTML du composant rendu
     */
    public static function render($component, array $attributes = [])
    {
        // Définir plusieurs chemins de composants
        $componentPaths = [
            __DIR__ . '/views/components/' . $component . '.blade.php',
            __DIR__ . '/modules/' . $component . '.blade.php',
            // __DIR__ . '/components/' . $component . '.blade.php',
        ];
        
        $componentPath = null;
        
        // Vérifier chaque chemin jusqu'à trouver un fichier existant
        foreach ($componentPaths as $path) {
            if (file_exists($path)) {
                $componentPath = $path;
                break;
            }
        }

        
        if ($componentPath === null) {
            throw new \Exception("Le composant '{$component}' n'existe pas dans les emplacements disponibles");
        }
        
        // Extraire le contenu du fichier
        $content = file_get_contents($componentPath);
        
        // Analyser les props du composant
        $props = self::extractProps($content);
        
        // Fusionner les attributs avec les props par défaut
        foreach ($props as $key => $defaultValue) {
            if (!isset($attributes[$key])) {
                $attributes[$key] = $defaultValue;
            }
        }
        
        // Créer un conteneur pour les attributs
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attributeString .= " {$key}";
                }
            } else {
                $attributeString .= " {$key}=\"{$value}\"";
            }
        }
        
        return "<{$component} {$attributeString}></{$component}>";
    }
    
    /**
     * Extrait les props et leurs valeurs par défaut à partir du contenu du composant
     *
     * @param string $content Le contenu du fichier composant
     * @return array Les props et leurs valeurs par défaut
     */
    private static function extractProps($content)
    {
        $props = [];
        
        // Regex simplifiée pour extraire les props
        if (preg_match('/@props\(\[(.*?)\]\)/s', $content, $matches)) {
            $propsString = $matches[1];
            
            // Extraction des propriétés individuelles
            preg_match_all("/'([^']+)'\s*=>\s*([^,]+),?/", $propsString, $propMatches, PREG_SET_ORDER);
            
            foreach ($propMatches as $match) {
                $name = $match[1];
                $value = trim($match[2]);
                
                // Conversion de la valeur
                if ($value === 'true') {
                    $value = true;
                } elseif ($value === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = $value + 0; // Conversion en nombre
                } elseif (preg_match('/^[\'"](.*)[\'"]$/', $value, $strMatch)) {
                    $value = $strMatch[1];
                }
                
                $props[$name] = $value;
            }
        }
        
        return $props;
    }
} 