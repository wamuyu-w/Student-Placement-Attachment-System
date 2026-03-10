<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CUEA Attachment System' ?></title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/theme.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/global.css') ?>?v=<?= time() ?>">
    <?php 
    if (isset($page_css)): 
        $css_files = is_array($page_css) ? $page_css : [$page_css];
        foreach ($css_files as $css_file):
    ?>
    <!-- this will dynamically include page-specific CSS files -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/' . $css_file) ?>?v=<?= time() ?>">
    <?php 
        endforeach;
    endif; 
    ?>
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
