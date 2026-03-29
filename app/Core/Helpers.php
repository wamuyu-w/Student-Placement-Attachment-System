<?php
//this file contains helper functions that can be used throughout the application
namespace App\Core;
// Helper functions for the application
class Helpers {
    // Generate base URL for assets and links
    public static function baseUrl($path = '') {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $dir = dirname($scriptName);
        
        // Normalize slashes for Windows compatibility
        $dir = str_replace('\\', '/', $dir);
        
        // Ensure no trailing slash
        $dir = rtrim($dir, '/');
        
        // Ensure path has leading slash if not empty
        if ($path && substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        
        return $dir . $path;
    }
    // Sanitize input data — strips whitespace and magic-quote slashes only.
    // Output encoding (htmlspecialchars) must be done at render time in views.
    public static function sanitize($data) {
        $data = trim($data ?? '');
        $data = stripslashes($data);
        return $data;
    }
    // Hash a password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    //time ago function to display how long ago a date was
    public static function timeAgo($datetime) {
        // Handle null or empty datetime
        if (!$datetime) return 'Unknown';
        
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        // Handle future dates
        if ($diff < 60) {
            return $diff . 's ago';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . 'm ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . 'h ago';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . 'd ago';
        } else {
            return date('M j, Y', $timestamp);
        }
    }
}
