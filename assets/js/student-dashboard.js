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
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'student/opportunities';
}

function handleViewApplications() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'student/applications';
}

function handleViewLogbook() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/public/') + 8);
    window.location.href = basePath + 'student/logbook';
}
