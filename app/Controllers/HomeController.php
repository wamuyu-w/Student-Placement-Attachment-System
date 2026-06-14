<?php
namespace App\Controllers;
/**
 * Class HomeController
 * 
 * Handles the application's landing page and any public-facing pages 
 * that do not require authentication or active sessions.
 */
class HomeController extends Controller {
    /**
     * Renders the public landing page of the application.
     * 
     * @return void
     */
    public function index() {
        $this->view('landing', [], false);
    }
}
