# Composants Twig Réutilisables avec Syntaxe Vue.js

Ce projet contient un ensemble de composants Twig réutilisables et personnalisables, conçus pour être utilisés avec TailwindCSS. Ce qui rend ce projet unique est la possibilité d'utiliser une syntaxe déclarative inspirée de Vue.js.

## Installation

1. Clonez ce dépôt:
   ```bash
   git clone https://github.com/votre-nom/twig-components.git
   cd twig-components
   ```

2. Installez les dépendances avec Composer:
   ```bash
   composer install
   ```

3. Assurez-vous que le dossier `src/views/cache` existe et est accessible en écriture:
   ```bash
   mkdir -p src/views/cache
   chmod 777 src/views/cache
   ```

## Démarrage rapide

Pour voir la démonstration des composants avec la syntaxe Vue.js:

```bash
php -S localhost:8000 vue-style-example.php
```

Ouvrez ensuite `http://localhost:8000` dans votre navigateur.

## Structure du projet

```
src/
├── TwigExtension/
│   ├── ComponentExtension.php      # Extension pour la syntaxe de fonction
│   ├── SimpleComponentsExtension.php  # Extension pour la syntaxe simplifiée
│   ├── TagComponentsExtension.php  # Extension pour la syntaxe Vue.js
│   └── ComponentTokenParser.php    # Parser pour la syntaxe Vue.js 
│
├── views/
│   ├── components/
│   │   ├── button.html.twig        # Composant Button
│   │   ├── card.html.twig          # Composant Card
│   │   └── README.md               # Documentation détaillée des composants
│   │
│   ├── cache/                      # Cache de Twig
│   └── working-demo.html.twig      # Page de démonstration
│
├── twig.php                        # Script d'initialisation de Twig
└── vue-style-example.php           # Démonstration syntaxe Vue.js
```

## Trois façons d'utiliser les composants

Ce projet offre trois syntaxes différentes pour utiliser les composants Twig, vous permettant de choisir celle qui convient le mieux à votre cas d'utilisation:

### 1. Syntaxe Twig standard (avec embed)

```twig
{% embed "components/button.html.twig" with {
    variant: 'primary',
    size: 'md'
} %}
    {% block content %}
        Cliquez-moi
    {% endblock %}
{% endembed %}
```

### 2. Syntaxe de fonction (plus concise)

```twig
{{ button({
    variant: 'primary',
    size: 'md'
}, 'Cliquez-moi') }}
```

### 3. Syntaxe inspirée de Vue.js (déclarative) ✨

```twig
{% button :variant="primary" :size="md" %}
    Cliquez-moi
{% endbutton %}
```

## Composants disponibles

### Card

Le composant Card est un conteneur flexible qui prend en charge les slots header, content, actions et footer.

**Paramètres:**
- `variant`: default, primary, success, warning, danger (défaut: default)
- `shadow`: none, sm, md, lg, xl (défaut: md)
- `rounded`: none, sm, md, lg, xl, full (défaut: md)
- `class`: classes CSS additionnelles
- `attrs`: attributs HTML additionnels

**Exemple avec syntaxe Vue.js:**

```twig
{% card :variant="primary" :shadow="lg" :rounded="md" %}
    <div slot="header">
        <h3 class="text-lg font-medium">Titre de la carte</h3>
    </div>
    
    <p>Contenu principal de la carte.</p>
    
    <div slot="actions">
        {% button :variant="outline" :size="sm" %}Annuler{% endbutton %}
        {% button :variant="primary" :size="sm" %}Confirmer{% endbutton %}
    </div>
    
    <div slot="footer">
        <p class="text-sm text-gray-500">Pied de la carte</p>
    </div>
{% endcard %}
```

### Button

Le composant Button est un bouton entièrement personnalisable qui peut être utilisé seul ou au sein d'autres composants.

**Paramètres:**
- `type`: button, submit, reset (défaut: button)
- `variant`: primary, secondary, success, danger, warning, outline (défaut: primary)
- `size`: sm, md, lg (défaut: md)
- `disabled`: true, false (défaut: false)
- `class`: classes CSS additionnelles
- `attrs`: attributs HTML additionnels

**Exemple avec syntaxe Vue.js:**

```twig
{% button :variant="primary" :size="md" :type="submit" %}
    Envoyer
{% endbutton %}
```

## Utilisation avec Alpine.js

Les composants prennent en charge les attributs Alpine.js, vous permettant de créer des interfaces interactives sans avoir besoin de JavaScript personnalisé. Vous pouvez utiliser tous les attributs Alpine.js standards (`x-data`, `x-show`, `x-bind`, etc.) directement dans vos composants.

```twig
<div x-data="{open: false, count: 0}">
    {% button :variant="primary" :size="md" x-on:click="open = !open" %}
        Afficher/Masquer
    {% endbutton %}
    
    <div x-show="open">
        <p>Contenu visible lorsque "open" est vrai</p>
        
        {% button :variant="outline" :size="sm" x-show="open" x-on:click="count++" %}
            Incrémenter compteur
        {% endbutton %}
        
        <p>Compteur: <span x-text="count"></span></p>
    </div>
</div>
```

## Intégration dans un projet existant

Pour intégrer ces composants dans votre projet Twig existant:

1. Copiez les fichiers du dossier `src/TwigExtension/` dans votre projet
2. Copiez les composants du dossier `src/views/components/` dans votre dossier de templates
3. Initialisez l'extension Twig de votre choix dans votre configuration:

```php
// Pour la syntaxe Vue.js
$tagExtension = new \App\TwigExtension\TagComponentsExtension();
$twig->addExtension($tagExtension);

// Vous pouvez ajouter des composants personnalisés
$tagExtension->addComponent('modal', 'components/modal.html.twig');
```

## Personnalisation

Tous les composants sont conçus pour être hautement personnalisables:

- Modifiez les classes TailwindCSS dans les composants pour adapter le style à votre projet
- Ajoutez de nouveaux paramètres ou variants selon vos besoins
- Créez de nouveaux composants en suivant la même approche
- Étendez les parsers pour ajouter des fonctionnalités supplémentaires

## Licence

MIT 