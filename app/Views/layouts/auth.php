<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login - CUEA Attachment System' ?></title>
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/theme.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/global.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/login.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="<?= Helpers::baseUrl('../assets/js/login.js') ?>?v=<?= time() ?>" defer></script>
    <script>
        window.APP_CONFIG = {
            baseUrl: "<?= rtrim(Helpers::baseUrl('/'), '/') ?>",
            isRewrite: <?= (strpos($_SERVER['REQUEST_URI'], '/index.php') === false && strpos($_SERVER['REQUEST_URI'], '/public/index.php') === false) ? 'true' : 'false' ?>,
            entryScript: "<?= (strpos($_SERVER['REQUEST_URI'], '/public/index.php') !== false) ? '/public/index.php' : ((strpos($_SERVER['REQUEST_URI'], '/index.php') !== false) ? '/index.php' : '') ?>"
        };
        function getRouteUrl(route) {
            const config = window.APP_CONFIG || { baseUrl: '', isRewrite: true, entryScript: '' };
            const cleanRoute = '/' + route.replace(/^\/+/, '');
            if (config.isRewrite) {
                return config.baseUrl + cleanRoute;
            } else {
                return config.baseUrl + config.entryScript + cleanRoute;
            }
        }
    </script>
</head>
<body>
    <?= $content; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for error in query parameter to show a pop up
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                showErrorPopup(urlParams.get('error'));
            }
        });

        function showErrorPopup(message) {
            document.getElementById('errorPopupMessage').textContent = message;
            document.getElementById('errorPopupModal').style.display = 'flex';
        }
        function closeErrorPopup() {
            document.getElementById('errorPopupModal').style.display = 'none';
        }
    </script>

    <!-- Error Popup Modal -->
    <div id="errorPopupModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; backdrop-filter: blur(4px); transition: all 0.3s ease;">
        <div style="background: white; padding: 32px; border-radius: 16px; width: 90%; max-width: 420px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); text-align: center; border: 1px solid #fee2e2;">
            <div style="width: 56px; height: 56px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 16px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">An Error Occurred</h3>
            <p id="errorPopupMessage" style="color: #64748b; font-size: 0.95rem; margin-bottom: 24px; line-height: 1.5; word-break: break-word;"></p>
            <button onclick="closeErrorPopup()" style="background: #8B1538; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; transition: background-color 0.2s;">
                Dismiss
            </button>
        </div>
    </div>
</body>
</html>
