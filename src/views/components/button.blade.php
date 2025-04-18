@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $variantClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500',
        'outline' => 'bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
    ];
    
    $sizeClasses = [
        'sm' => 'px-2.5 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    $disabledClasses = 'opacity-50 cursor-not-allowed';
    
    $classes = $baseClasses . ' ' . 
               ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . 
               ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . 
               ($disabled ? $disabledClasses : '') . ' ' . 
               $class;
               
    // S'assurer que les attributs sont fusionnés correctement
    $attributes = $attributes->merge(['class' => $classes, 'type' => $type]);
    if ($disabled) {
        $attributes = $attributes->merge(['disabled' => 'disabled']);
    }
@endphp

<button {!! $attributes !!}>
    {{ $slot }}
</button> 