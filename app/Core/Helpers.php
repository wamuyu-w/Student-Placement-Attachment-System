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
        // Handle null, empty datetime, or zero dates
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00' || $datetime === '0000-00-00') {
            return 'Unknown';
        }
        
        $timestamp = strtotime($datetime);
        if ($timestamp === false || $timestamp <= 0) {
            return 'Unknown';
        }
        
        $diff = time() - $timestamp;
        
        // Handle future dates or extremely recent dates
        if ($diff < 0) {
            return 'Just now';
        } elseif ($diff < 60) {
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

    // Generate initials avatar to replace external AI/dynamic avatars
    public static function getAvatar($name, $bgColor = '#8B1538', $color = '#ffffff', $cssClass = 'activity-avatar', $extraStyle = '') {
        $words = explode(' ', trim($name));
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr(trim($name), 0, 2));
        }
        
        return sprintf(
            '<div class="default-avatar %s" style="background-color: %s; color: %s; display: flex; align-items: center; justify-content: center; border-radius: 50%%; font-weight: 600; %s">%s</div>',
            htmlspecialchars($cssClass),
            htmlspecialchars($bgColor),
            htmlspecialchars($color),
            htmlspecialchars($extraStyle),
            htmlspecialchars($initials)
        );
    }
}
