// Student Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }

    // Notification click handler
    const notificationIcon = document.querySelector('.notification-icon');
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function () {
            alert('You have pending tasks! Check your logbook and reports.');
        });
    }
});

// Handle search input
function handleSearch(event) {
    const searchTerm = event.target.value.trim();

    if (searchTerm.length > 2) {
        // Implement search functionality
        console.log('Searching for:', searchTerm);
        // You can add AJAX call here to search opportunities, applications, etc.
    }
}

// Quick Action Handlers
function handleBrowseOpportunities() {
    window.location.href = '../Opportunities/student-opportunities.php';
}

function handleViewApplications() {
    window.location.href = '../Applications/student-applications.php';
}

function handleViewLogbook() {
    window.location.href = '../Logbook/student-logbook.php';
}
