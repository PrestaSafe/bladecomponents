@extends('layout')

@section('content')
    <x-header title="Mon titre" subtitle="Mon sous-titre" />
    <h1 class="text-2xl font-bold mb-4">Hello, {{ $name }}!</h1>
    <p class="text-gray-700">Welcome to Blade without Laravel!</p>
    
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
        
        <!-- Test avec bouton HTML standard -->
        <div x-data="{ counter: 0 }" class="p-4 border rounded">
            <h3 class="font-bold">Bouton HTML standard</h3>
            <button @click="counter++" class="px-4 py-2 bg-green-500 text-white rounded">
                Incrémenter
            </button>
            <span>Compteur: <span x-text="counter"></span></span>
        </div>
    </div>

    {{-- @include('button-demo') --}}

    <x-module-footer />
@endsection 