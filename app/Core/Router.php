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
            '/staff/students' => ['controller' => 'StaffController', 'action' => 'viewStudents'],
            '/staff/supervision' => ['controller' => 'StaffController', 'action' => 'supervision'],
            '/host/students' => ['controller' => 'HostController', 'action' => 'viewStudents'],
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
            '/admin/reports/host-performance' => ['controller' => 'ReportController', 'action' => 'hostPerformance'],
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
            '/admin/supervision/bulk' => ['controller' => 'BulkSupervisionController', 'action' => 'index'],
            '/admin/supervision/bulk/assign' => ['controller' => 'BulkSupervisionController', 'action' => 'processAssignment'],
            // I'm gonna add more routes here as we go along, but this is the basic idea
        ];
    }

    public function dispatch() {
        // Get current URL path relative to project root
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Get the directory where index.php resides (e.g., /project/public)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = str_replace('\\', '/', $scriptDir); // Normalize slashes for Windows
        
        // Remove the script directory from the URI to get the clean route        
        $path = str_replace($scriptDir, '', $uri);
        // Ensure path starts with a slash and has no trailing slash (except for root)
        if ($path === '') $path = '/';
    
        if (array_key_exists($path, $this->routes)) {
            $controllerName = "App\\Controllers\\" . $this->routes[$path]['controller'];
            $actionName = $this->routes[$path]['action'];

            $controller = new $controllerName();
            $controller->$actionName();
        } else {
            // Fallback to 404
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
