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
    window.location.href = '../Logbook/staff-logbook.php';
}

function handleViewStudents() {
    window.location.href = '../Students/staff-students.php';
}

function handleGenerateReport() {
    window.location.href = '../Reports/staff-reports.php';
}

function handleViewAttachments() {
    // This could also point to students page or a specific attachments view
    window.location.href = '../Students/staff-students.php';
}
