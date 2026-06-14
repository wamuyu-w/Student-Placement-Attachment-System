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
function handleReviewLogbook() {
    window.location.href = getRouteUrl('staff/logbook');
}

function handleViewStudents() {
    window.location.href = getRouteUrl('staff/students');
}

function handleGenerateReport() {
    window.location.href = getRouteUrl('staff/reports');
}

function handleViewAttachments() {
    window.location.href = getRouteUrl('staff/students');
}
