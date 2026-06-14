<?php
namespace App\Core;
// This is a very basic router implementation. It maps URL paths to specific controller actions.
// In a real application, you might want to use a more robust routing library or framework, but this serves the purpose for our project.
// it may look a bit long, but it's just a big array of routes and a simple dispatch method to handle incoming requests.
// usitense, the long ah list of routes is just a way to keep track of all the different pages and actions in our application. As we add more features, we'll just keep adding to this list.
//the dispatch method is where the magic happens(well not magic, but you get the gist of it). 
//It takes the incoming URL, matches it against our list of routes, and then calls the appropriate controller and action to handle the request.
// If no route matches, it returns a 404 error.
class Router {
    protected $routes = [];

    public function __construct() {
        // I have registered all the working routes here  
        $this->routes = [
            '/' => ['controller' => 'HomeController', 'action' => 'index'],
            // removing the student registration route since all they do is log in
            // '/student/register' => ['controller' => 'AuthController', 'action' => 'registerStudent'],
            '/admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard'],
            '/admin/supervisors' => ['controller' => 'AdminController', 'action' => 'viewSupervisors'],
            '/admin/supervisors/create' => ['controller' => 'AdminController', 'action' => 'createSupervisor'],
            '/admin/supervisors/assign' => ['controller' => 'AdminController', 'action' => 'assignSupervisor'],
            '/admin/supervisors/bulk-upload' => ['controller' => 'AdminController', 'action' => 'bulkUploadSupervisors'],
            '/student/dashboard' => ['controller' => 'StudentController', 'action' => 'dashboard'],
            '/staff/dashboard' => ['controller' => 'StaffController', 'action' => 'dashboard'],
            '/host/dashboard' => ['controller' => 'HostController', 'action' => 'dashboard'],
            '/login/student' => ['controller' => 'AuthController', 'action' => 'loginStudent'],
            '/login/staff' => ['controller' => 'AuthController', 'action' => 'loginStaff'],
            '/login/host' => ['controller' => 'AuthController', 'action' => 'loginHost'],
            '/register/host' => ['controller' => 'AuthController', 'action' => 'registerHost'],
            '/auth/login' => ['controller' => 'AuthController', 'action' => 'processLogin'],
            '/auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
            '/auth/register/host' => ['controller' => 'AuthController', 'action' => 'processRegisterHost'],
            '/auth/forgot-password' => ['controller' => 'AuthController', 'action' => 'forgotPassword'],
            '/auth/forgot-password/submit' => ['controller' => 'AuthController', 'action' => 'processForgotPassword'],
            '/auth/reset-password' => ['controller' => 'AuthController', 'action' => 'resetPassword'],
            '/auth/reset-password/submit' => ['controller' => 'AuthController', 'action' => 'processResetPassword'],
            '/student/opportunities' => ['controller' => 'OpportunityController', 'action' => 'index'],
            '/student/opportunities/apply' => ['controller' => 'OpportunityController', 'action' => 'apply'],
            '/admin/opportunities' => ['controller' => 'OpportunityController', 'action' => 'adminManage'],
            '/host/opportunities' => ['controller' => 'OpportunityController', 'action' => 'hostManage'],
            '/opportunities/save' => ['controller' => 'OpportunityController', 'action' => 'save'],
            '/opportunities/delete' => ['controller' => 'OpportunityController', 'action' => 'delete'],
            '/admin/applications' => ['controller' => 'ApplicationController', 'action' => 'adminIndex'],
            '/admin/applications/program-status' => ['controller' => 'ApplicationController', 'action' => 'updateProgramStatus'],
            '/host/applications' => ['controller' => 'ApplicationController', 'action' => 'hostIndex'],
            '/host/applications/update-status' => ['controller' => 'ApplicationController', 'action' => 'updateJobStatusHost'],
            '/student/applications' => ['controller' => 'ApplicationController', 'action' => 'studentIndex'],
            '/student/applications/apply-session' => ['controller' => 'ApplicationController', 'action' => 'applySession'],
            '/student/applications/register-placement' => ['controller' => 'ApplicationController', 'action' => 'registerPlacement'],
            '/student/logbook' => ['controller' => 'LogbookController', 'action' => 'studentIndex'],
            '/student/logbook/create' => ['controller' => 'LogbookController', 'action' => 'createEntry'],
            '/staff/logbook' => ['controller' => 'LogbookController', 'action' => 'staffIndex'],
            '/host/logbook' => ['controller' => 'LogbookController', 'action' => 'hostIndex'],
            '/logbook/review' => ['controller' => 'LogbookController', 'action' => 'reviewEntry'],
            '/logbook/add-comment' => ['controller' => 'LogbookController', 'action' => 'addComment'],
            '/staff/students' => ['controller' => 'StaffController', 'action' => 'viewStudents'],
            '/staff/supervision' => ['controller' => 'StaffController', 'action' => 'supervision'],
            '/host/students' => ['controller' => 'HostController', 'action' => 'viewStudents'],
            '/host/students/progress' => ['controller' => 'HostController', 'action' => 'viewStudentProgress'],
            '/host/supervision' => ['controller' => 'HostController', 'action' => 'supervision'],
            '/host/supervision/generate' => ['controller' => 'HostController', 'action' => 'generateCode'],
            '/student/supervisor' => ['controller' => 'StudentController', 'action' => 'viewSupervisor'],
            '/staff/assessments' => ['controller' => 'AssessmentController', 'action' => 'index'],
            '/assessment/verify-code' => ['controller' => 'AssessmentController', 'action' => 'verifyCode'],
            '/staff/assessment/conduct' => ['controller' => 'AssessmentController', 'action' => 'conduct'],
            '/staff/assessments/schedule' => ['controller' => 'AssessmentController', 'action' => 'schedule'],
            '/assessment/submit' => ['controller' => 'AssessmentController', 'action' => 'submit'],
            '/assessment/view' => ['controller' => 'AssessmentController', 'action' => 'viewAssessment'],
            '/admin/students' => ['controller' => 'AdminController', 'action' => 'viewStudents'],
            '/admin/students/create' => ['controller' => 'AdminController', 'action' => 'createStudent'],
            '/admin/students/bulk-upload' => ['controller' => 'AdminController', 'action' => 'bulkUploadStudents'],
            '/admin/students/clear' => ['controller' => 'AdminController', 'action' => 'clearStudent'],
            '/admin/students/progress' => ['controller' => 'AdminController', 'action' => 'viewStudentProgress'],
            '/admin/reports' => ['controller' => 'ReportController', 'action' => 'adminIndex'],
            '/admin/reports/assessment-schedule' => ['controller' => 'ReportController', 'action' => 'assessmentSchedule'],
            '/admin/reports/assessment-summary' => ['controller' => 'ReportController', 'action' => 'assessmentSummary'],
            '/admin/reports/effectiveness' => ['controller' => 'ReportController', 'action' => 'effectiveness'],
            '/admin/reports/supervisor-stats' => ['controller' => 'ReportController', 'action' => 'supervisorStats'],
            '/admin/reports/host-performance' => ['controller' => 'ReportController', 'action' => 'adminHostPerformance'],
            '/admin/reports/placement-completions' => ['controller' => 'ReportController', 'action' => 'placementCompletions'],
            '/admin/reports/placement-impact' => ['controller' => 'ReportController', 'action' => 'placementImpact'],
            '/staff/reports' => ['controller' => 'ReportController', 'action' => 'staffIndex'],
            '/staff/reports/lecturer-grades' => ['controller' => 'ReportController', 'action' => 'lecturerGrades'],
            '/host/reports' => ['controller' => 'ReportController', 'action' => 'hostIndex'],
            '/host/reports/host-performance' => ['controller' => 'ReportController', 'action' => 'hostPerformance'],
            '/student/reports' => ['controller' => 'ReportController', 'action' => 'studentIndex'],
            '/student/reports/upload' => ['controller' => 'ReportController', 'action' => 'upload'],
            '/student/settings' => ['controller' => 'SettingsController', 'action' => 'studentIndex'],
            '/staff/settings' => ['controller' => 'SettingsController', 'action' => 'staffIndex'],
            '/admin/settings' => ['controller' => 'SettingsController', 'action' => 'adminIndex'],
            '/host/settings' => ['controller' => 'SettingsController', 'action' => 'hostIndex'],
            '/settings/update-profile' => ['controller' => 'SettingsController', 'action' => 'updateProfile'],
            '/settings/update-password' => ['controller' => 'SettingsController', 'action' => 'updatePassword'],
            '/auth/first-login' => ['controller' => 'SettingsController', 'action' => 'firstLogin'],
            '/auth/first-login/save' => ['controller' => 'SettingsController', 'action' => 'processFirstLogin'],
            '/reports/print/logbook' => ['controller' => 'LogbookController', 'action' => 'printLogbook'],
            '/assessment/print-summary' => ['controller' => 'AssessmentController', 'action' => 'printSummary'],
            '/reports/print/grades' => ['controller' => 'AssessmentController', 'action' => 'printSummary'],
            '/reports/print/completion' => ['controller' => 'ReportController', 'action' => 'printCompletion'],
            '/reports/print/supervisors' => ['controller' => 'ReportController', 'action' => 'printSupervisors'],
            '/reports/print/assessment-summary' => ['controller' => 'ReportController', 'action' => 'printAssessmentSummary'],
            '/reports/print/effectiveness' => ['controller' => 'ReportController', 'action' => 'printEffectiveness'],
            '/reports/print/assessment-schedule' => ['controller' => 'ReportController', 'action' => 'printAssessmentSchedule'],
            '/reports/print/supervisor-stats' => ['controller' => 'ReportController', 'action' => 'printSupervisorStats'],
            '/reports/print/host-performance' => ['controller' => 'ReportController', 'action' => 'printHostPerformance'],
            '/reports/print/placement-completions' => ['controller' => 'ReportController', 'action' => 'printPlacementCompletions'],
            '/reports/print/placement-impact' => ['controller' => 'ReportController', 'action' => 'printPlacementImpact'],
            '/admin/supervision/bulk' => ['controller' => 'BulkSupervisionController', 'action' => 'index'],
            '/admin/supervision/bulk/assign' => ['controller' => 'BulkSupervisionController', 'action' => 'processAssignment'],
            // I'm gonna add more routes here as we go along, but this is the basic idea
        ];
    }//end of function

    // Dispatches the request to the appropriate controller/action based on the URL
    public function dispatch() {
        // 1. Get only the path part of the URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // 2. Determine the project root (the directory that contains the "public" folder)
        //    Works for any script inside public, e.g. index.php, debug_route.php, etc.
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        $scriptDir  = dirname($scriptName);                     // e.g. /student-placement-attachment-system/public
        $projectRoot = preg_replace('#/public$#i', '', $scriptDir);
        $projectRoot = rtrim($projectRoot, '/');

        // 3. Strip the project root from the URI (case-insensitive for Windows)
        if ($projectRoot !== '' && stripos($uri, $projectRoot) === 0) {
            $uri = substr($uri, strlen($projectRoot));
        }

        // 4. Strip /public if still present (handles direct /public/ access)
        $uri = preg_replace('#^/public#i', '', $uri);

        // 5. Strip /index.php if present (non-rewrite fallback)
        $uri = preg_replace('#^/index\.php#i', '', $uri);

        // 6. Normalize: ensure leading slash, remove trailing slashes
        $path = '/' . trim($uri, '/');

        // 7. Match route
        if (array_key_exists($path, $this->routes)) {
            $controllerName = "App\\Controllers\\" . $this->routes[$path]['controller'];
            $actionName = $this->routes[$path]['action'];

            $controller = new $controllerName();
            $controller->$actionName();
        } else {
            http_response_code(404);
            echo "404 Not Found — Route '$path' not registered.";
        }
    }
}
