<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <title><?= $title ?? 'CUEA Attachment System' ?></title>
    <!-- Global Styles -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/theme.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/global.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/table-sort-filter.css') ?>?v=<?= time() ?>">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="main-content">
        <?php require_once __DIR__ . '/../partials/header.php'; ?>
        <?= $content; ?>
    </div>
    <script src="<?= Helpers::baseUrl('../assets/js/table-sort-filter.js') ?>?v=<?= time() ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.js-menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                });
                
                // Auto close when clicking outside
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                            sidebar.classList.remove('active');
                        }
                    }
                });

                // Auto close on link click (as requested)
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                       if (window.innerWidth <= 768) {
                           sidebar.classList.remove('active');
                       }
                    });
                });
            }
        });
    </script>
</body>
</html>
