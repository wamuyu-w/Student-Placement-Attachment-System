<?php
namespace App\Config;

// Database configuration class for managing database connections
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $name = 'attachmentmanagementsystem';  
    public $conn;

    //Provides the actual connection to the db
    public function connect() {
        $this->conn = null;
        try {
            //establishing a connection
            $this->conn = new \mysqli($this->host, $this->user, $this->pass, $this->name);
            //Throws an error if the connection fails
            if ($this->conn->connect_error) {
                throw new \Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch(\Exception $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}
