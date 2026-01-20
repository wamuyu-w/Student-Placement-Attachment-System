<?php
require_once '../config.php';

// Kill the session and clear everything
session_unset();
session_destroy();

// Send them back to the homepage
header("Location: ../index.php");
exit();
?>
