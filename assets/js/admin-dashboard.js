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
    window.location.href = getRouteUrl('admin/opportunities');
}

function handleGenerateReport() {
    window.location.href = getRouteUrl('admin/reports');
}

function handleAssignSupervisor() {
    window.location.href = getRouteUrl('admin/supervisors');
}
