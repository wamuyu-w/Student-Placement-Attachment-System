// Admin Dashboard 

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
            alert('Notifications feature coming soon!');
        });
    }
});

// Handle search input
function handleSearch(event) {
    const searchTerm = event.target.value.trim();
    
    if (searchTerm.length > 2) {
        // Implement search functionality
        console.log('Searching for:', searchTerm);
        // You can add AJAX call here to search
    }
}

// Quick Action Handlers
function handleAddOpportunity() {
    // Redirect to add opportunity page or show modal
    window.location.href = 'admin-opportunities-management.php';
}

function handleGenerateReport() {
    // Generate and download weekly report
    if (confirm('Generate weekly report? This will create a PDF document.')) {
        alert('Report generation feature coming soon!');
        // In the future, you can redirect to a report generation page:
        // window.location.href = 'reports.php?action=generate';
    }
}

function handleAssignSupervisor() {
    // Redirect to supervisor assignment page
    alert('Assign Supervisor feature - Redirecting to supervisors page...');
    // window.location.href = 'supervisors.php?action=assign';
}

// Note: Activity data is now loaded directly from PHP on page load
// To refresh, users can simply refresh the page
