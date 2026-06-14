<?php
namespace App\Models;
use App\Config\Database;

/**
 * Class Admin
 * 
 * Encapsulates the database queries specific to the administrative dashboard,
 * including high-level statistics and recent system activities.
 */
class Admin {
    //set variables that will be used
    private $db;
    private $conn;

    /**
     * Initializes the database connection for administrative operations.
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Retrieves aggregated system statistics for the admin dashboard overview.
     * Counts pending applications, active placements, opportunities, and eligible but unassigned students.
     * 
     * @return array Associative array of statistics
     */
    public function getDashboardStats() {
        $stats = [];
        $stats['pendingApps'] = $this->conn->query("SELECT COUNT(*) as count FROM jobapplication WHERE Status = 'Pending'")->fetch_assoc()['count'] ?? 0;
        $stats['activePlacements'] = $this->conn->query("SELECT COUNT(*) as count FROM attachment WHERE AttachmentStatus = 'Ongoing'")->fetch_assoc()['count'] ?? 0;
        $stats['opportunities'] = $this->conn->query("SELECT COUNT(*) as count FROM attachmentopportunity WHERE Status = 'Active'")->fetch_assoc()['count'] ?? 0;
        $stats['unassignedStudents'] = $this->conn->query("SELECT COUNT(DISTINCT s.StudentID) as count FROM student s LEFT JOIN attachment a ON s.StudentID = a.StudentID AND a.AttachmentStatus = 'Ongoing' WHERE a.AttachmentID IS NULL AND s.EligibilityStatus = 'Eligible'")->fetch_assoc()['count'] ?? 0;
        return $stats;
    }

    /**
     * Fetches and structures a merged timeline of recent activities.
     * Combines recent job applications from students and new opportunities posted by hosts.
     * 
     * @return array Chronologically sorted array of recent activity objects
     */
    public function getRecentActivities() {
        $activities = [];
        // Get recent applications
        $appsQuery = "SELECT ja.OpportunityID as ApplicationID, s.FirstName, s.LastName, ja.ApplicationDate, ja.Status as ApplicationStatus
                      FROM jobapplication ja
                      JOIN student s ON ja.StudentID = s.StudentID
                      ORDER BY ja.ApplicationDate DESC
                      LIMIT 2";
        $result = $this->conn->query($appsQuery);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $activities[] = [
                    'avatar' => \App\Core\Helpers::getAvatar($row['FirstName'] . ' ' . $row['LastName'], '#8B1538', '#fff', 'activity-avatar'),
                    'title' => 'New Application from ' . htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']),
                    'description' => 'Status: ' . htmlspecialchars($row['ApplicationStatus']),
                    'time' => $row['ApplicationDate']
                ];
            }
        }

        // Get new opportunities
        $oppQuery = "SELECT ao.OpportunityID, ho.OrganizationName, ao.Description, ao.ApplicationStartDate
                     FROM attachmentopportunity ao
                     JOIN hostorganization ho ON ao.HostOrgID = ho.HostOrgID
                     WHERE ao.Status = 'Active'
                     ORDER BY ao.ApplicationStartDate DESC
                     LIMIT 2";
        $result = $this->conn->query($oppQuery);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $activities[] = [
                    'avatar' => \App\Core\Helpers::getAvatar($row['OrganizationName'], '#10b981', '#fff', 'activity-avatar'),
                    'title' => htmlspecialchars($row['OrganizationName']) . ' posted a new opportunity',
                    'description' => htmlspecialchars(substr($row['Description'], 0, 50) . '...'),
                    'time' => $row['ApplicationStartDate']
                ];
            }
        }
        
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 4);
    }
}
