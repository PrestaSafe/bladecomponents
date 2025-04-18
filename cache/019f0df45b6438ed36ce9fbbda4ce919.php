<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e($title ?? 'Mon Application'); ?></title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <?php echo $__env->yieldContent('head'); ?>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</body>
</html> <?php /**PATH /Users/guillaume/Apps/bladecomponents/src/views/layout.blade.php ENDPATH**/ ?>