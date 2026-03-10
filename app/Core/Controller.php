<?php
namespace App\Core;

class Controller {
    public function model($model) {
        $modelClass = "App\\Models\\" . $model;
        return new $modelClass();
    }

    public function view($view, $data = [], $layout = 'main') {
        extract($data);
        ob_start();
        require_once __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        if ($layout) {
            require_once __DIR__ . '/../Views/layouts/' . $layout . '.php';
        } else {
            echo $content;
        }
    }

    public function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function requireAuth($role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== $role) {
            $loginRoutes = [
                'student'  => '/login/student',
                'staff'    => '/login/staff',
                'admin'    => '/login/staff',
                'host_org' => '/login/host'
            ];
            header("Location: " . Helpers::baseUrl($loginRoutes[$role] ?? '/login/student'));
            exit();
        }

        if (isset($_SESSION['force_password_change']) && $_SESSION['force_password_change'] === true) {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, '/auth/first-login') === false && strpos($uri, '/auth/logout') === false) {
                header("Location: " . Helpers::baseUrl('/auth/first-login'));
                exit();
            }
        }
    }

    protected function requireActiveStudent() {
        $this->requireAuth('student');
        if (isset($_SESSION['status']) && $_SESSION['status'] === 'Inactive') {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $this->json(['success' => false, 'message' => 'Unauthorized. Your account is in read-only mode.']);
            }
            $_SESSION['error_message'] = "Unauthorized. Your account is in read-only mode.";
            header("Location: " . Helpers::baseUrl('/student/dashboard'));
            exit();
        }
    }
}
