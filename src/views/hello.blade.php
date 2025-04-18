@extends('layout')

@section('content')
    <x-header title="Mon titre" subtitle="Mon sous-titre" />
    <h1 class="text-2xl font-bold mb-4">Hello, {{ $name }}!</h1>
    <p class="text-gray-700">Welcome to Blade without Laravel!</p>
    <x-button>
        Bouton par défaut
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </x-button>
    
    @include('button-demo')

    <x-module-footer />
@endsection 