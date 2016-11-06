<?php

class DbOperation
{
    private $con;
    private $pdo;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
        $this->pdo = $db->pdoConnect();
    }

    // Method to register a new student
    public function createStudent($name,$username,$pass){
        if (!$this->isStudentExists($username)) {
            $password = md5($pass);
            $apikey = $this->generateApiKey();
            $stmt = $this->con->prepare("INSERT INTO allstudents (name, username, password, api_key) values(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $username, $password, $apikey);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }

    // Start with this one as this method uses PDO
    public function addUser($employeeNumber, $jobTitle, $password, $employeeName) {
        $statement = $this->pdo->prepare("INSERT INTO user (EmployeeNumber, JobTItle, Password, EmployeeName, DateEntered) VALUES (?, ?, ?, ?, ?)");
        $hashedPassword = md5($password);
        $dateEntered = date('Y-m-d H:i:s');
        $affected = $statement->execute(array($employeeNumber, $jobTitle, $hashedPassword, $employeeName, $dateEntered));
        return $affected > 0 ;
    }

    // Method to let a student log in
    public function studentLogin($username,$pass){
        $password = md5($pass);
        $stmt = $this->con->prepare("SELECT * FROM allstudents WHERE username=? and password=?");
        $stmt->bind_param("ss",$username,$password);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows>0;
    }

    public function getAllPatients() {

        $statement = $this->pdo->prepare("SELECT id, name, username FROM allstudents");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getPatientById($patientId){
        // $password = md5($pass);
        $statement = $this->pdo->prepare("SELECT id, name, username FROM allstudents WHERE id=?");
        $statement->execute(array($patientId));
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    //Method to check the student username already exist or not
    private function isStudentExists($username) {
        $stmt = $this->con->prepare("SELECT id from allstudents WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    //Method to check the faculty username already exist or not
    private function isFacultyExists($username) {
        $stmt = $this->con->prepare("SELECT id from faculties WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    //Checking the student is valid or not by api key
    public function isValidStudent($api_key) {
        $stmt = $this->con->prepare("SELECT id from allstudents WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    //Checking the faculty is valid or not by api key
    public function isValidFaculty($api_key){
        $stmt = $this->con->prepare("SELECT id from faculties WHERE api_key=?");
        $stmt->bind_param("s",$api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows>0;
    }

    //Method to generate a unique api key every time
    private function generateApiKey(){
        return md5(uniqid(rand(), true));
    }
}