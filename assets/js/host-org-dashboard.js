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
    window.location.href = getRouteUrl('host/opportunities');
}

function handleViewApplications() {
    window.location.href = getRouteUrl('host/applications');
}

function handleManageStudents() {
    window.location.href = getRouteUrl('host/students');
}

function handleViewReports() {
    window.location.href = getRouteUrl('host/reports');
}
