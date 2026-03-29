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

    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
        $stmt->bind_param("si", $hashed, $userId);
        return $stmt->execute();
    }

    public function verifyPassword($userId, $password) {
        $user =$this->conn->query("SELECT Password FROM users WHERE UserID = $userId")->fetch_assoc();
        $stmt = $this->conn->prepare("SELECT Password FROM users WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        return $user ? password_verify($password, $user['Password']) : false;
    }
}
