
    <style>
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
    </style>

    
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Démonstration du Composant Button</h1>
        
        <h2 class="text-xl font-semibold mb-3">Variantes</h2>
        <div class="demo-grid">
            <x-button>Bouton par défaut</x-button>
            <x-button variant="primary">Primary</x-button>
            <x-button variant="secondary">Secondary</x-button>
            <x-button variant="success">Success</x-button>
            <x-button variant="danger">Danger</x-button>
            <x-button variant="warning">Warning</x-button>
            <x-button variant="outline">Outline</x-button>
        </div>
        
        <h2 class="text-xl font-semibold mb-3">Tailles</h2>
        <div class="demo-grid">
            <x-button size="sm">Small</x-button>
            <x-button size="md">Medium</x-button>
            <x-button size="lg">Large</x-button>
        </div>
        
        <h2 class="text-xl font-semibold mb-3">États</h2>
        <div class="demo-grid">
            <x-button>Actif</x-button>
            <x-button disabled="true">Désactivé</x-button>
        </div>
        
        <h2 class="text-xl font-semibold mb-3">Avec des classes personnalisées</h2>
        <div class="demo-grid">
            <x-button class="w-full">Pleine largeur</x-button>
            <x-button class="rounded-full">Arrondi</x-button>
        </div>
        
        <h2 class="text-xl font-semibold mb-3">Avec des attributs additionnels</h2>
        <div class="demo-grid">
            <x-button x-data="{}" @click="alert('Cliqué!')">Avec Alpine.js</x-button>
            <x-button id="special-button" data-testid="test-button">Avec attributs</x-button>
        </div>
    </div>
