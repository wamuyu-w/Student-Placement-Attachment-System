<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CUEA Attachment System' ?></title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/global.css') ?>">
    <?php if (isset($page_css)): ?>
    <!-- this will dynamically include page-specific CSS files based on the $page_css variable passed from the controller -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/' . $page_css) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="main-content">
        <?php require_once __DIR__ . '/../partials/header.php'; ?>
        <?= $content; ?>
    </div>
</body>
</html>
