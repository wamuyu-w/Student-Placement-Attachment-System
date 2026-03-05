<?php
namespace App\Core;
// this was supposed to be the base controller but I made another one in the core folder,
// I don't know why I did that, but I will keep this here for now. It can be used for shared logic between controllers if needed in the future.
// unaeza toa alafu utest kama itabreak the app but hopefully it won't since it's not being used yet.
class Controller {
    public function model($model) {
        $modelClass = "App\\Models\\" . $model;
        return new $modelClass();
    }

    public function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }
    
    public function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
