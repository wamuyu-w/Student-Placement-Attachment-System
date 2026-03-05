<?php
namespace App\Controllers;
// this controller will handle the landing page and any public-facing pages that don't require authentication
use App\Core\Controller;

class HomeController extends Controller {
    // this controller will handle the landing page and any public-facing pages that don't require authentication
    public function index() {
        $this->view('landing', [], false);
    }
}
