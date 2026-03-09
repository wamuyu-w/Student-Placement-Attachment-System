<?php 
// this file will be included in all views to render the sidebar navigation based on user type and active page
use App\Core\Helpers; 
$userType = $_SESSION['user_type'] ?? 'guest';
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" height="20px" width="20px" alt="cuea logo">
        </div>
        <h1>CUEA Attachment System</h1>
    </div>
    <nav class="sidebar-nav">
        <?php if ($userType === 'admin'): ?>
        <!-- MVC Link -->
        <a href="<?= Helpers::baseUrl('/admin/dashboard') ?>" class="nav-item <?= ($page === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?= Helpers::baseUrl('/admin/applications') ?>" class="nav-item <?= ($page === 'applications') ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Applications</span>
        </a>
        <a href="<?= Helpers::baseUrl('/admin/opportunities') ?>" class="nav-item <?= ($page === 'opportunities') ? 'active' : '' ?>">
            <i class="fas fa-lightbulb"></i>
            <span>Opportunities</span>
        </a>
        <!-- MVC Link -->
        <a href="<?= Helpers::baseUrl('/admin/supervisors') ?>" class="nav-item <?= ($page === 'supervisors') ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Supervisors</span>
        </a>
        <a href="<?= Helpers::baseUrl('/admin/students') ?>" class="nav-item <?= ($page === 'students') ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Students</span>
        </a>
        <a href="<?= Helpers::baseUrl('/admin/reports') ?>" class="nav-item <?= ($page === 'reports') ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        
        <?php elseif ($userType === 'staff'): ?>
        <!-- Staff Links -->
        <a href="<?= Helpers::baseUrl('/staff/dashboard') ?>" class="nav-item <?= ($page === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?= Helpers::baseUrl('/staff/students') ?>" class="nav-item <?= ($page === 'students') ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Students</span>
        </a>
        <a href="<?= Helpers::baseUrl('/staff/logbook') ?>" class="nav-item <?= ($page === 'logbook') ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Logbooks</span>
        </a>
        <a href="<?= Helpers::baseUrl('/staff/reports') ?>" class="nav-item <?= ($page === 'reports') ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        <a href="<?= Helpers::baseUrl('/staff/supervision') ?>" class="nav-item <?= ($page === 'supervision') ? 'active' : '' ?>">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Supervision</span>
        </a>

        <?php elseif ($userType === 'host_org'): ?>
        <!-- Host Org Links -->
        <a href="<?= Helpers::baseUrl('/host/dashboard') ?>" class="nav-item <?= ($page === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?= Helpers::baseUrl('/host/opportunities') ?>" class="nav-item <?= ($page === 'opportunities') ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i>
            <span>Opportunities</span>
        </a>
        <a href="<?= Helpers::baseUrl('/host/applications') ?>" class="nav-item <?= ($page === 'applications') ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Applications</span>
        </a>
        <a href="<?= Helpers::baseUrl('/host/students') ?>" class="nav-item <?= ($page === 'students') ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Students</span>
        </a>
        <a href="<?= Helpers::baseUrl('/host/logbook') ?>" class="nav-item <?= ($page === 'logbook') ? 'active' : '' ?>">
            <i class="fas fa-book"></i>
            <span>Logbook</span>
        </a>
        <a href="<?= Helpers::baseUrl('/host/reports') ?>" class="nav-item <?= ($page === 'reports') ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        <a href="<?= Helpers::baseUrl('/host/supervision') ?>" class="nav-item <?= ($page === 'supervision') ? 'active' : '' ?>">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Supervision</span>
        </a>
        
        <?php elseif ($userType === 'student'): ?>
        <!-- Student Links -->
        <a href="<?= Helpers::baseUrl('/student/dashboard') ?>" class="nav-item <?= ($page === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        <?php if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Inactive'): ?>
        <a href="<?= Helpers::baseUrl('/student/opportunities') ?>" class="nav-item <?= ($page === 'opportunities') ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i>
            <span>Opportunities</span>
        </a>
        <a href="<?= Helpers::baseUrl('/student/applications') ?>" class="nav-item <?= ($page === 'applications') ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>My Applications</span>
        </a>
        <?php endif; ?>
        <a href="<?= Helpers::baseUrl('/student/logbook') ?>" class="nav-item <?= ($page === 'logbook') ? 'active' : '' ?>">
            <i class="fas fa-book"></i>
            <span>Logbook</span>
        </a>
        <a href="<?= Helpers::baseUrl('/student/reports') ?>" class="nav-item <?= ($page === 'reports') ? 'active' : '' ?>">
            <i class="fas fa-file-pdf"></i>
            <span>Reports</span>
        </a>
        <a href="<?= Helpers::baseUrl('/student/supervisor') ?>" class="nav-item <?= ($page === 'supervisor') ? 'active' : '' ?>">
            <i class="fas fa-user-tie"></i>
            <span>Supervisor</span>
        </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
            <?php
            $settingsLink = match($userType) {
                'student' => '/student/settings',
                'staff' => '/staff/settings',
                'admin' => '/admin/settings',
                'host_org' => '/host/settings',
                default => '#'
            };
            ?>
            <a href="<?= Helpers::baseUrl($settingsLink) ?>" class="nav-item <?= ($page === 'settings') ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="<?= Helpers::baseUrl('/auth/logout') ?>" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>
