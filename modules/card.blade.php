@props([
    'variant' => 'default',
    'shadow' => 'md',
    'rounded' => 'md',
    'class' => '',
])

@php
    $baseClasses = 'overflow-hidden border border-gray-200';
    
    $variantClasses = [
        'default' => 'bg-white',
        'primary' => 'bg-blue-50 border-blue-100',
        'success' => 'bg-green-50 border-green-100',
        'warning' => 'bg-yellow-50 border-yellow-100',
        'danger' => 'bg-red-50 border-red-100',
    ];
    
    $shadowClasses = [
        'none' => '',
        'sm' => 'shadow-sm',
        'md' => 'shadow',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
    ];
    
    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
    ];
    
    $classes = $baseClasses . ' ' . 
              ($variantClasses[$variant] ?? $variantClasses['default']) . ' ' . 
              ($shadowClasses[$shadow] ?? $shadowClasses['md']) . ' ' . 
              ($roundedClasses[$rounded] ?? $roundedClasses['md']) . ' ' . 
              $class;
    
    $attributes = $attributes->merge(['class' => $classes]);
@endphp

<div {!! $attributes !!}>
    {{-- Header slot --}}
    @if (isset($header))
        <div class="border-b border-gray-200 px-4 py-3">
            {{ $header }}
        </div>
    @endif
    
    {{-- Default slot (content) --}}
    <div class="px-4 py-3">
        {{ $slot }}
    </div>
    
    {{-- Actions slot --}}
    @if (isset($actions))
        <div class="px-4 py-2 bg-gray-50 flex justify-end space-x-2">
            {{ $actions }}
        </div>
    @endif
    
    {{-- Footer slot --}}
    @if (isset($footer))
        <div class="border-t border-gray-200 px-4 py-3">
            {{ $footer }}
        </div>
    @endif
</div> 