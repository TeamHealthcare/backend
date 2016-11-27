<?php
// This does the work...
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

    // TODO:  Add exception handling to this in case query barfs...
    public function executeQueryToReturnData($query, $array) {
        $statement = $this->pdo->prepare($query);
        if (count($array) == 0)
            $statement->execute();
        else
            $statement->execute($array);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // TODO:  Add exception handling to this in case query barfs...
    public function executeAddUpdateQuery($query, $array) {
        $statement = $this->pdo->prepare($query);
        return $statement->execute($array) > 0;
    }

    // Start with this one as this method uses PDO
    public function addUser($employeeNumber, $jobTitle, $password, $employeeName) {
        $statement = $this->pdo->prepare("INSERT INTO user (EmployeeNumber, JobTItle, Password, EmployeeName, DateEntered) VALUES (?, ?, ?, ?, ?)");
        $hashedPassword = md5($password);
        $dateEntered = date('Y-m-d H:i:s');
        $affected = $statement->execute(array($employeeNumber, $jobTitle, $hashedPassword, $employeeName, $dateEntered));
        return $affected > 0 ;
    }

    // START CARRIER
    public function getAllCarriers() {
        $query = "SELECT InsuranceCarrierId, Carrier FROM insurancecarrier WHERE Active = 1";
        return $this->executeQueryToReturnData($query, []);
    }

    public function getCarrierById($insuranceCarrierId) {

        $query = "SELECT InsuranceCarrierId, Carrier, Address, Active FROM insurancecarrier WHERE InsuranceCarrierId = ?";
        return $this->executeQueryToReturnData($query, func_get_args());
    }

    public function addCarrier($carrier, $address) {
        $query = "INSERT INTO insurancecarrier (Carrier, Address, Active) VALUES (?, ?, 1)";
        return $this->executeAddUpdateQuery($query, func_get_args());
    }

    public function updateCarrier($carrier, $address, $active, $insuranceCarrierId) {
        $query = "UPDATE insurancecarrier SET Carrier = ?, Address = ?, Active = ? WHERE InsuranceCarrierId = ?";
        return $this->executeAddUpdateQuery($query, func_get_args());
    }
    // END CARRIER

    // START SERVICE
    public function getAllServices() {
        $query = "SELECT ServiceId, InsuranceCarrierId, Description, Cost FROM service";
        return $this->executeQueryToReturnData($query, []);
    }

    public function getAllServicesByCarrier($insuranceCarrierId) {
        $query = "SELECT ServiceId, InsuranceCarrierId, Description, Cost FROM service WHERE InsuranceCarrierId = ?";
        return $this->executeQueryToReturnData($query, func_get_args());
    }

    public function getServiceById($serviceId) {
        $query = "SELECT ServiceId, InsuranceCarrierId, Description, Cost FROM service WHERE ServiceId = ?";
        return $this->executeQueryToReturnData($query, func_get_args());
    }

    public function addService($insuranceCarrierId, $description, $cost) {
        $query = "INSERT INTO service (InsuranceCarrierId, Description, Cost) VALUES (?, ?, ?)";
        return $this->executeAddUpdateQuery($query, func_get_args());
    }

    public function updateService($insuranceCarrierId, $description, $cost, $serviceId) {
        $query = "UPDATE service SET InsuranceCarrierId = ?, Description = ?, Cost = ? WHERE ServiceId = ?";
        return $this->executeAddUpdateQuery($query, func_get_args());
    }
    // END SERVICE

    // TODO:  RETROFIT TO WORK LIKE CARRIER
    public function getAllPatients() {

//        $query = "SELECT PatientId, PatientName, PhoneNumber, Address, City, State, ZipCode";
//        $query .= ", InsuranceCarrierId, DateOfBirth, Gender, Physician FROM electronicpatient;";

        $query = "SELECT PatientId, PatientName, PhoneNumber, Address, City, State, ZipCode";
        $query .= ", InsuranceCarrierId, DateOfBirth, Gender FROM electronicpatient;";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getPatientById($patientId){
        // $password = md5($pass);
        $query = "SELECT PatientId, PatientName, PhoneNumber, Address, City, State, ZipCode";
        $query .= ", InsuranceCarrierId, DateOfBirth, Gender, Physician FROM electronicpatient ";
        $query .= "WHERE PatientId = ?";

        $statement = $this->pdo->prepare($query);
        $statement->execute(array($patientId));
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    public function addElectronicPatient($patientName, $phoneNumber, $address, $city, $state,
                                         $zipCode, $insuranceCarrierId, $dateOfBirth, $gender, $physician) {

        $query = "INSERT INTO electronicpatient ";
        $query .= "(PatientName, PhoneNumber, Address, City, State, ZipCode, InsuranceCarrierId, DateOfBirth, Gender, Physician) VALUES ";
        $query .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $statement = $this->pdo->prepare($query);
        $affected = $statement->execute(func_get_args());
        return $affected > 0 ;
    }

    public function updateElectronicPatient($patientName, $phoneNumber, $address, $city, $state,
                                            $zipCode, $insuranceCarrierId, $dateOfBirth, $gender, $physician, $patientid) {

        $query  = "UPDATE electronicpatient SET ";
        $query .= " PatientName = ?, ";
        $query .= " PhoneNumber = ?, ";
        $query .= " Address = ?, ";
        $query .= " City = ?, ";
        $query .= " State = ?, ";
        $query .= " ZipCode = ?, ";
        $query .= " InsuranceCarrierId = ?, ";
        $query .= " DateOfBirth = ?, ";
        $query .= " Gender = ?, ";
        $query .= " Physician = ? ";
        $query .= "WHERE PatientId = ?";

        $statement = $this->pdo->prepare($query);
        $affected = $statement->execute(func_get_args());
        return $affected > 0 ;
    }

    // TODO:  Since a lot of this code are duplicates, create one generic method (executeQueryToReturnData)
    // to handle a query then pass the query to the generic method
    public function getPatientsForDropdown() {
        $query = "SELECT PatientId, PatientName FROM electronicpatient;";
        return $this->executeQueryToReturnData($query);
    }
    // END TO RETROFIT TO WORK LIKE CARRIER

    // TODO:  RETROFIT MEDICAL ENCOUNTER TO WORK LIKE CARRIER
    public function getMedicalEncounters() {

        $query = "SELECT MedicalEncounterId, EncounterDate, Complaint, VitalSigns, Notes, PharmacyOrder, Diagnosis ";
        $query .= ", TreatmentPlan, Referral, FollowUpNotes, PatientId, LabOrderId FROM medicalencounter;";

        return $this->executeQueryToReturnData($query,[]);
    }

    public function getMedicalEncounterById($medicalencounterid) {

        $query = "SELECT MedicalEncounterId, EncounterDate, Complaint, VitalSigns, Notes, PharmacyOrder, Diagnosis ";
        $query .= ", TreatmentPlan, Referral, FollowUpNotes, PatientId, LabOrderId FROM medicalencounter WHERE MedicalEncounterId = ?";

        $statement = $this->pdo->prepare($query);
        $statement->execute(func_get_args());
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    public function addMedicalEncounter($encounterDate, $labOrderId, $complaint, $vitalSigns, $notes, $pharmacyOrder, $diagnosis,
                                        $treatmentPlan, $referral, $followupNotes, $patientId) {

        $query = "INSERT INTO medicalencounter ";
        $query .= "(EncounterDate, LabOrderId, Complaint, VitalSigns, Notes, PharmacyOrder, diagnosis, TreatmentPlan, Referral, FollowUpNotes, PatientId) VALUES ";
        $query .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


        $statement = $this->pdo->prepare($query);
        // $hashedPassword = md5($password);
        $dateEntered = date('Y-m-d H:i:s');

        // This works instead of execute(array(arg1, ..)), so use this...
        $affected = $statement->execute(func_get_args());
        return $affected > 0;
    }

    public function updateMedicalEncounter($encounterDate, $complaint, $vitalSigns, $notes, $pharmacyOrder, $diagnosis,
                                           $treatmentPlan, $referral, $followupNotes, $patientId, $medicalEncounterId) {

        $query  = "UPDATE medicalencounter  ";
        $query .= "SET ";
        $query .= "	EncounterDate = ?  ";
        $query .= "	,Complaint = ?  ";
        $query .= "	,VitalSigns = ?  ";
        $query .= "	,Notes = ?  ";
        $query .= "	,PharmacyOrder = ?  ";
        $query .= "	,Diagnosis = ?  ";
        $query .= "	,TreatmentPlan = ?  ";
        $query .= "	,Referral = ?  ";
        $query .= "	,FollowUpNotes = ?  ";
        $query .= "	,PatientId = ?  ";
        $query .= "WHERE ";
        $query .= "	MedicalEncounterId = ? ";

        $statement = $this->pdo->prepare($query);
        $affected = $statement->execute(func_get_args());
        return $affected > 0 ;
    }
    // END TO RETROFIT MEDICAL ENCOUNTER TO WORK LIKE CARRIER

    // TODO:  Remove everything below and use PDO
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //method to register a new faculty
    public function createFaculty($name,$username,$pass,$subject){
        if (!$this->isFacultyExists($username)) {
            $password = md5($pass);
            $apikey = $this->generateApiKey();
            $stmt = $this->con->prepare("INSERT INTO faculties (name, username, password, subject, api_key) values(?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $username, $password, $subject, $apikey);
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

    //method to let a faculty log in
    public function facultyLogin($username, $pass){
        $password = md5($pass);
        $stmt = $this->con->prepare("SELECT * FROM faculties WHERE username=? and password =?");
        $stmt->bind_param("ss",$username,$password);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows>0;
    }

    //Method to create a new assignment
    public function createAssignment($name,$detail,$facultyid,$studentid){
        $stmt = $this->con->prepare("INSERT INTO assignments (name,details,faculties_id,students_id) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii",$name,$detail,$facultyid,$studentid);
        $result = $stmt->execute();
        $stmt->close();
        if($result){
            return true;
        }
        return false;
    }

    //Method to update assignment status
    public function updateAssignment($id){
        $stmt = $this->con->prepare("UPDATE assignments SET completed = 1 WHERE id=?");
        $stmt->bind_param("i",$id);
        $result = $stmt->execute();
        $stmt->close();
        if($result){
            return true;
        }
        return false;
    }

    //Method to get all the assignments of a particular student
    public function getAssignments($studentid){
        $stmt = $this->con->prepare("SELECT * FROM assignments WHERE students_id=?");
        $stmt->bind_param("i",$studentid);
        $stmt->execute();
        $assignments = $stmt->get_result();
        $stmt->close();
        return $assignments;
    }

    //Method to get student details
    public function getStudent($username){
        $stmt = $this->con->prepare("SELECT * FROM allstudents WHERE username=?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $student;
    }

    //Method to fetch all students from database
    public function getAllStudents(){
        $stmt = $this->con->prepare("SELECT * FROM allstudents");
        $stmt->execute();
        $students = $stmt->get_result();
        $stmt->close();
        return $students;
    }

    //Method to get faculy details by username
    public function getFaculty($username){
        $stmt = $this->con->prepare("SELECT * FROM faculties WHERE username=?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $faculty;
    }

    //Method to get faculty name by id
    public function getFacultyName($id){
        $stmt = $this->con->prepare("SELECT name FROM faculties WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $faculty['name'];
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