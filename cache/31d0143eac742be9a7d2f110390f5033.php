<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'class' => '',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'class' => '',
]); ?>
<?php foreach (array_filter(([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
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
?>

<button <?php echo $attributes; ?>>
    <?php echo e($slot, false); ?>

</button> <?php /**PATH /Users/guillaume/Apps/bladecomponents/src/views/components/button.blade.php ENDPATH**/ ?>