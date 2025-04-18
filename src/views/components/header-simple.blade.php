@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? '';
    $showNavigation = $showNavigation ?? true;
    $bgColor = $bgColor ?? 'bg-white';
    $textColor = $textColor ?? 'text-gray-800';
    $class = $class ?? '';
    
    $baseClasses = 'w-full py-4 px-6 shadow-sm';
    $titleClasses = 'text-2xl font-bold';
    $subtitleClasses = 'text-sm mt-1 opacity-75';
    
    $finalClasses = "{$baseClasses} {$bgColor} {$textColor} {$class}";
@endphp

<header class="{{ $finalClasses }}">
    <div class="container mx-auto">
        @if (!empty($title))
            <h1 class="{{ $titleClasses }}">{{ $title }}</h1>
        @endif
        
        @if (!empty($subtitle))
            <p class="{{ $subtitleClasses }}">{{ $subtitle }}</p>
        @endif
        
        @if ($showNavigation)
            <nav class="mt-4">
                <ul class="flex space-x-4">
                    <li><a href="#" class="hover:underline">Accueil</a></li>
                    <li><a href="#" class="hover:underline">À propos</a></li>
                    <li><a href="#" class="hover:underline">Services</a></li>
                    <li><a href="#" class="hover:underline">Contact</a></li>
                </ul>
            </nav>
        @endif
        
        @if (!empty($slot))
            <div class="mt-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</header>