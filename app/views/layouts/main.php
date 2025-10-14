<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Website' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logo-min.svg">

    <!-- Global styles -->
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/typography.css">
    <link rel="stylesheet" href="/assets/css/index.css">



    <!-- Page-specific head content -->
    <?= $head ?? '' ?>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script src="/assets/js/navbar.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide?.createIcons) lucide.createIcons();
    });
    </script>

</head>

<body>
    <?= $content ?>
</body>

</html>