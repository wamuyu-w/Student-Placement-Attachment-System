/**
 * Dashboard Updates Script
 * Polls the API for recent activity every 10 seconds
 */

document.addEventListener('DOMContentLoaded', function () {
    const activityList = document.querySelector('.activity-list');

    if (!activityList) return;

    function fetchActivity() {
        fetch('../api/fetch-dashboard-activity.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.activities.length > 0) {
                    updateActivityList(data.activities);
                }
            })
            .catch(error => console.error('Error fetching dashboard activity:', error));
    }

    function updateActivityList(activities) {
        // Clear current list (or ideally diff it, but replace is simpler for now)
        activityList.innerHTML = '';

        activities.forEach(activity => {
            const item = document.createElement('div');
            item.className = 'activity-item';

            // Build HTML
            // Note: Avatar, Title, Description, Time expected from API
            item.innerHTML = `
                <img src="${activity.avatar}" alt="Avatar" class="activity-avatar">
                <div class="activity-content">
                    <div class="activity-title">${activity.title}</div>
                    <div class="activity-description">${activity.description}</div>
                    <div class="activity-time">${activity.time}</div>
                </div>
            `;

            // Add animation class if it's new (future enhancement)
            activityList.appendChild(item);
        });
    }

    // Poll every 10 seconds
    setInterval(fetchActivity, 10000);
});
