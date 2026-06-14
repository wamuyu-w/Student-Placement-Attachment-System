<?php
namespace App\Models;
use App\Config\Database;
/**
 * Class User
 * 
 * Base model handling authentication, password management, and unified profile
 * retrieval across all system user roles (Students, Lecturers, Admins, Hosts).
 */
class User {
    private $db;
    private $conn;

    /**
     * Initializes the database connection.
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    /**
     * Locates a user record based on their username.
     * 
     * @param string $username
     * @return array|null Associative array of user data or null
     */
    public function findUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Verifies a user's password. Automatically migrates legacy plain-text passwords
     * to secure Bcrypt hashes upon successful login.
     * 
     * @param int $userId
     * @param string $inputPassword The password provided during login
     * @param string $storedPassword The password currently stored in the DB (hashed or plain)
     * @return bool True if password is correct
     */
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

    /**
     * Retrieves the specific profile data for a student user.
     * 
     * @param int $userId The underlying users.UserID
     * @return array|null
     */
    public function getStudentProfile($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Retrieves the specific profile data for a staff member (Lecturer/Admin).
     * 
     * @param int $userId
     * @return array|null
     */
    public function getStaffProfile($userId) {
        // Staff/Lecturer/Admin - The student_id/Staff_Number is used for login/extracting details
        $stmt = $this->conn->prepare("SELECT * FROM lecturer WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Retrieves the specific profile data for a host organization.
     * 
     * @param int $userId
     * @return array|null
     */
    public function getHostProfile($userId) {
        // Host Organization - identified as H001/H002/H003
        $stmt = $this->conn->prepare("SELECT * FROM hostorganization WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Searches across all profile tables (Student, Lecturer, HostOrg) to find 
     * a user by their email address. Used primarily for password resets.
     * 
     * @param string $email
     * @return array|null Array containing UserID and Name, or null if not found
     */
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

    /**
     * Updates a user's password securely by hashing it first.
     * 
     * @param int $userId
     * @param string $newPassword Plain text new password
     * @return bool True on success
     */
    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
        $stmt->bind_param("si", $hashed, $userId);
        return $stmt->execute();
    }

    /**
     * Strict password verification against the database hash.
     * 
     * @param int $userId
     * @param string $password Plain text password to verify
     * @return bool True if valid
     */
    public function verifyPassword($userId, $password) {
        $stmt = $this->conn->prepare("SELECT Password FROM users WHERE UserID = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        return $user ? password_verify($password, $user['Password']) : false;
    }

    /**
     * Stores a generated password reset token and its expiration timestamp.
     * 
     * @param int $userId
     * @param string $token
     * @param string $expiry DATETIME string format
     * @return bool
     */
    public function savePasswordResetToken($userId, $token, $expiry) {
        $stmt = $this->conn->prepare("UPDATE users SET ResetToken = ?, ResetTokenExpiry = ? WHERE UserID = ?");
        $stmt->bind_param("ssi", $token, $expiry, $userId);
        return $stmt->execute();
    }

    /**
     * Locates a user based on a valid reset token.
     * 
     * @param string $token
     * @return array|null
     */
    public function findUserByResetToken($token) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE ResetToken = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Clears the password reset token fields to invalidate the token after use.
     * 
     * @param int $userId
     * @return bool
     */
    public function clearPasswordResetToken($userId) {
        $stmt = $this->conn->prepare("UPDATE users SET ResetToken = NULL, ResetTokenExpiry = NULL WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
}
