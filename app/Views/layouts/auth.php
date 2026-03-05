<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login - CUEA Attachment System' ?></title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/login.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?= Helpers::baseUrl('../assets/js/login.js') ?>" defer></script>
</head>
<body>
    <?= $content; ?>
</body>
</html>
