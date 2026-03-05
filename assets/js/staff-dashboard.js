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
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'staff/logbook';
}

function handleViewStudents() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'staff/students';
}

function handleGenerateReport() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'staff/reports';
}

function handleViewAttachments() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'staff/students';
}
