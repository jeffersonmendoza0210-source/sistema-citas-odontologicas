<?php
// Esto sincroniza las funciones de fecha de PHP (date, time, etc.)
date_default_timezone_set('America/Lima'); 

class Database {
    private $host = 'localhost';
    private $db_name = 'citas_medicas_db';
    private $username = 'root';
    private $password = ''; 
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8';
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Esto sincroniza la base de datos (SQL)
            $this->conn->exec("SET time_zone = '-05:00'");
            
        } catch(PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
        return $this->conn;
    }
}