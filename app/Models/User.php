<?php
namespace App\Models;
use App\Config\Database;
// class user is a base class for all user types (students, supervisors, admins) to handle common user-related database operations, such as authentication, fetching user details, 
//and any other user related functions in the future
class User {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function findUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function verifyAndMigratePassword($userId, $inputPassword, $storedPassword) {
        // Check if password is already hashed (Bcrypt starts with $2y$ or $2a$ or $2b$ and is 60 chars)
        if (preg_match('/^\$2[ayb]\$.{56}$/', $storedPassword)) {
            return password_verify($inputPassword, $storedPassword);
        } else {
            // Legacy plain text check
            if ($inputPassword === $storedPassword) {
                // It matches, so let's hash it for next time
                $newHash = password_hash($inputPassword, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                $stmt->bind_param("si", $newHash, $userId);
                $stmt->execute();
                return true;
            }
            return false;
        }
    }

    public function getStudentProfile($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getStaffProfile($userId) {
        // Staff/Lecturer/Admin - The student_id/Staff_Number is used for login/extracting details
        $stmt = $this->conn->prepare("SELECT * FROM lecturer WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getHostProfile($userId) {
        // Host Organization - identified as H001/H002/H003
        $stmt = $this->conn->prepare("SELECT * FROM hostorganization WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findUserByEmail($email) {
        // --- Student ---
        try {
            $stmtS = $this->conn->prepare(
                "SELECT s.UserID, COALESCE(CONCAT(s.FirstName, ' ', s.LastName), 'Student') as Name
                 FROM student s WHERE s.Email = ?"
            );
            if ($stmtS) {
                $stmtS->bind_param("s", $email);
                $stmtS->execute();
                $res = $stmtS->get_result();
                if ($res && $res->num_rows > 0) return $res->fetch_assoc();
            }
        } catch (\Throwable $e) {
            error_log("findUserByEmail student query error: " . $e->getMessage());
        }

        // --- Lecturer ---
        try {
            $stmtL = $this->conn->prepare(
                "SELECT l.UserID, COALESCE(l.Name, 'Staff Member') as Name FROM lecturer l WHERE l.Email = ? AND l.Email IS NOT NULL AND l.Email != ''"
            );
            if ($stmtL) {
                $stmtL->bind_param("s", $email);
                $stmtL->execute();
                $resL = $stmtL->get_result();
                if ($resL && $resL->num_rows > 0) return $resL->fetch_assoc();
            }
        } catch (\Throwable $e) {
            error_log("findUserByEmail lecturer query error: " . $e->getMessage());
        }

        // --- Host Organization ---
        try {
            $stmtH = $this->conn->prepare(
                "SELECT h.UserID, COALESCE(h.OrganizationName, 'Organization') as Name FROM hostorganization h WHERE h.Email = ?"
            );
            if ($stmtH) {
                $stmtH->bind_param("s", $email);
                $stmtH->execute();
                $resH = $stmtH->get_result();
                if ($resH && $resH->num_rows > 0) return $resH->fetch_assoc();
            }
        } catch (\Throwable $e) {
            error_log("findUserByEmail host query error: " . $e->getMessage());
        }

        return null;
    }

    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
        $stmt->bind_param("si", $hashed, $userId);
        return $stmt->execute();
    }

    public function verifyPassword($userId, $password) {
        $stmt = $this->conn->prepare("SELECT Password FROM users WHERE UserID = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        return $user ? password_verify($password, $user['Password']) : false;
    }

    public function savePasswordResetToken($userId, $token, $expiry) {
        $stmt = $this->conn->prepare("UPDATE users SET ResetToken = ?, ResetTokenExpiry = ? WHERE UserID = ?");
        $stmt->bind_param("ssi", $token, $expiry, $userId);
        return $stmt->execute();
    }

    public function findUserByResetToken($token) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE ResetToken = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function clearPasswordResetToken($userId) {
        $stmt = $this->conn->prepare("UPDATE users SET ResetToken = NULL, ResetTokenExpiry = NULL WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
}
