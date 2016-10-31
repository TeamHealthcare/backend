<?php

//Class DbConnect
class DbConnect
{
    //Variable to store database link
    private $con;

    // PDO connection handler
    private $pdo;

    private $opt;
    private $connectionMask = "mysql:host=%s;dbname=%s;charset=utf8mb4";

    //Class constructor
    function __construct()
    {

    }

    // TODO:  Once PDO routines are in place, remove method.  This method will connect to the database
    function connect()
    {
        include_once dirname(__FILE__) . '/Constants.php';

        //connecting to mysql database
        $this->con = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        //Checking if any error occurred while connecting
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        //finally returning the connection link 
        return $this->con;
    }

    function pdoConnect()
    {
        include_once dirname(__FILE__) . '/Constants.php';

        $this->opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        $this->pdo = new PDO(sprintf($this->connectionMask, DB_HOST, DB_NAME), DB_USERNAME, DB_PASSWORD, $this->opt);

        return $this->pdo;
    }

}

