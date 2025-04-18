<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e($title ?? 'Mon Application', false); ?></title>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <?php echo $__env->yieldContent('head'); ?>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</body>
</html> <?php /**PATH /Users/guillaume/Apps/bladecomponents/src/views/layout.blade.php ENDPATH**/ ?>