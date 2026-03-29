<?php
$userType = $_SESSION['user_type'] ?? 'guest';
$displayName = 'User';
$displayRole = 'Guest';
$avatarName = 'User';

if ($userType === 'student') {
    $displayName = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
    $displayRole = 'Student';
    $avatarName = $_SESSION['first_name'] ?? 'Student';
} elseif ($userType === 'staff' || $userType === 'admin') {
    $displayName = $_SESSION['name'] ?? 'Staff';
    $displayRole = $_SESSION['role'] ?? 'Lecturer';
    $avatarName = $_SESSION['name'] ?? 'Staff';
} elseif ($userType === 'host_org') {
    $displayName = $_SESSION['organization_name'] ?? 'Host Org';
    $displayRole = 'Host Organization';
    $avatarName = $_SESSION['organization_name'] ?? 'Host';
}
?>
<header class="main-header">
    <div class="header-left">
        <button class="mobile-menu-toggle js-menu-toggle" aria-label="Toggle Menu">
            <i class="fa fa-bars"></i>
        </button>
        <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
    </div>
    <div class="header-actions">
        <?php if(isset($headerActions)) echo $headerActions; ?>
        
        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($avatarName) ?>&background=8B1538&color=fff&size=128" alt="Profile" class="profile-img">
            <div class="profile-info">
                <div class="profile-name"><?= htmlspecialchars($displayName) ?></div>
                <div class="profile-role"><?= htmlspecialchars($displayRole) ?></div>
            </div>
        </div>
    </div>
</header>
