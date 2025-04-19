@extends('layout')

@section('content')
    <x-header title="Mon titre" subtitle="Mon sous-titre" />
    <h1 class="text-2xl font-bold mb-4">Hello, {{ $name }}!</h1>
    <p class="text-gray-700">Welcome to Blade without Laravel!</p>
    <x-module-card variant="primary">
        <x-slot name="header">
            <h3>Titre de la carte</h3>
        </x-slot>
        
        <p>Contenu principal (slot par défaut)</p>
        
        <x-slot name="actions">
            <x-button>Action</x-button>
        </x-slot>
        
        <x-slot name="footer">
            <p>Pied de page</p>
        </x-slot>
    </x-module-card>
    <!-- Tests avec composant x-button et différentes syntaxes Alpine.js -->
    <div class="space-y-4">
        <!-- Test 1: Syntaxe x-on:click -->
        <div x-data="{ count1: 0 }" class="p-4 border rounded">
            <h3 class="font-bold">Test 1: x-on:click</h3>
            <x-button x-on:click="count1++">
                Incrémenter (x-on:click)
            </x-button>
            <span>Compteur: <span x-text="count1"></span></span>
        </div>
        
        <!-- Test 2: Syntaxe @click -->
        <div x-data="{ count2: 0 }" class="p-4 border rounded">
            <h3 class="font-bold">Test 2: @click</h3>
            <x-button @click="count2++">
                Incrémenter (@click)
            </x-button>
            <span>Compteur: <span x-text="count2"></span></span>
        </div>
        
        <!-- Test 3: Syntaxe x-bind pour les événements -->
        <div x-data="{ count3: 0 }" class="p-4 border rounded">
            <h3 class="font-bold">Test 3: Version modifiée</h3>
            <x-button x-on:click.stop="count3++">
                Incrémenter (version modifiée)
            </x-button>
            <span>Compteur: <span x-text="count3"></span></span>
        </div>
        
        <!-- Test 4: Version alternative avec x-bind:click -->
        <div x-data="{ count4: 0, increment() { this.count4++ } }" class="p-4 border rounded">
            <h3 class="font-bold">Test 4: Avec méthode Alpine</h3>
            <x-button x-on:click="increment()">
                Incrémenter (avec méthode)
            </x-button>
            <span>Compteur: <span x-text="count4"></span></span>
        </div>

        <!-- Test 5: Syntaxe x-bind:click avec un attribut simple -->
        <div x-data="{ count5: 0 }" class="p-4 border rounded">
            <h3 class="font-bold">Test 5: x-bind:click simple</h3>
            <button 
                type="button" 
                class="px-4 py-2 bg-blue-500 text-white rounded"
                x-bind:class="{ 'bg-green-500': count5 > 0 }"
                x-bind:click="count5++"
            >
                Incrémenter (x-bind:click)
            </button>
            <span>Compteur: <span x-text="count5"></span></span>
        </div>

         <!-- Test 6: Syntaxe x-bind:click avec un attribut simple -->
         <div x-data="{ count6: 0 }" class="p-4 border rounded">
            <h3 class="font-bold">Test 6: x-bind:click simple</h3>
            <button 
                type="button" 
                class="px-4 py-2 bg-blue-500 text-white rounded"
                x-bind:class="{ 'bg-green-500': count6 > 0 }"
                x-bind:click="count6++"
            >
                Incrémenter (x-bind:click)
            </button>
            <span>Compteur: <span x-text="count6"></span></span>
        </div>
        
        <!-- Test avec fetch serveur -->
        <div x-data="{ 
            serverCount: 0, 
            serverResponse: null,
            incrementServer() {
                this.serverCount++;
                fetch('/server.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'counter=' + this.serverCount
                })
                .then(response => response.json())
                .then(data => {
                    this.serverResponse = data;
                    console.log('Réponse du serveur:', data);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            }
        }" class="p-4 border rounded bg-gray-50">
            <h3 class="font-bold">Test avec fetch côté serveur</h3>
            <x-button @click="incrementServer()" class="bg-purple-500 hover:bg-purple-600">
                Incrémenter et envoyer au serveur
            </x-button>
            <div class="mt-2">
                <span>Compteur local: <span x-text="serverCount"></span></span>
            </div>
            <div class="mt-2" x-show="serverResponse">
                <h4 class="font-semibold">Réponse du serveur:</h4>
                <div class="bg-white p-2 rounded shadow-sm mt-1">
                    <div><span class="font-medium">Message:</span> <span x-text="serverResponse?.message"></span></div>
                    <div><span class="font-medium">Compteur reçu:</span> <span x-text="serverResponse?.counter"></span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- @include('button-demo') --}}

    <x-module-footer />
@endsection 