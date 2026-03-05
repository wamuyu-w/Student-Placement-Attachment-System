<?php
namespace App\Config;
// Database configuration class for managing database connections
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $name = 'attachmentmanagementsystem';
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new \mysqli($this->host, $this->user, $this->pass, $this->name);
            if ($this->conn->connect_error) {
                throw new \Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch(\Exception $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}
