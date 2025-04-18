<?php

namespace App;

class Helpers
{
    /**
     * Génère une chaîne de classes CSS en fonction des conditions
     * 
     * Exemple d'utilisation:
     * classNames([
     *    'btn' => true,
     *    'btn-primary' => $type === 'primary',
     *    'btn-lg' => $size === 'large',
     * ]);
     *
     * @param array $classes Liste des classes avec leurs conditions
     * @return string
     */
    public static function classNames(array $classes): string
    {
        $classList = [];
        
        foreach ($classes as $class => $condition) {
            if ($condition) {
                $classList[] = $class;
            }
        }
        
        return implode(' ', $classList);
    }
    
    /**
     * Convertit un attribut booléen en attribut HTML
     *
     * @param string $attribute Nom de l'attribut
     * @param bool $condition Condition pour afficher l'attribut
     * @return string
     */
    public static function booleanAttribute(string $attribute, bool $condition): string
    {
        return $condition ? $attribute : '';
    }
    
    /**
     * Convertit un tableau d'attributs en chaîne HTML
     *
     * @param array $attributes Liste des attributs
     * @return string
     */
    public static function attributes(array $attributes): string
    {
        $html = [];
        
        foreach ($attributes as $key => $value) {
            // Si c'est un attribut booléen et qu'il est vrai
            if (is_bool($value)) {
                if ($value) {
                    $html[] = $key;
                }
            } else {
                $html[] = $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        return implode(' ', $html);
    }
} 