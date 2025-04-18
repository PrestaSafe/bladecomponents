# BladeComponents

Une implémentation légère du moteur de templates Blade de Laravel pour des projets PHP indépendants.

## Installation

```bash
composer require illuminate/view illuminate/filesystem illuminate/container illuminate/events
```

## Utilisation

### Configuration de base

```php
<?php
require 'vendor/autoload.php';

use App\BladeManager;

// Chemins pour les vues et le cache
$viewsPath = __DIR__ . '/views';
$cachePath = __DIR__ . '/cache';

// Création du gestionnaire Blade
$blade = new BladeManager($viewsPath, $cachePath);

// Rendu d'une vue
echo $blade->render('welcome', ['name' => 'John']);
```

### Configuration avancée

```php
<?php
// Création du gestionnaire Blade
$blade = new BladeManager($viewsPath, $cachePath);

// Ajout de chemins de vues supplémentaires
$blade->addViewPath(__DIR__ . '/other-views');

// Ajout de namespaces (pour organiser les vues)
$blade->addNamespace('admin', __DIR__ . '/admin/views');
$blade->addNamespace('emails', __DIR__ . '/email-templates');

// Ajout de chemins de composants (avec namespaces)
$blade->addComponentPath(__DIR__ . '/components');
$blade->addComponentPath(__DIR__ . '/modules', 'mymodules');

// Enregistrement automatique de tous les composants
$blade->registerComponents();

// Enregistrement manuel d'un composant avec alias
$blade->registerComponent('button', 'btn');
$blade->registerComponent('footer', 'module-footer', 'mymodules');

// Enregistrement de plusieurs composants en une seule fois
$blade->addComponents([
    ['name' => 'button', 'alias' => 'btn'],
    ['name' => 'card', 'alias' => 'panel'],
    ['name' => 'footer', 'alias' => 'page-footer', 'namespace' => 'mymodules']
]);

// Ajout de directives personnalisées
$blade->directive('uppercase', function ($expression) {
    return "<?php echo strtoupper($expression); ?>";
});

// Nettoyage du cache (utile pendant le développement)
$blade->clearCache();
```

### Utilisation des composants

Dans vos vues, vous pouvez utiliser les composants de cette façon:

```blade
<x-button>Cliquez ici</x-button>

<x-btn type="submit">Envoyer</x-btn>

<x-mymodules::card title="Mon titre">
    Contenu de la carte
</x-mymodules::card>
```

### Utilisation des vues avec namespace

```blade
@extends('admin::layout')

@section('content')
    <h1>Tableau de bord</h1>
@endsection
```

## Structure recommandée

```
project/
  ├── cache/              # Cache de compilation Blade
  ├── components/         # Composants Blade par défaut
  ├── modules/            # Modules avec leurs propres composants
  ├── src/                # Code source PHP
  │   ├── BladeManager.php
  │   └── ComponentManager.php
  ├── views/              # Vues Blade principales
  │   ├── components/     # Composants de base
  │   └── layouts/        # Layouts
  ├── vendor/             # Dépendances Composer
  ├── composer.json
  └── index.php           # Point d'entrée
```

## Exemples

### Fichier de composant (button.blade.php)

```blade
<button {{ $attributes->merge(['class' => 'btn']) }}>
    {{ $slot }}
</button>
```

### Fichier de vue avec composants (welcome.blade.php)

```blade
@extends('layouts.app')

@section('content')
    <h1>Bienvenue, {{ $name }}</h1>
    
    <x-button type="button" class="btn-primary">
        Cliquez ici
    </x-button>
    
    <x-mymodules::footer />
@endsection
``` 