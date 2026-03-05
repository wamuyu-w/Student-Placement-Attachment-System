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
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'host/opportunities';
}

function handleViewApplications() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'host/applications';
}

function handleManageStudents() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'host/students';
}

function handleViewReports() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'host/reports';
}
