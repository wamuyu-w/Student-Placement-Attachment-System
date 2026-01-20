// Student Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }
    
    // Notification click handler
    const notificationIcon = document.querySelector('.notification-icon');
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function() {
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
    // Redirect to opportunities page
    alert('Browse Opportunities feature - Redirecting to opportunities page...');
    // window.location.href = 'opportunities.php';
}

function handleViewApplications() {
    // Redirect to applications page
    alert('View My Applications feature - Redirecting to applications page...');
    // window.location.href = 'my-applications.php';
}

function handleViewLogbook() {
    // Redirect to logbook page
    alert('View Logbook feature - Redirecting to logbook page...');
    // window.location.href = 'logbook.php';
}
