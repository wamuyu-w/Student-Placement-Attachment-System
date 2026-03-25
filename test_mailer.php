<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Mailer.php';

use App\Core\Mailer;

$to = 'michellewachira25@gmail.com';
$subject = 'Test Default Mailer';
$body = 'This is a test with the updated app password.';

echo "Sending...\n";
$result = Mailer::send($to, $subject, $body);

if ($result) {
    echo "SENT SUCCESSFULLY!\n";
} else {
    echo "FAILED!\n";
}
