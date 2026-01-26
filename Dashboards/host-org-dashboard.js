// Dashboard functionality
document.addEventListener('DOMContentLoaded', function () {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const activityItems = document.querySelectorAll('.activity-item');

            activityItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});

// Navigation handlers
function handlePostPlacement() {
    window.location.href = '../Opportunities/host-org-opportunities-management.php';
}

function handleViewApplications() {
    window.location.href = '../Applications/host-org-applications.php';
}

function handleManageStudents() {
    window.location.href = '../Students/host-org-students.php';
}

function handleViewReports() {
    window.location.href = '../Reports/host-org-reports.php';
}
