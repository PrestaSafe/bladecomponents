# Composants Twig Réutilisables

Cette bibliothèque contient un ensemble de composants Twig réutilisables et personnalisables, conçus pour être utilisés avec TailwindCSS.

## Installation

Assurez-vous que Twig est correctement installé dans votre projet et que les composants sont accessibles dans le chemin de recherche de vos templates.

## Composants disponibles

### Card

Le composant Card est un conteneur flexible qui prend en charge les slots header, content, actions et footer.

**Paramètres:**
- `variant`: default, primary, success, warning, danger (défaut: default)
- `shadow`: none, sm, md, lg, xl (défaut: md)
- `rounded`: none, sm, md, lg, xl, full (défaut: md)
- `class`: classes CSS additionnelles
- `attrs`: attributs HTML additionnels
- `header`: contenu HTML pour l'en-tête (alternative au block header)
- `content`: contenu HTML principal (alternative au block content)
- `actions`: contenu HTML pour les actions (alternative au block actions)
- `footer`: contenu HTML pour le pied de page (alternative au block footer)

**Exemple d'utilisation avec embed:**

```twig
{% embed "components/card.html.twig" with {
    variant: 'primary',
    shadow: 'lg',
    rounded: 'md'
} %}
    {% block header %}
        <h3 class="text-lg font-medium">Titre de la carte</h3>
    {% endblock %}
    
    {% block content %}
        <p>Contenu principal de la carte.</p>
    {% endblock %}
    
    {% block actions %}
        {% embed "components/button.html.twig" with { variant: 'primary' } %}
            {% block content %}Action principale{% endblock %}
        {% endembed %}
    {% endblock %}
    
    {% block footer %}
        <p class="text-sm text-gray-500">Pied de la carte</p>
    {% endblock %}
{% endembed %}
```

**Exemple d'utilisation avec include:**

```twig
{% set card_header %}
    <h3 class="text-lg font-medium">Titre de la carte</h3>
{% endset %}

{% set card_content %}
    <p>Contenu principal de la carte.</p>
{% endset %}

{% set card_actions %}
    {% include "components/button.html.twig" with {
        variant: 'primary',
        content: 'Action principale'
    } %}
{% endset %}

{% set card_footer %}
    <p class="text-sm text-gray-500">Pied de la carte</p>
{% endset %}

{% include "components/card.html.twig" with {
    variant: 'primary',
    shadow: 'lg',
    rounded: 'md',
    header: card_header,
    content: card_content,
    actions: card_actions,
    footer: card_footer
} %}
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
- `content`: contenu HTML du bouton (alternative au block content)

**Exemple d'utilisation avec embed:**

```twig
{% embed "components/button.html.twig" with {
    variant: 'primary',
    size: 'md',
    attrs: {
        'data-action': 'submit-form'
    }
} %}
    {% block content %}
        Envoyer
    {% endblock %}
{% endembed %}
```

**Exemple d'utilisation avec include:**

```twig
{% include "components/button.html.twig" with {
    variant: 'primary',
    size: 'md',
    attrs: {
        'data-action': 'submit-form'
    },
    content: 'Envoyer'
} %}
```

## Utilisation des slots

Les composants peuvent être utilisés de deux façons différentes:

### 1. Avec la directive `embed` et des blocks

Les composants utilisent le système de blocks Twig pour définir des "slots" de contenu. Pour inclure du contenu dans un slot, vous devez utiliser la directive `{% block %}` avec le nom du slot correspondant.

- Pour le composant Card:
  - `header`: En-tête de la carte
  - `content`: Contenu principal (slot par défaut)
  - `actions`: Boutons d'action
  - `footer`: Pied de la carte

- Pour le composant Button:
  - `content`: Contenu du bouton

### 2. Avec la directive `include` et des variables

Les composants acceptent également du contenu sous forme de variables, ce qui permet de les utiliser avec `include` plutôt que `embed`.

Pour cela, définissez le contenu dans des variables avec `{% set %}` puis passez-les aux composants.

## Imbrication des composants

Vous pouvez imbriquer les composants les uns dans les autres. Par exemple, vous pouvez utiliser des boutons dans le slot `actions` d'une carte:

```twig
{% embed "components/card.html.twig" %}
    {% block content %}
        <p>Contenu de la carte</p>
    {% endblock %}
    
    {% block actions %}
        {% embed "components/button.html.twig" with { variant: 'outline' } %}
            {% block content %}Annuler{% endblock %}
        {% endembed %}
        
        {% embed "components/button.html.twig" with { variant: 'primary' } %}
            {% block content %}Confirmer{% endblock %}
        {% endembed %}
    {% endblock %}
{% endembed %}
```

Ou avec des variables:

```twig
{% set card_content %}
    <p>Contenu de la carte</p>
{% endset %}

{% set card_actions %}
    {% include "components/button.html.twig" with { variant: 'outline', content: 'Annuler' } %}
    {% include "components/button.html.twig" with { variant: 'primary', content: 'Confirmer' } %}
{% endset %}

{% include "components/card.html.twig" with {
    content: card_content,
    actions: card_actions
} %}
```

## Personnalisation

Tous les composants acceptent une propriété `class` qui vous permet d'ajouter des classes CSS supplémentaires, ainsi qu'une propriété `attrs` pour ajouter des attributs HTML arbitraires. 