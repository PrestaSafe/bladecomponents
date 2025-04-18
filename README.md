# Hello World avec Blade sans Laravel

Ce projet montre comment utiliser le moteur de template Blade de Laravel sans le framework complet.

## Installation

```
composer install
```

## Utilisation

Lancez un serveur PHP local :

```
php -S localhost:8000
```

Puis visitez http://localhost:8000 dans votre navigateur.

## Structure du projet

- `index.php` : Le point d'entrée qui initialise Blade et affiche notre template
- `src/views/hello.blade.php` : Notre template Blade avec la syntaxe {{ $name }} 