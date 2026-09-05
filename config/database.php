<?php

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $config;

    public $conn;

    public function __construct()
    {
        $configFile = __DIR__ . '/database.local.php';

        if (!file_exists($configFile)) {
            die("Falta el archivo de configuración de base de datos.");
        }

        $this->config = require $configFile;

        $this->host = $this->config['host'];
        $this->db_name = $this->config['db_name'];
        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }

    public function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}