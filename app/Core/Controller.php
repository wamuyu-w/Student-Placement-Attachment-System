<?php
namespace App\Core;
// Base controller class that other controllers will extend
// now this one I am sure is the one that works as the base controller for the MVC framework. hio ingine siko sure that much
//It provides methods to load models, views, return JSON responses, and handle authentication checks.
// The view method also supports layouts, allowing for a consistent structure across different views.
//The requireAuth method ensures that users are authenticated and have the correct role before accessing certain routes, and it also handles forced password changes on first login.
class Controller {
    // this method will load the specified model class
    public function model($model) {
        $modelClass = "App\\Models\\" . $model;
        return new $modelClass();
    }
    // this method will load the specified view file and pass data to it
    public function view($view, $data = [], $layout = 'main') {
        // Extract data array to variables
        extract($data);
        
        // Start output buffering to capture the view content
        ob_start();
        require_once __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        
        // Load the layout if specified
        if ($layout) {
            require_once __DIR__ . '/../Views/layouts/' . $layout . '.php';
        } else {
            echo $content;
        }
    }
    // this method will return a JSON response
    public function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    // this method will handle redirects
    protected function requireAuth($role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if logged in and has correct role
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== $role) {
            
            $loginRoutes = [
                'student' => '/login/student',
                'staff' => '/login/staff',
                'admin' => '/login/staff', 
                'host_org' => '/login/host'
            ];
            
            $route = $loginRoutes[$role] ?? '/login/student';
            
            // Redirect to MVC login page
            header("Location: " . Helpers::baseUrl($route));
            exit();
        }

        // Forced Password Change Check
        if (isset($_SESSION['force_password_change']) && $_SESSION['force_password_change'] === true) {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, '/auth/first-login') === false && strpos($uri, '/auth/logout') === false) {
                header("Location: " . Helpers::baseUrl('/auth/first-login'));
                exit();
            }
        }
    }
}
