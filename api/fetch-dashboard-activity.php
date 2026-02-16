<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection();
$userType = $_SESSION['user_type'] ?? '';
$activities = [];

// Helper function to calculate time ago
function getTimeAgo($datetime) {
    if (!$datetime) return 'Unknown';
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $timestamp);
}

try {
    if ($userType === 'student') {
        $studentID = $_SESSION['student_id'] ?? null;
        if ($studentID) {
            // Priority 1: Recent Applications
            $stmt = $conn->prepare("SELECT ApplicationDate, ApplicationStatus FROM attachmentapplication WHERE StudentID = ? ORDER BY ApplicationDate DESC LIMIT 2");
            $stmt->bind_param("i", $studentID);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=App&background=8B1538&color=fff&size=128',
                    'title' => 'Application submitted',
                    'description' => 'Status: ' . htmlspecialchars($row['ApplicationStatus']),
                    'time' => getTimeAgo($row['ApplicationDate'])
                ];
            }
            $stmt->close();
            
            // Priority 2: Placement Updates
            if (count($activities) < 4) {
                $stmt = $conn->prepare("SELECT a.StartDate, a.AttachmentStatus, ho.OrganizationName FROM attachment a JOIN hostorganization ho ON a.HostOrgID = ho.HostOrgID WHERE a.StudentID = ? ORDER BY a.StartDate DESC LIMIT 2");
                $stmt->bind_param("i", $studentID);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $activities[] = [
                        'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['OrganizationName']) . '&background=10b981&color=fff&size=128',
                        'title' => 'Placement at ' . htmlspecialchars($row['OrganizationName']),
                        'description' => 'Status: ' . htmlspecialchars($row['AttachmentStatus']),
                        'time' => getTimeAgo($row['StartDate'])
                    ];
                }
                $stmt->close();
            }

            // Priority 3: Supervisor Assignment
             if (count($activities) < 4) {
                $stmt = $conn->prepare("SELECT l.Name, a.StartDate FROM supervision s JOIN lecturer l ON s.LecturerID = l.LecturerID JOIN attachment a ON s.AttachmentID = a.AttachmentID WHERE a.StudentID = ? ORDER BY a.StartDate DESC LIMIT 1");
                $stmt->bind_param("i", $studentID);
                $stmt->execute();
                $res = $stmt->get_result();
                 while ($row = $res->fetch_assoc()) {
                    $activities[] = [
                        'avatar' => 'https://ui-avatars.com/api/?name=Sup&background=f59e0b&color=fff&size=128',
                        'title' => 'Supervisor assigned',
                        'description' => htmlspecialchars($row['Name']),
                        'time' => getTimeAgo($row['StartDate'])
                    ];
                }
                $stmt->close();
            }
        }
    } elseif ($userType === 'host_org') {
        $hostOrgID = $_SESSION['host_org_id'] ?? null;
        if ($hostOrgID) {
            // Recent Placements (Confirmed)
            $stmt = $conn->prepare("SELECT s.FirstName, s.LastName, s.Course, a.StartDate FROM attachment a JOIN student s ON a.StudentID = s.StudentID WHERE a.HostOrgID = ? ORDER BY a.StartDate DESC LIMIT 5");
             $stmt->bind_param("i", $hostOrgID);
            $stmt->execute();
            $res = $stmt->get_result();
             while ($row = $res->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['FirstName'] . ' ' . $row['LastName']) . '&background=8B1538&color=fff&size=128',
                    'title' => htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']),
                    'description' => htmlspecialchars($row['Course']),
                    'time' => getTimeAgo($row['StartDate'])
                ];
            }
            $stmt->close();
        }
    } elseif ($userType === 'staff') { // Staff/Lecturer
         // Logic for Staff to be added if needed, or re-use session ID check logic from staff-dashboard.php
         // For now, let's assume we can get LecturerID from Session
         $lecturerID = $_SESSION['LecturerID'] ?? null;
         if (!$lecturerID && isset($_SESSION['staff_number'])) {
             // Quick lookup if ID missing but StaffNumber exists
              $stmt = $conn->prepare("SELECT LecturerID FROM lecturer WHERE StaffNumber = ?");
              $stmt->bind_param("s", $_SESSION['staff_number']);
              $stmt->execute();
              $res = $stmt->get_result();
              if ($row = $res->fetch_assoc()) {
                  $lecturerID = $row['LecturerID'];
              }
              $stmt->close();
         }

         if ($lecturerID) {
             // Recent Logbooks
             $stmt = $conn->prepare("
                SELECT st.FirstName, st.LastName, st.Course, l.IssueDate 
                FROM supervision sv
                JOIN attachment a ON sv.AttachmentID = a.AttachmentID
                JOIN logbook l ON l.AttachmentID = a.AttachmentID
                JOIN student st ON a.StudentID = st.StudentID
                WHERE sv.LecturerID = ?
                ORDER BY l.IssueDate DESC LIMIT 5
             ");
             $stmt->bind_param("i", $lecturerID);
             $stmt->execute();
             $res = $stmt->get_result();
             while ($row = $res->fetch_assoc()) {
                $activities[] = [
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($row['FirstName'] . ' ' . $row['LastName']) . '&background=8B1538&color=fff&size=128',
                    'title' => htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']),
                    'description' => htmlspecialchars($row['Course']),
                    'time' => getTimeAgo($row['IssueDate'])
                ];
            }
            $stmt->close();
         }
    }

    echo json_encode(['success' => true, 'activities' => $activities]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
