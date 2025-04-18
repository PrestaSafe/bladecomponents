<?php
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
?>

<header class="<?php echo e($finalClasses); ?>">
    <div class="container mx-auto">
        <?php if(!empty($title)): ?>
            <h1 class="<?php echo e($titleClasses); ?>"><?php echo e($title); ?></h1>
        <?php endif; ?>
        
        <?php if(!empty($subtitle)): ?>
            <p class="<?php echo e($subtitleClasses); ?>"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
        
        <?php if($showNavigation): ?>
            <nav class="mt-4">
                <ul class="flex space-x-4">
                    <li><a href="#" class="hover:underline">Accueil</a></li>
                    <li><a href="#" class="hover:underline">À propos</a></li>
                    <li><a href="#" class="hover:underline">Services</a></li>
                    <li><a href="#" class="hover:underline">Contact</a></li>
                </ul>
            </nav>
        <?php endif; ?>
        
        <?php if(!empty($slot)): ?>
            <div class="mt-4">
                <?php echo e($slot); ?>

            </div>
        <?php endif; ?>
    </div>
</header><?php /**PATH /Users/guillaume/Apps/bladecomponents/src/views/components/header-simple.blade.php ENDPATH**/ ?>