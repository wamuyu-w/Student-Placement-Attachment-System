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
function handleAddOpportunity() {
    window.location.href = '../Opportunities/admin-opportunities-management.php';
}

function handleGenerateReport() {
    window.location.href = '../Reports/admin-reports.php';
}

function handleAssignSupervisor() {
    window.location.href = '../Supervisor/admin-supervisors.php';
}
