<?php

namespace App;

// using singleton pattern
class DatabaseConnection{

    // Hold the class instance.
    private static $instance = null;
    private static $conn;
    // private $config;
    // private $connectionParams;

    // Public constructor
    private function __construct()
    {
        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = require 'config.php';

        self::$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    }

    public static function getInstance()
    {
      if(!self::$instance)
      {
        self::$instance = new DatabaseConnection();
      }
     
      return self::$instance;
    }
    
    public function getConnection()
    {
      return self::$conn;
    }

}
